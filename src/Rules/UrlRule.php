<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ValidationRule;

class UrlRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'url';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!Str::isUrl($value, $parameters ?? [])) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid URL.';
    }
}
