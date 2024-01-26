<?php

namespace Slimak\Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slimak\Tests\Support\TestModel;
use Slimak\Tests\Support\TestModelCaseSensitive;
use Slimak\Tests\Support\TestModelSoftDeletes;

/**
 * Class ModelTest
 *
 * @package Slimak\Tests
 */
class ModelTest extends LaravelTestCase
{
    /**
     * @test
     */
    public function model()
    {
        // Create model, slug is generated automatically
        $model = TestModel::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test', $model->slug);

        // Update name
        $model->name = '** Slug Test **';
        $model->slug = null;
        $model->save();
        $this->assertSame('slug-test', $model->slug);

        // Create another model, same name
        $model2 = TestModel::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test-1', $model2->slug);

        // Update name, slug stays same
        $model2->name = 'Renamed';
        $model2->save();
        $this->assertSame('slug-test-1', $model2->slug);

        // Update name and set slug to null
        $model2->slug = null;
        $model2->save();
        $this->assertSame('renamed', $model2->slug);

        // Lookup model
        $find = TestModel::findBySlug('slug-test');
        $this->assertEquals($model->getKey(), $find->getKey());

        // Lookup with scope
        $find_with_scope = TestModel::whereSlug('slug-test')->first();
        $this->assertEquals($model->getKey(), $find_with_scope->getKey());

        // Lookup model with case-insensitive slug
        // Doesn't work with sqlite
        //$find_case_insensitive = TestModel::findBySlug('SLUG-TEST');
        //$this->assertEquals($model->getKey(), $find_case_insensitive->getKey());

        // Lookup model that doesn't exist
        $not_found = TestModel::findBySlug('doesnt-exist');
        $this->assertNull($not_found);

        // Find or fail
        $find2 = TestModel::findBySlugOrFail('slug-test');
        $this->assertEquals($model->getKey(), $find2->getKey());

        // Find or fail with case-insensitive slug
        // Doesn't work with sqlite
        //$find_case_insensitive2 = TestModel::findBySlugOrFail('SLUG-TEST');
        //$this->assertEquals($model->getKey(), $find_case_insensitive2->getKey());

        // Find deleted
        $model->delete();
        $find_deleted = TestModel::findBySlug('slug-test');
        $this->assertNull($find_deleted);

        // Re-create model with same name
        $recreate = TestModel::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test', $recreate->slug);
        $this->assertNotEquals($model->getKey(), $recreate->getKey());

        // Find or fail model that doesn't exist
        $this->expectException(ModelNotFoundException::class);
        TestModel::findBySlugOrFail('doesnt-exist');
    }

    /**
     * @test
     */
    public function model_soft_deletes()
    {
        // Create model, slug is generated automatically
        $model = TestModelSoftDeletes::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test', $model->slug);

        // Update name
        $model->name = '** Slug Test **';
        $model->slug = null;
        $model->save();
        $this->assertSame('slug-test', $model->slug);

        // Create another model, same name
        $model2 = TestModelSoftDeletes::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test-1', $model2->slug);

        // Update name, slug stays same
        $model2->name = 'Renamed';
        $model2->save();
        $this->assertSame('slug-test-1', $model2->slug);

        // Update name and set slug to null
        $model2->slug = null;
        $model2->save();
        $this->assertSame('renamed', $model2->slug);

        // Lookup model
        $find = TestModelSoftDeletes::findBySlug('slug-test');
        $this->assertEquals($model->getKey(), $find->getKey());

        // Lookup with scope
        $find_with_scope = TestModelSoftDeletes::whereSlug('slug-test')->first();
        $this->assertEquals($model->getKey(), $find_with_scope->getKey());

        // Lookup model with case-insensitive slug
        // Doesn't work with sqlite
        //$find_case_insensitive = TestModelSoftDeletes::findBySlug('SLUG-TEST');
        //$this->assertEquals($model->getKey(), $find_case_insensitive->getKey());

        // Lookup model that doesn't exist
        $not_found = TestModelSoftDeletes::findBySlug('doesnt-exist');
        $this->assertNull($not_found);

        // Find or fail
        $find2 = TestModelSoftDeletes::findBySlugOrFail('slug-test');
        $this->assertEquals($model->getKey(), $find2->getKey());

        // Find or fail with case-insensitive slug
        // Doesn't work with sqlite
        //$find_case_insensitive2 = TestModelSoftDeletes::findBySlugOrFail('SLUG-TEST');
        //$this->assertEquals($model->getKey(), $find_case_insensitive2->getKey());

        // Find deleted
        $model->delete();
        $find_deleted = TestModelSoftDeletes::findBySlug('slug-test');
        $this->assertNull($find_deleted);

        // Find soft-deleted with scope
        $find_soft_deleted = TestModelSoftDeletes::whereSlug('slug-test')->withTrashed()->first();
        $this->assertEquals($model->getKey(), $find_soft_deleted->getKey());
        $this->assertNotNull($find_soft_deleted->deleted_at);

        // Re-create model with same name
        // Recall that the old "slug-test-1" is now "renamed"
        $recreate = TestModelSoftDeletes::create(['name' => 'Slug Test']);
        $this->assertSame('slug-test-1', $recreate->slug);
        $this->assertNotEquals($model->getKey(), $recreate->getKey());

        // Find or fail model that doesn't exist
        $this->expectException(ModelNotFoundException::class);
        TestModelSoftDeletes::findBySlugOrFail('doesnt-exist');
    }
}
