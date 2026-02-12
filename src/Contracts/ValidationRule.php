<?php
namespace MB\Validation\Contracts;

use Illuminate\Translation\PotentiallyTranslatedString;

interface ValidationRule
{
    public static function alias(): string|array;
    public static function message(): string;

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $parameters
     * @param mixed $value
     * @param \Closure(string, ?string=): PotentiallyTranslatedString $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, array|null $parameters, \Closure $fail): void;
}