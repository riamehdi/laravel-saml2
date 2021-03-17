<?php

namespace DaVikingCode\LaravelSaml2;

use DaVikingCode\LaravelSaml2\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class LaravelSaml2ServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'davikingcode');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'davikingcode');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->app['router']->namespace('DaVikingCode\\LaravelSaml2\\Controllers')
            ->middleware(['web'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        // Middleware
        $this->app['router']->aliasMiddleware('auth.saml2' , Authenticate::class);

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->publishes([
            __DIR__.'/../config/laravelsaml2.php' => config_path('laravelsaml2.php'),
        ], 'laravelsaml2.config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelsaml2.php', 'laravelsaml2');

        // Register the service the package provides.
        $this->app->singleton('laravelsaml2', function ($app) {
            return new LaravelSaml2;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelsaml2'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file. ($ php artisan vendor:publish --tag=saml2.config)
        $this->publishes([
            __DIR__.'/../config/laravelsaml2.php' => config_path('laravelsaml2.php'),
        ], 'laravelsaml2.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/davikingcode'),
        ], 'saml2.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/davikingcode'),
        ], 'saml2.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/davikingcode'),
        ], 'saml2.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
