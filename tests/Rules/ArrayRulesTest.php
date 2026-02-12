<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class ArrayRulesTest extends ValidationTestCase
{
    public function test_array_passes(): void
    {
        $this->assertPasses(['field' => [1, 2]], ['field' => 'array']);
    }

    public function test_array_fails(): void
    {
        $this->assertFails(['field' => 'string'], ['field' => 'array']);
    }

    public function test_array_with_keys_passes(): void
    {
        $this->assertPasses(
            ['field' => ['name' => 'a', 'age' => 'b']],
            ['field' => 'array:name,age']
        );
    }

    public function test_array_with_keys_fails_when_extra_keys(): void
    {
        $this->assertFails(
            ['field' => ['name' => 'a', 'age' => 'b', 'extra' => 'c']],
            ['field' => 'array:name,age']
        );
    }

    public function test_list_passes(): void
    {
        $this->assertPasses(['field' => [1, 2, 3]], ['field' => 'list']);
    }

    public function test_list_fails_with_associative(): void
    {
        $this->assertFails(['field' => ['a' => 1]], ['field' => 'list']);
    }

    public function test_in_passes(): void
    {
        $this->assertPasses(['field' => 'red'], ['field' => 'in:red,green,blue']);
    }

    public function test_in_fails(): void
    {
        $this->assertFails(['field' => 'yellow'], ['field' => 'in:red,green,blue']);
    }

    public function test_not_in_passes(): void
    {
        $this->assertPasses(['field' => 'yellow'], ['field' => 'not_in:red,green,blue']);
    }

    public function test_not_in_fails(): void
    {
        $this->assertFails(['field' => 'red'], ['field' => 'not_in:red,green,blue']);
    }

    public function test_contains_passes(): void
    {
        $this->assertPasses(
            ['field' => ['a', 'b', 'c']],
            ['field' => 'contains:a,b']
        );
    }

    public function test_contains_fails(): void
    {
        $this->assertFails(
            ['field' => ['a', 'c']],
            ['field' => 'contains:a,b']
        );
    }

    public function test_doesnt_contain_passes(): void
    {
        $this->assertPasses(
            ['field' => ['a', 'c']],
            ['field' => 'doesnt_contain:b']
        );
    }

    public function test_doesnt_contain_fails(): void
    {
        $this->assertFails(
            ['field' => ['a', 'b', 'c']],
            ['field' => 'doesnt_contain:b']
        );
    }

    public function test_required_array_keys_passes(): void
    {
        $this->assertPasses(
            ['field' => ['name' => 'a', 'age' => 'b']],
            ['field' => 'required_array_keys:name,age']
        );
    }

    public function test_required_array_keys_fails(): void
    {
        $this->assertFails(
            ['field' => ['name' => 'a']],
            ['field' => 'required_array_keys:name,age']
        );
    }

    public function test_min_array_passes(): void
    {
        $this->assertPasses(['field' => [1, 2, 3]], ['field' => 'array|min:2']);
    }

    public function test_max_array_passes(): void
    {
        $this->assertPasses(['field' => [1, 2]], ['field' => 'array|max:5']);
    }
}
