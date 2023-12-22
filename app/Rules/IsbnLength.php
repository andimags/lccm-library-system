<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsbnLength implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(strlen($value) == 0){
            return;
        }

        if(!(strlen($value) == 10 | strlen($value) == 13)){
            $fail('The :attribute must be either 10 or 13 characters in length.');
        }
    }
}
