<?php

namespace MB\Validation\Registry;

use MB\Validation\Contracts\RuleRegistryInterface;
use MB\Validation\Contracts\ValidationRule;

class RuleRegistry implements RuleRegistryInterface
{
    /**
     * @var array Зарегистрированные правила [alias => className]
     */
    private static array $rules = [];

    /**
     * @var array Экземпляры правил [alias => instance]
     */
    private static array $instances = [];

    /**
    * Register Rule
    * @param class-string<ValidationRule> $ruleClass
    */
    public static function register(string ...$ruleClass): void
    {
        foreach ($ruleClass as $rule) {
            if (!is_subclass_of($rule, ValidationRule::class)) {
                throw new \InvalidArgumentException(
                    "Class must be implements \MB\Validation\Contracts\ValidationRule"
                );
            }

            $alias = $rule::alias();
            if (is_array($alias)) {
                foreach ($alias as $aliasRule) {
                    self::$rules[$aliasRule] = $rule;
                }
            } else {
                self::$rules[$alias] = $rule;
            }

        }
    }

    /**
     * Проверяет, зарегистрировано ли правило
     */
    public static function has(string $alias): bool
    {
        return isset(self::$rules[$alias]);
    }

    public static function getClass(string $alias)
    {
        if (!self::has($alias)) {
            throw new \InvalidArgumentException("Rule '{$alias}' not registered");
        }

        return self::$rules[$alias];
    }
    /**
     * Возвращает экземпляр правила
     */
    public static function get(string $alias): ValidationRule
    {
        if (!self::has($alias)) {
            throw new \InvalidArgumentException("Rule '{$alias}' not registered");
        }

        if (!isset(self::$instances[$alias])) {
            $className = self::$rules[$alias];
            self::$instances[$alias] = new $className();
        }

        return self::$instances[$alias];
    }

    /**
     * Возвращает все зарегистрированные алиасы
     */
    public static function all(): array
    {
        return array_keys(self::$rules);
    }

    /**
     * Очищает все зарегистрированные правила
     */
    public static function clear(): void
    {
        self::$rules = [];
        self::$instances = [];
    }
}
