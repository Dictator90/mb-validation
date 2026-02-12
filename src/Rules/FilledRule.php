<?php

namespace MB\Validation\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;

class FilledRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public static function alias(): string|array
    {
        return 'filled';
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (!Arr::has($this->data, $attribute)) {
            return;
        }

        if (is_null($value) || (is_string($value) && trim($value) === '') || (is_countable($value) && count($value) < 1)) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute field must have a value.';
    }
}
