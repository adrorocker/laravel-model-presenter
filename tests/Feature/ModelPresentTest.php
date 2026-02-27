<?php

declare(strict_types=1);

use AdroSoftware\LaravelModelPresenter\Tests\Models\User;

it('presents model', function () {
    $user = (new User())->fill([
        'first_name' => 'john',
        'last_name' => 'doe',
        'email' => 'adro@rocker.com',
    ]);

    expect($user->present()->fullName())->toBe('John Doe');

    $presenter = $user->present();

    expect($presenter)->toBeInstanceOf(\AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter::class);
});
