<?php

namespace Sabbir268\LaravelFileCaster;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Sabbir268\LaravelFileCaster\Helpers\FileWrapper;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FileCaster implements CastsAttributes
{

    protected $disk;
    protected $namePrefix;

    public function __construct($namePrefix = '')
    {
        $this->disk = config('filecaster.disk') ?? 'public';
        $this->namePrefix = $namePrefix;
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $class = $this->getClassName($model);
        return new FileWrapper($value, $model, $key);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_file($value)) {
            if (isset($attributes[$key])) {
                if (Storage::disk($this->disk)->exists($attributes[$key])) {
                    Storage::disk($this->disk)->delete($attributes[$key]);
                }
            }
            $file = $value;
            $class = $this->getClassName($model);
            $id = $this->getId($attributes, $model);

            $filenameWithExt = $this->getFileName($file);
            $path = $this->filePath($model, $attributes);
            $value = $file->storeAs($path, $filenameWithExt, $this->disk);
            return $value;
        } else {
            return $value;
        }
    }

    protected function getId($attributes = null, $modelName = null)
    {
        if (isset($attributes['id'])) {
            return $attributes['id'];
        } else {
            $model = $modelName::orderBy('id', 'desc')->first();
            if ($model) {
                return $model->id + 1;
            } else {
                return 1;
            }
        }
    }

    /**
     * @param  Model  $model
     * @return  mixed  <string>
     */
    protected function getClassName(Model $model): string
    {
        return strtolower(substr(get_class($model), strrpos(get_class($model), '\\') + 1));
    }

    /**
     * @param  Model  $model
     * @param  array<string, mixed>  $attributes
     * @return  mixed  <string|null>
     */
    protected function filePath(Model $model, $attributes)
    {
        $definedPath = config('filecaster.path');
        if ($definedPath == 'by_model_name_and_id') {
            return $this->pathByModelNameAndId($model, $attributes);
        } elseif ($definedPath == 'defined_path_in_model') {
            return $this->pathByDefinedPathInModel($model);
        } else {
            throw new \Exception("Invalid path defined in config");
        }
    }

    /**
     * @param  Model  $model
     * @param  array<string, mixed>  $attributes
     * @return  mixed  <string|null>
     */
    protected function pathByModelNameAndId(Model $model, $attributes)
    {
        $class = $this->getClassName($model);
        $id = $this->getId($attributes, $model);
        $path = $class . '/' . $id;
        return $path;
    }

    /**
     * @param  Model  $model
     * @return  mixed  <string|null>
     */
    protected function pathByDefinedPathInModel(Model $model)
    {
        if (!isset($model->fileUploadPath)) {
            throw new \Exception("Model does not have a variable named fileUploadPath");
        }
        return $model->fileUploadPath;
    }

    /**
     * @param File  $file
     * @return  mixed  <string|null>
     */
    protected function getFileName($file)
    {
        $fileName = config('filecaster.file_name');
        $namePrefix = $this->namePrefix;
        $name = '';
        if ($fileName == 'original_file_name') {
            $name = $file->getClientOriginalName();
        } elseif ($fileName == 'hash_name') {
            $name = $file->hashName();
        } else {
            throw new \Exception("Invalid file name defined in config");
        }
        if ($namePrefix && $namePrefix != '') {
            $name = $namePrefix . '-' . $name;
        }
        return $name;
    }
}
