<?php


namespace LaraPayNG;

use Illuminate\Support\Traits\Macroable;

class GatewayRepository
{
    use Macroable
    {
        __call as macroCall;
    }

    /**
     * The provider implementation.
     */
    protected $provider;

    /**
     * Create a new provider repository instance.
     *
     * @param PaymentGateway $provider
     *
     */
    public function __construct(PaymentGateway $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Handle dynamic calls into macros or pass missing methods to the store.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        } else {
            return call_user_func_array([$this->provider, $method], $parameters);
        }
    }
}
