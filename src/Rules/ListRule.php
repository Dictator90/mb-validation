<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class ListRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'list';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_array($value) || !array_is_list($value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must be a list.';
    }
}
