<?php

namespace LaraPayNG\Facades;

use Illuminate\Support\Facades\Facade;

class VoguePay extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'voguepay'; }
}
