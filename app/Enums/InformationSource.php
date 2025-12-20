<?php

namespace App\Enums;

enum InformationSource: string
{
    use HasEnumValues;
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Snapchat = 'snapchat';
    case Radio = 'radio';
    case TV = 'tv';
    case Mailer = 'mailer';
    case Flyer = 'flyer';
    case Referred = 'referred';
    case Other = 'other';

    public function getInt(): int
    {
        return match ($this) {
            InformationSource::Facebook => 1,
            InformationSource::Instagram => 2,
            InformationSource::Snapchat => 3,
            InformationSource::Radio => 4,
            InformationSource::TV => 5,
            InformationSource::Mailer => 6,
            InformationSource::Flyer => 7,
            InformationSource::Referred => 8,
            InformationSource::Other => 9,
        };
    }
}
