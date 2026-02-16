<?php

namespace MB\Validation;

use BadMethodCallException;
use MB\Messages\Contracts\MessagesInterface;
use MB\Support\Arr;
use MB\Support\Collection;
use MB\Support\Str;
use MB\Validation\Concerns\ValidatesAttributes;
use MB\Validation\Contracts\ReplacerRule;
use MB\Validation\Contracts\ValidatorInterface;
use MB\Validation\Message\MessageBag;
use MB\Validation\ValidatedInput;
use InvalidArgumentException;
use MB\Validation\Registry\RuleRegistry;
use MB\Validation\Rules\AcceptedRule;
use MB\Validation\Rules\AlphaDashRule;
use MB\Validation\Rules\AlphaNumRule;
use MB\Validation\Rules\AlphaRule;
use MB\Validation\Rules\AnyOfRule;
use MB\Validation\Rules\ArrayRule;
use MB\Validation\Rules\AsciiRule;
use MB\Validation\Rules\BetweenRule;
use MB\Validation\Rules\BooleanRule;
use MB\Validation\Rules\ConfirmedRule;
use MB\Validation\Rules\ContainsRule;
use MB\Validation\Rules\DateRule;
use MB\Validation\Rules\DateFormatRule;
use MB\Validation\Rules\DecimalRule;
use MB\Validation\Rules\DeclinedRule;
use MB\Validation\Rules\DifferentRule;
use MB\Validation\Rules\DigitsBetweenRule;
use MB\Validation\Rules\DigitsRule;
use MB\Validation\Rules\DoesntContainRule;
use MB\Validation\Rules\DoesntEndWithRule;
use MB\Validation\Rules\DoesntStartWithRule;
use MB\Validation\Rules\EndsWithRule;
use MB\Validation\Rules\FilledRule;
use MB\Validation\Rules\HexColorRule;
use MB\Validation\Rules\InRule;
use MB\Validation\Rules\IntegerRule;
use MB\Validation\Rules\IpRule;
use MB\Validation\Rules\Ipv4Rule;
use MB\Validation\Rules\Ipv6Rule;
use MB\Validation\Rules\JsonRule;
use MB\Validation\Rules\ListRule;
use MB\Validation\Rules\LowercaseRule;
use MB\Validation\Rules\MacAddressRule;
use MB\Validation\Rules\MaxRule;
use MB\Validation\Rules\MinRule;
use MB\Validation\Rules\NotInRule;
use MB\Validation\Rules\NotRegexRule;
use MB\Validation\Rules\NumericRule;
use MB\Validation\Rules\ProhibitedRule;
use MB\Validation\Rules\RegexRule;
use MB\Validation\Rules\RequiredArrayKeysRule;
use MB\Validation\Rules\RequiredIfRule;
use MB\Validation\Rules\RequiredRule;
use MB\Validation\Rules\RequiredUnlessRule;
use MB\Validation\Rules\SameRule;
use MB\Validation\Rules\SizeRule;
use MB\Validation\Rules\StartsWithRule;
use MB\Validation\Rules\StringRule;
use MB\Validation\Rules\TimezoneRule;
use MB\Validation\Rules\UlidRule;
use MB\Validation\Rules\UppercaseRule;
use MB\Validation\Rules\UrlRule;
use MB\Validation\Rules\UuidRule;
use Psr\Container\ContainerInterface;
use RuntimeException;
use stdClass;

class Validator implements ValidatorInterface
{
    use Concerns\FormatsMessages,
        ValidatesAttributes;

    /**
     * The messages / translation implementation.
     *
     * @var MessagesInterface
     */
    protected $message;

    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The Presence Verifier implementation.
     *
     * @var \MB\Validation\PresenceVerifierInterface
     */
    protected $presenceVerifier;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * Attributes that should be excluded from the validated data.
     *
     * @var array
     */
    protected $excludeAttributes = [];

    /**
     * The message bag instance.
     *
     * @var MessageBag
     */
    protected $messages;

    /**
     * @var class-string<RuleRegistry>
     */
    protected $registryClass = RuleRegistry::class;
    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The initial rules provided.
     *
     * @var array
     */
    protected $initialRules;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The current rule that is validating.
     *
     * @var string
     */
    protected $currentRule;

    /**
     * The array of wildcard attributes with their asterisks expanded.
     *
     * @var array
     */
    protected $implicitAttributes = [];

    /**
     * The callback that should be used to format the attribute.
     *
     * @var callable|null
     */
    protected $implicitAttributesFormatter;

    /**
     * The cached data for the "distinct" rule.
     *
     * @var array
     */
    protected $distinctValues = [];

    /**
     * All of the registered "after" callbacks.
     *
     * @var array
     */
    protected $after = [];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public $customMessages = [];

    /**
     * The array of fallback error messages.
     *
     * @var array
     */
    public $fallbackMessages = [];

    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    public $customAttributes = [];

    /**
     * The array of custom displayable values.
     *
     * @var array
     */
    public $customValues = [];

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = false;

    /**
     * Indicates that unvalidated array keys should be excluded, even if the parent array was validated.
     *
     * @var bool
     */
    public $excludeUnvalidatedArrayKeys = false;

    /**
     * All of the custom validator extensions.
     *
     * @var array
     */
    public $extensions = [];

    /**
     * All of the custom replacer extensions.
     *
     * @var array
     */
    public $replacers = [];

    /**
     * The validation rules that imply the field is required.
     *
     * @var string[]
     */
    protected $implicitRules = [
        'accepted',
        'accepted_if',
        'Declined',
        'DeclinedIf',
        'Filled',
        'Missing',
        'MissingIf',
        'MissingUnless',
        'MissingWith',
        'missing_with_all',
        'Present',
        'present_if',
        'present_unless',
        'present_with',
        'present_with_all',
        'required',
        'RequiredIf',
        'RequiredIfAccepted',
        'RequiredIfDeclined',
        'RequiredUnless',
        'RequiredWith',
        'RequiredWithAll',
        'RequiredWithout',
        'RequiredWithoutAll',
    ];

    /**
     * The validation rules which depend on other fields as parameters.
     *
     * @var string[]
     */
    protected $dependentRules = [
        'After',
        'AfterOrEqual',
        'Before',
        'BeforeOrEqual',
        'Confirmed',
        'Different',
        'ExcludeIf',
        'ExcludeUnless',
        'ExcludeWith',
        'ExcludeWithout',
        'Gt',
        'Gte',
        'Lt',
        'Lte',
        'AcceptedIf',
        'DeclinedIf',
        'RequiredIf',
        'RequiredIfAccepted',
        'RequiredIfDeclined',
        'RequiredUnless',
        'RequiredWith',
        'RequiredWithAll',
        'RequiredWithout',
        'RequiredWithoutAll',
        'PresentIf',
        'PresentUnless',
        'PresentWith',
        'PresentWithAll',
        'Prohibited',
        'ProhibitedIf',
        'ProhibitedIfAccepted',
        'ProhibitedIfDeclined',
        'ProhibitedUnless',
        'Prohibits',
        'MissingIf',
        'MissingUnless',
        'MissingWith',
        'MissingWithAll',
        'Same',
        'Unique',
    ];

    /**
     * The validation rules that can exclude an attribute.
     *
     * @var string[]
     */
    protected $excludeRules = ['exclude', 'exclude_if', 'exclude_unless', 'exclude_with', 'exclude_without'];

    /**
     * The size related validation rules (by alias).
     *
     * @var string[]
     */
    protected $sizeRules = ['size', 'between', 'min', 'max', 'gt', 'lt', 'gte', 'lte'];

    /**
     * The numeric related validation rules.
     *
     * @var string[]
     */
    protected $numericRules = ['Numeric', 'Integer', 'Decimal'];

    /**
     * The default numeric related validation rules.
     *
     * @var string[]
     */
    protected $defaultNumericRules = ['Numeric', 'Integer', 'Decimal'];

    /**
     * The current random hash for the validator.
     *
     * @var string|null
     */
    protected static $placeholderHash;

    /**
     * The exception to throw upon failure.
     *
     * @var class-string<ValidationException>
     */
    protected $exception = ValidationException::class;

    /**
     * The custom callback to determine if an exponent is within allowed range.
     *
     * @var callable|null
     */
    protected $ensureExponentWithinAllowedRangeUsing;

    /**
     * Create a new Validator instance.
     *
     * @param  MessagesInterface  $message
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     */
    public function __construct(
        MessagesInterface $message,
        array $data,
        array $rules,
        array $messages = [],
        array $attributes = [],
    ) {
        if (! isset(static::$placeholderHash)) {
            static::$placeholderHash = Str::random();
        }

        $this->initialRules = $rules;
        $this->message = $message;
        $this->customMessages = $messages;
        $this->data = $this->parseData($data);
        $this->customAttributes = $attributes;

        $this->registerBaseRules();
        $this->setRules($rules);
    }

    /**
     * Parse the data array, converting dots and asterisks.
     *
     * @param  array  $data
     * @return array
     */
    public function parseData(array $data)
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }

            $key = str_replace(
                ['.', '*'],
                ['__dot__'.static::$placeholderHash, '__asterisk__'.static::$placeholderHash],
                $key
            );

            $newData[$key] = $value;
        }

        return $newData;
    }

    /**
     * Replace the placeholders used in data keys.
     *
     * @param  array  $data
     * @return array
     */
    protected function replacePlaceholders($data)
    {
        $originalData = [];

        foreach ($data as $key => $value) {
            $originalData[$this->replacePlaceholderInString($key)] = is_array($value)
                ? $this->replacePlaceholders($value)
                : $value;
        }

        return $originalData;
    }

    /**
     * Replace the placeholders in the given string.
     *
     * @param  string  $value
     * @return string
     */
    protected function replacePlaceholderInString(string $value)
    {
        return str_replace(
            ['__dot__'.static::$placeholderHash, '__asterisk__'.static::$placeholderHash],
            ['.', '*'],
            $value
        );
    }

    /**
     * Replace each field parameter dot placeholder with dot.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceDotPlaceholderInParameters(array $parameters)
    {
        return array_map(function ($field) {
            return str_replace('__dot__'.static::$placeholderHash, '.', $field);
        }, $parameters);
    }

    /**
     * Add an after validation callback.
     *
     * @param  callable|array|string  $callback
     * @return $this
     */
    public function after($callback)
    {
        if (is_array($callback) && ! is_callable($callback)) {
            foreach ($callback as $rule) {
                $this->after(method_exists($rule, 'after') ? $rule->after(...) : $rule);
            }

            return $this;
        }

        $this->after[] = fn () => $callback($this);

        return $this;
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes(): bool
    {
        $this->messages = new MessageBag;

        [$this->distinctValues, $this->failedRules] = [[], []];

        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);

                continue;
            }

            if ($this->stopOnFirstFailure && $this->messages->isNotEmpty()) {
                break;
            }

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);

                if ($this->shouldBeExcluded($attribute)) {
                    break;
                }

                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }

        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);
            }
        }

        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Determine if the attribute should be excluded.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function shouldBeExcluded($attribute)
    {
        foreach ($this->excludeAttributes as $excludeAttribute) {
            if ($attribute === $excludeAttribute ||
                Str::startsWith($attribute, $excludeAttribute.'.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove the given attribute.
     *
     * @param  string  $attribute
     * @return void
     */
    protected function removeAttribute($attribute)
    {
        Arr::forget($this->data, $attribute);
        Arr::forget($this->rules, $attribute);
    }

    /**
     * Run the validator's rules against its data.
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validate()
    {
        if ($this->fails()) {
            throw new $this->exception($this);
        }

        return $this->validated();
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param  string  $errorBag
     * @return array
     *
     * @throws ValidationException
     */
    public function validateWithBag(string $errorBag)
    {
        try {
            return $this->validate();
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validated input container for the validated input.
     *
     * @param  array|null  $keys
     * @return ValidatedInput|array
     */
    public function safe(?array $keys = null)
    {
        return is_array($keys)
            ? (new ValidatedInput($this->validated()))->only($keys)
            : new ValidatedInput($this->validated());
    }

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validated()
    {
        if (! $this->messages) {
            $this->passes();
        }

        if ($this->messages->isNotEmpty()) {
            throw new $this->exception($this);
        }

        $results = [];

        $missingValue = new stdClass;

        foreach ($this->getRules() as $key => $rules) {
            $value = data_get($this->getData(), $key, $missingValue);

            if ($this->excludeUnvalidatedArrayKeys &&
                (in_array('array', $rules) || in_array('list', $rules)) &&
                $value !== null &&
                ! empty(preg_grep('/^'.preg_quote($key, '/').'\.+/', array_keys($this->getRules())))) {
                continue;
            }

            if ($value !== $missingValue) {
                Arr::set($results, $key, $value);
            }
        }

        return $this->replacePlaceholders($results);
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function validateAttribute($attribute, $rule)
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = ValidationRuleParser::parse($rule);

        if ($rule === '') {
            return;
        }

        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotInParameters($parameters);

            if ($keys = $this->getExplicitKeys($attribute)) {
                $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
            }
        }

        $value = $this->getValue($attribute);

        $validatable = $this->isValidatable($rule, $attribute, $value);

        // Object-based rules (including ClosureValidationRule and other RuleContract implementations)
        if (is_object($rule) && $rule instanceof Contracts\Rule) {
            if (! $validatable) {
                return;
            }

            if ($rule instanceof Contracts\ValidatorAwareRule) {
                $rule->setValidator($this);
            }

            if (! $rule->passes($attribute, $value)) {
                $messages = $rule->message();

                foreach ((array) $messages as $message) {
                    $this->messages->add($attribute, (string) $message);
                }

                $this->failedRules[$attribute][\get_class($rule)] = $parameters;
            }

            return;
        }

        if (!is_string($rule) || !$this->registryClass::has($rule)) {
            return;
        }

        $ruleInstance = $this->registryClass::get($rule);
        if ($ruleInstance instanceof ReplacerRule) {
            $this->addReplacer($rule, fn () => $ruleInstance::replace(...func_get_args()));
        }

        $invokableRule = InvokableValidationRule::make($ruleInstance);
        $invokableRule->setValidator($this);
        $invokableRule->setData($this->data);

        if ($validatable && !$invokableRule->passes($attribute, $value, $parameters)) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }

    /**
     * Determine if the given rule depends on other fields.
     *
     * @param  string  $rule
     * @return bool
     */
    protected function dependsOnOtherFields($rule)
    {
        return in_array($rule, $this->dependentRules);
    }

    /**
     * Get the explicit keys from an attribute flattened with dot notation.
     *
     * E.g. 'foo.1.bar.spark.baz' -> [1, 'spark'] for 'foo.*.bar.*.baz'
     *
     * @param  string  $attribute
     * @return array
     */
    protected function getExplicitKeys($attribute)
    {
        $pattern = str_replace('\*', '([^\.]+)', preg_quote($this->getPrimaryAttribute($attribute), '/'));

        if (preg_match('/^'.$pattern.'/', $attribute, $keys)) {
            array_shift($keys);

            return $keys;
        }

        return [];
    }

    /**
     * Get the primary attribute name.
     *
     * For example, if "name.0" is given, "name.*" will be returned.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getPrimaryAttribute($attribute)
    {
        foreach ($this->implicitAttributes as $unparsed => $parsed) {
            if (in_array($attribute, $parsed, true)) {
                return $unparsed;
            }
        }

        return $attribute;
    }

    /**
     * Replace each field parameter which has an escaped dot with the dot placeholder.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceDotInParameters(array $parameters)
    {
        return array_map(function ($field) {
            return static::encodeAttributeWithPlaceholder((string) ($field ?? ''));
        }, $parameters);
    }

    /**
     * Replace each field parameter which has asterisks with the given keys.
     *
     * @param  array  $parameters
     * @param  array  $keys
     * @return array
     */
    protected function replaceAsterisksInParameters(array $parameters, array $keys)
    {
        return array_map(function ($field) use ($keys) {
            return vsprintf(str_replace('*', '%s', $field), $keys);
        }, $parameters);
    }

    /**
     * Determine if the attribute is validatable.
     *
     * @param  object|string  $rule
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value)
    {
        if (in_array($rule, $this->excludeRules)) {
            return true;
        }

        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
               $this->passesOptionalCheck($attribute) &&
               $this->isNotNullIfMarkedAsNullable($rule, $attribute) &&
               (!is_string($rule) || $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute));
    }

    /**
     * Determine if the field is present, or the rule implies required.
     *
     * @param  object|string  $rule
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value)
    {
        if (is_string($value) && trim($value) === '') {
            return $this->isImplicit($rule);
        }

        return $this->validatePresent($attribute, $value) ||
               $this->isImplicit($rule);
    }

    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param  object|string  $rule
     * @return bool
     */
    protected function isImplicit($rule)
    {
        return $rule instanceof Contracts\ImplicitRule ||
               in_array($rule, $this->implicitRules);
    }

    /**
     * Determine if the attribute passes any optional check.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function passesOptionalCheck($attribute)
    {
        if (! $this->hasRule($attribute, ['Sometimes'])) {
            return true;
        }

        $data = ValidationData::initializeAndGatherData($attribute, $this->data);

        return array_key_exists($attribute, $data)
            || array_key_exists($attribute, $this->data);
    }

    /**
     * Determine if the attribute fails the nullable check.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @return bool
     */
    protected function isNotNullIfMarkedAsNullable($rule, $attribute)
    {
        if ($this->isImplicit($rule) || ! $this->hasRule($attribute, ['nullable'])) {
            return true;
        }

        return ! is_null(Arr::get($this->data, $attribute, 0));
    }

    /**
     * Determine if it's a necessary presence validation.
     *
     * This is to avoid possible database type comparison errors.
     *
     * @param string $rule
     * @param string $attribute
     * @return bool
     */
    protected function hasNotFailedPreviousRuleIfPresenceRule(string $rule, string $attribute): bool
    {
        return !in_array($rule, ['unique', 'exists']) || !$this->messages->has($attribute);
    }

    /**
     * Check if we should stop further validations on a given attribute.
     *
     * @param string $attribute
     * @return bool
     */
    protected function shouldStopValidating(string $attribute): bool
    {
        $cleanedAttribute = $this->replacePlaceholderInString($attribute);

        if ($this->hasRule($attribute, ['bail'])) {
            return $this->messages->has($cleanedAttribute);
        }

        if (isset($this->failedRules[$cleanedAttribute]) &&
            array_key_exists('uploaded', $this->failedRules[$cleanedAttribute])) {
            return true;
        }

        return
            $this->hasRule($attribute, $this->implicitRules)
            && isset($this->failedRules[$cleanedAttribute])
            && array_intersect(array_keys($this->failedRules[$cleanedAttribute]), $this->implicitRules);
    }

    /**
     * Add a failed rule and error message to the collection.
     *
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     * @return void
     */
    public function addFailure(string $attribute, string $rule, array $parameters = [])
    {
        if (!$this->messages) {
            $this->passes();
        }

        $attributeWithPlaceholders = $attribute;

        $attribute = $this->replacePlaceholderInString($attribute);

        if (in_array($rule, $this->excludeRules)) {
            $this->excludeAttribute($attribute);
            return;
        }

        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotPlaceholderInParameters($parameters);
        }

        $this->messages->add($attribute, $this->makeReplacements(
            $this->getMessage($attributeWithPlaceholders, $rule),
            $attribute,
            $rule,
            $parameters
        ));

        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Add the given attribute to the list of excluded attributes.
     *
     * @param  string  $attribute
     * @return void
     */
    protected function excludeAttribute(string $attribute): void
    {
        $this->excludeAttributes[] = $attribute;
        $this->excludeAttributes = array_unique($this->excludeAttributes);
    }

    /**
     * Returns the data which was valid.
     *
     * @return array
     */
    public function valid(): array
    {
        if (!$this->messages) {
            $this->passes();
        }

        return array_diff_key(
            $this->data, $this->attributesThatHaveMessages()
        );
    }

    /**
     * Returns the data which was invalid.
     *
     * @return array
     */
    public function invalid(): array
    {
        if (!$this->messages) {
            $this->passes();
        }

        $invalid = array_intersect_key(
            $this->data, $this->attributesThatHaveMessages()
        );

        $result = [];

        $failed = Arr::only(Arr::dot($invalid), array_keys($this->failed()));

        foreach ($failed as $key => $failure) {
            Arr::set($result, $key, $failure);
        }

        return $result;
    }

    /**
     * Generate an array of all attributes that have messages.
     *
     * @return array
     */
    protected function attributesThatHaveMessages()
    {
        return (new Collection($this->messages()->toArray()))
            ->map(fn ($message, $key) => explode('.', $key)[0])
            ->unique()
            ->flip()
            ->all();
    }

    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed(): array
    {
        return $this->failedRules;
    }

    /**
     * Get the message container for the validator.
     *
     * @return MessageBag
     */
    public function messages(): MessageBag
    {
        if (!$this->messages) {
            $this->passes();
        }

        return $this->messages;
    }

    /**
     * An alternative more semantic shortcut to the message container.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->messages();
    }

    /**
     * Get the messages for the instance.
     *
     * @return MessageBag
     */
    public function getMessageBag(): MessageBag
    {
        return $this->messages();
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param string $attribute
     * @param array|string $rules
     * @return bool
     */
    public function hasRule(string $attribute, array|string $rules): bool
    {
        return !is_null($this->getRule($attribute, $rules));
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * @param string $attribute
     * @param array|string $rules
     * @return array|null
     */
    protected function getRule(string $attribute, array|string $rules): ?array
    {
        if (!array_key_exists($attribute, $this->rules)) {
            return null;
        }

        $rules = (array) $rules;

        foreach ($this->rules[$attribute] as $rule) {
            [$rule, $parameters] = ValidationRuleParser::parse($rule);

            if (in_array($rule, $rules)) {
                return [$rule, $parameters];
            }
        }

        return null;
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->getData();
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $this->parseData($data);
        $this->setRules($this->initialRules);

        return $this;
    }

    /**
     * Get the value of a given attribute.
     *
     * @param string $attribute
     * @return mixed
     */
    public function getValue(string $attribute): mixed
    {
        return Arr::get($this->data, $attribute);
    }

    /**
     * Set the value of a given attribute.
     *
     * @param string $attribute
     * @param  mixed  $value
     * @return static
     */
    public function setValue(string $attribute, mixed $value): static
    {
        Arr::set($this->data, $attribute, $value);

        return $this;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get the validation rules with key placeholders removed.
     *
     * @return array
     */
    public function getRulesWithoutPlaceholders(): array
    {
        return (new Collection($this->rules))
            ->mapWithKeys(fn ($value, $key) => [
                static::decodeAttributeWithPlaceholder($key) => $value,
            ])
            ->all();
    }

    /**
     * Set the validation rules.
     *
     * @param  array  $rules
     * @return $this
     */
    public function setRules(array $rules): static
    {
        $rules = (new Collection($rules))
            ->mapWithKeys(function ($value, $key) {
                return [static::encodeAttributeWithPlaceholder($key) => $value];
            })
            ->toArray();

        $this->initialRules = $rules;
        $this->rules = [];
        $this->addRules($rules);

        return $this;
    }

    /**
     * Append new validation rules to the validator.
     *
     * @param  array  $rules
     * @return $this
     */
    public function appendRules(array $rules): static
    {
        $rules = (new Collection($rules))
            ->map(function ($value) {
                return is_string($value) ? explode('|', $value) : $value;
            })
            ->all();

        return $this->setRules(array_merge_recursive($this->getRulesWithoutPlaceholders(), $rules));
    }

    /**
     * Parse the given rules and merge them into current rules.
     *
     * @param  array  $rules
     * @return void
     *@internal
     *
     */
    public function addRules(array $rules): void
    {
        $response = (new ValidationRuleParser($this->data))
            ->explode(ValidationRuleParser::filterConditionalRules($rules, $this->data));

        foreach ($response->rules as $key => $rule) {
            $this->rules[$key] = array_merge($this->rules[$key] ?? [], $rule);
        }

        $this->implicitAttributes = array_merge(
            $this->implicitAttributes,
            $response->implicitAttributes
        );
    }

    /**
     * Instruct the validator to stop validating after the first rule failure.
     *
     * @param bool $stopOnFirstFailure
     * @return $this
     */
    public function stopOnFirstFailure(bool $stopOnFirstFailure = true): static
    {
        $this->stopOnFirstFailure = $stopOnFirstFailure;

        return $this;
    }

    /**
     * Register an array of custom validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addExtensions(array $extensions): void
    {
        if ($extensions) {
            $keys = array_map(Str::snake(...), array_keys($extensions));
            $extensions = array_combine($keys, array_values($extensions));
        }

        $this->extensions = array_merge($this->extensions, $extensions);
    }

    /**
     * Register an array of custom implicit validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addImplicitExtensions(array $extensions)
    {
        $this->addExtensions($extensions);

        foreach ($extensions as $rule => $extension) {
            $this->implicitRules[] = Str::lower($rule);
        }
    }

    /**
     * Register an array of custom dependent validator extensions.
     *
     * @param  array  $extensions
     * @return void
     */
    public function addDependentExtensions(array $extensions)
    {
        $this->addExtensions($extensions);

        foreach ($extensions as $rule => $extension) {
            $this->dependentRules[] = Str::lower($rule);
        }
    }

    /**
     * Register a custom validator extension.
     *
     * @param string $rule
     * @param \Closure|string $extension
     * @return static
     */
    public function addExtension(string $rule, \Closure|string $extension): static
    {
        $this->extensions[Str::lower($rule)] = $extension;

        return $this;
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param string $rule
     * @param \Closure|string $extension
     * @return static
     */
    public function addImplicitExtension(string $rule, \Closure|string $extension): static
    {
        $this->addExtension($rule, $extension);
        $this->implicitRules[] = Str::lower($rule);

        return $this;
    }

    /**
     * Register a custom dependent validator extension.
     *
     * @param string $rule
     * @param \Closure|string $extension
     * @return static
     */
    public function addDependentExtension(string $rule, \Closure|string $extension): static
    {
        $this->addExtension($rule, $extension);
        $this->dependentRules[] = Str::lower($rule);

        return $this;
    }

    /**
     * Register an array of custom validator message replacers.
     *
     * @param  array  $replacers
     * @return static
     */
    public function addReplacers(array $replacers): static
    {
        if ($replacers) {
            $keys = array_map(Str::snake(...), array_keys($replacers));
            $replacers = array_combine($keys, array_values($replacers));
        }

        $this->replacers = array_merge($this->replacers, $replacers);

        return $this;
    }

    /**
     * Register a custom validator message replacer.
     *
     * @param string $rule
     * @param \Closure|string $replacer
     * @return static
     */
    public function addReplacer(string $rule, \Closure|string $replacer): static
    {
        $this->replacers[Str::snake($rule)] = $replacer;

        return $this;
    }

    /**
     * Set the custom messages for the validator.
     *
     * @param  array  $messages
     * @return $this
     */
    public function setCustomMessages(array $messages)
    {
        $this->customMessages = array_merge($this->customMessages, $messages);

        return $this;
    }

    /**
     * Set the custom attributes on the validator.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributeNames(array $attributes)
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    /**
     * Add custom attributes to the validator.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function addCustomAttributes(array $attributes)
    {
        $this->customAttributes = array_merge($this->customAttributes, $attributes);

        return $this;
    }

    /**
     * Set the callback that used to format an implicit attribute.
     *
     * @param  callable|null  $formatter
     * @return $this
     */
    public function setImplicitAttributesFormatter(?callable $formatter = null)
    {
        $this->implicitAttributesFormatter = $formatter;

        return $this;
    }

    /**
     * Set the custom values on the validator.
     *
     * @param  array  $values
     * @return $this
     */
    public function setValueNames(array $values)
    {
        $this->customValues = $values;

        return $this;
    }

    /**
     * Add the custom values for the validator.
     *
     * @param  array  $customValues
     * @return $this
     */
    public function addCustomValues(array $customValues)
    {
        $this->customValues = array_merge($this->customValues, $customValues);

        return $this;
    }

    /**
     * Set the fallback messages for the validator.
     *
     * @param  array  $messages
     * @return void
     */
    public function setFallbackMessages(array $messages)
    {
        $this->fallbackMessages = $messages;
    }

    /**
     * Get the Presence Verifier implementation.
     *
     * @param  string|null  $connection
     * @return \MB\Validation\PresenceVerifierInterface
     *
     * @throws \RuntimeException
     */
    public function getPresenceVerifier($connection = null)
    {
        if (! isset($this->presenceVerifier)) {
            throw new RuntimeException('Presence verifier has not been set.');
        }

        if ($this->presenceVerifier instanceof DatabasePresenceVerifierInterface) {
            $this->presenceVerifier->setConnection($connection);
        }

        return $this->presenceVerifier;
    }

    /**
     * Set the Presence Verifier implementation.
     *
     * @param  \MB\Validation\PresenceVerifierInterface  $presenceVerifier
     * @return void
     */
    public function setPresenceVerifier(PresenceVerifierInterface $presenceVerifier)
    {
        $this->presenceVerifier = $presenceVerifier;
    }

    /**
     * Get the exception to throw upon failed validation.
     *
     * @return class-string<ValidationException>
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Set the exception to throw upon failed validation.
     *
     * @param  class-string<ValidationException>  $exception
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setException($exception)
    {
        if (! is_a($exception, ValidationException::class, true)) {
            throw new InvalidArgumentException(
                sprintf('Exception [%s] is invalid. It must extend [%s].', $exception, ValidationException::class)
            );
        }

        $this->exception = $exception;

        return $this;
    }

    /**
     * Ensure exponents are within range using the given callback.
     *
     * @param callable(int $scale, string $attribute, mixed $value) $callback
     * @return $this
     */
    public function ensureExponentWithinAllowedRangeUsing($callback)
    {
        $this->ensureExponentWithinAllowedRangeUsing = $callback;

        return $this;
    }

    /**
     * Get the messages / translation implementation.
     *
     * @return MessagesInterface
     */
    public function getTranslator(): MessagesInterface
    {
        return $this->message;
    }

    /**
     * Set the messages / translation implementation.
     *
     * @param  MessagesInterface  $message
     * @return void
     */
    public function setTranslator(MessagesInterface $message): void
    {
        $this->message = $message;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  ContainerInterface  $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Call a custom validator extension.
     *
     * @param  string  $rule
     * @param  array  $parameters
     * @return bool|null
     */
    protected function callExtension($rule, $parameters)
    {
        $callback = $this->extensions[$rule];

        if (is_callable($callback)) {
            return $callback(...array_values($parameters));
        } elseif (is_string($callback)) {
            return $this->callClassBasedExtension($callback, $parameters);
        }

        return null;
    }

    /**
     * Call a class based validator extension.
     *
     * @param  string  $callback
     * @param  array  $parameters
     * @return bool
     */
    protected function callClassBasedExtension($callback, $parameters)
    {
        [$class, $method] = Str::parseCallback($callback, 'validate');

        if ($this->container === null) {
            throw new RuntimeException('A container is required for class-based validator extensions.');
        }

        return $this->container->get($class)->{$method}(...array_values($parameters));
    }

    protected function registerBaseRules()
    {
        $this->registryClass::register(...$this->getBaseRules());
    }

    protected function getBaseRules()
    {
        return [
            AcceptedRule::class,
            AlphaDashRule::class,
            AlphaNumRule::class,
            AlphaRule::class,
            AnyOfRule::class,
            ArrayRule::class,
            AsciiRule::class,
            BetweenRule::class,
            BooleanRule::class,
            ConfirmedRule::class,
            ContainsRule::class,
            DateFormatRule::class,
            DateRule::class,
            DecimalRule::class,
            DeclinedRule::class,
            DifferentRule::class,
            DigitsBetweenRule::class,
            DigitsRule::class,
            DoesntContainRule::class,
            DoesntEndWithRule::class,
            DoesntStartWithRule::class,
            EndsWithRule::class,
            FilledRule::class,
            HexColorRule::class,
            InRule::class,
            IntegerRule::class,
            IpRule::class,
            Ipv4Rule::class,
            Ipv6Rule::class,
            JsonRule::class,
            ListRule::class,
            LowercaseRule::class,
            MacAddressRule::class,
            MaxRule::class,
            MinRule::class,
            NotInRule::class,
            NotRegexRule::class,
            NumericRule::class,
            ProhibitedRule::class,
            RegexRule::class,
            RequiredArrayKeysRule::class,
            RequiredIfRule::class,
            RequiredRule::class,
            RequiredUnlessRule::class,
            SameRule::class,
            SizeRule::class,
            StartsWithRule::class,
            StringRule::class,
            TimezoneRule::class,
            UlidRule::class,
            UppercaseRule::class,
            UrlRule::class,
            UuidRule::class,
        ];
    }

    /**
     * Encode the attribute with the placeholder hash.
     *
     * @param  string  $attribute
     * @return string
     */
    protected static function encodeAttributeWithPlaceholder(string $attribute)
    {
        return str_replace('\.', '__dot__'.static::$placeholderHash, $attribute);
    }

    /**
     * Decode an attribute with a placeholder hash.
     *
     * @param  string  $attribute
     * @return string
     */
    protected static function decodeAttributeWithPlaceholder(string $attribute)
    {
        return str_replace('__dot__'.static::$placeholderHash, '\\.', $attribute);
    }

    /**
     * Flush the validator's global state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$placeholderHash = null;
    }

    /**
     * Handle dynamic calls to class methods.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $rule = Str::snake(substr($method, 8));

        if (isset($this->extensions[$rule])) {
            return $this->callExtension($rule, $parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
