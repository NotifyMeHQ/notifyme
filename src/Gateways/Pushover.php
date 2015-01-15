<?php

namespace NotifyMeHQ\NotifyMe\Gateways;

use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class Pushover extends AbstractGateway implements Notifier
{
    /**
     * Gateway API endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.pushover.net';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'pushover';

    /**
     * Pushover API version.
     *
     * @var string
     */
    protected $version = '1';

    /**
     * Pushover allowed sounds.
     *
     * @var string[]
     */
    protected $allowedSounds = [
        'pushover',
        'bike',
        'bugle',
        'cashregister',
        'classical',
        'cosmic',
        'falling',
        'gamelan',
        'incoming',
        'intermission',
        'magic',
        'mechanical',
        'pianobar',
        'siren',
        'spacealarm',
        'tugboat',
        'alien',
        'climb',
        'persistent',
        'echo',
        'updown',
        'none', // silent
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['token']);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $params = [];

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString('messages.json'), $params);
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
        $params['user'] = $this->array_get($options, 'to', '');
        $params['device'] = $this->array_get($options, 'device', '');
        $params['title'] = $this->array_get($options, 'title', '');
        $params['message'] = $message;

        if (isset($params['sound'])) {
            $params['sound'] = in_array($params['sound'], $this->allowedSounds) ? $params['sound'] : 'pushover';
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body'            => $params,
        ]);

        if ($rawResponse->getStatusCode() == 200) {
            $response = $this->parseResponse($rawResponse->getBody());
            $success = (bool) $response['status'];
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
            'message'       => $success ? 'Message sent' : implode(', ', $response['errors']),
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
            'errors' => [$msg],
        ];
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
