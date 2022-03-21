<?php

namespace Slimak\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class LaravelTestCase
 *
 * @package Slimak\Tests
 */
abstract class LaravelTestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
    }
}
