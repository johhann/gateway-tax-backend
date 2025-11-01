<?php

namespace App\Enums;

enum ProfileUserStatus: string
{
    case SUBMITTED = 'submitted';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case ACCEPTED = 'accepted';
    case CHANGE_REQUEST = 'change_request';

    public static function getValues(): array
    {
        return [
            self::SUBMITTED->value,
            self::PROCESSING->value,
            self::PROCESSED->value,
            self::ACCEPTED->value,
            self::CHANGE_REQUEST->value,
        ];
    }
}
