<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use AdroSoftware\LaravelModelPresenter\Presenter\JsonableTrait;

abstract class ModelPresenter implements ModelPresenterInterface
{
    use JsonableTrait;

    public function __construct(
        protected Model $model,
    ) {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function __get($name): mixed
    {
        return $this->model->$name;
    }

    public function __set($name, $value): void
    {
        $this->model->$name = $value;
    }

    public function __call($name, $arguments): mixed
    {
        return $this->model->$name ?? $this->model->$name(...$arguments);
    }

    public function __isset($name)
    {
        return property_exists($this->model, $name);
    }

    protected function carbon($date)
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

    public function toArray()
    {
        return $this->model->toArray();
    }
}
