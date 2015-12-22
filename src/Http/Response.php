<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Http;

use NotifyMeHQ\Contracts\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * Has the message sent by the gateway?
     *
     * @var bool
     */
    protected $success;

    /**
     * The response message from the notification gateway.
     *
     * @var string
     */
    protected $message;

    /**
     * The raw response information.
     *
     * @var array
     */
    protected $response;

    /**
     * Determine if the message has been sent by the gateway.
     *
     * @return bool
     */
    public function isSent()
    {
        return (bool) $this->success;
    }

    /**
     * Get the response message from the gateway.
     *
     * @return string
     */
    public function message()
    {
        return (string) $this->message;
    }

    /**
     * Get the raw data from the gateway.
     *
     * @return array
     */
    public function raw()
    {
        return $this->response;
    }

    /**
     * Set the raw response array from the gateway.
     *
     * @param array $response
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function setRaw(array $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Map the given array onto the response's properties.
     *
     * @param array $attributes
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
