<?php

namespace MB\Validation\Rules;

use MB\Support\Str;
use MB\Validation\Contracts\ReplacerRule;
use MB\Validation\Contracts\ValidatorInterface;
use MB\Validation\Contracts\ValidationRule;

class ArrayRule implements ValidationRule, ReplacerRule
{
    public static function alias(): string|array
    {
        return 'array';
    }

    public static function replace($message, $attribute, $rule, $parameters, ValidatorInterface $validator): ?string
    {
        return $validator->replaceAcceptedIf($message, $attribute, $rule, $parameters);
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!is_array($value)) {
            $fail($attribute);
        } elseif (!empty($parameters)) {
            if (!empty(array_diff_key($value, array_fill_keys($parameters, '')))) {
                $fail($attribute);
            }
        }
    }
}
