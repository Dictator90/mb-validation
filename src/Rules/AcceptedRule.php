<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class AcceptedRule implements ValidationRule
{
    protected static array $acceptable = ['yes', 'on', '1', 1, true, 'true'];

    public static function alias(): string|array
    {
        return 'accepted';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (is_null($value) || (is_string($value) && trim($value) === '') || (is_countable($value) && count($value) < 1)) {
            $fail($attribute, self::message());
            return;
        }

        if (!in_array($value, self::$acceptable, true)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be accepted.';
    }
}
