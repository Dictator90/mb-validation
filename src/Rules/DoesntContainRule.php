<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class DoesntContainRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'doesnt_contain';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_array($value)) {
            $fail($attribute, self::message());
            return;
        }

        foreach ($parameters ?? [] as $parameter) {
            if (in_array($parameter, $value)) {
                $fail($attribute, self::message());
                return;
            }
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must not contain: :values';
    }
}
