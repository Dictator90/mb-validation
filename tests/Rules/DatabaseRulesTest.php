<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\DatabasePresenceVerifierInterface;
use MB\Validation\Tests\ValidationTestCase;

class DatabaseRulesTest extends ValidationTestCase
{
    public function test_exists_rule_uses_presence_verifier(): void
    {
        $factory = clone $this->factory;
        $factory->setPresenceVerifier(new InMemoryPresenceVerifier([
            'users' => [
                ['email' => 'john@example.com'],
            ],
        ]));

        $validator = $factory->make(
            ['email' => 'john@example.com'],
            ['email' => 'exists:users,email']
        );

        $this->assertTrue($validator->passes());
    }

    public function test_unique_rule_uses_presence_verifier(): void
    {
        $factory = clone $this->factory;
        $factory->setPresenceVerifier(new InMemoryPresenceVerifier([
            'users' => [
                ['email' => 'john@example.com'],
            ],
        ]));

        $validator = $factory->make(
            ['email' => 'john@example.com'],
            ['email' => 'unique:users,email']
        );

        $this->assertTrue($validator->fails());
    }
}

class InMemoryPresenceVerifier implements DatabasePresenceVerifierInterface
{
    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    private array $collections;

    public function __construct(array $collections)
    {
        $this->collections = $collections;
    }

    public function setConnection($connection)
    {
        // In-memory stub ignores connection switches.
    }

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $rows = $this->collections[$collection] ?? [];
        $count = 0;

        foreach ($rows as $row) {
            if (($row[$column] ?? null) !== $value) {
                continue;
            }

            if ($excludeId !== null && $idColumn !== null && ($row[$idColumn] ?? null) == $excludeId) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $rows = $this->collections[$collection] ?? [];
        $count = 0;

        foreach ($rows as $row) {
            if (in_array($row[$column] ?? null, $values, true)) {
                $count++;
            }
        }

        return $count;
    }
}
