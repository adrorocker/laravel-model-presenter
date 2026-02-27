# Getting Started

This guide will walk you through installing and setting up Laravel Model Presenter in your application.

## Installation

Install the package via Composer:

```bash
composer require adrosoftware/laravel-model-presenter
```

No service provider registration is required - the package works out of the box.

## Basic Setup

### Step 1: Create Your First Presenter

Create a new presenter class in your application. We recommend placing presenters in `app/Presenters`:

```php
<?php

namespace App\Presenters;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

class UserPresenter extends ModelPresenter
{
    public function fullName(): string
    {
        return ucwords("{$this->model->first_name} {$this->model->last_name}");
    }
}
```

### Step 2: Configure Your Model

Update your Eloquent model to use the presenter:

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

### Step 3: Use the Presenter

Now you can access your presenter methods:

```php
$user = User::find(1);

// Call presenter methods
echo $user->present()->fullName(); // "John Doe"
```

## Directory Structure

We recommend organizing your presenters like this:

```
app/
├── Models/
│   ├── User.php
│   ├── Post.php
│   └── Comment.php
└── Presenters/
    ├── UserPresenter.php
    ├── PostPresenter.php
    └── CommentPresenter.php
```

## Next Steps

- Learn more about [Creating Presenters](creating-presenters.md)
- Explore [Advanced Usage](advanced-usage.md)
- Check the [API Reference](api-reference.md)
