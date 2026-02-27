<?php

declare(strict_types=1);

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\PresentModel;
use AdroSoftware\LaravelModelPresenter\Presenter\PresenterClassNotDefinedException;
use Illuminate\Database\Eloquent\Model;

it('throws exception when presenter property is not defined', function () {
    $model = new class extends Model implements ModelPresentable {
        use PresentModel;
    };

    $model->present();
})->throws(PresenterClassNotDefinedException::class, 'The `presenter` class is not defined');

it('throws exception when presenter class does not exist', function () {
    $model = new class extends Model implements ModelPresentable {
        use PresentModel;
        protected string $presenter = 'NonExistentPresenter';
    };

    $model->present();
})->throws(InvalidArgumentException::class, 'Presenter class `NonExistentPresenter` does not exist');

it('throws exception when presenter does not implement interface', function () {
    $model = new class extends Model implements ModelPresentable {
        use PresentModel;
        protected string $presenter = stdClass::class;
    };

    $model->present();
})->throws(InvalidArgumentException::class, 'must implement');
