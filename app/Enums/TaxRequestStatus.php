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
}
