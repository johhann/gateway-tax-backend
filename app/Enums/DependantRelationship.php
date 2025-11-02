<?php

namespace App\Enums;

enum DependantRelationship: string
{
    use HasEnumValues;
    case Son = 'Son';
    case Daughter = 'Daughter';
    case StepChild = 'StepChild';
    case HalfBrother = 'Half Brother';
    case HalfSister = 'Half Sister';
    case Stepbrother = 'Stepbrother';
    case Stepsister = 'Stepsister';
    case FosterChild = 'Foster Child';
    case Nephew = 'Nephew';
    case Niece = 'Niece';
    case Grandchild = 'Grandchild';
    case Grandparent = 'Grandparent';
    case Parent = 'Parent';
    case Brother = 'Brother';
    case Cousin = 'Cousin';
    case Sister = 'Sister';
    case Aunt = 'Aunt';
    case Uncle = 'Uncle';
    case Other = 'Other';

    public static function options(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }
}
