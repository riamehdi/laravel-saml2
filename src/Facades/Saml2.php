<?php

namespace DaVikingCode\LaravelSaml2\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelSaml2 extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelsaml2';
    }
}
