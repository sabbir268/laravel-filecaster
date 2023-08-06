# laravel-filecaster

A simple file casting for laravel model to handle file upload and retrieve

---

## Installation

```sh
composer require sabbirh/laravel-filecaster
```

## Configuration

**Service Provider Registration**
In `config/app.php`, add in `providers` array -

```php
'providers' => ServiceProvider::defaultProviders()->merge([
    // ...
    \Sabbirh\LaravelFileCaster\FileCasterServiceProvider::class,
    // ...
])->toArray(),
```

**Facade Class Alias**
Add in aliases array -

```php
'aliases' => Facade::defaultAliases()->merge([
    // ...
    'filecast' => \Sabbirh\LaravelFileCaster\FileCaster::class,
    // ...
])->toArray(),
```

## Use from Model

#### Import FileCaster class

```php
use Sabbirh\LaravelFileCaster\FileCaster;
```

### Example: manage "image" file upload and retrieve

Let's assume, we have Blog model and there is a column `image` to store image file.

```php
use App\Models;
use Sabbirh\LaravelFileCaster\FileCaster;

class Blog extends Model
{
    // ...
    protected $casts = [
        'image' => FileCaster::class,
    ];
    // ...
}

```

Or, you can use `filecast` as alias of `Sabbirh\LaravelFileCaster\FileCaster` class.

```php
use App\Models;

class Blog extends Model
{
    // ...
    protected $casts = [
        'image' => 'filecast',
    ];
    // ...
}
```

Now when you will create a new Blog model instance and has a file from request assign to `image` property, it will automatically upload the file to `storage/app/public/{class_name}/{id}` directory and store the file name with path in `image` column.

```php
$blog = new Blog();
$blog->image = $request->file('image');
$blog->save();
```

And when you will retrieve the model instance, it will automatically retrieve the file path from `image` column and you can use it as like as a string.

```php
$blog = Blog::find(1);
echo $blog->image; // output: /storage/blog/1/image.jpg
```

There several methods you can use to retrieve the file information.

```php
// get file name
$blog->image->name;
// get file extension
$blog->image->extension;
// get file size
$blog->image->size;
// get file mime type
$blog->image->mime;
// get file url
$blog->image->url;
// check if file exists
$blog->image->exists;
```

## Contribution

You're open to create any Pull request.
