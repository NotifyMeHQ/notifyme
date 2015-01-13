<?php

namespace NotifyMeHQ\NotifyMe;

use Illuminate\Support\ServiceProvider;

class NotifyMeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('NotifyMeHQ\NotifyMe\Contracts\Factory', function ($app) {
            return new NotifyMeManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['NotifyMeHQ\NotifyMe\Contracts\Factory'];
    }
}
