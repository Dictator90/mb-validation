<?php

namespace MB\Validation\Contracts;

interface CompilableRules
{
    /**
     * Compile the rules into a format the validator can use.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $data
     * @param  mixed  $context
     * @return object  Object with 'rules' and optionally 'implicitAttributes' keys
     */
    public function compile(string $attribute, $value, array $data, $context): object;
}
