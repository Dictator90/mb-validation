<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class DigitsBetweenRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'digits_between';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (count($parameters ?? []) < 2) {
            throw new \InvalidArgumentException('Validation rule digits_between requires at least 2 parameters.');
        }

        $length = strlen((string) $value);

        $valid = preg_match('/[^0-9]/', (string) $value) !== 1
            && $length >= $parameters[0]
            && $length <= $parameters[1];

        if (!$valid) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be between :min and :max digits.';
    }
}
