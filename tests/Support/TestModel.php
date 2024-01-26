<?php

namespace Slimak\Tests\Support;

use Slimak\SluggedModel;

/**
 * Class TestModel
 *
 * @package Slimak\Tests\Support
 *
 * @property string  $name
 * @property ?string $slug
 */
class TestModel extends SluggedModel
{
    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
