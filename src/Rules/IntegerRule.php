<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class IntegerRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return ['integer', 'int'];
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $strict = isset($parameters[0]) && $parameters[0] === 'strict';

        if ($strict && !is_int($value)) {
            $fail($attribute, self::message());
            return;
        }

        if (!$strict && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be an integer.';
    }
}
