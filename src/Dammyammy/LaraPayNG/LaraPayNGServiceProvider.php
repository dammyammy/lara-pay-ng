<?php namespace Dammyammy\LaraPayNG;

use Dammyammy\LaraPayNG\Gateways\GTPay\GTPay;
use Dammyammy\LaraPayNG\Gateways\VoguePay\VoguePay;
use Dammyammy\LaraPayNG\Gateways\WebPay\WebPay;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class LaraPayNGServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['lara-pay-ng'] = $this->app->share(function($app)
        {
            return new PaymentGatewayManager($this->app['config'], $app);
        });

        $this->app['pay'] = $this->app->share(function($app)
        {
            return new PaymentGatewayManager($this->app['config'], $app);
        });

        $this->app['gtpay'] = $this->app->share(function($app)
        {
            return new GTPay($this->app['config'], $app);
        });

        $this->app['webpay'] = $this->app->share(function($app)
        {
            return new WebPay($this->app['config'], $app);
        });

        $this->app['voguepay'] = $this->app->share(function($app)
        {
            return new VoguePay($this->app['config'], $app);
        });



        AliasLoader::getInstance()->alias('GTPay', '\Dammyammy\LaraPayNG\Facades\GTPay');
        AliasLoader::getInstance()->alias('Pay', '\Dammyammy\LaraPayNG\Facades\Pay');
        AliasLoader::getInstance()->alias('VoguePay', '\Dammyammy\LaraPayNG\Facades\VoguePay');
        AliasLoader::getInstance()->alias('WebPay', '\Dammyammy\LaraPayNG\Facades\WebPay');
	}

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('dammyammy\lara-pay-ng','lara-pay-ng' );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return ['lara-pay-ng', 'gtpay', 'voguepay', 'webpay', 'pay'];
	}

}
