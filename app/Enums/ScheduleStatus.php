<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    use HasEnumValues;
    case Pending = 'pending';
    case Assigned = 'assigned';
    case Canceled = 'canceled';
    case Completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Assigned => 'warning',
            self::Canceled => 'danger',
            self::Completed => 'primary',
        };
    }
}
