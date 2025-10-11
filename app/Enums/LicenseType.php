<?php

namespace App\Enums;

enum LicenseType: string
{
    case DriverLicense = 'driver_license';
    case StateID = 'state_id';
    case Passport = 'passport';
}
