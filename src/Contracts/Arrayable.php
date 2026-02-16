<?php

namespace MB\Validation\Contracts;

interface Arrayable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
