<?php

namespace Dinkbit\Notifyme;

use Illuminate\Support\ServiceProvider;

class NotifymeServiceProvider extends ServiceProvider
{

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
        $this->app->bindShared('Dinkbit\Notifyme\Contracts\Factory', function ($app) {
            return new NotifymeManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Dinkbit\Notifyme\Contracts\Factory'];
    }

}
