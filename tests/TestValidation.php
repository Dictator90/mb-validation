<?php

namespace MB\Validation\Tests;

use PHPUnit\Framework\TestCase;

class TestValidation extends TestCase
{
    public function test_base_validation(): void
    {
        $translator = new \MB\Messages\ArrayMessages([], 'en');
        $translator->addMessages('en', 'validation', [
            'string' => ':attribute must be string'
        ]);
        $validation = new \MB\Validation\Validator(
            $translator,
            [
                'name' => 'John',
                'age' => [
                    'value' => 4545
                ],
                'email' => 'john@example.com',
                'base' => null,
                'array' => [
                    'name' => 'Base',
                    'age' => '18',
                ]
            ],
            [
                'age' => 'required|array:value',
                'age.value' => 'required|int:strict',
            ],
            [
                //'required' => ':attribute is required'
            ]
        );

        $this->assertTrue($validation->passes(), 'Validation should pass: ' . json_encode($validation->messages()->toArray()));
    }
}
