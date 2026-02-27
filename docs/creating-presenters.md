# Creating Presenters

This guide covers everything you need to know about creating and using presenters.

## Basic Presenter

A presenter extends the `ModelPresenter` abstract class:

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
}
```

## Accessing Model Data

### Direct Property Access

You can access model attributes directly on the presenter:

```php
class UserPresenter extends ModelPresenter
{
    public function greeting(): string
    {
        // Access model attributes directly
        return "Hello, {$this->first_name}!";
    }
}
```

### Using $this->model

For clarity or when dealing with complex logic, use `$this->model`:

```php
class UserPresenter extends ModelPresenter
{
    public function isAdmin(): bool
    {
        return $this->model->role === 'admin';
    }

    public function postsCount(): int
    {
        return $this->model->posts()->count();
    }
}
```

### Calling Model Methods

Model methods can be called through the presenter:

```php
$user->present()->posts; // Accesses $user->posts relationship
```

## Formatting Data

### Date Formatting

Use the built-in `carbon()` helper for date formatting:

```php
class UserPresenter extends ModelPresenter
{
    public function memberSince(): string
    {
        return $this->carbon($this->model->created_at)->format('F j, Y');
    }

    public function lastActiveAgo(): string
    {
        return $this->carbon($this->model->last_active_at)->diffForHumans();
    }

    public function birthdayFormatted(): string
    {
        return $this->carbon($this->model->birthday)->format('M d');
    }
}
```

### Number Formatting

```php
class ProductPresenter extends ModelPresenter
{
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
}
```

### String Formatting

```php
class ArticlePresenter extends ModelPresenter
{
    public function excerpt(int $length = 150): string
    {
        return Str::limit($this->model->content, $length);
    }

    public function readTime(): string
    {
        $words = str_word_count($this->model->content);
        $minutes = ceil($words / 200);

        return "{$minutes} min read";
    }

    public function slug(): string
    {
        return Str::slug($this->model->title);
    }
}
```

## Conditional Display

```php
class UserPresenter extends ModelPresenter
{
    public function displayName(): string
    {
        if ($this->model->nickname) {
            return $this->model->nickname;
        }

        return "{$this->model->first_name} {$this->model->last_name}";
    }

    public function avatarUrl(): string
    {
        return $this->model->avatar_path
            ?? "https://ui-avatars.com/api/?name={$this->fullName()}";
    }

    public function statusBadge(): string
    {
        return match($this->model->status) {
            'active' => '<span class="badge badge-success">Active</span>',
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'suspended' => '<span class="badge badge-danger">Suspended</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }
}
```

## Working with Relationships

```php
class PostPresenter extends ModelPresenter
{
    public function authorName(): string
    {
        return $this->model->author->present()->fullName();
    }

    public function commentsSummary(): string
    {
        $count = $this->model->comments_count ?? $this->model->comments()->count();

        return $count === 1 ? '1 comment' : "{$count} comments";
    }

    public function tagsList(): string
    {
        return $this->model->tags->pluck('name')->implode(', ');
    }
}
```

## Type Hinting the Model

For better IDE support, add a `@property` annotation:

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
        // IDE now provides autocomplete for $this->model
        return "{$this->model->first_name} {$this->model->last_name}";
    }
}
```

## Best Practices

1. **Keep presenters focused on display logic** - Don't add business logic to presenters
2. **Use descriptive method names** - `formattedPrice()` is better than `price()`
3. **Return strings for display** - Presenters should generally return display-ready strings
4. **Leverage the carbon helper** - Use `$this->carbon()` for consistent date handling
5. **Type hint your model** - Use `@property` for better IDE support
