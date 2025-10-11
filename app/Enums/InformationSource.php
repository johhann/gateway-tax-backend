<?php

namespace App\Enums;

enum InformationSource: string
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Snapchat = 'snapchat';
    case Radio = 'radio';
    case TV = 'tv';
    case Mailer = 'mailer';
    case Flyer = 'flyer';
    case Referred = 'referred';
    case Other = 'other';
}
