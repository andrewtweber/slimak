<?php

namespace Slimak\Tests\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimak\SluggedModel;

/**
 * Class TestModelSoftDeletes
 *
 * @package Slimak\Tests\Support
 *
 * @property string  $name
 * @property ?string $slug
 * @property ?Carbon $deleted_at
 */
class TestModelSoftDeletes extends SluggedModel
{
    use SoftDeletes;

    protected $table = 'test_models_soft_deletes';

    protected $guarded = [];

    public $timestamps = false;
}
