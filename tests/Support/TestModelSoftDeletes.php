<?php

namespace Slimak\Tests\Support;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slimak\SluggedModel;

/**
 * Class TestModelSoftDeletes
 *
 * @package Slimak\Tests\Support
 */
class TestModelSoftDeletes extends SluggedModel
{
    use SoftDeletes;

    protected $table = 'test_models_soft_deletes';

    protected $guarded = [];

    public $timestamps = false;
}
