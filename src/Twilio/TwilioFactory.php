<?php

namespace NotifyMeHQ\NotifyMe\Twilio;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class TwilioFactory implements FactoryInterface
{
    /**
     * Create a new twilio gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Twilio\TwilioGateway
     */
    public function make(array $config)
    {
        return new TwilioGateway($config);
    }
}
