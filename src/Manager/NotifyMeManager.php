<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Manager;

use InvalidArgumentException;
use NotifyMeHQ\Contracts\FactoryInterface;
use NotifyMeHQ\Contracts\ManagerInterface;
use NotifyMeHQ\Support\Arr;

class NotifyMeManager implements ManagerInterface
{
    /**
     * The active connection instances.
     *
     * @var \NotifyMeHQ\Contracts\GatewayInterface[]
     */
    protected $connections = [];

    /**
     * The connection factory instance.
     *
     * @var \NotifyMeHQ\Contracts\FactoryInterface
     */
    protected $factory;

    /**
     * The connection configuration.
     *
     * @var array[]
     */
    protected $config;

    /**
     * The default connection name.
     *
     * @var string
     */
    protected $default;

    /**
     * Create a new notifyme manager instance.
     *
     * @param \NotifyMeHQ\Contracts\FactoryInterface $factory
     * @param array[]                                $config
     * @param string                                 $default
     *
     * @return void
     */
    public function __construct(FactoryInterface $factory, array $config, $default)
    {
        $this->factory = $factory;
        $this->config = $config;
        $this->default = $default;
    }

    /**
     * Get a connection instance.
     *
     * @param string|null $name
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->default;

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Reconnect to the given connection.
     *
     * @param string|null $name
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function reconnect($name = null)
    {
        $name = $name ?: $this->default;

        $this->disconnect($name);

        return $this->connection($name);
    }

    /**
     * Disconnect from the given connection.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function disconnect($name = null)
    {
        $name = $name ?: $this->default;

        unset($this->connections[$name]);
    }

    /**
     * Make the connection instance.
     *
     * @param string $name
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    protected function makeConnection($name)
    {
        $config = $this->getConnectionConfig($name);

        return $this->factory->make($config);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     *
     * @return array
     */
    public function getConnectionConfig($name)
    {
        $name = $name ?: $this->default;

        if (!is_array($config = Arr::get($this->config, $name)) && !$config) {
            throw new InvalidArgumentException("Connection [$name] not configured.");
        }

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->default;
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->default = $name;
    }

    /**
     * Return all of the created connections.
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface[]
     */
    public function getConnections()
    {
        return $this->connections;
    }
}
