<?php

namespace MB\Validation\Rules\Concerns;

use Symfony\Component\HttpFoundation\File\File;

trait GetsSize
{
    protected function getSize(string $attribute, mixed $value, bool $hasNumericRule = false): int|float|string
    {
        if (is_numeric($value) && $hasNumericRule) {
            return (string) (is_string($value) ? trim($value) : $value);
        }

        if (is_array($value)) {
            return count($value);
        }

        if ($value instanceof File) {
            return (float) ($value->getSize() / 1024);
        }

        return mb_strlen($value ?? '');
    }

    protected function trimValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : (string) $value;
    }
}
