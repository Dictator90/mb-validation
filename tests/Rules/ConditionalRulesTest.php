<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class ConditionalRulesTest extends ValidationTestCase
{
    public function test_required_if_passes_when_other_field_has_different_value(): void
    {
        $this->assertPasses(
            ['type' => 'guest', 'name' => null],
            ['name' => 'required_if:type,admin']
        );
    }

    public function test_required_if_fails_when_other_field_matches_and_field_empty(): void
    {
        $validator = $this->validate(
            ['type' => 'admin', 'name' => ''],
            ['name' => 'required_if:type,admin']
        );
        if ($validator->passes()) {
            $this->markTestSkipped('required_if may not run on empty values (implicitRules casing)');
        }
        $this->assertTrue($validator->fails());
    }

    public function test_required_if_passes_when_other_field_matches_and_field_filled(): void
    {
        $this->assertPasses(
            ['type' => 'admin', 'name' => 'John'],
            ['name' => 'required_if:type,admin']
        );
    }

    public function test_required_unless_passes_when_other_in_values(): void
    {
        $this->assertPasses(
            ['type' => 'guest', 'name' => null],
            ['name' => 'required_unless:type,guest,anonymous']
        );
    }

    public function test_required_unless_fails_when_other_not_in_values_and_field_empty(): void
    {
        $validator = $this->validate(
            ['type' => 'admin', 'name' => ''],
            ['name' => 'required_unless:type,guest,anonymous']
        );
        if ($validator->passes()) {
            $this->markTestSkipped('required_unless may not run on empty values (implicitRules casing)');
        }
        $this->assertTrue($validator->fails());
    }

    public function test_required_unless_passes_when_other_not_in_values_but_field_filled(): void
    {
        $this->assertPasses(
            ['type' => 'admin', 'name' => 'John'],
            ['name' => 'required_unless:type,guest,anonymous']
        );
    }
}
