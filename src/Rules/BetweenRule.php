<?php

namespace MB\Validation\Rules;

use Brick\Math\BigNumber;
use MB\Validation\Exceptions\MathException;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Rules\Concerns\GetsSize;

class BetweenRule implements ValidationRule
{
    use GetsSize;

    public static function alias(): string|array
    {
        return 'between';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if (count($parameters ?? []) < 2) {
            throw new \InvalidArgumentException('Validation rule between requires at least 2 parameters.');
        }

        try {
            $size = $this->getSize($attribute, $value, is_numeric($value));
            $min = $this->trimValue($parameters[0]);
            $max = $this->trimValue($parameters[1]);

            $passes = BigNumber::of($size)->isGreaterThanOrEqualTo($min)
                && BigNumber::of($size)->isLessThanOrEqualTo($max);

            if (!$passes) {
                $fail($attribute, self::message());
            }
        } catch (MathException) {
            $fail($attribute, self::message());
        }
    }

    public static function message(): string
    {
        return 'The :attribute must be between :min and :max.';
    }
}
