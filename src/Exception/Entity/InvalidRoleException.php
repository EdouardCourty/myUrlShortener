<?php

namespace App\Exception\Entity;

use App\Entity\User;
use Exception;

class InvalidRoleException extends Exception
{
    public static function createFromUserAndRole(User $user, string $wantedRole): self
    {
        return new self(sprintf('Cannot assign role %s to user %s', $wantedRole, $user));
    }
}
