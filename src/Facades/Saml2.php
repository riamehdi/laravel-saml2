<?php

namespace DaVikingCode\Saml2\Facades;

use Illuminate\Support\Facades\Facade;

class Saml2 extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'saml2';
    }
}
