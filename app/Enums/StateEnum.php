<?php

namespace App\Enums;

enum StateEnum: string
{
    case Alabama = 'Alabama';
    case Alaska = 'Alaska';
    case Arizona = 'Arizona';
    case Arkansas = 'Arkansas';
    case California = 'California';
    case Colorado = 'Colorado';
    case Connecticut = 'Connecticut';
    case Delaware = 'Delaware';
    case Florida = 'Florida';
    case Georgia = 'Georgia';
    case Hawaii = 'Hawaii';
    case Idaho = 'Idaho';
    case Illinois = 'Illinois';
    case Indiana = 'Indiana';
    case Iowa = 'Iowa';
    case Kansas = 'Kansas';
    case Kentucky = 'Kentucky';
    case Louisiana = 'Louisiana';
    case Maine = 'Maine';
    case Maryland = 'Maryland';
    case Massachusetts = 'Massachusetts';
    case Michigan = 'Michigan';
    case Minnesota = 'Minnesota';
    case Mississippi = 'Mississippi';
    case Missouri = 'Missouri';
    case Montana = 'Montana';
    case Nebraska = 'Nebraska';
    case Nevada = 'Nevada';
    case New_Hampshire = 'New Hampshire';
    case New_Jersey = 'New Jersey';
    case New_Mexico = 'New Mexico';
    case New_York = 'New York';
    case North_Carolina = 'North Carolina';
    case North_Dakota = 'North Dakota';
    case Ohio = 'Ohio';
    case Oklahoma = 'Oklahoma';
    case Oregon = 'Oregon';
    case Pennsylvania = 'Pennsylvania';
    case Rhode_Island = 'Rhode Island';
    case South_Carolina = 'South Carolina';
    case South_Dakota = 'South Dakota';
    case Tennessee = 'Tennessee';
    case Texas = 'Texas';
    case Utah = 'Utah';
    case Vermont = 'Vermont';
    case Virginia = 'Virginia';
    case Washington = 'Washington';
    case West_Virginia = 'West Virginia';
    case Wisconsin = 'Wisconsin';
    case Wyoming = 'Wyoming';
    case International = 'International';

    /**
     * Get all values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
