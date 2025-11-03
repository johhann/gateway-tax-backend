<?php

namespace App\Enums;

enum RefundFee: string
{
    use HasEnumValues;
    case DirectDeposit = 'direct_deposit';
    case EFile = 'e_file';
}
