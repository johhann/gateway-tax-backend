<?php

namespace App\Enums;

enum RefundMethod: string
{
    use HasEnumValues;
    case PickupAtOffice = 'pickup_at_office';
    case DirectDeposit = 'direct_deposit';
}
