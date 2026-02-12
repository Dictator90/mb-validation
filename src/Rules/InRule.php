<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class InRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'in';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule in requires at least 1 parameter.');
        }

        if (is_array($value)) {
            foreach ($value as $element) {
                if (is_array($element)) {
                    $fail($attribute, self::message());
                    return;
                }
            }
            if (count(array_diff($value, $parameters)) !== 0) {
                $fail($attribute, self::message());
            }
            return;
        }

        if (!in_array((string) $value, $parameters)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The selected :attribute is invalid.';
    }
}
