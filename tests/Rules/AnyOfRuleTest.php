<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

/**
 * AnyOfRule requires validator context for sub-rules - skip if InvokableValidationRule
 * doesn't receive validator when used internally.
 */
class AnyOfRuleTest extends ValidationTestCase
{
    public function test_any_of_passes_when_first_rule_passes(): void
    {
        $this->assertPasses(
            ['field' => 'hello'],
            ['field' => 'any_of:string,integer']
        );
    }

    public function test_any_of_passes_when_second_rule_passes(): void
    {
        $this->assertPasses(
            ['field' => 42],
            ['field' => 'any_of:string,integer']
        );
    }

    public function test_any_of_fails_when_no_rule_passes(): void
    {
        $this->assertFails(
            ['field' => []],
            ['field' => 'any_of:string,integer']
        );
    }
}
