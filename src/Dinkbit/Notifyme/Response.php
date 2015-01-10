<?php

namespace Dinkbit\Notifyme;

class Response
{
    /**
     * Response information.
     *
     * @var array
     */
    protected $response;

    /**
     * Returns if message has been sent by the Gateway.
     *
     * @return bool
     */
    public function isSent()
    {
        return (bool) $this->success;
    }

    /**
     * The response message from the payment gateway
     *
     * @return string.
     */
    public function message()
    {
        return $this->message;
    }
    
    /**
     * Gateway raw data
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
     * @param  array $response
     *
     * @return $this
     */
    public function setRaw(array $response)
    {
        $this->response = $response;

        return $this;
    }
    /**
     * Map the given array onto the response's properties.
     *
     * @param  array $attributes
     *
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
