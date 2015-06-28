<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three LTD <support@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\NotifyMe;

trait HttpGatewayTrait
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
     * @param \GuzzleHttp\Message\Response $rawResponse
     *
     * @return array
     */
    protected function responseError($rawResponse)
    {
        return $rawResponse->json() ?: $this->jsonError($rawResponse);
    }

    /**
     * Get the default json response.
     *
     * @param \GuzzleHttp\Message\Response $rawResponse
     *
     * @return array
     */
    abstract protected function jsonError($rawResponse);

    /**
     * Build request url from string.
     *
     * @param string $endpoint
     *
     * @return string
     */
    protected function buildUrlFromString($endpoint)
    {
        return $this->getRequestUrl().'/'.$endpoint;
    }

    /**
     * Get the gateway request url.
     *
     * @return string
     */
    abstract protected function getRequestUrl();
}
