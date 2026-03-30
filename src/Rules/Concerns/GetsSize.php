<?php

namespace MB\Validation\Rules\Concerns;

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

        if ($this->isFileLikeValue($value)) {
            $size = $this->extractFileSize($value);
            if ($size !== null) {
                return (string) ($size / 1024);
            }
        }

        return mb_strlen($value ?? '');
    }

    protected function isInvalidFileLikeValue(mixed $value): bool
    {
        if (!is_object($value) || !method_exists($value, 'isValid')) {
            return false;
        }

        $isValid = $value->isValid();
        return is_bool($isValid) && $isValid === false;
    }

    protected function isFileLikeValue(mixed $value): bool
    {
        return is_object($value) && method_exists($value, 'getSize');
    }

    protected function extractFileSize(mixed $value): ?float
    {
        if (!$this->isFileLikeValue($value)) {
            return null;
        }

        $size = $value->getSize();
        return is_numeric($size) ? (float) $size : null;
    }

    protected function trimValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : (string) $value;
    }
}
