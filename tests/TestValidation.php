<?php

namespace MB\Validation\Tests;

use PHPUnit\Framework\TestCase;

class TestValidation extends TestCase
{
    public function test_base_validation(): void
    {
        $arrayLoader = new \Illuminate\Translation\ArrayLoader();
        $arrayLoader->addMessages('en', 'validation', [
            'string' => ':attribute must be string'
        ]);
        $translator = new \Illuminate\Translation\Translator($arrayLoader, 'en');
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
