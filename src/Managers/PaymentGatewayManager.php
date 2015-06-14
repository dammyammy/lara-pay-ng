<?php

namespace LaraPayNG\Managers;

use LaraPayNG\CashEnvoy;
use LaraPayNG\Exceptions\UnknownPaymentGatewayException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Manager;
use LaraPayNG\GTPay;
use LaraPayNG\Repositories\GatewayRepository;
use LaraPayNG\SimplePay;
use LaraPayNG\VoguePay;
use LaraPayNG\WebPay;

class PaymentGatewayManager extends Manager
{
    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Config $config)
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
     * Create an instance of the SimplePay  API driver.
     *
     * @return SimplePay
     */
    public function createSimplePayDriver()
    {
        return $this->repository(new SimplePay($this->config));
    }

    /**
     * Create an instance of the CashEnvoy  API driver.
     *
     * @return CashEnvoy
     */
    public function createCashEnvoyDriver()
    {
        return $this->repository(new CashEnvoy($this->config));
    }

    /**
     * Create a new driver repository with the given implementation.
     *
     * @param  PaymentGateway $provider
     *
     * @return \LaraPayNG\GatewayRepository
     */
    protected function repository($provider)
    {
        return new GatewayRepository($provider);
    }

    /**
     * Get the default provider driver name.
     *
     * @throws UnknownPaymentGatewayException
     * @return string
     */
    public function getDefaultDriver()
    {
        $driver = $this->config->get('lara-pay-ng.gateways.driver');

        if (in_array($driver, ['gtpay', 'webpay', 'voguepay', 'simplepay', 'cashenvoy'])) {
            return $driver;
        }

        throw new UnknownPaymentGatewayException;
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->config->set('lara-pay-ng.gateways.driver', $name);
    }
}
