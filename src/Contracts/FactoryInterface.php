<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Contracts;

interface FactoryInterface
{
    /**
     * Create a new gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function make(array $config);
}
