<?php

namespace MB\Validation\Contracts;

use MB\Messages\Contracts\MessagesInterface;
use MB\Validation\Message\MessageBag;

interface ValidatorInterface
{
    public function passes(): bool;

    public function fails(): bool;

    public function messages(): MessageBag;

    public function errors(): MessageBag;

    public function getMessageBag(): MessageBag;

    public function getData(): array;

    public function getTranslator(): MessagesInterface;

    public function setTranslator(MessagesInterface $message): void;

    /**
     * @param  array|null  $keys
     * @return \MB\Validation\ValidatedInput|array
     */
    public function safe(?array $keys = null);

    /**
     * @return array
     *
     * @throws \MB\Validation\ValidationException
     */
    public function validated();
}
