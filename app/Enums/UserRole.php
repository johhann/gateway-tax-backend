<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function getValues(): array
    {
        return [
            self::ADMIN->value,
            self::USER->value,
        ];
    }
}
