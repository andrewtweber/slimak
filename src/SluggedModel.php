<?php

namespace Slimak;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimak\Support\Slugger;

/**
 * Class SluggedModel
 *
 * @package Slimak
 */
abstract class SluggedModel extends Model
{
    /**
     * Name of column to store slugs in
     */
    protected string $slug_column = 'slug';

    /**
     * Convert to lowercase?
     */
    protected bool $slug_lowercase = true;

    /**
     * The character to separate words
     */
    protected string $slug_glue = '-';

    /**
     * Slugs which are not allowed
     */
    protected array $reserved_slugs = [];

    /**
     * Column used for model routing
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->slug_column;
    }

    /**
     * Get the first record with the given slug
     *
     * @param string $slug
     *
     * @return ?static
     */
    public static function findBySlug($slug)
    {
        return static::whereSlug($slug)->first(); /** @phpstan-ignore-line */
    }

    /**
     * Get the first record with the given slug or throw an exception
     *
     * @param string $slug
     *
     * @return ?static
     * @throws ModelNotFoundException
     */
    public static function findBySlugOrFail($slug)
    {
        return static::whereSlug($slug)->firstOrFail(); /** @phpstan-ignore-line */
    }

    /**
     * Base string which is slugged
     *
     * @return string|null
     */
    protected function slugBase(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Slugged version of base string
     * Separated into protected method so that child classes can easily override
     *
     * @return string|null
     */
    protected function slugify(): ?string
    {
        return Slugger::slugify($this->slugBase(), $this->slug_lowercase, $this->slug_glue);
    }

    /**
     * Query scope
     *
     * @param Builder $query
     * @param string  $slug
     */
    public function scopeWhereSlug(Builder $query, string $slug)
    {
        $query->where($this->slug_column, '=', $slug);
    }

    /**
     * @return bool
     */
    protected function usesSoftDeleteTrait(): bool
    {
        $traits = [];

        /**
         * @see https://github.com/andrewtweber/laravel-snaccs/blob/master/src/helpers.php
         */
        $class = get_class($this);
        $autoload = true;

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        $traits = array_unique($traits);

        return in_array(
            SoftDeletes::class,
            $traits
        );
    }

    /**
     * Generate a new slug and verify that it is unique
     */
    public function generateSlug()
    {
        $slug = $base_slug = $this->slugify();

        $counter = 0;

        if (! $base_slug) {
            $this->setSlug(null);
            return;
        }

        do {
            if ($counter > 0) {
                $slug = $base_slug . '-' . $counter;
            }

            $query = self::whereSlug($slug);

            if ($this->exists) {
                $query = $query->where($this->getKeyName(), '!=', $this->getKey());
            }
            if ($this->usesSoftDeleteTrait()) {
                $query = $query->withTrashed(); /** @phpstan-ignore-line */
            }

            $exists = (in_array($slug, $this->reserved_slugs) || $query->first());

            $counter++;
        } while ($exists);

        $this->setSlug($slug);
    }

    /**
     * Override save method to generate a slug
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        if ($this->getSlug() === null) {
            $this->generateSlug();
        }

        return parent::save($options);
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->attributes[$this->slug_column] ?? null;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug)
    {
        $this->attributes[$this->slug_column] = $slug;
    }

    /**
     * Free up slug when deleting
     */
    protected function clearSlug()
    {
        $this->attributes[$this->slug_column] = null;
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $this->clearSlug();

        return parent::delete();
    }
}

