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
            self::PROCESSED => 'blue',
            self::PAUSED => 'warning',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
        };
    }

    public function label(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ASSIGNED => 'Assigned',
            self::PROCESSED => 'Processed',
            self::PAUSED => 'Paused',
            self::CANCELLED => 'Canceled',
            self::COMPLETED => 'Completed',
        };
    }

    public function order(): ?string
    {
        return match ($this) {
            self::PENDING => 1,
            self::PROCESSED => 2,
            self::ASSIGNED => 3,
            self::PAUSED => 4,
            self::COMPLETED => 5,
            self::CANCELLED => 6,
        };
    }
}
