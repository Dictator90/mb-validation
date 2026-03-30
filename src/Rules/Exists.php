<?php

namespace MB\Validation\Rules;

use Closure;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Contracts\ValidatorAwareRule;
use MB\Validation\Contracts\ValidatorInterface;
use RuntimeException;

class Exists implements ValidationRule, ValidatorAwareRule
{
    /**
     * @var ValidatorInterface|null
     */
    protected $validator;

    /**
     * @var array<int, Closure>
     */
    protected array $queryCallbacks = [];

    public static function alias(): string|array
    {
        return 'exists';
    }

    /**
     * Register additional query callback.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function where(Closure $callback): static
    {
        $this->queryCallbacks[] = $callback;

        return $this;
    }

    /**
     * Get registered query callbacks.
     *
     * @return array<int, Closure>
     */
    public function queryCallbacks(): array
    {
        return $this->queryCallbacks;
    }

    public function validate(string $attribute, mixed $value, array|null $parameters, \Closure $fail): void
    {
        if (! $this->validator || ! method_exists($this->validator, 'validateExists')) {
            throw new RuntimeException('Exists rule requires validator implementation with validateExists method.');
        }

        if (! $this->validator->validateExists($attribute, $value, $parameters ?? [])) {
            $fail($attribute);
        }
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
