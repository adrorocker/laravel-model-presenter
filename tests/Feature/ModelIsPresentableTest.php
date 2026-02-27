<?php

declare(strict_types=1);

namespace AdroSoftware\LaravelModelPresenter\Tests\Feature;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable;
use AdroSoftware\LaravelModelPresenter\Tests\Models\User;

it('model has present method', function () {
    $user = new User();

    expect($user)->toBeInstanceOf(ModelPresentable::class);
    expect(method_exists($user, 'present'))->toBeTrue();
});
