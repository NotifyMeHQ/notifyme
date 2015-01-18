<?php

namespace NotifyMeHQ\NotifyMe\Webhook;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class WebhookFactory implements FactoryInterface
{
    /**
     * Create a new webhook gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Webhook\WebhookGateway
     */
    public function make(array $config)
    {
        return new WebhookGateway($config);
    }
}
