<?php

namespace App\Enums;

enum FilingStatus: string
{
    use HasEnumValues;
    case Single = 'single';
    case HeadOfHousehold = 'head_of_household';
    case MarriedFilingJointly = 'married_filing_jointly';
    case MarriedFilingSeparately = 'married_filing_separately';
    case QualifyingWidower = 'qualifying_widower';

    public function getInt(): int
    {
        return match ($this) {
            FilingStatus::Single => 1,
            FilingStatus::MarriedFilingJointly => 2,
            FilingStatus::MarriedFilingSeparately => 3,
            FilingStatus::HeadOfHousehold => 4,
            FilingStatus::QualifyingWidower => 5,
        };
    }
}
