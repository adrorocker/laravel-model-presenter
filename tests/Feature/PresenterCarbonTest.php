<?php

declare(strict_types=1);

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;
use AdroSoftware\LaravelModelPresenter\Tests\Models\User;
use Carbon\Carbon;

it('formats dates using carbon helper', function () {
    $presenter = new class (new User()) extends ModelPresenter {
        public function formattedDate(): string
        {
            return $this->carbon('2024-01-15')->format('F j, Y');
        }
    };

    expect($presenter->formattedDate())->toBe('January 15, 2024');
});

it('handles null date with carbon helper', function () {
    $presenter = new class (new User()) extends ModelPresenter {
        public function formattedDate(): Carbon
        {
            return $this->carbon(null);
        }
    };

    expect($presenter->formattedDate())->toBeInstanceOf(Carbon::class);
});

it('handles DateTime instance with carbon helper', function () {
    $date = new DateTime('2024-06-20');

    $presenter = new class (new User()) extends ModelPresenter {
        public ?DateTime $testDate = null;

        public function formattedDate(): string
        {
            return $this->carbon($this->testDate)->format('Y-m-d');
        }
    };

    $presenter->testDate = $date;

    expect($presenter->formattedDate())->toBe('2024-06-20');
});
