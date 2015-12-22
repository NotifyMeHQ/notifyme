<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Factory;

use InvalidArgumentException;
use NotifyMeHQ\Contracts\FactoryInterface;

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
        if (isset($this->factories[$name])) {
            return $this->factories[$name];
        }

        if (class_exists($class = $this->inflect($name))) {
            return $this->factories[$name] = new $class();
        }

        throw new InvalidArgumentException("Unsupported factory [$name].");
    }

    /**
     * Get the factory class name from the driver name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function inflect($name)
    {
        $driver = ucfirst($name);

        return "NotifyMeHQ\\Adapters\\{$driver}\\{$driver}Factory";
    }
}
