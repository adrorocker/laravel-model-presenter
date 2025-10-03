<?php

declare(strict_types=1);

namespace AdroSoftware\LaravelModelPresenter\Tests\Models;

use AdroSoftware\LaravelModelPresenter\Presenter\Model\ModelPresentable;
use AdroSoftware\LaravelModelPresenter\Presenter\Model\PresentModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @package AdroSoftware\LaravelModelPresenter\Tests\Models
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 */
class User extends Model implements ModelPresentable
{
    use PresentModel;

    protected string $presenter = UserPresenter::class;
    protected $fillable = ['first_name', 'last_name', 'email'];
}
