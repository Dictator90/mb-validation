<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class DigitsRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'digits';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule digits requires at least 1 parameter.');
        }

        $valid = (is_numeric($value) || is_string($value))
            && preg_match('/[^0-9]/', (string) $value) !== 1
            && strlen((string) $value) == $parameters[0];

        if (!$valid) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be :digits digits.';
    }
}
