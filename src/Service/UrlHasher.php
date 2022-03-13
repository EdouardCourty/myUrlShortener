<?php

namespace App\Service;

use Hashids\Hashids;

class UrlHasher
{
    public const HASHES_LENGHT = 8;

    public function getHasher(): Hashids
    {
        return new Hashids($_ENV['APP_SECRET'], self::HASHES_LENGHT);
    }
}
