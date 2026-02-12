<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class DateRulesTest extends ValidationTestCase
{
    public function test_date_passes(): void
    {
        $this->assertPasses(['field' => '2024-01-15'], ['field' => 'date']);
    }

    public function test_date_passes_with_datetime(): void
    {
        $this->assertPasses(['field' => '2024-01-15 10:30:00'], ['field' => 'date']);
    }

    public function test_date_fails(): void
    {
        $this->assertFails(['field' => 'invalid-date'], ['field' => 'date']);
    }

    public function test_date_format_passes(): void
    {
        $this->assertPasses(['field' => '15-01-2024'], ['field' => 'date_format:d-m-Y']);
    }

    public function test_date_format_fails(): void
    {
        $this->assertFails(['field' => '2024-01-15'], ['field' => 'date_format:d-m-Y']);
    }
}
