<?php

namespace NotifyMeHQ\NotifyMe;

interface FactoryInterface
{
    /**
     * Create a new gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\GatewayInterface
     */
    public function make(array $config);
}
