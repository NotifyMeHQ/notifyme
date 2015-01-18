<?php

namespace NotifyMeHQ\NotifyMe;

use GuzzleHttp\Client;

abstract class AbstractGateway
{
    /**
     * Configuration options.
     *
     * @var string[]
     */
    protected $config;

    /**
     * Get gateway display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return property_exists($this->displayName) ? $this->displayName : '';
    }

    /**
     * Commit a HTTP request.
     *
     * @param string   $method
     * @param string   $url
     * @param string[] $params
     * @param string[] $options
     *
     * @return mixed
     */
    abstract protected function commit($method = 'post', $url, array $params = [], array $options = []);

    /**
     * Map HTTP response to response object.
     *
     * @param bool  $success
     * @param array $response
     *
     * @return \NotifyMeHQ\NotifyMe\Response
     */
    abstract protected function mapResponse($success, $response);

    /**
     * Parse a json response to an array.
     *
     * @param string $body
     *
     * @return array
     */
    protected function parseResponse($body)
    {
        return json_decode($body, true);
    }

    /**
     * Get error response from server or fallback to general error.
     *
     * @param string $rawResponse
     *
     * @return array
     */
    protected function responseError($rawResponse)
    {
        return $this->parseResponse($rawResponse->getBody()) ?: $this->jsonError($rawResponse);
    }

    /**
     * Get the default json response.
     *
     * @param string $rawResponse
     *
     * @return array
     */
    abstract protected function jsonError($rawResponse)

    /**
     * Get the gateway request url.
     *
     * @return string
     */
    abstract protected function getRequestUrl();

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        return new Client();
    }

    /**
     * Build requirest url from string.
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
     * Require specific config values.
     *
     * @param string[] $options
     * @param string[] $required
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function requires($options, array $required = [])
    {
        foreach ($required as $key) {
            if (!array_key_exists(trim($key), $options)) {
                throw new \InvalidArgumentException("Missing required parameter: {$key}");
            }
        }

        return true;
    }
}
