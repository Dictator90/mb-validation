<?php

namespace MB\Validation\Contracts;

interface MessageBagInterface
{
    /**
     * @return array<string, array<string>>
     */
    public function getMessages(): array;

    public function getMessageBag(): static;

    /**
     * @param  MessageProviderInterface|array<string, array<string>>  $messages
     * @return static
     */
    public function merge($messages);

    public function add($key, $message);

    public function first($key = null, $format = null);

    public function get($key, $format = null);

    public function all($format = null);

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    /**
     * @return array<string, array<string>>
     */
    public function toArray(): array;
}
