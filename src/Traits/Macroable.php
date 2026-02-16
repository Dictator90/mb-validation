<?php

namespace MB\Validation\Traits;

use BadMethodCallException;

trait Macroable
{
    /**
     * @var array<string, callable>
     */
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    public static function mixin(object $mixin, bool $replace = true): void
    {
        $methods = get_class_methods($mixin);
        foreach ($methods as $method) {
            if ($replace || ! isset(static::$macros[$method])) {
                static::$macros[$method] = [$mixin, $method];
            }
        }
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }
        return call_user_func_array(static::$macros[$method], $parameters);
    }

    /**
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }
        $macro = static::$macros[$method];
        return $macro instanceof \Closure
            ? call_user_func_array($macro->bindTo($this, static::class), $parameters)
            : call_user_func_array($macro, array_merge([$this], $parameters));
    }
}
