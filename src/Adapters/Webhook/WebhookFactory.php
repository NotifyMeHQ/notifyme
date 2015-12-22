<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Webhook;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\FactoryInterface;
use NotifyMeHQ\Support\Arr;

class WebhookFactory implements FactoryInterface
{
    /**
     * Create a new webhook gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Adapters\Webhook\WebhookGateway
     */
    public function make(array $config)
    {
        Arr::requires($config, ['endpoint']);

        $client = new Client();

        return new WebhookGateway($client, $config);
    }
}
