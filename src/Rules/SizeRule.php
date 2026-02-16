<?php

namespace MB\Validation\Rules;

use Brick\Math\BigNumber;
use MB\Validation\Exceptions\MathException;
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
                $fail($attribute);
            }
        } catch (MathException) {
            $fail($attribute);
        }
    }
}
