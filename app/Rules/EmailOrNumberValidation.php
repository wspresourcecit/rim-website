<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailOrNumberValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
     public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value = str_replace([' ', '-', '(', ')'], '', $value);
            $value = preg_replace('/^(?:\+?880|0)?/', '+880', $value);
            if (!preg_match("/^(\+?88)?01[3-9]\d{8}$/", $value)) {
                $fail("Invalid phone number or email.");
            }
        }
    }
}
