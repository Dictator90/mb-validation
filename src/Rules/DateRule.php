<?php

namespace MB\Validation\Rules;

use DateTimeInterface;
use MB\Validation\Contracts\ValidationRule;

class DateRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'date';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if ($value instanceof DateTimeInterface) {
            return;
        }

        try {
            if ((!is_string($value) && !is_numeric($value)) || strtotime($value) === false) {
                $fail($attribute, self::message());
                return;
            }
        } catch (\Exception) {
            $fail($attribute, self::message());
            return;
        }

        $date = date_parse($value);

        if (!checkdate((int) $date['month'], (int) $date['day'], (int) $date['year'])) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute is not a valid date.';
    }
}
