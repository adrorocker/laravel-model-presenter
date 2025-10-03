<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter\Model;

interface ModelPresentable
{
    public function present(): ModelPresenterInterface;
}
