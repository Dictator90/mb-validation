<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class UlidRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'ulid';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_string($value) || !preg_match('/^[0-7][0-9A-HJKMNP-TV-Z]{25}$/', $value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid ULID.';
    }
}
