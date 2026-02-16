<?php

namespace MB\Validation\Tests;

use MB\Validation\Factory;

class RuleMessagesTest extends ValidationTestCase
{
    public function test_required_message_matches_lang_ru(): void
    {
        $validator = $this->factory->make([], ['field' => 'required']);

        $this->assertTrue($validator->fails());
        $message = $validator->errors()->first('field');

        $this->assertSame('Поле field обязательно для заполнения.', $message);
    }

    public function test_min_string_message_matches_lang_ru(): void
    {
        $validator = $this->factory->make(['field' => 'hi'], ['field' => 'string|min:5']);

        $this->assertTrue($validator->fails());
        $message = $validator->errors()->first('field');

        $this->assertSame('Длина поля field должна быть не менее 5 символов.', $message);
    }
}

