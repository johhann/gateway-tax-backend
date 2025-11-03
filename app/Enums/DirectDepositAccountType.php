<?php

namespace App\Enums;

enum DirectDepositAccountType: string
{
    use HasEnumValues;
    case Checking = 'checking';
    case Saving = 'saving';
    case CertificateOfDeposit = 'certificate_of_deposit';
}
