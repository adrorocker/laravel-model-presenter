<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter;

use AdroSoftware\LaravelModelPresenter\Support\Json\Json;

trait JsonableTrait
{
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param mixed $options
     */
    public function toJson($options = 0): string
    {
        $opts = is_int($options) ? $options : 0;

        return Json::encode($this->jsonSerialize(), $opts);
    }
}
