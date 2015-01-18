<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Joseph Cohen <joseph.cohen@dinkbit.com>
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
