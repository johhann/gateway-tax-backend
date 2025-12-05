<?php

namespace App\Enums;

enum GrantType: string
{
    use HasEnumValues;
    case PASSWORD = 'password';
}
