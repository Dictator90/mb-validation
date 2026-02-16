<?php

namespace MB\Validation\Contracts;

use MB\Messages\PotentiallyMessagesString;

interface ValidationRule
{
    public static function alias(): string|array;

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array|null  $parameters
     * @param  \Closure(string, ?string=): PotentiallyMessagesString|string  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, array|null $parameters, \Closure $fail): void;
}