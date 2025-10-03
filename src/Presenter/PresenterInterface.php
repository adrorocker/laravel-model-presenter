<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

interface PresenterInterface extends ArrayAccess, Arrayable, JsonSerializable, Jsonable
{
}
