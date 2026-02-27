# Laravel Model Presenter

A simple and elegant way to separate presentation logic from your Laravel Eloquent models.

## Introduction

The Presenter pattern helps keep your models clean by extracting presentation and formatting logic into dedicated presenter classes. Instead of cluttering your models with display-related methods, you can organize them in presenters.

## Installation

```bash
composer require adrosoftware/laravel-model-presenter
```

## Requirements

- PHP 8.4+
- Laravel 11 or 12

### Version Compatibility

| PHP | Laravel 11 | Laravel 12 |
|-----|------------|------------|
| 8.4 | ✅         | ✅         |
| 8.5 | ✅         | ✅         |

## Quick Start

### 1. Create a Presenter

Create a presenter class that extends `ModelPresenter`:

```php
<?php

namespace App\Presenters;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

class UserPresenter extends ModelPresenter
{
    public function fullName(): string
    {
        return "{$this->model->first_name} {$this->model->last_name}";
    }

    public function formattedCreatedAt(): string
    {
        return $this->carbon($this->model->created_at)->format('F j, Y');
    }
}
```

### 2. Configure Your Model

Add the `PresentModel` trait and implement `ModelPresentable` interface:

```php
<?php

namespace App\Models;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\PresentModel;
use App\Presenters\UserPresenter;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = UserPresenter::class;
}
```

### 3. Use the Presenter

```php
$user = User::find(1);

// Access presenter methods
echo $user->present()->fullName();
echo $user->present()->formattedCreatedAt();

// Access model attributes through the presenter
echo $user->present()->email;
```

## AI-Assisted Development

This package includes [Laravel Boost](https://github.com/laravel/boost) guidelines for AI coding assistants. After installing, run `php artisan boost:install` to enable intelligent code suggestions.

## Documentation

For detailed documentation, see the [docs](docs/) directory:

- [Getting Started](docs/getting-started.md)
- [Creating Presenters](docs/creating-presenters.md)
- [Advanced Usage](docs/advanced-usage.md)
- [API Reference](docs/api-reference.md)

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Style

```bash
composer lint      # Check code style
composer lint:fix  # Fix code style
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
