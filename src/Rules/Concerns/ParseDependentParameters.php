<?php

namespace MB\Validation\Rules\Concerns;

use MB\Support\Arr;
use MB\Support\Str;

trait ParseDependentParameters
{
    protected function parseDependentRuleParameters(array $parameters): array
    {
        $other = Arr::get($this->data, $parameters[0]);
        $values = array_slice($parameters, 1);

        return [$values, $other];
    }

    protected function convertValuesToBoolean(array $values): array
    {
        return array_map(function ($value) {
            if ($value === 'true') {
                return true;
            }
            if ($value === 'false') {
                return false;
            }

            return $value;
        }, $values);
    }

    protected function convertValuesToNull(array $values): array
    {
        return array_map(function ($value) {
            return Str::lower((string) $value) === 'null' ? null : $value;
        }, $values);
    }

    protected function isRequired(mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        if (is_countable($value) && count($value) < 1) {
            return false;
        }

        return true;
    }
}
