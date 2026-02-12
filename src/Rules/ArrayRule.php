<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ReplacerRule;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Validator;

class ArrayRule implements ValidationRule, ReplacerRule
{
    public static function alias(): string|array
    {
        return 'array';
    }

    public static function message(): string
    {
        return 'field must be array';
    }

    public static function replace($message, $attribute, $rule, $parameters, Validator|\Illuminate\Contracts\Validation\Validator $validator): ?string
    {
        return $validator->replaceAcceptedIf($message, $attribute, $rule, $parameters);
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_array($value)) {
            $fail($attribute);
        } elseif (!empty($parameters)) {
            if (!empty(array_diff_key($value, array_fill_keys($parameters, '')))) {
                $fail($attribute, $this::message());
            }
        }
    }
}
