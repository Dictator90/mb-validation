<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;

class NotInRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'not_in';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule not_in requires at least 1 parameter.');
        }

        if (is_array($value)) {
            foreach ($value as $element) {
                if (!is_array($element) && in_array((string) $element, $parameters)) {
                    $fail($attribute, self::message());
                    return;
                }
            }
            return;
        }

        if (in_array((string) $value, $parameters)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The selected :attribute is invalid.';
    }
}
