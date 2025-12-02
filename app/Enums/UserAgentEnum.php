<?php

namespace App\Enums;

enum UserAgentEnum: string
{
    use HasEnumValues;
    case IOS = 'ios';
    case Android = 'android';
    case Web = 'web';
}
