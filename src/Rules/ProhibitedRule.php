<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Rules\Concerns\ParseDependentParameters;

class ProhibitedRule implements ValidationRule
{
    use ParseDependentParameters;

    public static function alias(): string|array
    {
        return 'prohibited';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if ($this->isRequired($value)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute field is prohibited.';
    }
}
