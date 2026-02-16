<?php

namespace MB\Validation;

use MB\Messages\FileMessages;
use MB\Messages\Contracts\MessagesInterface;

final class DefaultMessages
{
    public static function create(string $lang = 'en'): MessagesInterface
    {
        $messages = new FileMessages(\dirname(__DIR__) . '/lang', $lang);

        return $messages;
    }
}

