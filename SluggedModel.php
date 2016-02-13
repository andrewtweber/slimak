<?php namespace Slimak;

use Slimak\Support\Slugger;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class SluggedModel extends Eloquent
{
    /**
     * Name of column to store slugs in
     *
     * @var string
     */
    protected $slug_column = 'slug';

    /**
     * Convert to lowercase?
     *
     * @var bool
     */
    protected $slug_lowercase = true;

    /**
     * The character to separate words
     *
     * @var string
     */
    protected $slug_glue = '-';

    /**
     * Slugs which are not allowed
     *
     * @var array
     */
    protected $reserved_slugs = [];

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
     * @return static
     */
    public static function findBySlug($slug)
    {
        return self::whereSlug($slug)->first();
    }

    /**
     * Get the first record with the given slug or throw an exception
     *
     * @param string $slug
     *
     * @return static
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findBySlugOrFail($slug)
    {
        return self::whereSlug($slug)->firstOrFail();
    }

    /**
     * Base string which is slugged
     *
     * @return string
     */
    protected function slugBase()
    {
        return $this->attributes['name'];
    }

    /**
     * Slugged version of base string
     * Separated into protected method so that child classes can easily override
     *
     * @return string
     */
    protected function slugify()
    {
        return Slugger::slugify($this->slugBase(), $this->slug_lowercase, $this->slug_glue);
    }

    /**
     * Query scope
     *
     * @param QueryBuilder $query
     * @param string       $slug
     *
     * @return QueryBuilder
     */
    public function scopeWhereSlug($query, $slug)
    {
        return $query->where($this->slug_column, '=', $slug);
    }

    /**
     * Generate a new slug and verify that it is unique
     *
     * @return void
     */
    public function generateSlug()
    {
        $slug = $base_slug = $this->slugify();
        $counter = 0;

        if (! $base_slug) {
            $this->setSlug(null);
        }

        do {
            if ($counter > 0) {
                $slug = $base_slug . '-' . $counter;
            }

            $exists = (in_array($slug, $this->reserved_slugs) || self::whereSlug($slug)->first()); // ->withTrashed

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

        return parent::save();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->attributes[$this->slug_column] ?? null;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->attributes[$this->slug_column] = $slug;
    }
}

