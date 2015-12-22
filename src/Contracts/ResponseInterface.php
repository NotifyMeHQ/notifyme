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

interface ResponseInterface
{
    /**
     * Determine if the message has been sent by the gateway.
     *
     * @return bool
     */
    public function isSent();

    /**
     * Get the response message from the gateway.
     *
     * @return string
     */
    public function message();

    /**
     * Get the raw data from the gateway.
     *
     * @return array
     */
    public function raw();

    /**
     * Set the raw response array from the gateway.
     *
     * @param array $response
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function setRaw(array $response);

    /**
     * Map the given array onto the response's properties.
     *
     * @param array $attributes
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function map(array $attributes);
}
