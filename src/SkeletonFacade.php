<?php

namespace Vswteam\Skeleton;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vswteam\Skeleton\Skeleton
 */
class SkeletonFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'skeleton';
    }
}
