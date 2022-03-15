<?php

namespace App\Exception\Api;

use Exception;

class ShortcodeAlreadyInUseException extends Exception
{
    public static function createFromShortcode(string $shortcode): self
    {
        return new self(sprintf(
            'The shortcode %s is already in use.',
            $shortcode
        ));
    }
}
