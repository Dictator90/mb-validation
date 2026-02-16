<?php

namespace MB\Validation\Rules;

use MB\Support\Arr;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\InvokableValidationRule;
use MB\Validation\Registry\RuleRegistry;

class AnyOfRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'any_of';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $rules = Arr::map($parameters, fn ($val) => RuleRegistry::has($val) ? RuleRegistry::get($val) : false);

        $passes = true;
        foreach ($rules as $ruleInstance) {
            if ($ruleInstance instanceof ValidationRule) {
                $invokableRule = InvokableValidationRule::make($ruleInstance);
                if (!$invokableRule->passes($attribute, $value, $parameters)) {
                    $passes = false;
                } else {
                    $passes = true;
                    break;
                }
            }
        }

        if (!$passes) {
            $fail($attribute);
        }
    }
}
