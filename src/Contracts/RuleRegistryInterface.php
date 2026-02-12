<?php
namespace MB\Validation\Contracts;

interface RuleRegistryInterface
{
    public static function register(string ...$ruleClass): void;
    public static function has(string $alias): bool;
    public static function get(string $alias): ValidationRule;
}