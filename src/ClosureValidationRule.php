<?php

namespace MB\Validation;

use MB\Messages\Traits\CreatesPotentiallyMessagesStrings;
use MB\Validation\Contracts\Rule as RuleContract;
use MB\Validation\Contracts\ValidatorAwareRule;

class ClosureValidationRule implements RuleContract, ValidatorAwareRule
{
    use CreatesPotentiallyMessagesStrings;

    /**
     * The callback that validates the attribute.
     *
     * @var \Closure
     */
    public \Closure $callback;

    /**
     * Indicates if the validation callback failed.
     *
     * @var bool
     */
    public $failed = false;

    /**
     * The validation error messages.
     *
     * @var array
     */
    public $messages = [];

    /**
     * The current validator.
     *
     * @var \MB\Validation\Validator
     */
    protected $validator;

    /**
     * Create a new Closure based validation rule.
     *
     * @param  \Closure  $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->failed = false;
        $this->messages = [];

        ($this->callback)($attribute, $value, function ($attribute, $message = null) {
            $this->failed = true;

            $this->messages[] = $this->pendingPotentiallyMessagesString($attribute, $message);

            return $this->messages[\count($this->messages) - 1];
        }, $this->validator);

        return ! $this->failed;
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
     * Set the current validator.
     *
     * @param  \MB\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator): static
    {
        $this->validator = $validator;

        return $this;
    }
}
