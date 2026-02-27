<?php

declare(strict_types=1);

use AdroSoftware\LaravelModelPresenter\Tests\Models\User;
use AdroSoftware\LaravelModelPresenter\Tests\Models\UserPresenter;

beforeEach(function () {
    $this->user = new User();
    $this->user->fill([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);
});

it('returns the underlying model via getModel', function () {
    $presenter = $this->user->present();

    expect($presenter->getModel())->toBe($this->user);
});

it('accesses model attributes via magic __get', function () {
    $presenter = $this->user->present();

    expect($presenter->first_name)->toBe('John');
    expect($presenter->last_name)->toBe('Doe');
    expect($presenter->email)->toBe('john@example.com');
});

it('sets model attributes via magic __set', function () {
    $presenter = $this->user->present();
    $presenter->first_name = 'Jane';

    expect($presenter->first_name)->toBe('Jane');
    expect($this->user->first_name)->toBe('Jane');
});

it('checks attribute existence via magic __isset', function () {
    $presenter = $this->user->present();

    expect(isset($presenter->first_name))->toBeTrue();
    expect(isset($presenter->nonexistent))->toBeFalse();
});

it('calls model methods via magic __call', function () {
    $presenter = $this->user->present();

    expect($presenter->getTable())->toBe('users');
});

it('provides array access via offsetExists', function () {
    $presenter = $this->user->present();

    expect(isset($presenter['first_name']))->toBeTrue();
    expect(isset($presenter['nonexistent']))->toBeFalse();
});

it('provides array access via offsetGet', function () {
    $presenter = $this->user->present();

    expect($presenter['first_name'])->toBe('John');
    expect($presenter['email'])->toBe('john@example.com');
});

it('provides array access via offsetSet', function () {
    $presenter = $this->user->present();
    $presenter['first_name'] = 'Jane';

    expect($presenter['first_name'])->toBe('Jane');
    expect($this->user->first_name)->toBe('Jane');
});

it('provides array access via offsetUnset', function () {
    $presenter = $this->user->present();
    unset($presenter['email']);

    expect($this->user->email)->toBeNull();
});

it('converts to array via toArray', function () {
    $presenter = $this->user->present();
    $array = $presenter->toArray();

    expect($array)->toBeArray();
    expect($array['first_name'])->toBe('John');
    expect($array['last_name'])->toBe('Doe');
    expect($array['email'])->toBe('john@example.com');
});

it('converts to JSON via toJson', function () {
    $presenter = $this->user->present();
    $json = $presenter->toJson();

    expect($json)->toBeString();
    expect($json)->toContain('"first_name":"John"');
    expect($json)->toContain('"last_name":"Doe"');
});

it('converts to JSON with options', function () {
    $presenter = $this->user->present();
    $json = $presenter->toJson(JSON_PRETTY_PRINT);

    expect($json)->toContain("\n");
});

it('implements JsonSerializable', function () {
    $presenter = $this->user->present();
    $data = $presenter->jsonSerialize();

    expect($data)->toBeArray();
    expect($data['first_name'])->toBe('John');
});

it('caches presenter instance', function () {
    $presenter1 = $this->user->present();
    $presenter2 = $this->user->present();

    expect($presenter1)->toBe($presenter2);
});

it('returns correct presenter type', function () {
    $presenter = $this->user->present();

    expect($presenter)->toBeInstanceOf(UserPresenter::class);
});
