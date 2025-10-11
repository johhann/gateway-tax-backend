<?php

namespace App\Enums;

enum FilingStatus: string
{
    case Single = 'single';
    case HeadOfHousehold = 'head_of_household';
    case MarriedFilingJointly = 'married_filing_jointly';
    case MarriedFilingSeparately = 'married_filing_separately';
    case QualifyingWidower = 'qualifying_widower';
}
