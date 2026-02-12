<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class NotRegexRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'not_regex';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule not_regex requires at least 1 parameter.');
        }

        if (preg_match($parameters[0], (string) $value) >= 1) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute format is invalid.';
    }
}
