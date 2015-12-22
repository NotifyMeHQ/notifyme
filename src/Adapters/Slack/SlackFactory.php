<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Slack;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\FactoryInterface;
use NotifyMeHQ\Support\Arr;

class SlackFactory implements FactoryInterface
{
    /**
     * Create a new slack gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Adapters\Slack\SlackGateway
     */
    public function make(array $config)
    {
        Arr::requires($config, ['token']);

        $client = new Client();

        return new SlackGateway($client, $config);
    }
}
