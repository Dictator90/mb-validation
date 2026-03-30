<?php

namespace MB\Validation\Rules;

use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Contracts\ValidatorAwareRule;
use MB\Validation\Contracts\ValidatorInterface;
use RuntimeException;

class EmailRule implements ValidationRule, ValidatorAwareRule
{
    /**
     * @var ValidatorInterface|null
     */
    protected $validator;

    public static function alias(): string|array
    {
        return 'email';
    }

    public function validate(string $attribute, mixed $value, array|null $parameters, \Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! $this->validator || ! method_exists($this->validator, 'validateEmail')) {
            throw new RuntimeException('Email rule requires validator implementation with validateEmail method.');
        }

        if (! $this->validator->validateEmail($attribute, $value, $parameters ?? [])) {
            $fail($attribute);
        }
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
