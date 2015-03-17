<?php namespace Invisnik\LaravelSteamAuth;

use Illuminate\Support\ServiceProvider;

class SteamServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['steamauth'] = $this->app->share(
            function () {
                return new SteamAuth();
            }
        );
    }

}