<?php

namespace App\Enums;

enum CollectionName: string
{
    use HasEnumValues;
    case IdFront = 'id_front';
    case IdBack = 'id_back';
    case W2 = 'w2';
    case MISC1099 = 'misc_1099';
    case SharedRiders = 'shared_riders';
    case MortgageStatement = 'mortgage_statement';
    case TuitionStatement = 'tuition_statement';
    case Misc = 'misc';
    case Check = 'check';
    case PDFAttachments = 'pdf_attachments';
}
