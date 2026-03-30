<?php

namespace MB\Validation\Concerns;

use MB\Validation\Factory;
use ReflectionObject;

trait ValidatesProperties
{
    /**
     * Default validator locale.
     */
    protected string $validatorLang = 'ru';

    /**
     * Define validation rules for object properties.
     *
     * @return array<string, mixed>
     */
    abstract protected function rules(): array;

    /**
     * Validate object properties using package validator.
     *
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     * @return array<string, mixed>
     */
    public function validate(array $messages = [], array $attributes = []): array
    {
        return $this->validationFactory()->validate(
            $this->validationData(),
            $this->rules(),
            $messages,
            $attributes
        );
    }

    /**
     * Create validation factory used by this trait.
     */
    protected function validationFactory(): Factory
    {
        return Factory::create(lang: $this->validatorLang);
    }

    /**
     * Build validation payload from all initialized properties.
     *
     * @return array<string, mixed>
     */
    protected function validationData(): array
    {
        $data = [];
        $reflection = new ReflectionObject($this);
        $excluded = ['validatorLang' => true];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (!$property->isInitialized($this)) {
                continue;
            }

            $name = $property->getName();
            if (isset($excluded[$name])) {
                continue;
            }

            $data[$name] = $property->getValue($this);
        }

        return $data;
    }
}
