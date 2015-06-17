<?php namespace LaraPayNG\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use LaraPayNG\CashEnvoy;
use LaraPayNG\Commands\PurgeDatabaseCommand;
use LaraPayNG\GTPay;
use LaraPayNG\Managers\PaymentGatewayManager;
use LaraPayNG\SimplePay;
use LaraPayNG\VoguePay;
use LaraPayNG\WebPay;

class LaraPayNGServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGateways();

        $this->registerAliases();

        $this->registerCommands();
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
            require __DIR__ . '/../resources/routes.php';
        }

        // views
        $this->publishes([
            __DIR__. '/../resources/views/' => base_path('/resources/views/')
        ], 'views');


        // config
        $this->publishes([
            __DIR__. '/../resources/lara-pay-ng.php' => config_path('lara-pay-ng.php')
        ], 'config');

        //migrations
        $this->publishes([
            __DIR__.'/../resources/migrations/' => database_path('/migrations')
        ], 'migrations');

        // controllers
        $this->publishes([
            __DIR__. '/../resources/controllers/' => base_path('app/Http/Controllers/')
        ], 'controllers');

        // Make commands Available
        $this->commands('command.lara-pay-ng.purge-database');
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->bindShared('command.lara-pay-ng.purge-database', function ($app) {
            return new PurgeDatabaseCommand();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
                    'gtpay', 'voguepay', 'webpay', 'cashenvoy', 'simplepay',
                    'pay', 'lara-pay-ng', 'command.lara-pay-ng.purge-database'
        ];
    }

    private function registerAliases()
    {
        AliasLoader::getInstance()->alias('GTPay', '\LaraPayNG\Facades\GTPay');
        AliasLoader::getInstance()->alias('Pay', '\LaraPayNG\Facades\Pay');
        AliasLoader::getInstance()->alias('VoguePay', '\LaraPayNG\Facades\VoguePay');
        AliasLoader::getInstance()->alias('WebPay', '\LaraPayNG\Facades\WebPay');
        AliasLoader::getInstance()->alias('SimplePay', '\LaraPayNG\Facades\SimplePay');
        AliasLoader::getInstance()->alias('CashEnvoy', '\LaraPayNG\Facades\CashEnvoy');
    }

    private function registerGateways()
    {
        $this->app['lara-pay-ng'] = $this->app->share(function ($app) {
            return new PaymentGatewayManager($this->app['config'], $app);
        });

        $this->app['pay'] = $this->app->share(function ($app) {
            return new PaymentGatewayManager($this->app['config'], $app);
        });

        $this->app['gtpay'] = $this->app->share(function ($app) {
            return new GTPay($this->app['config'], $app);
        });

        $this->app['webpay'] = $this->app->share(function ($app) {
            return new WebPay($this->app['config'], $app);
        });

        $this->app['voguepay'] = $this->app->share(function ($app) {
            return new VoguePay($this->app['config'], $app);
        });

        $this->app['simplepay'] = $this->app->share(function ($app) {
            return new SimplePay($this->app['config'], $app);
        });

        $this->app['cashenvoy'] = $this->app->share(function ($app) {
            return new CashEnvoy($this->app['config'], $app);
        });
    }
}
