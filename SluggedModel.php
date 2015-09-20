<?php namespace App;

use App\Support\Slugger;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class SluggedModel extends Eloquent
{
    /**
     * Convert to lowercase?
     *
     * @var bool
     */
    protected $slug_lower = true;

    /**
     * Slugs which are not allowed
     *
     * @var array
     */
    protected $reserved_slugs = [];

    /**
     * Get the first record with the given slug
     *
     * @param  string  $slug
     * @return static
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', '=', $slug)->firstOrFail();
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
        return Slugger::slugify($this->slugBase(), $this->slug_lower);
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

        do {
            if ($counter > 0) {
                $slug = $base_slug . '-' . $counter;
            }

            $exists = (in_array($slug, $this->reserved_slugs) || self::where('slug', '=', $slug)->first()); // ->withTrashed

            $counter++;

        } while ($exists);

        $this->slug = $slug;
    }

    /**
     * Override save method to generate a slug
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if ($this->slug === null) {
            $this->generateSlug();
        }

        return parent::save();
    }
}

