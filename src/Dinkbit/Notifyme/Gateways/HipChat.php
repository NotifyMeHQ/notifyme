<?php

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Notifier;
use Dinkbit\Notifyme\Response;

class HipChat extends AbstractGateway implements Notifier
{
    /**
     * Gateway API endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.hipchat.com';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'hipchat';

    /**
     * HipChat API version.
     *
     * @var string
     */
    protected $version = 'v2';

    /**
     * HipChat message background color.
     *
     * @var string
     */
    protected $colors = [
        'yellow',
        'red',
        'gray',
        'green',
        'purple',
        'random',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['token']);

        $config['from'] = $this->array_get($config, 'from', '');

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

        return $this->commit('post', $this->buildUrlFromString("room/{$room}/message"), $params);
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
        $params['auth_token'] = $this->array_get($options, 'token', $this->config['token']);

        $params['id'] = $this->array_get($options, 'to', '');
        $params['from'] = $this->array_get($options, 'from', $this->config['from']);

        $color = $this->array_get($options, 'color', 'yellow');

        if (! in_array($color, $this->colors)) {
            $color = 'yellow';
        }

        $params['color'] = $color;
        $params['message'] = $message;
        $params['notify'] = $this->array_get($options, 'notify', false);
        $params['message_format'] = $this->array_get($options, 'format', 'text');

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $token = $params['auth_token'];

        unset($params['auth_token']);

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $params,
        ]);

        if ($rawResponse->getStatusCode() == 204) {
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
            'message'       => $success ? 'Message sent' : $response['error']['message'],
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
        if (! $this->isJson($rawResponse->getBody())) {
            return $this->jsonError($rawResponse);
        }

        return $this->parseResponse($rawResponse->getBody());
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
            'error' => [
                'message' => $msg,
            ],
        ];
    }

    /**
     * Check if string is a valid JSON.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Get the request url.
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint.'/'.$this->version;
    }
}
