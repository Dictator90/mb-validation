<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class DecimalRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'decimal';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule decimal requires at least 1 parameter.');
        }

        if (!is_numeric($value)) {
            $fail($attribute, self::message());
            return;
        }

        $matches = [];
        if (preg_match('/^[+-]?\d*\.?(\d*)$/', (string) $value, $matches) !== 1) {
            $fail($attribute, self::message());
            return;
        }

        $decimals = strlen(end($matches));

        if (!isset($parameters[1])) {
            if ($decimals != $parameters[0]) {
                $fail($attribute, self::message());
            }
            return;
        }

        if ($decimals < $parameters[0] || $decimals > $parameters[1]) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must have :decimal decimal places.';
    }
}
