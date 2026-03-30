<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class UuidRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'uuid';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_string($value)) {
            $fail($attribute, self::message());
            return;
        }

        $version = null;

        if (!empty($parameters) && count($parameters) === 1) {
            $version = $parameters[0];

            if ($version !== 'max') {
                $version = (int) $parameters[0];
            }
        }

        $pattern = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/';
        if (!preg_match($pattern, $value)) {
            $fail($attribute, self::message());
            return;
        }

        if (is_int($version) && $version >= 1 && $version <= 8) {
            if ((int) $value[14] !== $version) {
                $fail($attribute, self::message());
            }
            return;
        }

        if ($version === 'max') {
            return;
        }

        if ($version !== null) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid UUID.';
    }
}
