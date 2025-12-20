<?php

namespace App\Enums;

enum LicenseType: string
{
    use HasEnumValues;
    case DriverLicense = 'driver_license';
    case StateID = 'state_id';
    case Passport = 'passport';

    public function getInt(): int
    {
        return match ($this) {
            LicenseType::DriverLicense => 1,
            LicenseType::StateID => 2,
            LicenseType::Passport => 3,
        };
    }
}
