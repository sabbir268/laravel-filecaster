<?php

namespace Sabbirh\LaravelFileCaster;


use Sabbirh\LaravelFileCaster\Helpers\FileWrapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FileCaster implements CastsAttributes
{

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $class = strtolower(substr(get_class($model), strrpos(get_class($model), '\\') + 1));
        return new FileWrapper($value, $class, $attributes['id']);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (request()->hasFile($key)) {
            if (isset($attributes[$key])) {
                if (Storage::disk('public')->exists($attributes[$key])) {
                    Storage::disk('public')->delete($attributes[$key]);
                }
            }
            $file = request()->file($key);
            $class = strtolower(substr(get_class($model), strrpos(get_class($model), '\\') + 1));
            $id = $this->getId($attributes, $model);
            $filenameWithExt = $file->getClientOriginalName();
            $path = $class . '/' . $id;
            $value = $file->storeAs($path, $filenameWithExt, 'public');
            return $value;
        } else {
            return $value;
        }
    }

    public function getId($attributes = null, $modelName = null)
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
}
