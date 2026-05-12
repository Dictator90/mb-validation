# MB Validation

[Русская версия](ru.README.md)

Framework-agnostic validation library inspired by Laravel-style rules, without coupling to a framework.

## Features

- String rules (`required|string|min:3`)
- Object and closure rules
- Localized messages (`ru` / `en`)
- Wildcard array rules (`items.*.name`)
- Strict handling of unknown string rules (fail-fast)
- Optional file-like validation without requiring `symfony/http-foundation`

## Installation

```bash
composer require mb4it/validation
```

`symfony/http-foundation` is optional.  
File-related rules work with file-like objects that expose methods such as:

- `getSize()`
- `getPath()` or `getRealPath()`
- `guessExtension()`
- `getMimeType()`

## Quick Start

```php
use MB\Validation\Factory;

$factory = Factory::create(lang: 'en'); // or 'ru'

$validator = $factory->make(
    ['name' => 'John', 'age' => 30],
    ['name' => 'required|string|min:3', 'age' => 'required|integer|between:18,99']
);

if ($validator->fails()) {
    $errors = $validator->errors()->toArray();
} else {
    $validated = $validator->validated();
}
```

## Factory API

### Create Factory

```php
use MB\Validation\Factory;

$factory = Factory::create(lang: 'ru');
```

Signature:

```php
Factory::create(
    ?MessagesInterface $message = null,
    ?ContainerInterface $container = null,
    string $lang = 'ru'
)
```

If `MessagesInterface` is not provided, factory uses `DefaultMessages::create($lang)`.

### One-shot Validation

```php
$validated = $factory->validate($data, $rules, $messages = [], $attributes = []);
```

`validate()` throws `MB\Validation\ValidationException` if validation fails.

### Strict Mode

Unknown string rules throw `InvalidArgumentException` by default.

Use backward-compatible mode if needed:

```php
$factory = Factory::create()->allowUnknownRules();
```

## Supported Rule Groups

- Presence / structure: `required`, `required_if`, `required_unless`, `required_array_keys`, `filled`, `array`, `list`, `boolean`
- Type: `string`, `numeric`, `integer`, `decimal`
- Size: `min`, `max`, `between`, `size`, `digits`, `digits_between`
- String: `alpha`, `alpha_num`, `alpha_dash`, `contains`, `doesnt_contain`, `starts_with`, `ends_with`, `doesnt_start_with`, `doesnt_end_with`, `uppercase`, `lowercase`, `ascii`
- Format / network: `email`, `url`, `ip`, `ipv4`, `ipv6`, `mac_address`, `hex_color`, `uuid`, `ulid`, `json`
- Date / time: `date`, `date_format`, `timezone`
- Set / comparison: `in`, `not_in`, `same`, `different`, `prohibited`, `any_of`
- Database: `exists`, `unique` (requires `PresenceVerifierInterface`)

## Messages and Localization

Default files:

- `lang/en/validation.php`
- `lang/ru/validation.php`

Message resolution order:

1. Inline messages passed to `make()` / `validate()`
2. Validator fallback custom messages
3. Translator (`MessagesInterface`)

Inline message example:

```php
$validator = $factory->make(
    ['email' => null],
    ['email' => 'required|email'],
    ['email.required' => 'We need your email address.']
);
```

## Validate Object Properties via Trait

Use `MB\Validation\Concerns\ValidatesProperties` for DTO/value object validation.

Trait behavior:

- collects initialized public/protected/private properties via reflection
- supports wildcard keys (`items.*.name`, `profile.tags.*`)
- validates with `Factory::create(lang: $this->validatorLang)`

### Example

```php
use MB\Validation\Concerns\ValidatesProperties;

final class ProductDto
{
    use ValidatesProperties;

    protected string $validatorLang = 'ru'; // optional

    public function __construct(
        public string $title,
        protected array $items,
        private array $profile,
    ) {}

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'profile.tags.*' => 'required|string',
        ];
    }
}

$dto = new ProductDto(
    title: 'Notebook',
    items: [['name' => 'A4'], ['name' => 'A5']],
    profile: ['tags' => ['office', 'paper']]
);

$validated = $dto->validate();
```

### Get Errors with Trait

`validate()` throws `ValidationException`:

```php
use MB\Validation\ValidationException;

try {
    $validated = $dto->validate();
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['message1', ...]]
}
```

For non-throwing flow, expose a validator helper in your class:

```php
use MB\Validation\Factory;
use MB\Validation\Validator;

public function validator(): Validator
{
    return Factory::create(lang: $this->validatorLang)->make(
        $this->validationData(),
        $this->rules()
    );
}
```

## `exists` / `unique` and Presence Verifier

```php
use MB\Validation\Factory;
use MB\Validation\PresenceVerifierInterface;

final class MyPresenceVerifier implements PresenceVerifierInterface
{
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        return 0;
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        return 0;
    }
}

$factory = Factory::create();
$factory->setPresenceVerifier(new MyPresenceVerifier());
```

## Custom Rules

### Closure Rule

```php
use MB\Validation\Validator;

$validator = $factory->make(
    ['field' => 'bad'],
    [
        'field' => [
            function (string $attribute, mixed $value, callable $fail, Validator $validator): void {
                if ($value === 'bad') {
                    $fail($attribute, 'Custom closure rule failed');
                }
            },
        ],
    ]
);
```

### Class-based Rule

Implement `MB\Validation\Contracts\ValidationRule` and register your alias in rule registry.

## Testing

```bash
composer test
```

or

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

## License

MIT (see `LICENSE`).
