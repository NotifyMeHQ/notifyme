<?php

namespace NotifyMeHQ\NotifyMe\Gitter;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class GitterFactory implements FactoryInterface
{
    /**
     * Create a new gitter gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Gitter\GitterGateway
     */
    public function make(array $config)
    {
        return new GitterGateway($config);
    }
}
