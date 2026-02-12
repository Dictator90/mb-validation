<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class BooleanRulesTest extends ValidationTestCase
{
    public function test_boolean_passes_with_true(): void
    {
        $this->assertPasses(['field' => true], ['field' => 'boolean']);
    }

    public function test_boolean_passes_with_false(): void
    {
        $this->assertPasses(['field' => false], ['field' => 'boolean']);
    }

    public function test_boolean_passes_with_one(): void
    {
        $this->assertPasses(['field' => 1], ['field' => 'boolean']);
    }

    public function test_boolean_passes_with_zero(): void
    {
        $this->assertPasses(['field' => 0], ['field' => 'boolean']);
    }

    public function test_boolean_fails(): void
    {
        $this->assertFails(['field' => 'maybe'], ['field' => 'boolean']);
    }

    public function test_accepted_passes_with_yes(): void
    {
        $this->assertPasses(['field' => 'yes'], ['field' => 'accepted']);
    }

    public function test_accepted_passes_with_one(): void
    {
        $this->assertPasses(['field' => 1], ['field' => 'accepted']);
    }

    public function test_accepted_fails_with_no(): void
    {
        $this->assertFails(['field' => 'no'], ['field' => 'accepted']);
    }

    public function test_accepted_fails_when_empty(): void
    {
        $this->assertFails(['field' => ''], ['field' => 'accepted']);
    }

    public function test_declined_passes_with_no(): void
    {
        $this->assertPasses(['field' => 'no'], ['field' => 'declined']);
    }

    public function test_declined_passes_with_zero(): void
    {
        $this->assertPasses(['field' => 0], ['field' => 'declined']);
    }

    public function test_declined_fails_with_yes(): void
    {
        $this->assertFails(['field' => 'yes'], ['field' => 'declined']);
    }
}
