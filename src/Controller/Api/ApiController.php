<?php

namespace App\Controller\Api;

use App\Repository\Exception\LinkNotFoundException;
use App\Repository\LinkRepository;
use App\Serializer\LinkSerializer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Throwable;

#[Route(host: 'api.%app_domain%')]
class ApiController extends AbstractController
{
    public function __construct(
        private LinkRepository $linkRepository,
        private LinkSerializer $linkSerializer
    ) {
    }

    /**
     * @OA\Tag(name="Link")
     */
    #[Route('/link/{shortcode}', 'api_link', methods: ['GET'])]
    public function linkAction(string $shortcode): JsonResponse
    {
        try {
            $link = $this->linkRepository->resolve($shortcode);

            return $this->formatApiResponse($this->linkSerializer->serialize($link));
        } catch (LinkNotFoundException $exception) {
            return $this->formatApiResponse(null, $exception);
        }
    }

    private function formatApiResponse(?array $content, ?Throwable $exception = null): JsonResponse
    {
        return new JsonResponse([
            'content' => $content,
            'error' => $exception?->getMessage(),
            'timestamp' => (new DateTime('now'))->getTimestamp()
        ]);
    }
}
