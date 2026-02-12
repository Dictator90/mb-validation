<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class Ipv6Rule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'ipv6';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid IPv6 address.';
    }
}
