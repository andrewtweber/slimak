## Ślimak

Ślimak makes slugging easy for Laravel's Eloquent models.

### Installation

Given how small this package is, and how often people extend
Eloquent, I think it's best to simply copy and paste the files
and modify them as you need.

### Usage

Basic usage. You simply need to extend the `SluggedModel` class.
If your model has two columns, `name` and `slug`, it works out
of the box with no configuration necessary.

```php
class Album extends SluggedModel
{
}
```

#### Sample Migration

```php
Schema::create('albums', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('slug')->nullable()->unique();
    $table->timestamps();
});
```

#### Generating Slugs

If you save an album, and it does not yet have a slug, it will
generate a new one:

```php
$album = new Album(['name' => 'My Test Album']);
$album->save();

echo $album->slug; // 'my-test-album'
```

In case of duplicates, it will append a counter, e.g.:

```php
$album2 = Album::create(['name' => 'My Test Album']);
echo $album2->slug; // 'my-test-album1'
```

You can also easily fetch models based on their slug:

```php
$album = Album::findBySlug('my-test-album');
```

Similarly, you can check for the model and throw an
`Illuminate\Database\Eloquent\ModelNotFoundException;` if it doesn't exist

```php
$album = Album::findBySlugOrFail('does-not-exist');
```

Note: the previous 2 methods do not include "trashed" models (soft deleted).

You can use this query scope to have more control over retrieving:

```php
$album = Album::whereSlug('my-test-album')->withTrashed()->first();
```

#### Case-Sensitive Slugs

If your slugs are case-sensitive, modify your model to also use the
provided trait.

```php
class BlogPost extends SluggedModel
{
    use CaseSensitiveSlug;

    protected $slug_lowercase = false;
}
```

Make sure to also disable converting to lowercase, if you're using the
default slugger.

### Configuration

The `SluggedModel` class has a few protected attributes that you can
override per model.

See below for an example.

```php
class Album extends SluggedModel
{
    // Name of the column to store slugs. Default 'slug'
    protected $slug_column = 'url';

    // Convert to lowercase? Default true
    protected $slug_lowercase = false;

    // Character(s) to separate words. Default '-' (hyphen)
    protected $slug_glue = '_';

    // Do not allow these slugs; will instead use photos1, etc.
    // Default []
    protected $reserved_slugs = ['photos'];

    // Slug this instead of the "name" column
    protected function slugBase()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Completely override how the slug is generated
    protected function slugify()
    {
        return str_random(6);
    }
}
```

By default, `slugify` calls `Slugger::slugify`. This is a helpful method
that you can use in other places if you need it.

### Advanced

I recommend using [Router binding](http://andrew.cool/blog/54/Router-Binding-in-Laravel)

