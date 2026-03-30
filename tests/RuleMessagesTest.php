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

    public function test_required_message_matches_lang_en(): void
    {
        $validator = Factory::create(lang: 'en')->make([], ['field' => 'required']);

        $this->assertTrue($validator->fails());
        $message = $validator->errors()->first('field');

        $this->assertSame('The field field is required.', $message);
    }

    public function test_default_factory_constructor_uses_ru_locale(): void
    {
        $validator = (new Factory())->make([], ['field' => 'required']);

        $this->assertTrue($validator->fails());
        $message = $validator->errors()->first('field');

        $this->assertSame('Поле field обязательно для заполнения.', $message);
    }
}

