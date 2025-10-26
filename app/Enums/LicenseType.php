<?php

namespace App\Enums;

enum LicenseType: string
{
    use HasEnumValues;
    case DriverLicense = 'driver_license';
    case StateID = 'state_id';
    case Passport = 'passport';
}
