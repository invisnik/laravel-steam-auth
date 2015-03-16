<?php namespace Invisnik\LaravelSteamAuth;

use Illuminate\Support\ServiceProvider;

class SteamServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['steam'] = $this->app->share(
            function () {
                return new \SteamAuth();
            }
        );
    }

}