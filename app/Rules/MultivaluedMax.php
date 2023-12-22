<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultivaluedMax implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $max;

    public function __construct($max)
    {
        $this->max = $max;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $array = [];

        if (is_string($value) && strpos($value, ';') !== false) {
            $array = explode(';', $value);
        } else {
            $array = json_decode($value, true);
            if (!is_array($array)) {
                $array = [];
            }
        }

        if(count($array) <= $this->max){
            return;
        }
        else{
            $fail('The :attribute may not have more than ' . $this->max . ' groups.');
        }
    }
}
