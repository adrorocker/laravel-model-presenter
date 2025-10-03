<?php

namespace AdroSoftware\LaravelModelPresenter\Tests\Feature;

use AdroSoftware\LaravelModelPresenter\Tests\Models\User;

it('can present model', function () {
    $user = new User();

    expect(method_exists($user, 'present'))->toBeTrue();

    // $user->fill([
    //     'name' => 'adro rocker',
    //     'email' => 'adro@rocker.com',
    // ]);
    //
    // // $presenter = new class ($user) extends \AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter {
    // //     public function fullName(): string
    // //     {
    // //         return strtoupper($this->name);
    // //     }
    // //
    // //     public
    // // };
    // //
    // // $user->fill([
    // //     'name' => 'john doe',
    // //     'email' => '',
    // // ]);
    // //
    // // expect($presenter->fullName())->toBe('JOHN DOE');
});
