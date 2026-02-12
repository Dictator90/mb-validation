<?php

namespace MB\Validation\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Rules\Concerns\ParseDependentParameters;

class RequiredIfRule implements ValidationRule, DataAwareRule
{
    use ParseDependentParameters;

    protected array $data = [];

    public static function alias(): string|array
    {
        return 'required_if';
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (count($parameters ?? []) < 2) {
            throw new \InvalidArgumentException('Validation rule required_if requires at least 2 parameters.');
        }

        if (!Arr::has($this->data, $parameters[0])) {
            return;
        }

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || $other === null)) {
            if (!$this->isRequired($value)) {
                $fail($attribute, self::message());
            }
        }
    }

    public static function message(): string
    {
        return 'The :attribute field is required when :other is :value.';
    }
}
