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

namespace NotifyMeHQ\NotifyMe\Gitter;

use NotifyMeHQ\NotifyMe\FactoryInterface;

class GitterFactory implements FactoryInterface
{
    /**
     * Create a new gitter gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\Gitter\GitterGateway
     */
    public function make(array $config)
    {
        return new GitterGateway($config);
    }
}
