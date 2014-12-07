<?php

namespace Dammyammy\LaraPayNG\Facades;

use Illuminate\Support\Facades\Facade;

class GTPay extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'gtpay'; }
}
