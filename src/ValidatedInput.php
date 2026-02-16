<?php

namespace MB\Validation;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Minimal validated input container (Laravel-compatible style).
 */
class ValidatedInput implements ArrayAccess, Countable, IteratorAggregate
{
    public function __construct(
        protected array $input = []
    ) {}

    /**
     * Get a subset of the validated input.
     *
     * @param  array  $keys
     * @return static
     */
    public function only(array $keys): static
    {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->input)) {
                $result[$key] = $this->input[$key];
            }
        }
        return new static($result);
    }

    public function all(): array
    {
        return $this->input;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->input);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->input[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->input[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->input[$offset]);
    }

    public function count(): int
    {
        return count($this->input);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->input);
    }
}
