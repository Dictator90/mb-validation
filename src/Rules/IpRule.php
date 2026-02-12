<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class IpRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'ip';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid IP address.';
    }
}
