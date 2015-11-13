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
    // Do not convert to lowercase
    protected $slug_lower = false;

    // Do not allow these slugs
    // Will instead use photos1, etc.
    protected $reserved_slugs = ['photos'];

    // Slug this instead of the "name" column
    protected function slugBase()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Completely override how the slug is generated
    protected function slugify()
    {
        return strtolower($this->title);
    }
}
```

By default, `slugify` calls `Slugger::slugify`. This is a helpful method
that you can use in other places if you need it.

### Advanced

I recommend using [Router binding](http://andrew.cool/blog/54/Router-Binding-in-Laravel)

