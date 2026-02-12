<?php

namespace MB\Validation\Rules;

use DateTime;
use DateTimeZone;
use MB\Validation\Contracts\ValidationRule;
use ValueError;

class DateFormatRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'date_format';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule date_format requires at least 1 parameter.');
        }

        if (!is_string($value) && !is_numeric($value)) {
            $fail($attribute, self::message());
            return;
        }

        foreach ($parameters as $format) {
            try {
                $date = DateTime::createFromFormat('!' . $format, (string) $value, new DateTimeZone('UTC'));

                if ($date && $date->format($format) == $value) {
                    return;
                }
            } catch (ValueError) {
                $fail($attribute, self::message());
                return;
            }
        }

        $fail($attribute, self::message());
    }

    public static function message(): string
    {
        return 'The :attribute does not match the format :format.';
    }
}
