<?php

namespace LaraPayNG\Facades;

use Illuminate\Support\Facades\Facade;

class CashEnvoy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cashenvoy';
    }
}
