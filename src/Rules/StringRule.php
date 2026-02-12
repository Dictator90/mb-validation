<?php

namespace MB\Validation\Rules;

use Closure;
use MB\Validation\Contracts\ValidationRule;

class StringRule implements ValidationRule
{

    public static function alias(): string|array
    {
        return ['string', 'str'];
    }

    public function validate(string $attribute, mixed $value, array|null $parameters, $fail): void
    {
        if (!is_string($value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return "The :attribute is not a valid string.";
    }
}