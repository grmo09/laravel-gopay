<?php

namespace grmo09\LaravelGoPay;

use Illuminate\Support\ServiceProvider;

/**
 * Class GopayServiceProvider
 * @package grmo09\LaravelGoPay
 */
class GopayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('gopay.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (is_dir($vendor = __DIR__ . '/../vendor')) {
            require_once $vendor . '/autoload.php';
        }

        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'gopay'
        );

        $this->app->singleton('LaravelGoPay', function ($app) {
            return new LaravelGoPay();
        });
    }
}