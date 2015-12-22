<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Campfire;

use GuzzleHttp\Client;
use NotifyMeHQ\Adapters\Contracts\FactoryInterface;
use NotifyMeHQ\Support\Arr;

class CampfireFactory implements FactoryInterface
{
    /**
     * Create a new campfire gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Adapters\Campfire\CampfireGateway
     */
    public function make(array $config)
    {
        Arr::requires($config, ['from', 'token']);

        $client = new Client();

        return new CampfireGateway($client, $config);
    }
}
