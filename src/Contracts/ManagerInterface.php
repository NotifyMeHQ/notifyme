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

interface ManagerInterface
{
    /**
     * Get a connection instance.
     *
     * @param string|null $name
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function connection($name = null);

    /**
     * Reconnect to the given connection.
     *
     * @param string|null $name
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface
     */
    public function reconnect($name = null);

    /**
     * Disconnect from the given connection.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function disconnect($name = null);

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     *
     * @return array
     */
    public function getConnectionConfig($name);

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection();

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection($name);

    /**
     * Return all of the created connections.
     *
     * @return \NotifyMeHQ\Contracts\GatewayInterface[]
     */
    public function getConnections();
}
