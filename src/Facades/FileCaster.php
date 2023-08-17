<?php

namespace Sabbirh\LaravelFileCaster\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sabbirh\LaravelFileCaster\FileCaster
 */

class FileCaster extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     *
     */
    protected static function getFacadeAccessor(): String
    {
        return 'filecaster';
    }
}
