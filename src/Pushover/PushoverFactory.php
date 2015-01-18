<?php

namespace NotifyMeHQ\NotifyMe\Pushover;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class PushoverFactory implements FactoryInterface
{
    /**
     * Create a new pushover gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Pushover\PushoverGateway
     */
    public function make(array $config)
    {
        return new PushoverGateway($config);
    }
}
