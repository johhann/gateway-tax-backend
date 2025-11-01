<?php

namespace App\Enums;

enum ProfileProgressStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case PROCESSED = 'processed';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public static function getValues(): array
    {
        return [
            self::PENDING->value,
            self::ASSIGNED->value,
            self::PROCESSED->value,
            self::PAUSED->value,
            self::CANCELLED->value,
            self::COMPLETED->value,
        ];
    }

    public function color(): ?string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ASSIGNED => 'info',
            self::PROCESSED => 'success',
            self::PAUSED => 'warning',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
        };
    }
}
