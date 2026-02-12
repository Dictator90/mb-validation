<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ValidationRule;

class UuidRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'uuid';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $version = null;

        if (!empty($parameters) && count($parameters) === 1) {
            $version = $parameters[0];

            if ($version !== 'max') {
                $version = (int) $parameters[0];
            }
        }

        if (!Str::isUuid($value, $version)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid UUID.';
    }
}
