<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value) {
            $value = str_replace([' ', '-', '(', ')'], '', $value);
            $value = preg_replace('/^(?:\+?880|0)?/', '+880', $value);

            if (!preg_match("/^(\+?88)?01[3-9]\d{8}$/", $value)) {
                $fail("This number is invalid.");
            }
        }
    }
}
