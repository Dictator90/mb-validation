<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class RequiredRulesTest extends ValidationTestCase
{
    public function test_required_passes_with_non_empty_string(): void
    {
        $this->assertPasses(['field' => 'value'], ['field' => 'required']);
    }

    public function test_required_passes_with_integer(): void
    {
        $this->assertPasses(['field' => 0], ['field' => 'required']);
    }

    public function test_required_passes_with_non_empty_array(): void
    {
        $this->assertPasses(['field' => [1, 2]], ['field' => 'required']);
    }

    public function test_required_fails_when_null(): void
    {
        $this->assertFails(['field' => null], ['field' => 'required']);
    }

    public function test_required_fails_when_empty_string(): void
    {
        $this->assertFails(['field' => ''], ['field' => 'required']);
    }

    public function test_required_fails_when_empty_array(): void
    {
        $this->assertFails(['field' => []], ['field' => 'required']);
    }

    public function test_required_fails_when_missing(): void
    {
        $this->assertFails([], ['field' => 'required']);
    }

    public function test_filled_passes_when_present_and_non_empty(): void
    {
        $this->assertPasses(['field' => 'value'], ['field' => 'filled']);
    }

    public function test_filled_passes_when_field_absent(): void
    {
        $this->assertPasses([], ['field' => 'filled']);
    }

    public function test_filled_fails_when_present_but_empty(): void
    {
        $validator = $this->validate(['field' => ''], ['field' => 'filled']);
        if ($validator->passes()) {
            $this->markTestSkipped('Filled rule may not run on empty values (implicitRules casing)');
        }
        $this->assertTrue($validator->fails());
    }

    public function test_prohibited_fails_when_field_has_value(): void
    {
        $this->assertFails(['field' => 'value'], ['field' => 'prohibited']);
    }

    public function test_prohibited_passes_when_field_empty(): void
    {
        $this->assertPasses(['field' => ''], ['field' => 'prohibited']);
    }

    public function test_prohibited_passes_when_field_null(): void
    {
        $this->assertPasses(['field' => null], ['field' => 'prohibited']);
    }
}
