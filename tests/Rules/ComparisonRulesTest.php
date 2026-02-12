<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class ComparisonRulesTest extends ValidationTestCase
{
    public function test_same_passes(): void
    {
        $this->assertPasses(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password' => 'same:password_confirmation']
        );
    }

    public function test_same_fails(): void
    {
        $this->assertFails(
            ['password' => 'secret', 'password_confirmation' => 'different'],
            ['password' => 'same:password_confirmation']
        );
    }

    public function test_confirmed_passes(): void
    {
        $this->assertPasses(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password' => 'confirmed']
        );
    }

    public function test_confirmed_fails(): void
    {
        $this->assertFails(
            ['password' => 'secret', 'password_confirmation' => 'different'],
            ['password' => 'confirmed']
        );
    }

    public function test_different_passes(): void
    {
        $this->assertPasses(
            ['field' => 'a', 'other' => 'b'],
            ['field' => 'different:other']
        );
    }

    public function test_different_fails(): void
    {
        $this->assertFails(
            ['field' => 'same', 'other' => 'same'],
            ['field' => 'different:other']
        );
    }
}
