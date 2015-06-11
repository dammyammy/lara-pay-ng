<?php namespace LaraPayNG;


use Illuminate\Foundation\AliasLoader;

use Illuminate\Support\ServiceProvider;

class LaraPayNGServiceProvider extends ServiceProvider {

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

        AliasLoader::getInstance()->alias('GTPay', '\LaraPayNG\Facades\GTPay');
        AliasLoader::getInstance()->alias('Pay', '\LaraPayNG\Facades\Pay');
        AliasLoader::getInstance()->alias('VoguePay', '\LaraPayNG\Facades\VoguePay');
        AliasLoader::getInstance()->alias('WebPay', '\LaraPayNG\Facades\WebPay');
	}


    /**
     * Bootstrap the application services.
     *
     * Publishes package config file to applications config folder :) Thanks busayo
     *
     * @return void
     */
    public function boot()
    {
        // routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/resources/routes.php';
        }

        // config
        $this->publishes([
            __DIR__. '/resources/lara-pay-ng.php' => config_path('lara-pay-ng.php')
        ], 'config');

        //migrations
        $this->publishes([
            __DIR__.'/resources/migrations/' => database_path('/migrations')
        ], 'migrations');

        // controllers
        $this->publishes([
            __DIR__. '/resources/controllers/' => base_path('app/Http/Controllers/')
        ], 'controllers');
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
