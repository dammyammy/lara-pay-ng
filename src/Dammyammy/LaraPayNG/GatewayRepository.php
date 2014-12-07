<?php


namespace Dammyammy\LaraPayNG;

class GatewayRepository {

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
     * Handle dynamic calls into macros or pass missing methods to the provider.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {

        return call_user_func_array([$this->provider, $method], $parameters);

    }
}