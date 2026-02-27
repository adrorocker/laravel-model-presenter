<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

use AdroSoftware\LaravelModelPresenter\Presenter\PresenterClassNotDefinedException;
use InvalidArgumentException;

trait PresentModel
{
    protected ?ModelPresenterInterface $presenterInstance = null;

    /**
     * @throws PresenterClassNotDefinedException
     * @throws InvalidArgumentException
     */
    public function present(): ModelPresenterInterface
    {
        if ($this->presenterInstance !== null) {
            return $this->presenterInstance;
        }

        if (!property_exists($this, 'presenter')) {
            throw new PresenterClassNotDefinedException("The `presenter` class is not defined");
        }

        $presenterClass = $this->presenter;

        if (!class_exists($presenterClass)) {
            throw new InvalidArgumentException("Presenter class `{$presenterClass}` does not exist");
        }

        if (!is_subclass_of($presenterClass, ModelPresenterInterface::class)) {
            throw new InvalidArgumentException(
                "Presenter class `{$presenterClass}` must implement " . ModelPresenterInterface::class,
            );
        }

        $this->presenterInstance = new $presenterClass($this);

        return $this->presenterInstance;
    }
}
