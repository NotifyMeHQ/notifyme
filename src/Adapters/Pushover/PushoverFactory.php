<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Pushover;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\FactoryInterface;
use NotifyMeHQ\Support\Arr;

class PushoverFactory implements FactoryInterface
{
    /**
     * Create a new pushover gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Adapters\Pushover\PushoverGateway
     */
    public function make(array $config)
    {
        Arr::requires($config, ['token']);

        $client = new Client();

        return new PushoverGateway($client, $config);
    }
}
