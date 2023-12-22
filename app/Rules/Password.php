<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Password implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!(strlen($value) >= 8 && preg_match('/[A-Z]+/', $value) && preg_match('/\d+/', $value))){
            $fail('The :attribute must be 8 characters or more and contain at least one uppercase letter and one digit.');
        }
        else{
            return;
        }
    }
}
