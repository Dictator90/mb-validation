<?php

namespace MB\Validation\Rules;

use Brick\Math\BigNumber;
use Illuminate\Support\Exceptions\MathException;
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
            $fail($attribute, self::message());
            return;
        }

        try {
            $size = $this->getSize($attribute, $value, is_numeric($value));
            $min = $this->trimValue($parameters[0]);

            if (!BigNumber::of($size)->isGreaterThanOrEqualTo($min)) {
                $fail($attribute, self::message());
            }
        } catch (MathException) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be at least :min.';
    }
}
