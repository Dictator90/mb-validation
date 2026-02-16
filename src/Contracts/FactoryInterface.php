<?php

namespace MB\Validation\Contracts;

use MB\Messages\Contracts\MessagesInterface;
use MB\Validation\Validator;

interface FactoryInterface
{
    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $attributes = []): Validator;

    /**
     * Validate the given data against the provided rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return array
     *
     * @throws \MB\Validation\ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $attributes = []): array;

    public function getTranslator(): MessagesInterface;
}
