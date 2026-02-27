---
name: Presenter Patterns
description: Advanced patterns and best practices for Laravel Model Presenter including inheritance, API resources, testing, and real-world examples.
---

# Advanced Presenter Patterns

## Presenter Inheritance

Create a base presenter for shared functionality across all presenters:

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

    protected function formatCurrency(int $cents): string
    {
        return '$' . number_format($cents / 100, 2);
    }
}
```

Then extend in model-specific presenters:

```php
<?php

namespace App\Presenters;

use App\Models\Order;

/**
 * @property Order $model
 */
class OrderPresenter extends BasePresenter
{
    public function totalFormatted(): string
    {
        return $this->formatCurrency($this->model->total_cents);
    }
}
```

## API Resource Integration

Combine presenters with Laravel API Resources for clean API responses:

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
            'posts_count' => $presenter->postsCount(),
        ];
    }
}
```

## Blade Template Usage

### Assign to Variable for Multiple Uses

```blade
@php($presenter = $user->present())

<div class="profile-card">
    <img src="{{ $presenter->avatarUrl() }}" alt="{{ $presenter->fullName() }}">
    <h2>{{ $presenter->fullName() }}</h2>
    <p class="bio">{{ $presenter->bio() }}</p>
    <span class="member-since">Member since {{ $presenter->memberSince() }}</span>
</div>
```

### In Loops

```blade
@foreach($posts as $post)
    <article>
        <h2>{{ $post->present()->title() }}</h2>
        <p>{{ $post->present()->excerpt(200) }}</p>
        <footer>
            <span>{{ $post->present()->authorName() }}</span>
            <time>{{ $post->present()->publishedAtFormatted() }}</time>
            <span>{{ $post->present()->readTime() }}</span>
        </footer>
    </article>
@endforeach
```

### HTML Output with Status Badges

```blade
{{-- Use {!! !!} for HTML output --}}
{!! $user->present()->statusBadge() !!}
{!! $order->present()->paymentStatusBadge() !!}
```

## Working with Relationships

Access related model presenters through the parent presenter:

```php
<?php

namespace App\Presenters;

use App\Models\Post;

/**
 * @property Post $model
 */
class PostPresenter extends BasePresenter
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

    public function categoryList(): string
    {
        return $this->model->categories->pluck('name')->implode(', ');
    }

    public function tagsList(): string
    {
        return $this->model->tags
            ->map(fn ($tag) => "<span class=\"tag\">{$tag->name}</span>")
            ->implode(' ');
    }
}
```

**Performance Tip:** Eager load relationships to avoid N+1 queries:

```php
$posts = Post::with(['author', 'categories', 'tags'])->get();

foreach ($posts as $post) {
    echo $post->present()->authorName(); // No additional query
}
```

## Testing Strategies

### Unit Testing Presenters

```php
<?php

namespace Tests\Unit\Presenters;

use App\Models\User;
use App\Presenters\UserPresenter;
use PHPUnit\Framework\TestCase;

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

    public function test_display_name_prefers_nickname(): void
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'nickname' => 'Johnny',
        ]);

        $presenter = new UserPresenter($user);

        $this->assertEquals('Johnny', $presenter->displayName());
    }
}
```

### Integration Testing with Factories

```php
<?php

namespace Tests\Feature\Presenters;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPresenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_since_formats_date_correctly(): void
    {
        $user = User::factory()->create([
            'created_at' => '2024-01-15 10:30:00',
        ]);

        $this->assertEquals('January 15, 2024', $user->present()->memberSince());
    }

    public function test_posts_count_returns_correct_count(): void
    {
        $user = User::factory()
            ->has(Post::factory()->count(3))
            ->create();

        $this->assertEquals('3 posts', $user->present()->postsCount());
    }
}
```

## Type Hinting with @property

Add `@property` annotations for IDE autocomplete and static analysis:

```php
<?php

namespace App\Presenters;

use App\Models\Product;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

/**
 * @property Product $model
 * @property int $id
 * @property string $name
 * @property int $price_cents
 * @property int $stock
 * @property string $status
 * @property \Carbon\Carbon $created_at
 */
class ProductPresenter extends ModelPresenter
{
    // IDE now provides autocomplete for $this->model and direct properties
}
```

## Real-World Example: E-commerce Product Presenter

```php
<?php

namespace App\Presenters;

use App\Models\Product;
use Illuminate\Support\Str;

/**
 * @property Product $model
 */
class ProductPresenter extends BasePresenter
{
    public function formattedPrice(): string
    {
        return $this->formatCurrency($this->model->price_cents);
    }

    public function salePrice(): ?string
    {
        if (!$this->model->sale_price_cents) {
            return null;
        }

        return $this->formatCurrency($this->model->sale_price_cents);
    }

    public function discount(): ?string
    {
        if (!$this->model->sale_price_cents) {
            return null;
        }

        $percentage = round(
            (1 - $this->model->sale_price_cents / $this->model->price_cents) * 100
        );

        return "{$percentage}% off";
    }

    public function stockStatus(): string
    {
        return match(true) {
            $this->model->stock === 0 => 'Out of Stock',
            $this->model->stock < 5 => "Only {$this->model->stock} left!",
            $this->model->stock < 20 => 'Low Stock',
            default => 'In Stock',
        };
    }

    public function stockBadgeClass(): string
    {
        return match(true) {
            $this->model->stock === 0 => 'badge-danger',
            $this->model->stock < 5 => 'badge-warning',
            default => 'badge-success',
        };
    }

    public function shortDescription(int $length = 100): string
    {
        return Str::limit($this->model->description, $length);
    }

    public function categoryBreadcrumb(): string
    {
        return $this->model->category->ancestors
            ->push($this->model->category)
            ->pluck('name')
            ->implode(' > ');
    }

    public function mainImageUrl(): string
    {
        return $this->model->images->first()?->url
            ?? asset('images/placeholder-product.png');
    }

    public function averageRating(): string
    {
        $avg = $this->model->reviews_avg_rating ?? 0;

        return number_format($avg, 1);
    }

    public function reviewsSummary(): string
    {
        $count = $this->model->reviews_count ?? 0;

        return match($count) {
            0 => 'No reviews yet',
            1 => '1 review',
            default => "{$count} reviews",
        };
    }
}
```

## Best Practices

1. **Keep presenters focused on display logic** - Don't add business logic, validation, or data manipulation.

2. **Use descriptive method names** - `formattedPrice()` is clearer than `price()`, `memberSince()` better than `createdAt()`.

3. **Return strings for display** - Presenters should return display-ready strings, not raw data.

4. **Leverage the `carbon()` helper** - Use it for all date formatting for consistency.

5. **Add `@property` annotations** - Improves IDE support and catches errors early.

6. **Create a base presenter** - Share common formatting methods across presenters.

7. **Eager load relationships** - Prevent N+1 queries when accessing related data.

8. **Cache expensive computations** - For complex calculations, consider caching results.

9. **Write tests** - Presenters are easy to unit test since they're simple PHP classes.

10. **Use for HTML output sparingly** - Prefer Blade components for complex HTML; use presenters for simple badges/labels.
