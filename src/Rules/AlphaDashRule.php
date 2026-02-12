<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class AlphaDashRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return ['alpha_dash', 'alpha-dash'];
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_string($value) && !is_numeric($value)) {
            $fail($attribute, self::message());
            return;
        }

        $ascii = isset($parameters[0]) && $parameters[0] === 'ascii';
        $pattern = $ascii ? '/\A[a-zA-Z0-9_-]+\z/u' : '/\A[\pL\pM\pN_-]+\z/u';

        if (preg_match($pattern, (string) $value) !== 1) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute may only contain letters, numbers, dashes and underscores.';
    }
}
