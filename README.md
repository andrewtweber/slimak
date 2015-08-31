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
$album = new Album(['name' => 'Test']);
$album->save();

echo $album->slug; // 'test'
```

In case of duplicates, it will append a counter, e.g.:

```php
$album2 = Album::create(['name' => 'Test']);
echo $album2->slug; // 'test1'
```

You can also easily fetch models based on their slug:

```php
$album = Album::findBySlug('test');
```

If the slug does not exist, it will throw an `Illuminate\Database\Eloquent\ModelNotFoundException;`

### Configuration

The `SluggedModel` class has a few protected attributes that you can
override per model.

See below for an example.

```php
class Album extends SluggedModel
{
    // Slug this column instead of 'name'
    protected $slug_base = 'title';

    // Do not convert to lowercase
    protected $slug_lower = false;

    // Do not allow these slugs
    protected $reserved_slugs = ['photos'];

    // Completely override slug generation
    protected function slugify()
    {
        return strtolower($this->title);
    }
}
```

