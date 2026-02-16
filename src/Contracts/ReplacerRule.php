<?php

namespace MB\Validation\Contracts;

interface ReplacerRule
{
    public static function replace($message, $attribute, $rule, $parameters, ValidatorInterface $validator): ?string;
}