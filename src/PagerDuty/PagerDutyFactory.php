<?php

namespace NotifyMeHQ\NotifyMe\PagerDuty;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class PagerDutyFactory implements FactoryInterface
{
    /**
     * Create a new pagerduty gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\PagerDuty\PagerDutyGateway
     */
    public function make(array $config)
    {
        return new PagerDutyGateway($config);
    }
}
