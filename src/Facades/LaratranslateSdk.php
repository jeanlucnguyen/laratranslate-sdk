<?php

namespace Jeanlucnguyen\LaratranslateSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeanlucnguyen\LaratranslateSdk\LaratranslateSdk
 */
class LaratranslateSdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Jeanlucnguyen\LaratranslateSdk\LaratranslateSdk::class;
    }
}
