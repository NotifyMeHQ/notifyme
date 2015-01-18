<?php

namespace NotifyMeHQ\NotifyMe\Slack;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class SlackFactory implements FactoryInterface
{
    /**
     * Create a new slack gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Slack\SlackGateway
     */
    public function make(array $config)
    {
        return new SlackGateway($config);
    }
}
