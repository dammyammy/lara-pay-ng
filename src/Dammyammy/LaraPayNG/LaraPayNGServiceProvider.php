<?php namespace Dammyammy\LaraPayNG;

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
//        $this->app['lara-pay-ng'] = $this->app->share(function($app)
//        {
//            return new LaraPayNG;
//        });

        $this->app['lara-pay-ng'] = $this->app->share(function($app)
        {
            return new PaymentGatewayManager($app);
        });

        AliasLoader::getInstance()->alias('Pay', 'Dammyammy\LaraPayNG\PaymentGateway');
	}

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('dammyammy\lara-pay-ng');
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return ['lara-pay-ng'];
	}

}
