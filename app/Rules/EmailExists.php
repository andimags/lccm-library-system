<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Patron;

class EmailExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Patron::where('registration_status', 'accepted')->where('email', $value)->exists()){
            return;
        }

        $fail('The :attribute is not associated to any patron.');
    }
}
