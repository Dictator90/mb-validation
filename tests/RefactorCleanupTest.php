<?php

namespace MB\Validation\Tests;

use InvalidArgumentException;
use MB\Validation\Concerns\ValidatesProperties;
use MB\Validation\Contracts\ValidationRule;
use MB\Validation\Factory;
use MB\Validation\InvokableValidationRule;
use MB\Validation\Registry\RuleRegistry;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RefactorCleanupTest extends TestCase
{
    public function test_unknown_rule_throws_in_strict_mode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Factory::create()->make(['name' => 'John'], ['name' => 'unknown_rule'])->passes();
    }

    public function test_unknown_rule_is_ignored_when_unknown_rules_are_allowed(): void
    {
        $validator = Factory::create()->allowUnknownRules()->make(
            ['name' => 'John'],
            ['name' => 'unknown_rule']
        );

        $this->assertTrue($validator->passes());
    }

    public function test_internal_rule_lists_only_reference_registered_rules(): void
    {
        $validator = Factory::create()->make([], []);
        $reflection = new ReflectionClass($validator);

        foreach (['implicitRules', 'dependentRules', 'excludeRules', 'sizeRules', 'numericRules', 'defaultNumericRules'] as $property) {
            $aliases = $reflection->getProperty($property)->getValue($validator);

            foreach ($aliases as $alias) {
                $this->assertTrue(
                    RuleRegistry::has($alias),
                    sprintf('Validator::$%s contains unregistered rule alias [%s].', $property, $alias)
                );
            }
        }
    }

    public function test_readme_supported_rules_are_registered(): void
    {
        Factory::create()->make([], []);

        $readme = file_get_contents(__DIR__ . '/../README.md');
        preg_match('/## Supported Rule Groups(?P<section>.*?)## Messages and Localization/s', $readme, $matches);
        preg_match_all('/`([^`]+)`/', $matches['section'] ?? '', $ruleMatches);

        $aliases = array_filter($ruleMatches[1], static fn (string $alias): bool => !str_contains($alias, 'PresenceVerifierInterface'));

        $this->assertNotEmpty($aliases);

        foreach ($aliases as $alias) {
            $this->assertTrue(RuleRegistry::has($alias), sprintf('README documents unregistered rule [%s].', $alias));
        }
    }

    public function test_rule_registry_get_returns_new_instance_each_time(): void
    {
        RuleRegistry::register(StatefulCounterRule::class);

        $this->assertNotSame(
            RuleRegistry::get('stateful_counter'),
            RuleRegistry::get('stateful_counter')
        );
    }

    public function test_rule_registry_state_does_not_leak_between_validator_runs(): void
    {
        RuleRegistry::register(StatefulCounterRule::class);

        $validator = Factory::create()->make(['field' => 'value'], ['field' => 'stateful_counter']);

        $this->assertTrue($validator->passes());
        $this->assertTrue($validator->passes());
    }

    public function test_invokable_validation_rule_does_not_reuse_messages_between_runs(): void
    {
        $rule = InvokableValidationRule::make(new FailsForBadValueRule());
        $validator = Factory::create()->make(['field' => 'bad'], ['field' => [$rule]]);

        $this->assertTrue($validator->fails());
        $this->assertSame(['Custom failure message'], $validator->errors()->get('field'));

        $validator->setData(['field' => 'good']);

        $this->assertTrue($validator->passes());
        $this->assertSame([], $validator->errors()->get('field'));
        $this->assertSame([], $rule->message());
    }

    public function test_basic_required_string_min_validation_still_works(): void
    {
        $this->assertTrue(
            Factory::create()->make(['name' => 'John'], ['name' => 'required|string|min:3'])->passes()
        );
    }

    public function test_wildcard_validation_still_works(): void
    {
        $validator = Factory::create()->make(
            ['items' => [['name' => 'First'], ['name' => 'Second']]],
            ['items.*.name' => 'required|string']
        );

        $this->assertTrue($validator->passes());
    }

    public function test_validates_properties_trait_still_works(): void
    {
        $fixture = new RefactorCleanupValidatesPropertiesFixture(
            name: 'Catalog',
            items: [['name' => 'First'], ['name' => 'Second']]
        );

        $this->assertSame(
            [
                'name' => 'Catalog',
                'items' => [['name' => 'First'], ['name' => 'Second']],
            ],
            $fixture->validate()
        );
    }
}

final class StatefulCounterRule implements ValidationRule
{
    private int $calls = 0;

    public static function alias(): string|array
    {
        return 'stateful_counter';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        $this->calls++;

        if ($this->calls > 1) {
            $fail($attribute, 'State leaked between validations.');
        }
    }
}

final class FailsForBadValueRule implements ValidationRule
{
    public static function alias(): string|array
    {
        return 'fails_for_bad_value';
    }

    public function validate(string $attribute, mixed $value, ?array $parameters, \Closure $fail): void
    {
        if ($value === 'bad') {
            $fail($attribute, 'Custom failure message');
        }
    }
}

final class RefactorCleanupValidatesPropertiesFixture
{
    use ValidatesProperties;

    public function __construct(
        private string $name,
        private array $items,
    ) {
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
        ];
    }
}
