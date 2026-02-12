<?php

namespace MB\Validation\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;

class ConfirmedRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public static function alias(): string|array
    {
        return 'confirmed';
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $confirmationAttribute = $parameters[0] ?? $attribute . '_confirmation';
        $other = Arr::get($this->data, $confirmationAttribute);

        if ($value !== $other) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute confirmation does not match.';
    }
}
