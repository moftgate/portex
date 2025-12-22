<?php

namespace App\Enums;

use App\Concerns\EnumToArray;

enum Role: string
{
    use EnumToArray;

    case ADMIN = 'admin';
    case USER = 'user';

    public function text(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };

    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isOrganizator(): bool
    {
        return $this === self::USER;
    }
}
