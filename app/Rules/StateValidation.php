<?php

namespace App\Rules;

use App\Enums\StateEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StateValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $states = StateEnum::values();

        if (! in_array($value, $states)) {
            $fail('The selected state is invalid.');
        }
    }
}
