<?php

namespace Dinkbit\Notifyme;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class NotifymeManager extends Manager implements Contracts\Factory
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\Slack
     */
    protected function createSlackDriver()
    {
        $config = $this->app['config']['services.slack'];

        return new \Dinkbit\Notifyme\Gateways\Slack($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\HipChat
     */
    protected function createHipchatDriver()
    {
        $config = $this->app['config']['services.hipchat'];

        return new \Dinkbit\Notifyme\Gateways\HipChat($config);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException("No Notifyme driver was specified.");
    }
}
