<?php

namespace App\Serializer;

use App\Entity\Link;
use App\Service\UrlHasher;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkSerializer
{
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private UrlHasher $urlHasher,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[ArrayShape([
        'shortcode' => 'string',
        'custom_shortcode' => 'null|string',
        'usages' => 'int',
        'public_url' => 'string',
        'redirect_url' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string'
    ])]
    public function serialize(Link $link): array
    {
        $shortcode = $this->urlHasher->getHasher()->encode($link->getId());

        return [
            'shortcode' => $shortcode,
            'custom_shortcode' => $link->getCustomShortcode(),
            'usages' => $link->getUsageCount(),
            'public_url' => $this->urlGenerator->generate('redirect', [
                'shortcode' => $link->getCustomShortcode() ?? $shortcode
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'redirect_url' => $link->getUrl(),
            'created_at' => $link->getCreatedAt()->format(self::DATETIME_FORMAT),
            'updated_at' => $link->getUpdatedAt()->format(self::DATETIME_FORMAT)
        ];
    }
}
