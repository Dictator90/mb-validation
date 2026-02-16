<?php

namespace MB\Validation\Contracts;

/**
 * Invokable validation rule (Laravel-style): passes(attribute, value) and message().
 */
interface Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool;

    /**
     * Get the validation error message.
     *
     * @return array<string>|string
     */
    public function message();
}
