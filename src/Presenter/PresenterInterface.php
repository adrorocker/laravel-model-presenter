<?php

namespace AdroSoftware\LaravelModelPresenter\Presenter;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * @extends ArrayAccess<string, mixed>
 * @extends Arrayable<string, mixed>
 */
interface PresenterInterface extends ArrayAccess, Arrayable, JsonSerializable, Jsonable {}
