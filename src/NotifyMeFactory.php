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

namespace NotifyMeHQ\NotifyMe;

use InvalidArgumentException;

class NotifyMeFactory implements FactoryInterface
{
    /**
     * The current factory instances.
     *
     * @var \NotifyMeHQ\NotifyMe\FactoryInterface[]
     */
    protected $factories = [];

    /**
     * Create a new gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\GatewayInterface
     */
    public function make(array $config)
    {
        return $this->factory($config['driver'])->make($config);
    }

    /**
     * Get a factory instance by name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \NotifyMeHQ\NotifyMe\FactoryInterface
     */
    public function factory($name)
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException("A driver must be specified.");
        }

        if (isset($this->factories['name'])) {
            return $this->factories['name'];
        }

        $driver = ucfirst($config['driver']);
        $class = "NotifyMeHQ\\{$driver}\\{$driver}Factory";

        if (class_exists($class)) {
            return $this->factories['name'] = new $class();
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}].");
    }
}
