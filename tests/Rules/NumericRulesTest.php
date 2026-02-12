<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class NumericRulesTest extends ValidationTestCase
{
    public function test_integer_passes(): void
    {
        $this->assertPasses(['field' => 42], ['field' => 'integer']);
    }

    public function test_integer_passes_with_string_number(): void
    {
        $this->assertPasses(['field' => '42'], ['field' => 'integer']);
    }

    public function test_integer_fails_with_float(): void
    {
        $this->assertFails(['field' => 42.5], ['field' => 'integer']);
    }

    public function test_integer_fails_with_string(): void
    {
        $this->assertFails(['field' => 'hello'], ['field' => 'integer']);
    }

    public function test_numeric_passes(): void
    {
        $this->assertPasses(['field' => '42.5'], ['field' => 'numeric']);
    }

    public function test_numeric_fails(): void
    {
        $this->assertFails(['field' => 'hello'], ['field' => 'numeric']);
    }

    public function test_decimal_passes(): void
    {
        $this->assertPasses(['field' => '1.23'], ['field' => 'decimal:2']);
    }

    public function test_decimal_fails_wrong_places(): void
    {
        $this->assertFails(['field' => '1.2'], ['field' => 'decimal:2']);
    }

    public function test_digits_passes(): void
    {
        $this->assertPasses(['field' => '12345'], ['field' => 'digits:5']);
    }

    public function test_digits_fails(): void
    {
        $this->assertFails(['field' => '1234'], ['field' => 'digits:5']);
    }

    public function test_digits_between_passes(): void
    {
        $this->assertPasses(['field' => '12345'], ['field' => 'digits_between:3,6']);
    }

    public function test_digits_between_fails(): void
    {
        $this->assertFails(['field' => '12'], ['field' => 'digits_between:3,6']);
    }

    public function test_min_numeric_passes(): void
    {
        $this->assertPasses(['field' => 10], ['field' => 'numeric|min:5']);
    }

    public function test_min_numeric_fails(): void
    {
        $this->assertFails(['field' => 3], ['field' => 'numeric|min:5']);
    }

    public function test_max_numeric_passes(): void
    {
        $this->assertPasses(['field' => 5], ['field' => 'numeric|max:10']);
    }

    public function test_between_numeric_passes(): void
    {
        $this->assertPasses(['field' => 5], ['field' => 'numeric|between:1,10']);
    }

    public function test_size_numeric_passes(): void
    {
        $this->assertPasses(['field' => 5], ['field' => 'numeric|size:5']);
    }
}
