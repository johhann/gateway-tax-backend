<?php

namespace App\Enums;

enum ScheduleType: string
{
    use HasEnumValues;
    case OnlineCall = 'online_call';
    case InPersonMeeting = 'in_person_meeting';

    public function color(): string
    {
        return match ($this) {
            self::OnlineCall => 'success',
            self::InPersonMeeting => 'warning',
        };
    }
}
