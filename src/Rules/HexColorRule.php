<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class HexColorRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'hex_color';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (preg_match('/^#(?:(?:[0-9a-f]{3}){1,2}|(?:[0-9a-f]{4}){1,2})$/i', (string) $value) !== 1) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must be a valid hexadecimal color.';
    }
}
