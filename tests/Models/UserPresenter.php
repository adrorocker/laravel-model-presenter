<?php

declare(strict_types=1);

namespace AdroSoftware\LaravelModelPresenter\Tests\Models;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresenter;

/**
 * @property User $model
 */
class UserPresenter extends ModelPresenter
{
    public function fullName(): string
    {
        return ucwords($this->model->first_name . ' ' . $this->model->last_name);
    }
}
