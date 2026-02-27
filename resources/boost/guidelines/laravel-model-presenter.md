# Laravel Model Presenter

## Package Overview

`adrosoftware/laravel-model-presenter` implements the Presenter pattern for Laravel Eloquent models. It separates presentation/display logic from models, keeping them clean while providing formatted output for views and APIs.

**Requirements:** PHP 8.4+, Laravel 11-12

## Core Components

| Component | Namespace | Purpose |
|-----------|-----------|---------|
| `ModelPresenter` | `AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter` | Abstract base class for presenters |
| `PresentModel` | `AdroSoftware\LaravelModelPresenter\Presenter\Model\PresentModel` | Trait to add to models |
| `ModelPresentable` | `AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable` | Interface for presentable models |

## Quick Setup

### 1. Create a Presenter

```php
<?php

namespace App\Presenters;

use App\Models\User;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

/**
 * @property User $model
 */
class UserPresenter extends ModelPresenter
{
    public function fullName(): string
    {
        return "{$this->model->first_name} {$this->model->last_name}";
    }

    public function memberSince(): string
    {
        return $this->carbon($this->model->created_at)->format('F j, Y');
    }
}
```

### 2. Configure the Model

```php
<?php

namespace App\Models;

use App\Presenters\UserPresenter;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\PresentModel;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = UserPresenter::class;
}
```

### 3. Use in Views/Controllers

```php
$user = User::find(1);
$user->present()->fullName();      // "John Doe"
$user->present()->memberSince();   // "January 15, 2024"
$user->present()->email;           // Transparent model access
```

## Key Features

### Transparent Model Access

Presenters proxy attribute access to the underlying model:

```php
$presenter = $user->present();
$presenter->email;           // Returns $user->email
$presenter->posts;           // Returns $user->posts relationship
$presenter->getModel();      // Returns the underlying model
```

### Built-in `carbon()` Helper

Format dates consistently using the protected `carbon()` method:

```php
public function publishedAt(): string
{
    return $this->carbon($this->model->published_at)->diffForHumans();
}
```

### Presenter Caching

The presenter instance is cached on the model. Multiple calls to `present()` return the same instance for performance.

### JSON/Array Support

Presenters implement `JsonSerializable`, `Jsonable`, and `ArrayAccess`:

```php
$presenter->toArray();           // Convert to array
$presenter->toJson();            // Convert to JSON
$presenter['email'];             // Array access
json_encode($presenter);         // Works with json_encode
```

## Common Patterns

### Conditional Display

```php
public function displayName(): string
{
    return $this->model->nickname ?? "{$this->model->first_name} {$this->model->last_name}";
}

public function statusBadge(): string
{
    return match($this->model->status) {
        'active' => '<span class="badge-success">Active</span>',
        'pending' => '<span class="badge-warning">Pending</span>',
        default => '<span class="badge-secondary">Unknown</span>',
    };
}
```

### Number Formatting

```php
public function formattedPrice(): string
{
    return '$' . number_format($this->model->price / 100, 2);
}

public function stockStatus(): string
{
    return $this->model->stock > 0
        ? "{$this->model->stock} in stock"
        : 'Out of stock';
}
```

### Status Labels

```php
public function statusLabel(): string
{
    return match($this->model->status) {
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
        default => 'Unknown',
    };
}
```

## Directory Convention

Place presenters in `app/Presenters/`:

```
app/
└── Presenters/
    ├── UserPresenter.php
    ├── PostPresenter.php
    └── ProductPresenter.php
```

## Error Handling

| Error | Cause | Exception |
|-------|-------|-----------|
| Missing `$presenter` property | Model doesn't define presenter class | `PresenterClassNotDefinedException` |
| Invalid presenter class | Class doesn't exist | `InvalidArgumentException` |
| Invalid presenter type | Class doesn't implement `ModelPresenterInterface` | `InvalidArgumentException` |
