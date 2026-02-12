<?php
namespace MB\Validation\Contracts;

use Illuminate\Contracts\Validation\Validator;

interface ReplacerRule
{
    public static function replace($message, $attribute, $rule, $parameters, Validator $validator): string|null;
}