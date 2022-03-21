<?php

namespace Slimak\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class LaravelTestCase
 *
 * @package Slimak\Tests
 */
abstract class LaravelTestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigrations();
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory() . '/database.sqlite',
            'prefix' => '',
        ]);
    }

    /**
     * Migrations for test models
     */
    protected function runMigrations()
    {
        file_put_contents($this->getTempDirectory() . '/database.sqlite', null);

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
        });

        Schema::create('test_models_case_sensitive', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
        });

        Schema::create('test_models_soft_deletes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->softDeletes();
        });
    }

    protected function initializeDirectory(string $directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }

    protected function getTempDirectory(): string
    {
        return __DIR__ . '/temp';
    }
}
