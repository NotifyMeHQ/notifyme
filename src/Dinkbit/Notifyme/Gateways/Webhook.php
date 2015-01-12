<?php

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Notifier;
use Dinkbit\Notifyme\Response;

class Webhook extends AbstractGateway implements Notifier
{
    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'webhook';

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $to = $this->array_get($options, 'to', '');

        return $this->commit('post', $to, $message);
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'        => false,
            'timeout'           => '80',
            'connect_timeout'   => '30',
            'headers'           => [
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'notifyme-webhook/1.0',
            ],
            'json'              => $params,
        ]);

        $response = [];

        if ($rawResponse->getStatusCode() == 200) {
            $success = true;
        } else {
            $response['error'] = $rawResponse->getStatusCode().' Webhook failed delivery';
        }

        return $this->mapResponse($success, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function mapResponse($success, $response)
    {
        return (new Response())->setRaw($response)->map([
            'success'       => $success,
            'message'       => $success ? 'Message sent' : $response['error'],
        ]);
    }

    /**
     * Parse JSON response to array.
     *
     * @param  $body
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
     * Default JSON response.
     *
     * @param string $rawResponse
     *
     * @return array
     */
    public function jsonError($rawResponse)
    {
        $msg = 'API Response not valid.';
        $msg .= " (Raw response API {$rawResponse->getBody()})";

        return [
            'error' => $msg,
        ];
    }

    /**
     * Get the request url.
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint;
    }
}
