<?php

namespace App\Enums;

enum MeetingType: string
{
    use HasEnumValues;
    case OnlineCall = 'online_call';
    case InPersonMeeting = 'in_person_meeting';

}
