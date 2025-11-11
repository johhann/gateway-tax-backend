<?php

namespace App\Enums;

enum TaxRequestStatus: string
{
    use HasEnumValues;

    case Pending = 'pending';
    case Processing = 'processing';
    case Processed = 'processed';
    case ReadyForPickup = 'ready_for_pickup';
    case Canceled = 'canceled';
    case Completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Processing => 'warning',
            self::Processed => 'success',
            self::ReadyForPickup => 'info',
            self::Canceled => 'danger',
            self::Completed => 'success',
        };
    }
}
