# Advanced Usage

This guide covers advanced patterns and techniques for using Laravel Model Presenter.

## Presenter Caching

The presenter instance is cached on the model. Multiple calls to `present()` return the same instance:

```php
$user = User::find(1);

$presenter1 = $user->present();
$presenter2 = $user->present();

$presenter1 === $presenter2; // true
```

This improves performance when calling `present()` multiple times in views.

## Array Access

Presenters implement `ArrayAccess`, allowing you to access attributes like an array:

```php
$presenter = $user->present();

echo $presenter['email'];        // Array access
echo $presenter->email;          // Property access
echo $presenter->getModel()->email; // Direct model access
```

## JSON Serialization

Presenters implement `JsonSerializable` and `Jsonable`:

```php
$presenter = $user->present();

// Convert to array
$array = $presenter->toArray();

// Convert to JSON
$json = $presenter->toJson();
$json = $presenter->toJson(JSON_PRETTY_PRINT);

// Works with json_encode
$json = json_encode($presenter);
```

## Using in Blade Templates

### Basic Usage

```blade
<h1>{{ $user->present()->fullName() }}</h1>
<p>Member since: {{ $user->present()->memberSince() }}</p>
```

### Assigning to a Variable

```blade
@php($presenter = $user->present())

<div class="profile">
    <h1>{{ $presenter->fullName() }}</h1>
    <p>{{ $presenter->bio() }}</p>
    <span>{{ $presenter->memberSince() }}</span>
</div>
```

### In Loops

```blade
@foreach($users as $user)
    <tr>
        <td>{{ $user->present()->fullName() }}</td>
        <td>{{ $user->present()->email }}</td>
        <td>{!! $user->present()->statusBadge() !!}</td>
    </tr>
@endforeach
```

## Using in API Resources

Combine presenters with Laravel API Resources:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $presenter = $this->present();

        return [
            'id' => $this->id,
            'full_name' => $presenter->fullName(),
            'avatar_url' => $presenter->avatarUrl(),
            'member_since' => $presenter->memberSince(),
            'status' => $presenter->statusLabel(),
        ];
    }
}
```

## Presenter Inheritance

Create a base presenter for shared functionality:

```php
<?php

namespace App\Presenters;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

abstract class BasePresenter extends ModelPresenter
{
    public function createdAtFormatted(): string
    {
        return $this->carbon($this->model->created_at)->format('M j, Y');
    }

    public function updatedAtFormatted(): string
    {
        return $this->carbon($this->model->updated_at)->format('M j, Y');
    }

    public function timeAgo(string $column): string
    {
        return $this->carbon($this->model->$column)->diffForHumans();
    }
}
```

Then extend it in your presenters:

```php
<?php

namespace App\Presenters;

class UserPresenter extends BasePresenter
{
    public function fullName(): string
    {
        return "{$this->model->first_name} {$this->model->last_name}";
    }
}
```

## Testing Presenters

### Unit Testing

```php
<?php

namespace Tests\Unit\Presenters;

use App\Models\User;
use App\Presenters\UserPresenter;
use Tests\TestCase;

class UserPresenterTest extends TestCase
{
    public function test_full_name_combines_first_and_last_name(): void
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $presenter = new UserPresenter($user);

        $this->assertEquals('John Doe', $presenter->fullName());
    }

    public function test_full_name_via_present_method(): void
    {
        $user = new User([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $user->present()->fullName());
    }
}
```

### Testing with Factories

```php
public function test_member_since_formats_date_correctly(): void
{
    $user = User::factory()->create([
        'created_at' => '2024-01-15 10:30:00',
    ]);

    $this->assertEquals('January 15, 2024', $user->present()->memberSince());
}
```

## Error Handling

### Missing Presenter Class

If the presenter property points to a non-existent class:

```php
class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = 'App\Presenters\NonExistent';
}

$user->present(); // Throws InvalidArgumentException
```

### Missing Presenter Property

If a model doesn't define the `$presenter` property:

```php
class User extends Model implements ModelPresentable
{
    use PresentModel;
    // Missing: protected string $presenter = ...
}

$user->present(); // Throws PresenterClassNotDefinedException
```

### Invalid Presenter Class

If the presenter doesn't implement `ModelPresenterInterface`:

```php
class InvalidPresenter
{
    // Doesn't extend ModelPresenter
}

class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = InvalidPresenter::class;
}

$user->present(); // Throws InvalidArgumentException
```

## Performance Tips

1. **Avoid expensive operations** - Cache results of expensive computations
2. **Use eager loading** - Load relationships before presenting to avoid N+1
3. **Leverage presenter caching** - Call `present()` freely; it's cached

```php
// Good: Eager load relationships
$users = User::with('posts', 'profile')->get();

foreach ($users as $user) {
    echo $user->present()->postsCount(); // No additional queries
}
```
