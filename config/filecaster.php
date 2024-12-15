<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the filesystem disk that should be used
    | Supported disks: "local", "public", "s3" ...
    |
    */
    'disk' => 'public',

    /**
     * File storage
     *
     * Here you may specify the file storage that should be used
     * Supported storages: "local", "cloud"
     */

    'storage' => 'local',


    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',


    /*
    |--------------------------------------------------------------------------
    | File path options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the file path that should be used
    | Supported path options: "by_model_name_and_id", "defined_path_in_model"
    |
    | "by_model_name_and_id" will create directory with model name and id like: "user/1"
    | "defined_path_in_model" will create directory which is defined in model, in model there must be public a property $fileUploadPath
    |
    */

    'path' => 'by_model_name_and_id',

    /*
    |--------------------------------------------------------------------------
    | File name
    |--------------------------------------------------------------------------
    |
    | Here you may specify the file name that should be used
    | Supported names: "original_file_name", "hash_name"
    |
    */
    'file_name' => 'original_file_name',
];
