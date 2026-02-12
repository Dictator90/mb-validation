<?php

namespace MB\Validation\Rules;

use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;

class RequiredArrayKeysRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'required_array_keys';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_array($value)) {
            $fail($attribute, self::message());
            return;
        }

        foreach ($parameters ?? [] as $param) {
            if (!Arr::exists($value, $param)) {
                $fail($attribute, self::message());
                return;
            }
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must have keys for: :values';
    }
}
