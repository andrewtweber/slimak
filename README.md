## Ślimak

Ślimak makes slugging easy for Laravel's Eloquent models.

### Installation

Given how small this package is, and how often people extend
Eloquent, I think it's best to simply copy and paste the files
and modify them as you need.

### Usage

Basic usage. You simply need two columns, `name` and `slug`

```
class Album extends SluggedModel
{
}
```

If you save an album, and it does not yet have a slug, it will
generate a new one:

```
$album = new Album(['name' => 'Test']);
$album->save();

echo $album->slug; // 'test'
```

In case of duplicates, it will append a counter, e.g.:

```
$album2 = Album::create(['name' => 'Test']);
echo $album2->slug; // 'test1'
```

You can also easily fetch models based on their slug:

```
$album = Album::findBySlug('test');
```

If the slug does not exist, it will throw an `Illuminate\Database\Eloquent\ModelNotFoundException;`

### Configuration

Here are some configuration options.

```
class Album
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

