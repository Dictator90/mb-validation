<?php

namespace MB\Validation\Tests;

use MB\Validation\Concerns\ValidatesProperties;
use MB\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidatesPropertiesTraitTest extends TestCase
{
    public function test_validate_returns_validated_data_for_object_properties(): void
    {
        $subject = new ValidatesPropertiesFixture(
            name: 'John',
            items: [['name' => 'Item 1']],
            profile: ['tags' => ['php', 'validation']],
            code: 'A-1'
        );

        $validated = $subject->validate();

        $this->assertSame('John', $validated['name']);
        $this->assertSame('A-1', $validated['code']);
        $this->assertSame('Item 1', $validated['items'][0]['name']);
        $this->assertSame('php', $validated['profile']['tags'][0]);
    }

    public function test_validate_reads_private_and_protected_properties(): void
    {
        $subject = new ValidatesPropertiesFixture(
            name: 'Jane',
            items: [['name' => 'Protected item']],
            profile: ['tags' => ['private-tag']],
            code: 'PRV-42'
        );

        $validated = $subject->validate();

        $this->assertArrayHasKey('items', $validated);
        $this->assertArrayHasKey('profile', $validated);
        $this->assertArrayHasKey('code', $validated);
        $this->assertSame('PRV-42', $validated['code']);
    }

    public function test_validate_supports_wildcard_rules_for_arrays(): void
    {
        $subject = new ValidatesPropertiesFixture(
            name: 'John',
            items: [['name' => 'ok'], ['name' => 'second']],
            profile: ['tags' => ['ok']],
            code: 'A-1'
        );

        $validated = $subject->validate();

        $this->assertSame('ok', $validated['items'][0]['name']);
        $this->assertSame('second', $validated['items'][1]['name']);
    }

    public function test_validate_uses_validator_lang_for_messages(): void
    {
        $subject = new ValidatesPropertiesFixture(
            name: '',
            items: [['name' => 'Item 1']],
            profile: ['tags' => ['php']],
            code: 'A-1',
            validatorLang: 'en'
        );

        try {
            $subject->validate();
            $this->fail('ValidationException was expected.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('required', strtolower($e->getMessage()));
        }
    }
}

class ValidatesPropertiesFixture
{
    use ValidatesProperties;

    public string $name;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $items;

    /**
     * @var array<string, mixed>
     */
    private array $profile;

    private string $code;

    public function __construct(
        string $name,
        array $items,
        array $profile,
        string $code,
        string $validatorLang = 'ru'
    ) {
        $this->name = $name;
        $this->items = $items;
        $this->profile = $profile;
        $this->code = $code;
        $this->validatorLang = $validatorLang;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'profile' => 'required|array',
            'profile.tags.*' => 'required|string',
            'code' => 'required|string',
        ];
    }
}
