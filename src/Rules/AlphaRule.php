<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class AlphaRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'alpha';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_string($value)) {
            $fail($attribute, self::message());
            return;
        }

        $ascii = isset($parameters[0]) && $parameters[0] === 'ascii';
        $pattern = $ascii ? '/\A[a-zA-Z]+\z/u' : '/\A[\pL\pM]+\z/u';

        if (preg_match($pattern, $value) !== 1) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute may only contain letters.';
    }
}
