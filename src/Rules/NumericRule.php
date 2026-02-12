<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class NumericRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'numeric';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (isset($parameters[0]) && $parameters[0] === 'strict' && is_string($value)) {
            $fail($attribute, self::message());
            return;
        }

        if (!is_numeric($value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a number.';
    }
}
