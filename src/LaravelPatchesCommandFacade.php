<?php

namespace Vswteam\LaravelPatchesCommand;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vswteam\LaravelPatchesCommand\LaravelPatchesCommand
 */
class LaravelPatchesCommandFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-patches-command';
    }
}
