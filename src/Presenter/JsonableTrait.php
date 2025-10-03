<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter;

use AdroSoftware\LaravelModelPresenter\Support\Json\Json;

trait JsonableTrait
{
    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return Json::encode($this->jsonSerialize(), $options);
    }
}
