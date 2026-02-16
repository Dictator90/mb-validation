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

### Rules

Most Laravel‑style rules are supported, e.g.:

- **Presence / structure**: `required`, `required_if`, `required_unless`, `required_array_keys`, `filled`, `array`, `list`, `boolean`
- **Type**: `string`, `numeric`, `integer`, `decimal`
- **Size**: `min`, `max`, `between`, `size`, `digits`, `digits_between`
- **Strings**: `alpha`, `alpha_num`, `alpha_dash`, `contains`, `doesnt_contain`, `starts_with`, `ends_with`, `doesnt_start_with`, `doesnt_end_with`, `uppercase`, `lowercase`, `ascii`
- **Dates / time**: `date`, `date_format`, `timezone`
- **Network / IDs**: `ip`, `ipv4`, `ipv6`, `mac_address`, `hex_color`, `url`, `uuid`, `ulid`
- **Sets / comparison**: `in`, `not_in`, `same`, `different`, `any_of`

Rules can be defined as strings (`'required|string|min:3'`), as rule objects, or as closures (inline rules).

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

