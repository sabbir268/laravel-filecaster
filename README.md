# laravel-filecaster

A simple file casting for laravel model to handle file upload and retrieve

---

## Installation

```sh
composer require sabbir268/laravel-filecaster
```

## Configuration

**Cast Class Alias**
Add in aliases array (optional)

```php
'aliases' => Facade::defaultAliases()->merge([
    // ...
    'filecast' => Sabbir268\LaravelFileCaster\FileCaster::class,
    // ...
])->toArray(),
```

## Use from Model

#### Import FileCaster class

```php
use Sabbir268\LaravelFileCaster\FileCaster;
```

### Example: manage "image" file upload and retrieve

Let's assume, we have Blog model and there is a column `image` to store image file.

```php
use App\Models;
use Sabbir268\LaravelFileCaster\FileCaster;

class Blog extends Model
{
    // ...
    protected $casts = [
        'image' => FileCaster::class,
    ];
    // ...
}

```

Or, you can use `filecast` as alias of `Sabbir268\LaravelFileCaster\FileCaster` class.

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

#### There several methods/property you can use to retrieve the file information.

```php
// get file name
$blog->image->name; // output: image.jpg

// get file extension
$blog->image->extension; // output: jpg

// get file size
$blog->image->size; // output: 1024

// get file mime type
$blog->image->mime; // output: image/jpeg

// get file http url
$blog->image->url; // output: http://example.com/storage/blog/1/image.jpg

// get file full path
$blog->image->path; // output: /var/www/html/storage/app/public/blog/1/image.jpg

// get storage directory
$blog->image->dir; // output: /var/www/html/storage/app/public/blog/1

// check if file exists
$blog->image->exists; // output: true
```

If you want to get manipulated image url, you can use `ur('WIDTHxHEIGHT')` method.

```php
$blog->image->url('200x200'); // output: http://example.com/storage/cache/200x200/blog-2-image1.jpg
```

Note: It will create a manipulated image in storage cache directory. You will need `gd` or `imagick` extension installed in your server.

If you want to delete the file, you can use `delete()` method.

```php
$blog->image->remove(); // output: true
```

## Contribution

You're open to create any pull request.
