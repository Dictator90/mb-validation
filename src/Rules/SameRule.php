<?php

namespace MB\Validation\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;

class SameRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public static function alias(): string|array
    {
        return 'same';
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule same requires at least 1 parameter.');
        }

        $other = Arr::get($this->data, $parameters[0]);

        if ($value !== $other) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute and :other must match.';
    }
}
