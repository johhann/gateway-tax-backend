<?php

namespace App\Enums;

enum RefundType: string
{
    use HasEnumValues;
    case TenMinutes = 'ten_minutes';
    case OneHour = 'one_hour';
    case TenToFourteenDays = 'ten_to_fourteen_days';
}
