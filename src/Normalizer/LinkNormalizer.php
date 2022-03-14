<?php

namespace App\Normalizer;

use App\Entity\Link;
use App\Service\UrlHasher;
use JetBrains\PhpStorm\ArrayShape;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkNormalizer
{
    public function __construct(
        private UrlHasher $urlHasher,
        private UrlGeneratorInterface $urlGenerator,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * @throws JsonException
     */
    #[ArrayShape([
        'shortcode' => 'string',
        'custom_shortcode' => 'null|string',
        'usages' => 'int',
        'public_url' => 'string',
        'redirect_url' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string'
    ])]
    public function normalize(Link $link): array
    {
        $shortcode = $this->urlHasher->getHasher()->encode($link->getId());

        $serializedEntity = json_decode($this->serializer->serialize($link, 'json', SerializationContext::create()->setGroups([
            'apiGetLink'
        ])->setSerializeNull(true)), true, 512, JSON_THROW_ON_ERROR);

        return array_merge($serializedEntity, [
            'shortcode' => $shortcode,
            'public_url' => $this->urlGenerator->generate('redirect', [
                'shortcode' => $link->getCustomShortcode() ?? $shortcode
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
}
