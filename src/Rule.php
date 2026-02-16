<?php

namespace MB\Validation;

use MB\Support\Arr;
use MB\Validation\Contracts\Arrayable;
use MB\Validation\Traits\Macroable;
use MB\Validation\Rules\AnyOfRule;
use MB\Validation\Rules\ArrayRule;
use MB\Validation\Rules\Can;
use MB\Validation\Rules\DateRule;
use MB\Validation\Rules\Dimensions;
use MB\Validation\Rules\Email;
use MB\Validation\Rules\Enum;
use MB\Validation\Rules\ExcludeIf;
use MB\Validation\Rules\Exists;
use MB\Validation\Rules\File;
use MB\Validation\Rules\ImageFile;
use MB\Validation\Rules\In;
use MB\Validation\Rules\NotIn;
use MB\Validation\Rules\Numeric;
use MB\Validation\Rules\ProhibitedIf;
use MB\Validation\Rules\RequiredIf;
use MB\Validation\Rules\Unique;

class Rule
{
    use Macroable;

    /**
     * Get a can constraint builder instance.
     *
     * @param  string  $ability
     * @param  mixed  ...$arguments
     * @return \MB\Validation\Rules\Can
     */
    public static function can($ability, ...$arguments)
    {
        return new Can($ability, $arguments);
    }

    /**
     * Apply the given rules if the given condition is truthy.
     *
     * @param  callable|bool  $condition
     * @param  \MB\Validation\Contracts\ValidationRule|\MB\Validation\Contracts\InvokableRule|\MB\Validation\Contracts\Rule|\Closure|array|string  $rules
     * @param  \MB\Validation\Contracts\ValidationRule|\MB\Validation\Contracts\InvokableRule|\MB\Validation\Contracts\Rule|\Closure|array|string  $defaultRules
     * @return \MB\Validation\ConditionalRules
     */
    public static function when($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $rules, $defaultRules);
    }

    /**
     * Apply the given rules if the given condition is falsy.
     *
     * @param  callable|bool  $condition
     * @param  \MB\Validation\Contracts\ValidationRule|\MB\Validation\Contracts\InvokableRule|\MB\Validation\Contracts\Rule|\Closure|array|string  $rules
     * @param  \MB\Validation\Contracts\ValidationRule|\MB\Validation\Contracts\InvokableRule|\MB\Validation\Contracts\Rule|\Closure|array|string  $defaultRules
     * @return \MB\Validation\ConditionalRules
     */
    public static function unless($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $defaultRules, $rules);
    }

    /**
     * Get an array rule builder instance.
     *
     * @param  array|null  $keys
     * @return \MB\Validation\Rules\ArrayRule
     */
    public static function array($keys = null)
    {
        return new ArrayRule(...func_get_args());
    }

    /**
     * Create a new nested rule set.
     *
     * @param  callable  $callback
     * @return \MB\Validation\NestedRules
     */
    public static function forEach($callback)
    {
        return new NestedRules($callback);
    }

    /**
     * Get a unique constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \MB\Validation\Rules\Unique
     */
    public static function unique($table, $column = 'NULL')
    {
        return new Unique($table, $column);
    }

    /**
     * Get an exists constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \MB\Validation\Rules\Exists
     */
    public static function exists($table, $column = 'NULL')
    {
        return new Exists($table, $column);
    }

    /**
     * Get an in rule builder instance.
     *
     * @param  \MB\Validation\Contracts\Arrayable|\BackedEnum|\UnitEnum|array|string  $values
     * @return \MB\Validation\Rules\In
     */
    public static function in($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new In(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a not_in rule builder instance.
     *
     * @param  \MB\Validation\Contracts\Arrayable|\BackedEnum|\UnitEnum|array|string  $values
     * @return \MB\Validation\Rules\NotIn
     */
    public static function notIn($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new NotIn(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a required_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \MB\Validation\Rules\RequiredIf
     */
    public static function requiredIf($callback)
    {
        return new RequiredIf($callback);
    }

    /**
     * Get a exclude_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \MB\Validation\Rules\ExcludeIf
     */
    public static function excludeIf($callback)
    {
        return new ExcludeIf($callback);
    }

    /**
     * Get a prohibited_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \MB\Validation\Rules\ProhibitedIf
     */
    public static function prohibitedIf($callback)
    {
        return new ProhibitedIf($callback);
    }

    /**
     * Get a date rule builder instance.
     *
     * @return \MB\Validation\Rules\DateRule
     */
    public static function date()
    {
        return new DateRule;
    }

    /**
     * Get a datetime rule builder instance.
     */
    public static function dateTime(): DateRule
    {
        return (new DateRule)->format('Y-m-d H:i:s');
    }

    /**
     * Get an email rule builder instance.
     *
     * @return \MB\Validation\Rules\Email
     */
    public static function email()
    {
        return new Email;
    }

    /**
     * Get an enum rule builder instance.
     *
     * @param  class-string  $type
     * @return \MB\Validation\Rules\Enum
     */
    public static function enum($type)
    {
        return new Enum($type);
    }

    /**
     * Get a file rule builder instance.
     *
     * @return \MB\Validation\Rules\File
     */
    public static function file()
    {
        return new File;
    }

    /**
     * Get an image file rule builder instance.
     *
     * @param  bool  $allowSvg
     * @return \MB\Validation\Rules\ImageFile
     */
    public static function imageFile($allowSvg = false)
    {
        return new ImageFile($allowSvg);
    }

    /**
     * Get a dimensions rule builder instance.
     *
     * @param  array  $constraints
     * @return \MB\Validation\Rules\Dimensions
     */
    public static function dimensions(array $constraints = [])
    {
        return new Dimensions($constraints);
    }

    /**
     * Get a numeric rule builder instance.
     *
     * @return \MB\Validation\Rules\Numeric
     */
    public static function numeric()
    {
        return new Numeric;
    }

    /**
     * Get an "any of" rule builder instance.
     *
     * @param  array  $rules
     * @return \MB\Validation\Rules\AnyOfRule
     *
     * @throws \InvalidArgumentException
     */
    public static function anyOf($rules)
    {
        return new AnyOfRule($rules);
    }

    /**
     * Get a contains rule builder instance.
     *
     * @param  \MB\Validation\Contracts\Arrayable|\BackedEnum|\UnitEnum|array|string  $values
     * @return \MB\Validation\Rules\Contains
     */
    public static function contains($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new Rules\Contains(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a "does not contain" rule builder instance.
     *
     * @param  \MB\Validation\Contracts\Arrayable|\BackedEnum|\UnitEnum|array|string  $values
     * @return \MB\Validation\Rules\DoesntContain
     */
    public static function doesntContain($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new Rules\DoesntContain(is_array($values) ? $values : func_get_args());
    }

    /**
     * Compile a set of rules for an attribute.
     *
     * @param  string  $attribute
     * @param  array  $rules
     * @param  array|null  $data
     * @return object|\stdClass
     */
    public static function compile($attribute, $rules, $data = null)
    {
        $parser = new ValidationRuleParser(
            Arr::undot(Arr::wrap($data))
        );

        if (is_array($rules) && ! array_is_list($rules)) {
            $nested = [];

            foreach ($rules as $key => $rule) {
                $nested[$attribute.'.'.$key] = $rule;
            }

            $rules = $nested;
        } else {
            $rules = [$attribute => $rules];
        }

        return $parser->explode(ValidationRuleParser::filterConditionalRules($rules, $data));
    }
}
