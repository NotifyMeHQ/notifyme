<?php

namespace NotifyMeHQ\NotifyMe\Campfire;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class CampfireFactory implements FactoryInterface
{
    /**
     * Create a new campfire gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Campfire\CampfireGateway
     */
    public function make(array $config)
    {
        return new CampfireGateway($config);
    }
}
