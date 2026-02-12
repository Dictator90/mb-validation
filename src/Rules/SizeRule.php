<?php

namespace MB\Validation\Rules;

use Brick\Math\BigNumber;
use Illuminate\Support\Exceptions\MathException;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Rules\Concerns\GetsSize;

class SizeRule implements ValidationRule
{
    use GetsSize;

    public static function alias(): string|array
    {
        return 'size';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Validation rule size requires at least 1 parameter.');
        }

        try {
            $size = $this->getSize($attribute, $value, is_numeric($value));
            $expected = $this->trimValue($parameters[0]);

            if (!BigNumber::of($size)->isEqualTo($expected)) {
                $fail($attribute, self::message());
            }
        } catch (MathException) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be :size.';
    }
}
