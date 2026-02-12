<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class BooleanRule implements ValidationRule
{
    protected static array $acceptable = [true, false, 0, 1, '0', '1'];

    public static function alias(): string|array
    {
        return 'boolean';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $acceptable = isset($parameters[0]) && $parameters[0] === 'strict' ? [true, false] : self::$acceptable;

        if (!in_array($value, $acceptable, true)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must be true or false.';
    }
}
