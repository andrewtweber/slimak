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
     * @throws ModelNotFoundException
     */
    public static function findBySlugOrFail($slug)
    {
        return self::whereSlug($slug)->firstOrFail();
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
        return in_array(
            SoftDeletes::class,
            array_keys((new \ReflectionClass(self::class))->getTraits())
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
                $query = $query->withTrashed();
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

