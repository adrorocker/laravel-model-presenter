<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

use Illuminate\Database\Eloquent\Model;
use AdroSoftware\LaravelModelPresenter\Presenter\PresenterInterface;

interface ModelPresenterInterface extends PresenterInterface
{
    public function getModel(): Model;
}
