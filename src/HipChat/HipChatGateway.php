<?php

namespace NotifyMeHQ\NotifyMe\HipChat;

use NotifyMeHQ\NotifyMe\AbstractGateway;
use NotifyMeHQ\NotifyMe\Contracts\Gateway;
use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class HipChatGateway extends AbstractGateway implements Gateway, Notifier
{
    /**
     * Gateway api endpoint.
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
     * HipChat api version.
     *
     * @var string
     */
    protected $version = 'v2';

    /**
     * HipChat message background colours.
     *
     * @var string[]
     */
    protected $colours = [
        'yellow',
        'red',
        'gray',
        'green',
        'purple',
        'random',
    ];

    /**
     * Create a new hipchat gateway instance.
     *
     * @param string[] $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->requires($config, ['token']);

        $config['from'] = array_get($config, 'from', '');

        $this->config = $config;
    }

    /**
     * Send a notification.
     *
     * @param string   $message
     * @param string[] $options
     *
     * @return \NotifyMeHQ\NotifyMe\Response
     */
    public function notify($message, array $options = [])
    {
        $room = array_get($options, 'to', '');

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
        $params['auth_token'] = array_get($options, 'token', $this->config['token']);

        $params['id'] = array_get($options, 'to', '');
        $params['from'] = array_get($options, 'from', $this->config['from']);

        $color = array_get($options, 'color', 'yellow');

        if (!in_array($color, $this->colours)) {
            $color = 'yellow';
        }

        $params['color'] = $color;
        $params['message'] = $message;
        $params['notify'] = array_get($options, 'notify', false);
        $params['message_format'] = array_get($options, 'format', 'text');

        return $params;
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
    protected function commit($method = 'post', $url, array $params = [], array $options = [])
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
     * Map HTTP response to response object.
     *
     * @param bool  $success
     * @param array $response
     *
     * @return \NotifyMeHQ\NotifyMe\Response
     */
    protected function mapResponse($success, $response)
    {
        return (new Response())->setRaw($response)->map([
            'success' => $success,
            'message' => $success ? 'Message sent' : $response['error']['message'],
        ]);
    }

    /**
     * Get the default json response.
     *
     * @param string $rawResponse
     *
     * @return array
     */
    protected function jsonError($rawResponse)
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
     * Get the request url.
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint.'/'.$this->version;
    }
}
