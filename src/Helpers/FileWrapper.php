<?php

namespace Sabbirh\LaravelFileCaster\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileWrapper
{
    protected $value;
    protected $className;
    protected $id;
    protected $data = [];

    public function __construct($value, $className, $id)
    {
        $this->value = $value;
        $this->className = $className;
        $this->id = $id;
        $this->data = [
            'url' => [$this, 'url'],
            'name' => [$this, 'name'],
            'size' => [$this, 'size'],
            'extension' => [$this, 'extension'],
            'mime' => [$this, 'mime'],
            'lastModified' => [$this, 'lastModified'],
            'exists' => [$this, 'exists'],
        ];


        if (!$this->value || ($this->value && !Storage::disk('public')->exists($this->value))) {
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

    public function url($dimension = null): String
    {
        if (!$dimension) {
            $file = Storage::disk('public')->url($this->value);
        } else {
            // $file = resizeImage($dimension, Str::slug($this->className), $this->id, $this->value);
            $file = Storage::disk('public')->url($this->value);
        }

        return $file;
    }

    public function name(): String
    {
        $file = Storage::disk('public')->url($this->value);
        $filenameWithExt = basename($file);

        return $filenameWithExt;
    }

    public function size(): String
    {
        return Storage::disk('public')->size($this->value);
    }

    public function extension(): String
    {
        $file = Storage::disk('public')->url($this->value);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return $extension;
    }

    public function mime(): String
    {
        return Storage::disk('public')->mimeType($this->value);
    }

    public function lastModified(): String
    {
        $lastModified = Storage::disk('public')->lastModified($this->value);
        return date('Y-m-d H:i:s', $lastModified);
    }



    public function exists(): bool
    {
        if (!$this->value || ($this->value && !Storage::disk('public')->exists($this->value))) {
            return false;
        }

        return true;
    }
}
