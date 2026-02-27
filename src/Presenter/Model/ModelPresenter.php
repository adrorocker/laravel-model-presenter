<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use AdroSoftware\LaravelModelPresenter\Presenter\JsonableTrait;

abstract class ModelPresenter implements ModelPresenterInterface
{
    use JsonableTrait;

    public function __construct(
        protected Model $model,
    ) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function __get(string $name): mixed
    {
        return $this->model->$name;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->model->$name = $value;
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->model, $name)) {
            return $this->model->$name(...$arguments);
        }

        return $this->model->$name;
    }

    public function __isset(string $name): bool
    {
        return isset($this->model->$name);
    }

    protected function carbon(string|DateTimeInterface|null $date): Carbon
    {
        return new Carbon($date);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->model->$offset);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->model->toArray();
    }
}
