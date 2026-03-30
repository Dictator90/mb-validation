# MB Validation

Framework-agnostic библиотека валидации в стиле Laravel rules, но без зависимости от фреймворка.

- строковые правила (`required|string|min:3`);
- object/closure-правила;
- локализация сообщений (`ru`/`en`);
- wildcard-правила для массивов (`items.*.name`);
- строгий режим неизвестных правил (fail-fast).

---

## Установка

```bash
composer require mb4it/validation
```

`symfony/http-foundation` is optional.  
File rules work with any file-like object that provides methods such as `getSize()` and path/mime helpers (`getPath`/`getRealPath`, `guessExtension`, `getMimeType`), so the package does not require HttpFoundation classes at runtime.

---

## Быстрый старт

```php
use MB\Validation\Factory;

$factory = Factory::create(lang: 'ru'); // или 'en'

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

---

## API фабрики (подробно)

### Создание

```php
use MB\Validation\Factory;

$factory = Factory::create(lang: 'ru');
```

Сигнатура:

```php
Factory::create(
    ?MessagesInterface $message = null,
    ?ContainerInterface $container = null,
    string $lang = 'ru'
)
```

Если `MessagesInterface` не передан, используется `DefaultMessages::create($lang)`.

### Одноразовая проверка

```php
$validated = $factory->validate($data, $rules, $messages = [], $attributes = []);
```

`validate()` бросает `MB\Validation\ValidationException`, если данные невалидны.

### Режим строгих правил

По умолчанию пакет работает в strict-режиме:

- неизвестное строковое правило -> `InvalidArgumentException`.

Для backward-compatible поведения:

```php
$factory = Factory::create()->allowUnknownRules();
```

---

## Поддерживаемые правила (основные группы)

- Presence/structure: `required`, `required_if`, `required_unless`, `required_array_keys`, `filled`, `array`, `list`, `boolean`
- Type: `string`, `numeric`, `integer`, `decimal`
- Size: `min`, `max`, `between`, `size`, `digits`, `digits_between`
- String: `alpha`, `alpha_num`, `alpha_dash`, `contains`, `doesnt_contain`, `starts_with`, `ends_with`, `doesnt_start_with`, `doesnt_end_with`, `uppercase`, `lowercase`, `ascii`
- Format/network: `email`, `url`, `ip`, `ipv4`, `ipv6`, `mac_address`, `hex_color`, `uuid`, `ulid`, `json`
- Date/time: `date`, `date_format`, `timezone`
- Sets/comparison: `in`, `not_in`, `same`, `different`, `any_of`
- Database: `exists`, `unique` (нужен `PresenceVerifierInterface`)

---

## Сообщения и локализация

Стандартные файлы:

- `lang/ru/validation.php`
- `lang/en/validation.php`

При ошибке поиск сообщения происходит в таком порядке:

1. inline custom messages (`$messages` в `make()`/`validate()`);
2. fallback/custom messages валидатора;
3. перевод из `MessagesInterface`.

Пример inline сообщений:

```php
$validator = $factory->make(
    ['email' => null],
    ['email' => 'required|email'],
    ['email.required' => 'Нужно указать email']
);
```

---

## Trait для валидации свойств объекта

Для DTO/ValueObject/Command-классов можно использовать:

- `MB\Validation\Concerns\ValidatesProperties`.

Trait:

- собирает **инициализированные** public/protected/private свойства через reflection;
- поддерживает wildcard-правила (`items.*.name`, `profile.tags.*`);
- валидирует через `Factory::create(lang: $this->validatorLang)`.

### Полный пример

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

### Как получить ошибки через trait

`validate()` бросает `ValidationException`:

```php
use MB\Validation\ValidationException;

try {
    $validated = $dto->validate();
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['message1', ...]]
}
```

### Non-throwing flow (через `Validator`)

Так как `validationData()` и `rules()` в trait защищенные, обычно добавляют метод-обертку в вашем классе:

```php
use MB\Validation\Factory;
use MB\Validation\Validator;

final class ProductDto
{
    // ... trait + rules + свойства

    public function validator(): Validator
    {
        return Factory::create(lang: $this->validatorLang)->make(
            $this->validationData(),
            $this->rules()
        );
    }
}

$validator = $dto->validator();

if ($validator->fails()) {
    $errors = $validator->errors()->toArray();
}
```

---

## `exists` / `unique`: подключение PresenceVerifier

Для `exists` и `unique` нужен verifier:

```php
use MB\Validation\Factory;
use MB\Validation\PresenceVerifierInterface;

final class MyPresenceVerifier implements PresenceVerifierInterface
{
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        // Реализация под ваш storage (DB/ORM/API)
        return 0;
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        return 0;
    }
}

$factory = Factory::create();
$factory->setPresenceVerifier(new MyPresenceVerifier());

$validator = $factory->make(
    ['email' => 'john@example.com'],
    ['email' => 'unique:users,email']
);
```

---

## Кастомные правила

### 1) Closure rule

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

### 2) Class-based rule

Реализуйте `MB\Validation\Contracts\ValidationRule` и зарегистрируйте правило в реестре (alias).

---

## Рекомендации для production

- Используйте strict режим правил (default).
- Передавайте локаль явно (`Factory::create(lang: 'ru'|'en')`) в точках входа.
- Для object-validation через trait держите `rules()` близко к классу DTO.
- Для `exists`/`unique` добавляйте verifier на уровне composition root.
- В CI гоняйте:
  - `composer test`
  - `composer audit`

---

## Тестирование

Запуск тестов:

```bash
composer test
```

или:

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

---

## Лицензия

MIT. См. файл `LICENSE`.
## MB Validation

Framework‑agnostic, rule‑based validation library inspired by Laravel’s validator, but decoupled from the framework and from `illuminate/translation`.  
It uses [`mb4it/messages`](https://packagist.org/packages/mb4it/messages) for message lookup and supports multiple locales out of the box.

### Installation

```bash
composer require mb4it/validation
```

### Basic usage

```php
use MB\Validation\Factory;

$factory = Factory::create(lang: 'en'); // or 'ru'

$validator = $factory->make(
    ['name' => 'John', 'age' => 30],
    ['name' => 'required|string|min:3', 'age' => 'required|integer|between:18,99']
);

if ($validator->fails()) {
    // Array of errors grouped by attribute
    $errors = $validator->errors()->toArray();
} else {
    // Validated data
    $data = $validator->validated();
}
```

### Factory and messages

- **`Factory::create(?MessagesInterface $message = null, ?ContainerInterface $container = null, string $lang = 'ru')`**
  - If you do not pass a `MessagesInterface`, the factory internally uses `DefaultMessages::create($lang)` which is a `FileMessages` instance pointing to this package’s `lang` directory.
  - By default `lang = 'ru'`; pass `'en'` to use English messages.
- You can also construct the factory manually:

```php
use MB\Messages\FileMessages;
use MB\Validation\Factory;

$messages = new FileMessages(__DIR__.'/vendor/mb4it/validation/lang', 'en');
$factory  = new Factory($messages);
```

`new Factory()` without explicit messages also uses Russian locale (`ru`) by default.

### Rules

Most Laravel‑style rules are supported, e.g.:

- **Presence / structure**: `required`, `required_if`, `required_unless`, `required_array_keys`, `filled`, `array`, `list`, `boolean`
- **Type**: `string`, `numeric`, `integer`, `decimal`
- **Size**: `min`, `max`, `between`, `size`, `digits`, `digits_between`
- **Strings**: `alpha`, `alpha_num`, `alpha_dash`, `contains`, `doesnt_contain`, `starts_with`, `ends_with`, `doesnt_start_with`, `doesnt_end_with`, `uppercase`, `lowercase`, `ascii`
- **Dates / time**: `date`, `date_format`, `timezone`
- **Network / IDs**: `email`, `ip`, `ipv4`, `ipv6`, `mac_address`, `hex_color`, `url`, `uuid`, `ulid`
- **Database**: `exists`, `unique` (requires presence verifier implementation)
- **Sets / comparison**: `in`, `not_in`, `same`, `different`, `any_of`

Rules can be defined as strings (`'required|string|min:3'`), as rule objects, or as closures (inline rules).

Unknown string rules are strict by default and throw `InvalidArgumentException`.  
If you need backward-compatible behavior, call `Factory::create()->allowUnknownRules()`.

### Default messages and locales

All default validation messages are stored in:

- `lang/en/validation.php` — English
- `lang/ru/validation.php` — Russian

Keys follow the familiar convention:

- Simple rules: `validation.required`, `validation.string`, `validation.boolean`, etc.
- Size rules by type: `validation.min.string`, `validation.min.numeric`, `validation.min.array`, `validation.min.file`, etc.

When validation fails, the validator:

1. Looks for **inline custom messages** you passed to `Factory::make(...)`.
2. Looks for **custom arrays** (`fallbackMessages`, custom messages in the factory/validator).
3. Falls back to the translator (your `MessagesInterface` implementation — by default `FileMessages` + this package’s lang files).

If no translation exists for a key, the key itself is returned (e.g. `validation.unknown_rule`), so you can easily spot missing entries.

### Overriding messages

You can override or replace messages by:

- Passing your own `MessagesInterface` to `Factory`:

  ```php
  $factory = new Factory($customMessages);
  ```

- Adding additional translation files / locales in your own project and pointing `FileMessages` to your path.
- Providing per‑call custom messages when creating a validator:

  ```php
  $validator = $factory->make(
      ['email' => null],
      ['email' => 'required|email'],
      ['email.required' => 'We need your email address.']
  );
  ```

### Custom rules

You can register your own rules via the rule registry and use them in string notation, or pass them as rule objects / closures:

- **Closure rule**:

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

The closure receives `$attribute`, `$value`, a `$fail` callback, and the current `Validator`.  
Calling `$fail($attribute, '...')` adds a failure with the given message; omitting the message delegates to the normal translation lookup (`validation.<rule>`).

For more advanced scenarios you can implement `MB\Validation\Contracts\ValidationRule` and register the rule in the rule registry so it can be used by alias in rule strings.

### Trait for object properties

You can validate any object with a reusable trait:

```php
use MB\Validation\Concerns\ValidatesProperties;

final class ProductDto
{
    use ValidatesProperties;

    protected string $validatorLang = 'ru'; // optional, default is 'ru'

    public function __construct(
        public string $title,
        private array $items,
    ) {}

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
        ];
    }
}

$dto = new ProductDto('My title', [['name' => 'item-1']]);
$validated = $dto->validate();
```

The trait collects initialized public/protected/private properties via reflection, supports wildcard paths like `items.*.name`, and validates through the package `Factory`.

#### Getting errors with trait

`validate()` throws `ValidationException` when validation fails:

```php
use MB\Validation\ValidationException;

try {
    $validated = $dto->validate();
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['message 1', ...]]
}
```

If you need non-throwing flow, create a validator manually using the same trait data/rules and check `fails()` / `errors()`:

```php
$validator = Factory::create(lang: $this->validatorLang)->make(
    $this->validationData(),
    $this->rules()
);

if ($validator->fails()) {
    $errors = $validator->errors()->toArray();
}
```

### Testing

The library ships with an extensive PHPUnit test suite under `tests/`, including:

- Rule‑focused tests under `tests/Rules/*` (numeric rules, string rules, required/conditional rules, format/date rules, etc.).
- Message tests that assert that default messages from `lang/en|ru/validation.php` are correctly used after placeholder substitution.

You can run the tests with:

```bash
composer test
```
or
```bash
vendor/bin/phpunit
```

