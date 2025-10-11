<?php

namespace App\Enums;

enum RefundFee: string
{
    case DirectDeposit = 'direct_deposit';
    case EFile = 'e_file';
}
