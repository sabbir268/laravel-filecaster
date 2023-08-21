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
    | File path
    |--------------------------------------------------------------------------
    |
    | Here you may specify the file path that should be used
    | Supported paths: "by_model_name_and_id", "defined_path_in_model", "default"
    |
    | "by_model_name_and_id" will create directory with model name and id like: "user/1"
    | "defined_path_in_model" will create directory which is defined in model, in model there must be property public $fileUploadPath
    | "default" will store file in Storage path
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
