<?php

namespace Dinkbit\Notifyme;

use Dinkbit\Notifyme\Gateways\Campfire;
use Dinkbit\Notifyme\Gateways\Gitter;
use Dinkbit\Notifyme\Gateways\HipChat;
use Dinkbit\Notifyme\Gateways\Slack;
use Dinkbit\Notifyme\Gateways\Twilio;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class NotifymeManager extends Manager implements Contracts\Factory
{
    /**
     * Get a driver instance.
     *
     * @param string $driver
     *
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

        return new Slack($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\HipChat
     */
    protected function createHipchatDriver()
    {
        $config = $this->app['config']['services.hipchat'];

        return new HipChat($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\Twilio
     */
    protected function createTwilioDriver()
    {
        $config = $this->app['config']['services.twilio'];

        return new Twilio($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\Campfire
     */
    protected function createCampfireDriver()
    {
        $config = $this->app['config']['services.campfire'];

        return new Campfire($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Dinkbit\Notifyme\Gateways\Gitter
     */
    protected function createGitterDriver()
    {
        $config = $this->app['config']['services.gitter'];

        return new Gitter($config);
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
