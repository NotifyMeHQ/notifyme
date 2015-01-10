<?php

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Notifier;
use Dinkbit\Notifyme\Response;

class Campfire extends AbstractGateway implements Notifier
{
    /**
     * Gateway API endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://{domain}.campfirenow.com';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'campfire';

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['from', 'token']);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $params = [];

        $room = $this->array_get($options, 'to', '');

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString("room/{$room}/speak.json"), $params);
    }

    /**
     * Add a message to the request.
     *
     * @param string   $message
     * @param string[] $params
     * @param string[] $options
     *
     * @return array
     */
    protected function addMessage($message, array $params, array $options)
    {
        $params['token'] = $this->array_get($options, 'token', $this->config['token']);
        $params['from'] = $this->array_get($options, 'from', $this->config['from']);
        $params['message'] = $message;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $token = $params['token'];

        unset($params['token']);

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Authorization' => 'Basic ' . base64_encode($token . ':x'),
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'notifyme/1.0 (https://github.com/dinkbit/notifyme)',
            ],
            'json' => $params,
        ]);

        if ($rawResponse->getStatusCode() == 201) {
            $response = [];
            $success = true;
        } else {
            $response = $this->responseError($rawResponse);
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
        return str_replace('{domain}', $this->config['from'], $this->endpoint);
    }
}
