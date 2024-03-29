<?php

namespace App\Controller;

use App\Entity\Link;
use App\Exception\Repository\LinkNotFoundException;
use App\Form\LinkFormType;
use App\Repository\LinkRepository;
use App\Service\UrlHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
            $customShortcode = $link->getCustomShortcode();

            if ($customShortcode !== null && !$this->linkRepository->isShortcodeAvailable($customShortcode)) {
                $form->addError(new FormError('This custom shortcode is already in use.'));

                return $this->render('app/index.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $existingLink = $this->linkRepository->findOneBy([
                'url' => $link->getUrl()
            ]);

            if ($existingLink instanceof Link && !$customShortcode) {
                return $this->redirectToLinkPage($this->urlHasher->getHasher()->encode($existingLink->getId()));
            }

            $this->linkRepository->add($link);

            return $this->redirectToLinkPage($customShortcode ?? $this->urlHasher->getHasher()->encode($link->getId()));
        }

        return $this->render('app/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function redirectToLinkPage(string $redirectCode): Response
    {
        return $this->redirect($this->generateUrl('link_view', [
            'shortcode' => $redirectCode
        ]));
    }

    #[Route(path: '/link/{shortcode}', name: 'link_view')]
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
