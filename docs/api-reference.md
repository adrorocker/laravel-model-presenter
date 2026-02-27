# API Reference

Complete reference for all classes and interfaces in Laravel Model Presenter.

## Interfaces

### ModelPresentable

Interface for models that can be presented.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

interface ModelPresentable
{
    public function present(): ModelPresenterInterface;
}
```

**Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `present()` | `ModelPresenterInterface` | Returns the presenter instance for this model |

---

### ModelPresenterInterface

Interface for model presenters.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

interface ModelPresenterInterface extends PresenterInterface
{
    public function getModel(): Model;
}
```

**Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `getModel()` | `Model` | Returns the underlying Eloquent model |

---

### PresenterInterface

Base interface for all presenters.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter;

interface PresenterInterface extends ArrayAccess, Arrayable, JsonSerializable, Jsonable
{
}
```

**Extends:**
- `ArrayAccess` - Access attributes as array elements
- `Arrayable` - Convert to array
- `JsonSerializable` - JSON serialization support
- `Jsonable` - Laravel's JSON conversion interface

---

## Classes

### ModelPresenter

Abstract base class for model presenters.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

abstract class ModelPresenter implements ModelPresenterInterface
{
    public function __construct(protected Model $model);
}
```

**Constructor:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$model` | `Model` | The Eloquent model to present |

**Public Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `getModel()` | `Model` | Get the underlying Eloquent model |
| `toArray()` | `array<string, mixed>` | Convert the model to an array |
| `toJson($options = 0)` | `string` | Convert to JSON string |
| `jsonSerialize()` | `array<string, mixed>` | Get JSON serializable data |
| `offsetExists($offset)` | `bool` | Check if offset exists (ArrayAccess) |
| `offsetGet($offset)` | `mixed` | Get value at offset (ArrayAccess) |
| `offsetSet($offset, $value)` | `void` | Set value at offset (ArrayAccess) |
| `offsetUnset($offset)` | `void` | Unset value at offset (ArrayAccess) |

**Protected Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `carbon($date)` | `Carbon` | Create a Carbon instance from a date |

**Magic Methods:**

| Method | Description |
|--------|-------------|
| `__get($name)` | Access model attributes as properties |
| `__set($name, $value)` | Set model attributes as properties |
| `__isset($name)` | Check if model attribute is set |
| `__call($name, $arguments)` | Proxy method calls to the model |

---

## Traits

### PresentModel

Trait that adds presentation capability to Eloquent models.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

trait PresentModel
{
    public function present(): ModelPresenterInterface;
}
```

**Usage:**

```php
class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = UserPresenter::class;
}
```

**Properties Required:**

| Property | Type | Description |
|----------|------|-------------|
| `$presenter` | `string` | Fully qualified class name of the presenter |

**Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `present()` | `ModelPresenterInterface` | Get or create the presenter instance |

**Exceptions:**

| Exception | When |
|-----------|------|
| `PresenterClassNotDefinedException` | `$presenter` property is not defined |
| `InvalidArgumentException` | Presenter class does not exist |
| `InvalidArgumentException` | Presenter does not implement `ModelPresenterInterface` |

---

### JsonableTrait

Trait that provides JSON serialization.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter;

trait JsonableTrait
{
    public function jsonSerialize(): array;
    public function toJson($options = 0): string;
}
```

---

## Exceptions

### PresenterClassNotDefinedException

Thrown when a model using `PresentModel` trait doesn't define the `$presenter` property.

```php
namespace AdroSoftware\LaravelModelPresenter\Presenter;

class PresenterClassNotDefinedException extends Exception
{
}
```

---

## Utility Classes

### Json

Static utility class for JSON encoding/decoding with error handling.

```php
namespace AdroSoftware\LaravelModelPresenter\Support\Json;

class Json
{
    public static function encode(mixed $value, int $options = 0, int $depth = 512): string;
    public static function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0): mixed;
}
```

**Methods:**

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `encode()` | `mixed $value`, `int $options = 0`, `int $depth = 512` | `string` | Encode to JSON, throws on error |
| `decode()` | `string $json`, `bool $assoc = false`, `int $depth = 512`, `int $options = 0` | `mixed` | Decode JSON, throws on error |

**Exceptions:**

Both methods throw `InvalidArgumentException` on encoding/decoding errors.
