<?php

namespace MB\Validation\Rules;

use Brick\Math\BigNumber;
use MB\Validation\Exceptions\MathException;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Rules\Concerns\GetsSize;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MinRule implements ValidationRule
{
    use GetsSize;

    public static function alias(): string|array
    {
        return 'min';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule min requires at least 1 parameter.');
        }

        if ($value instanceof UploadedFile && !$value->isValid()) {
            $fail($attribute);
            return;
        }

        try {
            $size = $this->getSize($attribute, $value, is_numeric($value));
            $min = $this->trimValue($parameters[0]);

            if (!BigNumber::of($size)->isGreaterThanOrEqualTo($min)) {
                $fail($attribute);
            }
        } catch (MathException) {
            $fail($attribute);
        }
    }
}
