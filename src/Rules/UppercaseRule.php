<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ValidationRule;

class UppercaseRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'uppercase';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (Str::upper((string) $value) !== (string) $value) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be uppercase.';
    }
}
