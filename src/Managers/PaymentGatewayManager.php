<?php

namespace LaraPayNG\Managers;

use LaraPayNG\CashEnvoy;
use LaraPayNG\DataRepositories\DataRepository;
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
    /**
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * @param Config $config
     * @param DataRepository $dataRepository
     */
    public function __construct(DataRepository $dataRepository, Config $config)
    {
        $this->config = $config;
        $this->dataRepository = $dataRepository;
    }

    /**
     * Create an instance of the GTPay driver.
     *
     * @return GTPay
     */
    public function createGtPayDriver()
    {
        return $this->repository(new GTPay($this->dataRepository, $this->config));
    }
    /**
     * Create an instance of the WebPay API driver.
     *
     * @return WebPay
     */
    public function createWebPayDriver()
    {
        return $this->repository(new WebPay($this->dataRepository, $this->config));
    }
    /**
     * Create an instance of the VoguePay  API driver.
     *
     * @return VoguePay
     */
    public function createVoguePayDriver()
    {
        return $this->repository(new VoguePay($this->dataRepository, $this->config));
    }

    /**
     * Create an instance of the SimplePay  API driver.
     *
     * @return SimplePay
     */
    public function createSimplePayDriver()
    {
        return $this->repository(new SimplePay($this->dataRepository, $this->config));
    }

    /**
     * Create an instance of the CashEnvoy  API driver.
     *
     * @return CashEnvoy
     */
    public function createCashEnvoyDriver()
    {
        return $this->repository(new CashEnvoy($this->dataRepository, $this->config));
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
        $driver = strtolower($this->config->get('lara-pay-ng.gateways.driver'));

        if (in_array($driver, ['gtpay', 'webpay', 'voguepay', 'simplepay', 'cashenvoy'])) {
            return $driver;
        }

        throw new UnknownPaymentGatewayException;
    }

    /**
     * Set the default payment driver name.
     *
     * @param  string $name
     *
     * @return CashEnvoy|SimplePay|VoguePay
     * @throws UnknownPaymentGatewayException
     */
    public function with($name)
    {
//        return $this->config->set('lara-pay-ng.gateways.driver', $name);
        $name = strtolower($name);

        switch($name) {
            case "gtpay":
                return $this->createGtPayDriver();
                break;

            case "webpay":
                return $this->createWebPayDriver();
                break;

            case "simplepay":
                return $this->createSimplePayDriver();
                break;

            case "cashenvoy":
                return $this->createCashEnvoyDriver();
                break;

            case "voguepay":
                return $this->createVoguePayDriver();
                break;

            default:
                throw new UnknownPaymentGatewayException;
                break;

        }
    }
}
