<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Cachet HQ <support@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\NotifyMe;

use InvalidArgumentException;
use NotifyMe\Contracts\FactoryInterface;

class NotifyMeFactory implements FactoryInterface
{
    /**
     * The current factory instances.
     *
     * @var \NotifyMeHQ\Contracts\FactoryInterface[]
     */
    protected $factories = [];

    /**
     * Create a new gateway instance.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function make(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        return $this->factory($config['driver'])->make($config);
    }

    /**
     * Get a factory instance by name.
     *
     * @param string $name
     *
     * @return \NotifyMeHQ\Contracts\FactoryInterface
     */
    public function factory($name)
    {
        if (isset($this->factories['name'])) {
            return $this->factories['name'];
        }

        $driver = ucfirst($name);
        $class = "NotifyMeHQ\\{$driver}\\{$driver}Factory";

        if (class_exists($class)) {
            return $this->factories['name'] = new $class();
        }

        throw new InvalidArgumentException("Unsupported factory [$name].");
    }
}
