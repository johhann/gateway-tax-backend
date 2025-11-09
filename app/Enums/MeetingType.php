<?php

namespace App\Enums;

enum MeetingType: string
{
    use HasEnumValues;
    case OnlineCall = 'online_call';
    case InPersonMeeting = 'in-person meeting';

}
