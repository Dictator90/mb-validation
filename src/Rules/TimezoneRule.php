<?php

namespace MB\Validation\Rules;

use DateTimeZone;
use MB\Support\Str;
use MB\Validation\Contracts\ValidationRule;

class TimezoneRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'timezone';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $group = constant(DateTimeZone::class . '::' . Str::upper($parameters[0] ?? 'ALL'));
        $country = isset($parameters[1]) ? Str::upper($parameters[1]) : null;

        if (!in_array($value, timezone_identifiers_list($group, $country), true)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be a valid timezone.';
    }
}
