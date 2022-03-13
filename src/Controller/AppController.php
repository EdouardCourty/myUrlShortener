<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkFormType;
use App\Repository\Exception\LinkNotFoundException;
use App\Repository\LinkRepository;
use App\Service\UrlHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(host: '%app_domain%')]
class AppController extends AbstractController
{
    public function __construct(
        private LinkRepository $linkRepository,
        private UrlHasher $urlHasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(path: '/', name: 'homepage')]
    public function homepageAction(Request $request): Response
    {
        $link = new Link();
        $form = $this->createForm(LinkFormType::class, $link);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($link->getCustomShortcode() === null) {
                $existingLink = $this->linkRepository->findOneBy([
                    'url' => $link->getUrl()
                ]);

                if ($existingLink instanceof Link) {
                    return $this->redirect($this->generateUrl('link_created', [
                        'shortcode' => $this->urlHasher->getHasher()->encode($existingLink->getId())
                    ]));
                }
            } else {
                $existingShortcodedLink = $this->linkRepository->findOneBy([
                    'customShortcode' => $link->getCustomShortcode()
                ]);

                if ($existingShortcodedLink instanceof Link) {
                    $this->addFlash('error', sprintf(
                        'The %s shortcode is already in use, please chose another one.',
                        $link->getCustomShortcode()
                    ));

                    return $this->redirectToRoute('homepage');
                }
            }

            $this->linkRepository->add($link);

            $redirectLink = $link->getCustomShortcode() ?: $this->urlHasher->getHasher()->encode($link->getId());

            return $this->redirect($this->generateUrl('link_created', [
                'shortcode' => $redirectLink
            ]));
        }

        $latestLinks = $this->linkRepository->findBy([], ['id' => 'DESC'], 12);

        $formattedLatestLinks = array_map(fn (Link $link) => [
            'shortcode' => $this->urlHasher->getHasher()->encode($link->getId()),
            'url' => $link->getUrl(),
            'customShortcode' => $link->getCustomShortcode()
        ], $latestLinks);

        return $this->render('app/index.html.twig', [
            'form' => $form->createView(),
            'latestLinks' => $formattedLatestLinks
        ]);
    }

    #[Route(path: '/link/{shortcode}', name: 'link_created')]
    public function afterSubmissionAction(string $shortcode): Response
    {
        try {
            $link = $this->linkRepository->resolve($shortcode);

            return $this->render('app/link-create-success.html.twig', [
                'redirectUrl' => $link->getUrl(),
                'shortcode' => $shortcode
            ]);
        } catch (LinkNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/redirect/{shortcode}', name: 'redirect')]
    public function redirectAction(string $shortcode): Response
    {
        try {
            $link = $this->linkRepository->resolve($shortcode);

            $link->incrementUsage();
            $this->entityManager->flush();

            return $this->redirect($link->getUrl());
        } catch (LinkNotFoundException $exception) {
            $this->addFlash('error', $exception->getMessage());

            throw new NotFoundHttpException();
        }
    }
}
