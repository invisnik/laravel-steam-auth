<?php

namespace Invisnik\LaravelSteamAuth;

use Illuminate\Support\ServiceProvider;

class SteamServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishableResources();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('steamauth', function () {
            return new SteamAuth($this->app->get('request'));
        });
    }

    /**
     * @return void
     */
    private function registerPublishableResources(): void
    {
        $this->publishes([
            $this->getConfigurationPath() => config_path($this->getShortPackageName().'.php'),
        ], $this->getPackageName().' (configuration)');
    }

    /**
     * @return string
     */
    private function getConfigurationPath(): string
    {
        return __DIR__.'/../config/'.$this->getShortPackageName().'.php';
    }

    /**
     * @return string
     */
    private function getShortPackageName(): string
    {
        return 'steam-auth';
    }

    /**
     * @return string
     */
    private function getPackageName(): string
    {
        return 'laravel-steam-auth';
    }
}
