<?php

namespace App\Enums;

use BackedEnum;

trait HasEnumValues
{
    public function getLabel(): string
    {
        return __($this->value);
    }

    public static function values(?array $data = null): array
    {
        return collect($data ?? self::cases())
            ->map(fn (BackedEnum $value) => $value->value)
            ->toArray();
    }
}
