<?php

namespace MB\Validation\Contracts;

interface MessageProviderInterface
{
    public function getMessageBag(): MessageBagInterface;
}
