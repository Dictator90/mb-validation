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
}
