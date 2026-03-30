<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class AsciiRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'ascii';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_scalar($value)) {
            $fail($attribute, self::message());
            return;
        }

        if (!preg_match('/\A[\x00-\x7F]*\z/', (string) $value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must only contain single-byte alphanumeric characters and symbols.';
    }
}
