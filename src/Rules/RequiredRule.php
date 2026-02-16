<?php

namespace MB\Validation\Rules;

use Closure;
use MB\Validation\Contracts\ValidationRule;

class RequiredRule implements ValidationRule
{

    public static function alias(): string|array
    {
        return 'required';
    }

    public function validate(string $attribute, mixed $value, array|null $parameters, $fail): void
    {
        if (
            is_null($value)
            || is_string($value) && trim($value) === ''
            || is_countable($value) && count($value) < 1
        ) {
            $fail($attribute);
        }
    }
}