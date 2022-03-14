<?php

namespace App\Controller\Api;

use App\Entity\Link;
use App\Exception\Repository\LinkNotFoundException;
use App\Form\LinkFormType;
use App\Normalizer\LinkNormalizer;
use App\Repository\LinkRepository;
use JsonException;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(host: 'api.%app_domain%')]
class ApiController extends AbstractController
{
    public function __construct(
        private LinkRepository $linkRepository,
        private LinkNormalizer $linkNormalizer
    ) {
    }

    /**
     * @OA\Tag(name="Link")
     * @throws LinkNotFoundException|JsonException
     */
    #[Route(path: '/links/{shortcode}', name: 'api_link', methods: ['GET'])]
    public function linkAction(string $shortcode): JsonResponse
    {
        $link = $this->linkRepository->resolve($shortcode);

        return $this->json($this->linkNormalizer->normalize($link));
    }

    /**
     * @throws JsonException
     * @OA\Tag(name="Link")
     */
    #[Route(path: '/links/new', name: 'api_create_redirect', methods: ['POST'])]
    public function createRedirectionAction(Request $request): JsonResponse
    {
        $link = new Link();

        $form = $this->createForm(LinkFormType::class, $link, [
            'csrf_protection' => false
        ]);

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $form->submit($data, true);

        if ($form->isValid()) {
            $existingLink = $this->linkRepository->findOneBy([
                'url' => $link->getUrl()
            ]);

            if ($existingLink instanceof Link) {
                return $this->json($this->linkNormalizer->normalize($existingLink));
            }

            $this->linkRepository->add($link);

            return $this->json($this->linkNormalizer->normalize($link));
        }

        $errors = [];

        foreach ($form->getErrors(true) as $formError) {
            $errors[] = $formError->getMessage();
        }

        throw new InvalidArgumentException(implode(' ', $errors));
    }
}
