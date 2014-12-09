<?php


namespace Dammyammy\LaraPayNG;

use Dammyammy\LaraPayNG\Gateways\GTPay\GTPay;
use Dammyammy\LaraPayNG\Gateways\VoguePay\VoguePay;
use Dammyammy\LaraPayNG\Gateways\WebPay\WebPay;
use Illuminate\Config\Repository;
use Illuminate\Support\Manager;

class PaymentGatewayManager extends Manager {

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {

        $this->config = $config;
    }

    /**
     * Create an instance of the GTPay driver.
     *
     * @return GTPay
     */
    public function createGtPayDriver()
    {
        return $this->repository(new GTPay($this->config));
    }
    /**
     * Create an instance of the WebPay API driver.
     *
     * @return WebPay
     */
    public function createWebPayDriver()
    {
        return $this->repository(new WebPay($this->config));
    }
    /**
     * Create an instance of the VoguePay  API driver.
     *
     * @return VoguePay
     */
    public function createVoguePayDriver()
    {
        return $this->repository(new VoguePay($this->config));
    }

    /**
     * Create a new driver repository with the given implementation.
     *
     * @param  PaymentGateway $provider
     *
     * @return \Dammyammy\LaraPayNG\GatewayRepository
     */
    protected function repository($provider)
    {
        return new GatewayRepository($provider);
    }

    /**
     * Get the default provider driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return isset($this->app['config']['lara-pay-ng::gateways.driver'])
                ? $this->app['config']['lara-pay-ng::gateways.driver']
                : 'gtpay';
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['lara-pay-ng::gateways.driver'] = $name;
    }

}
