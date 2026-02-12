<?php

namespace MB\Validation;

use MB\Validation\Contracts\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Translation\CreatesPotentiallyTranslatedStrings;

class InvokableValidationRule implements ValidatorAwareRule
{
    use CreatesPotentiallyTranslatedStrings;

    /**
     * The invokable that validates the attribute.
     *
     * @var ValidationRule
     */
    protected $invokable;

    /**
     * Indicates if the validation invokable failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The validation error messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * The current validator.
     *
     * @var \MB\Validation\Validator
     */
    protected $validator;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a new explicit Invokable validation rule.
     *
     * @param ValidationRule $invokable
     */
    protected function __construct(ValidationRule $invokable)
    {
        $this->invokable = $invokable;
    }

    /**
     * Create a new implicit or explicit Invokable validation rule.
     *
     * @param  ValidationRule $invokable
     * @return InvokableValidationRule
     */
    public static function make($invokable)
    {
        if ($invokable->implicit ?? false) {
            return new class($invokable) extends InvokableValidationRule {};
        }

        return new InvokableValidationRule($invokable);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value, array $parameters): bool
    {
        $this->failed = false;

        if ($this->invokable instanceof DataAwareRule) {
            $this->invokable->setData($this->validator->getData());
        }

        if ($this->invokable instanceof ValidatorAwareRule) {
            $this->invokable->setValidator($this->validator);
        }


        $this->invokable->validate(
            $attribute,
            $value,
            $parameters,
            function ($attribute, $message = null) {
                $this->failed = true;
                if ($message) {
                    $this->messages[$attribute] = $message;
                }
            }
        );

        if ($this->messages) {
            $this->validator->setCustomMessages($this->messages);
        }

        return !$this->failed;
    }

    /**
     * Get the underlying invokable rule.
     *
     * @return ValidationRule
     */
    public function invokable(): ValidationRule
    {
        return $this->invokable;
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function message(): array
    {
        return $this->messages;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the current validator.
     *
     * @param  \MB\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
