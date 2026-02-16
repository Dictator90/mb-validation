<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\DataAwareRule;
use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;

class DifferentRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public static function alias(): string|array
    {
        return 'different';
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule different requires at least 1 parameter.');
        }

        foreach ($parameters as $parameter) {
            if (Arr::has($this->data, $parameter)) {
                $other = Arr::get($this->data, $parameter);

                if ($value === $other) {
                    $fail($attribute, self::message());
                    return;
                }
            }
        }
    }

    public static function message(): string
    {
        return 'The :attribute and :other must be different.';
    }
}
