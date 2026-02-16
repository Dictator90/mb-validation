<?php

namespace MB\Validation\Tests;

use MB\Validation\Factory;
use MB\Validation\Validator;

class ValidatorTest extends ValidationTestCase
{
    public function test_validator_creation(): void
    {
        $validator = $this->validate(['name' => 'John'], ['name' => 'required|string']);
        $this->assertInstanceOf(Validator::class, $validator);
    }

    public function test_validate_method_returns_validated_data(): void
    {
        $data = ['name' => 'John', 'age' => 25];
        $rules = ['name' => 'required|string', 'age' => 'required|integer'];
        $validated = $this->factory->validate($data, $rules);
        $this->assertEquals($data, $validated);
    }

    public function test_validate_method_throws_on_failure(): void
    {
        $this->expectException(\MB\Validation\ValidationException::class);
        $this->factory->validate([], ['name' => 'required']);
    }

    public function test_combined_rules(): void
    {
        $this->assertPasses(
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'age' => 30,
                'items' => ['a', 'b'],
            ],
            [
                'name' => 'required|string|min:3|max:100',
                'email' => 'required|string',
                'age' => 'required|integer|between:18,99',
                'items' => 'required|array|min:1',
            ]
        );
    }

    public function test_messages_returns_errors_on_failure(): void
    {
        $validator = $this->validate([], ['field' => 'required']);
        $this->assertTrue($validator->fails());
        $this->assertNotEmpty($validator->messages()->toArray());
    }

    public function test_safe_returns_validated_input(): void
    {
        $data = ['name' => 'John'];
        $validator = $this->validate($data, ['name' => 'required|string']);
        $safe = $validator->safe();
        $this->assertEquals(['name' => 'John'], $safe->all());
    }

    public function test_closure_rule_passes_without_fail(): void
    {
        $validator = $this->factory->make(
            ['field' => 'ok'],
            [
                'field' => [
                    function (string $attribute, mixed $value, callable $fail, Validator $validator): void {
                    },
                ],
            ]
        );

        $this->assertTrue($validator->passes());
        $this->assertTrue($validator->errors()->isEmpty());
    }

    public function test_closure_rule_fails_with_custom_message(): void
    {
        $validator = $this->factory->make(
            ['field' => 'bad'],
            [
                'field' => [
                    function (string $attribute, mixed $value, callable $fail, Validator $validator): void {
                        if ($value === 'bad') {
                            $fail($attribute, 'Closure rule failed');
                        }
                    },
                ],
            ]
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(['Closure rule failed'], $validator->errors()->get('field'));
    }

    public function test_safe_with_keys_returns_subset(): void
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $validator = $this->validate($data, [
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        $safe = $validator->safe(['email']);

        $this->assertSame(['email' => 'john@example.com'], $safe->all());
    }

    public function test_custom_inline_message_overrides_default(): void
    {
        $validator = $this->factory->make(
            ['field' => null],
            ['field' => 'required'],
            ['field.required' => 'Custom required message']
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            ['Custom required message'],
            $validator->errors()->get('field')
        );
    }

    public function test_required_error_message_contains_attribute_name(): void
    {
        // Устанавливаем переводы для атрибутов через mb4it/messages
        $validator = $this->factory->make(
            ['friendly' => null],
            ['friendly' => 'required']
        );

        $this->assertTrue($validator->fails());
        $messages = $validator->errors()->get('friendly');

        $this->assertNotEmpty($messages);
        // По умолчанию имя атрибута подставляется как есть (friendly)
        $this->assertStringContainsString('friendly', $messages[0]);
    }

    public function test_validation_exception_message_uses_first_error(): void
    {
        try {
            $this->factory->validate([], ['name' => 'required']);
            $this->fail('Expected ValidationException to be thrown.');
        } catch (\MB\Validation\ValidationException $e) {
            $this->assertStringContainsString('name', $e->getMessage());
            // In default Russian locale ensure message comes from lang and mentions attribute.
            $this->assertStringContainsString('обязательно для заполнения', $e->getMessage());
        }
    }

    public function test_default_message_for_required_rule_with_implicit_messages(): void
    {
        $factory = new Factory();

        $validator = $factory->make([], ['field' => 'required']);

        $this->assertTrue($validator->fails());
        $messages = $validator->errors()->get('field');
        $this->assertNotEmpty($messages);
        $first = $messages[0];
        $this->assertStringContainsString('required', strtolower($first));
        $this->assertNotSame('validation.required', $first);
    }

    public function test_default_message_for_size_rule_min_string_with_implicit_messages(): void
    {
        $factory = new Factory();

        $validator = $factory->make(
            ['name' => 'ab'],
            ['name' => 'string|min:5']
        );

        $this->assertTrue($validator->fails());
        $messages = $validator->errors()->get('name');
        $this->assertNotEmpty($messages);
        $first = $messages[0];
        $this->assertStringContainsString('5', $first);
    }
}
