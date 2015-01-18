<?php

namespace NotifyMeHQ\NotifyMe\HipChat;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class HipChatFactory implements FactoryInterface
{
    /**
     * Create a new hipchat gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\HipChat\HipChatGateway
     */
    public function make(array $config)
    {
        return new HipChatGateway($config);
    }
}
