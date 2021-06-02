<?php

namespace Evelution\LivewireTables;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Evelution\LivewireTables\Skeleton\SkeletonClass
 */
class LivewireTablesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'livewire-tables';
    }
}
