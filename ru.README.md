# MB Validation

[English version](README.md)

Фреймворк-агностичная библиотека валидации в стиле Laravel rules, без жесткой привязки к какому-либо фреймворку.

## Возможности

- Строковые правила (`required|string|min:3`)
- Object и closure правила
- Локализация сообщений (`ru` / `en`)
- Wildcard-правила для массивов (`items.*.name`)
- Строгая обработка неизвестных строковых правил (fail-fast)
- Опциональная файловая валидация без обязательного `symfony/http-foundation`

## Установка

```bash
composer require mb4it/validation
```

`symfony/http-foundation` — опциональная зависимость.  
Файловые правила работают с file-like объектами, которые предоставляют методы:

- `getSize()`
- `getPath()` или `getRealPath()`
- `guessExtension()`
- `getMimeType()`

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

## API фабрики

### Создание фабрики

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

Если `MessagesInterface` не передан, фабрика использует `DefaultMessages::create($lang)`.

### Одноразовая валидация

```php
$validated = $factory->validate($data, $rules, $messages = [], $attributes = []);
```

`validate()` бросает `MB\Validation\ValidationException`, если валидация не прошла.

### Strict mode

Неизвестные строковые правила по умолчанию приводят к `InvalidArgumentException`.

Для обратной совместимости:

```php
$factory = Factory::create()->allowUnknownRules();
```

## Поддерживаемые группы правил

- Presence / structure: `required`, `required_if`, `required_unless`, `required_array_keys`, `filled`, `array`, `list`, `boolean`
- Type: `string`, `numeric`, `integer`, `decimal`
- Size: `min`, `max`, `between`, `size`, `digits`, `digits_between`
- String: `alpha`, `alpha_num`, `alpha_dash`, `contains`, `doesnt_contain`, `starts_with`, `ends_with`, `doesnt_start_with`, `doesnt_end_with`, `uppercase`, `lowercase`, `ascii`
- Format / network: `email`, `url`, `ip`, `ipv4`, `ipv6`, `mac_address`, `hex_color`, `uuid`, `ulid`, `json`
- Date / time: `date`, `date_format`, `timezone`
- Set / comparison: `in`, `not_in`, `same`, `different`, `any_of`
- Database: `exists`, `unique` (требуется `PresenceVerifierInterface`)

## Сообщения и локализация

Файлы по умолчанию:

- `lang/en/validation.php`
- `lang/ru/validation.php`

Порядок разрешения сообщений:

1. Inline-сообщения из `make()` / `validate()`
2. Fallback custom messages валидатора
3. Translator (`MessagesInterface`)

Пример inline сообщений:

```php
$validator = $factory->make(
    ['email' => null],
    ['email' => 'required|email'],
    ['email.required' => 'Нужно указать email']
);
```

## Валидация свойств объекта через trait

Используйте `MB\Validation\Concerns\ValidatesProperties` для DTO/value object.

Поведение trait:

- собирает инициализированные public/protected/private свойства через reflection
- поддерживает wildcard-ключи (`items.*.name`, `profile.tags.*`)
- валидирует через `Factory::create(lang: $this->validatorLang)`

### Пример

```php
use MB\Validation\Concerns\ValidatesProperties;

final class ProductDto
{
    use ValidatesProperties;

    protected string $validatorLang = 'ru'; // необязательно

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

Для non-throwing сценария можно добавить helper-метод:

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

## `exists` / `unique` и Presence Verifier

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

## Кастомные правила

### Closure rule

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

### Class-based rule

Реализуйте `MB\Validation\Contracts\ValidationRule` и зарегистрируйте alias в реестре правил.

## Тестирование

```bash
composer test
```

или

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

## Лицензия

MIT (см. `LICENSE`).
