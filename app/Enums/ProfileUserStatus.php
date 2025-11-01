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

    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'secondary',
            self::PROCESSING => 'warning',
            self::PROCESSED => 'success',
            self::ACCEPTED => 'primary',
            self::CHANGE_REQUEST => 'danger',
        };
    }
}
