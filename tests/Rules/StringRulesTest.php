<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class StringRulesTest extends ValidationTestCase
{
    public function test_string_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'string']);
    }

    public function test_string_fails_for_integer(): void
    {
        $this->assertFails(['field' => 123], ['field' => 'string']);
    }

    public function test_string_fails_for_array(): void
    {
        $this->assertFails(['field' => []], ['field' => 'string']);
    }

    public function test_min_string_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'min:3']);
    }

    public function test_min_string_fails(): void
    {
        $this->assertFails(['field' => 'hi'], ['field' => 'min:5']);
    }

    public function test_max_string_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'max:10']);
    }

    public function test_max_string_fails(): void
    {
        $this->assertFails(['field' => 'hello world'], ['field' => 'max:5']);
    }

    public function test_between_string_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'between:3,10']);
    }

    public function test_between_string_fails(): void
    {
        $this->assertFails(['field' => 'hi'], ['field' => 'between:5,10']);
    }

    public function test_size_string_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'size:5']);
    }

    public function test_size_string_fails(): void
    {
        $this->assertFails(['field' => 'hi'], ['field' => 'size:5']);
    }

    public function test_alpha_passes(): void
    {
        $this->assertPasses(['field' => 'Hello'], ['field' => 'alpha']);
    }

    public function test_alpha_fails_with_numbers(): void
    {
        $this->assertFails(['field' => 'Hello123'], ['field' => 'alpha']);
    }

    public function test_alpha_num_passes(): void
    {
        $this->assertPasses(['field' => 'Hello123'], ['field' => 'alpha_num']);
    }

    public function test_alpha_dash_passes(): void
    {
        $this->assertPasses(['field' => 'hello-world_123'], ['field' => 'alpha_dash']);
    }

    public function test_ascii_passes(): void
    {
        if (!method_exists(\MB\Support\Str::class, 'isAscii')) {
            $this->markTestSkipped('Str::isAscii not available');
        }
        $this->assertPasses(['field' => 'hello'], ['field' => 'ascii']);
    }

    public function test_regex_passes(): void
    {
        $this->assertPasses(['field' => 'abc123'], ['field' => 'regex:/^[a-z]+\d+$/']);
    }

    public function test_regex_fails(): void
    {
        $this->assertFails(['field' => '123abc'], ['field' => 'regex:/^[a-z]+\d+$/']);
    }

    public function test_not_regex_fails_when_matches(): void
    {
        $this->assertFails(['field' => 'abc123'], ['field' => 'not_regex:/^[a-z]+\d+$/']);
    }

    public function test_not_regex_passes_when_not_matches(): void
    {
        $this->assertPasses(['field' => '123abc'], ['field' => 'not_regex:/^[a-z]+\d+$/']);
    }

    public function test_starts_with_passes(): void
    {
        $this->assertPasses(['field' => 'hello world'], ['field' => 'starts_with:hello']);
    }

    public function test_starts_with_fails(): void
    {
        $this->assertFails(['field' => 'world hello'], ['field' => 'starts_with:hello']);
    }

    public function test_ends_with_passes(): void
    {
        $this->assertPasses(['field' => 'hello world'], ['field' => 'ends_with:world']);
    }

    public function test_ends_with_fails(): void
    {
        $this->assertFails(['field' => 'hello world'], ['field' => 'ends_with:hello']);
    }

    public function test_doesnt_start_with_passes(): void
    {
        $this->assertPasses(['field' => 'world'], ['field' => 'doesnt_start_with:hello']);
    }

    public function test_doesnt_start_with_fails(): void
    {
        $this->assertFails(['field' => 'hello world'], ['field' => 'doesnt_start_with:hello']);
    }

    public function test_doesnt_end_with_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'doesnt_end_with:world']);
    }

    public function test_lowercase_passes(): void
    {
        $this->assertPasses(['field' => 'hello'], ['field' => 'lowercase']);
    }

    public function test_lowercase_fails(): void
    {
        $this->assertFails(['field' => 'Hello'], ['field' => 'lowercase']);
    }

    public function test_uppercase_passes(): void
    {
        $this->assertPasses(['field' => 'HELLO'], ['field' => 'uppercase']);
    }

    public function test_uppercase_fails(): void
    {
        $this->assertFails(['field' => 'Hello'], ['field' => 'uppercase']);
    }
}
