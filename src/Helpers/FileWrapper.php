<?php

namespace Sabbir268\LaravelFileCaster\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileWrapper
{
    protected $value;
    protected $model;
    protected $key;


    protected $methods = [];

    protected $driver;
    protected $disk;

    protected $supportedExtensions = ['jpg', 'png', 'gif', 'jpeg'];

    public function __construct($value, $model, $key)
    {
        $this->value = $value;
        $this->model = $model;
        $this->key = $key;

        $this->driver = config('filecaster.driver');
        $this->disk = config('filecaster.disk') ? config('filecaster.disk') : 'public';

        $this->methods = [
            'url' => [$this, 'url'],
            'name' => [$this, 'name'],
            'path' => [$this, 'path'],
            'dir' => [$this, 'dir'],
            'size' => [$this, 'size'],
            'extension' => [$this, 'extension'],
            'mime' => [$this, 'mime'],
            'lastModified' => [$this, 'lastModified'],
            'exists' => [$this, 'exists'],
            'height' => [$this, 'height'],
            'width' => [$this, 'width'],
        ];
        // check if value is empty or null
        if ((empty($this->value) || is_null($this->value))) {
            return $this->value = '';
        }

        if (!Storage::disk($this->disk)->exists($this->value)) {
            return $this->value = '';
        }
    }

    public function __get($name)
    {
        if (empty($this->value) || is_null($this->value)) {
            return $this->value = '';
        }

        if (isset($this->methods[$name]) && is_callable($this->methods[$name])) {
            return $this->methods[$name]();
        }
        throw new \Exception("Property or method '$name' does not exist.");
    }

    public function __toString(): String
    {
        return $this->value ? $this->value : '';
    }

    /**
     * @param  string  $dimension width x height (e.g. 200x200)
     * @return  mixed  <string|null>
     */
    public function url($dimension = null): mixed
    {
        if ((empty($this->value) || is_null($this->value))) {
            return $this->value = '';
        }

        if (!$dimension) {
            $file = Storage::disk($this->disk)->url($this->value);
        } else {
            $file = $this->resize($dimension, $this->value);
        }

        return $file;
    }


    /**
     * @return  string
     */
    protected function name(): String
    {
        $file = Storage::disk($this->disk)->url($this->value);
        $filenameWithExt = basename($file);

        return $filenameWithExt;
    }

    /**
     * @return  string
     */
    protected function path(): String
    {
        return Storage::disk($this->disk)->path($this->value);
    }

    /**
     * @return  string
     */
    protected function dir(): String
    {
        $file = Storage::disk($this->disk)->path($this->value);
        $path = dirname($file);

        return $path;
    }

    /**
     * @return  string
     */
    protected function size(): String
    {
        return Storage::disk($this->disk)->size($this->value);
    }

    /**
     * @return  string
     */
    protected function height(): String
    {
        $file = Storage::disk($this->disk)->path($this->value);
        if (!in_array($this->extension, $this->supportedExtensions)) {
            return '';
        }
        $height = isset(getimagesize($file)[1]) ? getimagesize($file)[1] : '';
        return $height;
    }

    /**
     * @return  string
     */
    protected function width(): String
    {
        $file = Storage::disk($this->disk)->path($this->value);
        if (!in_array($this->extension, $this->supportedExtensions)) {
            return '';
        }
        $width = isset(getimagesize($file)[0]) ? getimagesize($file)[0] : '';
        return $width;
    }

    /**
     * @return  string
     */
    protected function extension(): String
    {
        $file = Storage::disk($this->disk)->path($this->value);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return $extension;
    }

    /**
     * @return  string
     */
    protected function mime(): String
    {
        return Storage::disk($this->disk)->mimeType($this->value);
    }

    protected function lastModified(): String
    {
        $lastModified = Storage::disk($this->disk)->lastModified($this->value);
        return date('Y-m-d H:i:s', $lastModified);
    }


    /* 
    * @return  bool
    */
    protected function exists(): bool
    {
        if (!$this->value || ($this->value && !Storage::disk($this->disk)->exists($this->value))) {
            return false;
        }

        return true;
    }


    /**
     * @return  bool
     */

    public function remove(): bool
    {
        if ((empty($this->value) || is_null($this->value))) {
            return true;
        }

        $eraseFile = Storage::disk($this->disk)->delete($this->value);
        if ($eraseFile) {
            if (is_object($this->model)) {
                $this->model->update([$this->key => null]);
            }
        } else {
            return false;
        }
        return true;
    }


    /*
    * @param  string  $size
    * @param  string  $path
    *
    * @return  mixed  <string|null>
    */
    protected function resize($size = null, $path = null): mixed
    {
        $name = $this->name;
        $extension = $this->extension;
        $filename = Str::beforeLast($name, '.' . $extension);

        $cacheFolder = $size;
        $fit = false;
        $parentDir = Str::beforeLast($path, '/');

        if (!is_null($size)) {
            if (strpos($size, 'f') !== false) {
                $size = str_replace('f', '', $size);
                $fit = true;
            }

            $size = explode('x', $size);

            if (empty($size['0'])) {
                $size['0'] = null;
            }

            if (empty($size['1'])) {
                $size['1'] = null;
            }

            $width = $size['0'];
            $height = $size['1'];

            $imagePath = Storage::disk($this->disk)->path($this->value);



            if (!in_array($extension, $this->supportedExtensions)) {
                return Storage::disk($this->disk)->url($name);
            }

            if (!extension_loaded($this->driver)) {
                throw new \Exception("$this->driver driver not installed");
            }

            Image::configure(['driver' => $this->driver]);

            $imgThumb = "/cache/{$cacheFolder}/";

            if (!$fit) {
                $theimg = Image::make($imagePath)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream($extension, 100);
            } else {
                $theimg = Image::make($imagePath)->fit($width, $height, function ($constraint) {
                    // $constraint->aspectRatio();
                    // $constraint->upsize();
                })->stream($extension, 100);
            }

            $cacheFileName = str_replace('/', '-', $parentDir) . '-' . $filename;
            $savePath = $imgThumb . $cacheFileName . '.' . $extension;
            // check if file exists in cache
            if (!Storage::disk($this->disk)->exists($savePath)) {
                Storage::disk($this->disk)->put($savePath, $theimg);
            }
            return Storage::disk($this->disk)->url($savePath);
        } else {
            return Storage::disk($this->disk)->url($path);
        }
    }
}
