<?php

namespace App\Repository\Exception;

use Exception;

class LinkNotFoundException extends Exception
{
    public static function createFromShortcode(string $shortcode): self
    {
        return new self(sprintf(
            'No redirect link with code %s could be found.',
            $shortcode
        ));
    }
}
