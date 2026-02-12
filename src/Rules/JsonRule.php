<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class JsonRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'json';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (is_array($value) || is_null($value)) {
            $fail($attribute, self::message());
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            $fail($attribute, self::message());
            return;
        }

        if (!json_validate($value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid JSON string.';
    }
}
