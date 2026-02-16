<?php

namespace MB\Validation\Contracts;

/**
 * Source of table/connection/key for exists/unique rules (e.g. Eloquent Model).
 */
interface TableNameSource
{
    public function getTable(): string;

    public function getConnectionName(): ?string;

    public function getKeyName(): string;
}
