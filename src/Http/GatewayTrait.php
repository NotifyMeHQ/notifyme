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

trait GatewayTrait
{
    /**
     * The http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The configuration options.
     *
     * @var string[]
     */
    protected $config;

    /**
     * Get error response from server or fallback to general error.
     *
     * @param \GuzzleHttp\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function responseError($rawResponse)
    {
        return json_decode($rawResponse->getBody(), true) ?: $this->buildError($rawResponse);
    }

    /**
     * Build a fallback error.
     *
     * @param \GuzzleHttp\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function buildError($rawResponse)
    {
        return ['error' => "API Response not valid. (Raw response API {$rawResponse->getBody()})"];
    }

    /**
     * Build request url from string.
     *
     * @param string|null $endpoint
     *
     * @return string
     */
    protected function buildUrlFromString($endpoint = null)
    {
        if ($endpoint) {
            return $this->getRequestUrl().'/'.$endpoint;
        }

        return $this->getRequestUrl();
    }

    /**
     * Get the gateway request url.
     *
     * @return string
     */
    abstract protected function getRequestUrl();
}
