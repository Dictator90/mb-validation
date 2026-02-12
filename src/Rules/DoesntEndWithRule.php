<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ValidationRule;

class DoesntEndWithRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'doesnt_end_with';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule doesnt_end_with requires at least 1 parameter.');
        }

        if (Str::endsWith((string) $value, $parameters)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute may not end with one of the following: :values';
    }
}
