<?php

namespace NotifyMeHQ\NotifyMe;

use Illuminate\Support\Manager;
use InvalidArgumentException;
use NotifyMeHQ\NotifyMe\Contracts\Factory;
use NotifyMeHQ\NotifyMe\Gateways\Campfire;
use NotifyMeHQ\NotifyMe\Gateways\Gitter;
use NotifyMeHQ\NotifyMe\Gateways\HipChat;
use NotifyMeHQ\NotifyMe\Gateways\PagerDuty;
use NotifyMeHQ\NotifyMe\Gateways\Pushover;
use NotifyMeHQ\NotifyMe\Gateways\Slack;
use NotifyMeHQ\NotifyMe\Gateways\Twilio;
use NotifyMeHQ\NotifyMe\Gateways\Webhook;

class NotifyMeManager extends Manager implements Factory
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
     * @return \NotifyMeHQ\NotifyMe\Gateways\Slack
     */
    protected function createSlackDriver()
    {
        $config = $this->app['config']['services.slack'];

        return new Slack($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\HipChat
     */
    protected function createHipchatDriver()
    {
        $config = $this->app['config']['services.hipchat'];

        return new HipChat($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\Twilio
     */
    protected function createTwilioDriver()
    {
        $config = $this->app['config']['services.twilio'];

        return new Twilio($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\Campfire
     */
    protected function createCampfireDriver()
    {
        $config = $this->app['config']['services.campfire'];

        return new Campfire($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\Gitter
     */
    protected function createGitterDriver()
    {
        $config = $this->app['config']['services.gitter'];

        return new Gitter($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\PagerDuty
     */
    protected function createPagerdutyDriver()
    {
        $config = $this->app['config']['services.pagerduty'];

        return new PagerDuty($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\Pushover
     */
    protected function createPagerdutyDriver()
    {
        $config = $this->app['config']['services.pushover'];

        return new Pushover($config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \NotifyMeHQ\NotifyMe\Gateways\Webhook
     */
    protected function createWebhookDriver()
    {
        $config = $this->app['config']['services.webhook'];

        return new Webhook($config);
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
        throw new InvalidArgumentException("No NotifyMe driver was specified.");
    }
}
