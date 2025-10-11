<?php

namespace App\Enums;

enum DirectDepositAccountType: string
{
    case Saving = 'saving';
    case CertificateOfDeposit = 'certificate_of_deposit';
}
