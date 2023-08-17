<?php

namespace Sabbirh\LaravelFileCaster\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileWrapper
{
    protected $value;
    protected $className;
    protected $id;
    protected $data = [];

    protected $driver;
    protected $disk;

    public function __construct($value, $className, $id)
    {
        $this->value = $value;
        $this->className = $className;
        $this->id = $id;
        $this->driver = config('filecaster.driver');
        $this->disk = config('filecaster.disk') ? config('filecaster.disk') : 'public';

        $this->data = [
            'url' => [$this, 'url'],
            'name' => [$this, 'name'],
            'size' => [$this, 'size'],
            'extension' => [$this, 'extension'],
            'mime' => [$this, 'mime'],
            'lastModified' => [$this, 'lastModified'],
            'exists' => [$this, 'exists'],
        ];


        if (!$this->value || ($this->value && !Storage::disk($this->disk)->exists($this->value))) {
            return null;
        }
    }

    public function __get($name)
    {
        if (isset($this->data[$name]) && is_callable($this->data[$name])) {
            return $this->data[$name]();
        }

        throw new \Exception("Property or method '$name' does not exist.");
    }

    public function __toString(): String
    {
        return $this->value ? $this->value : '';
    }

    /**
     * @param  string  $dimension width x height (e.g. 200x200)
     * @return  string
     */
    public function url($dimension = null): String
    {
        if (!$dimension) {
            $file = Storage::disk($this->disk)->url($this->value);
        } else {
            $file = $this->resize($dimension, $this->value);
            // $file = Storage::disk($this->disk)->url($this->value);
        }

        return $file;
    }
    /**
     * @return  string
     */
    public function name(): String
    {
        $file = Storage::disk($this->disk)->url($this->value);
        $filenameWithExt = basename($file);

        return $filenameWithExt;
    }

    /**
     * @return  string
     */
    public function size(): String
    {
        return Storage::disk($this->disk)->size($this->value);
    }

    /**
     * @return  string
     */
    public function extension(): String
    {
        $file = Storage::disk($this->disk)->url($this->value);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return $extension;
    }

    /**
     * @return  string
     */
    public function mime(): String
    {
        return Storage::disk($this->disk)->mimeType($this->value);
    }

    public function lastModified(): String
    {
        $lastModified = Storage::disk($this->disk)->lastModified($this->value);
        return date('Y-m-d H:i:s', $lastModified);
    }


    /* 
    * @return  bool
    */
    public function exists(): bool
    {
        if (!$this->value || ($this->value && !Storage::disk($this->disk)->exists($this->value))) {
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
    public function resize($size = null, $path = null): mixed
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

            $supportedExtensions = ['jpg', 'png', 'gif', 'jpeg'];

            if (!in_array($extension, $supportedExtensions)) {
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
