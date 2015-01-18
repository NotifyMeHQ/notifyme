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
