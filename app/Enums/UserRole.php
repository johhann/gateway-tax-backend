<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case OPERATION = 'operation';
    case BRANCH_MANAGER = 'branch_manager';
    case ACCOUNTANT = 'accountant';
    case USER = 'user';

    public static function getValues(): array
    {
        return [
            self::ADMIN->value,
            self::OPERATION->value,
            self::BRANCH_MANAGER->value,
            self::ACCOUNTANT->value,
            self::USER->value,
        ];
    }
}
