<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

use AdroSoftware\LaravelModelPresenter\Presenter\PresenterClassNotDefinedException;

trait PresentModel
{
    /**
     * @throws PresenterClassNotDefinedException
     */
    public function present(): ModelPresenterInterface
    {
        if (!property_exists($this, 'presenter')) {
            throw new PresenterClassNotDefinedException("The `presenter` class is not defined");
        }

        $presenterClass = $this->presenter;

        /** @var ModelPresenterInterface $presenter */
        $presenter = new $presenterClass($this);

        return $presenter;
    }
}
