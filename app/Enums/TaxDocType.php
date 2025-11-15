<?php

namespace App\Enums;

enum TaxDocType: string
{
    use HasEnumValues;
    case Identification = 'Identification';
    case IncomeSources = 'Income Sources';
    case OtherItems = 'Other Items';
    case FormW2 = 'Form W-2';
    case Form1099 = 'Form 1099';
    case Form1098T = 'Form 1098-T';
    case MortgageStatements = 'Mortgage Statements';
    case Charity = 'Charity';
    case Medical = 'Medical';
    case Dependent = 'Dependent';
    case ChildCare = 'Child Care';
    case SelfEmployment = 'Self Employment';
    case DirectDeposit = 'Direct Deposit';
    case StockTransaction = 'Stock Transaction';
    case YearToYearOther = 'Year to Year Other Items';

    public static function fromCode(string $code): ?self
    {
        return match ($code) {
            '01' => self::Identification,
            '02' => self::IncomeSources,
            '03' => self::OtherItems,
            '04' => self::FormW2,
            '05' => self::Form1099,
            '06' => self::Form1098T,
            '07' => self::MortgageStatements,
            '08' => self::Charity,
            '09' => self::Medical,
            '10' => self::Dependent,
            '11' => self::ChildCare,
            '12' => self::SelfEmployment,
            '13' => self::DirectDeposit,
            '14' => self::StockTransaction,
            '15' => self::YearToYearOther,
            default => null,
        };
    }
}
