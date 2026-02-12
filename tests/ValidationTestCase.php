<?php

namespace MB\Validation\Tests;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MB\Validation\Factory;
use MB\Validation\Validator;
use PHPUnit\Framework\TestCase;

abstract class ValidationTestCase extends TestCase
{
    protected Factory $factory;
    protected Translator $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', []);
        $this->translator = new Translator($loader, 'en');
        $this->factory = new Factory($this->translator);
    }

    protected function validate(array $data, array $rules): Validator
    {
        return $this->factory->make($data, $rules);
    }

    protected function assertPasses(array $data, array $rules, ?string $message = null): void
    {
        $validator = $this->validate($data, $rules);
        $this->assertTrue($validator->passes(), $message ?? 'Validation should pass: ' . json_encode($validator->messages()->toArray()));
    }

    protected function assertFails(array $data, array $rules, ?string $message = null): void
    {
        $validator = $this->validate($data, $rules);
        $this->assertTrue($validator->fails(), $message ?? 'Validation should fail');
    }
}
