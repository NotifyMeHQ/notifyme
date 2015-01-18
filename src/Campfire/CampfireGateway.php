<?php

namespace NotifyMeHQ\NotifyMe\Campfire;

use NotifyMeHQ\NotifyMe\AbstractGateway;
use NotifyMeHQ\NotifyMe\Contracts\Gateway;
use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class CampfireGateway extends AbstractGateway implements Gateway, Notifier
{
    /**
     * Gateway api endpoint.
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
     * Campfire allowed message types.
     *
     * @var string[]
     */
    protected $allowedTypeMessages = [
        'TextMessage',
        'PasteMessage',
        'TweetMessage',
        'SoundMessage',
    ];

    /**
     * Campfire allowed sound types.
     *
     * @var string[]
     */
    protected $allowedSounds = [
        '56k',
        'bueller',
        'crickets',
        'dangerzone',
        'deeper',
        'drama',
        'greatjob',
        'horn',
        'horror',
        'inconceivable',
        'live',
        'loggins',
        'noooo',
        'nyan',
        'ohmy',
        'ohyeah',
        'pushit',
        'rimshot',
        'sax',
        'secret',
        'tada',
        'tmyk',
        'trombone',
        'vuvuzela',
        'yeah',
        'yodel',
    ];

    /**
     * Create a new campfire gateway instance.
     *
     * @param string[] $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->requires($config, ['from', 'token']);

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
        $params['token'] = array_get($options, 'token', $this->config['token']);
        $params['from'] = array_get($options, 'from', $this->config['from']);

        $type = array_get($options, 'type', 'TextMessage');

        if (!in_array($type, $this->allowedTypeMessages)) {
            $type = 'TextMessage';
        }

        $params['body'] = $message;

        if ($type == 'SoundMessage') {
            $params['body'] = in_array($message, $this->allowedSounds) ? $message : 'horn';
        }

        $params['type'] = $type;

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

        $token = $params['token'];

        unset($params['token']);
        unset($params['from']);

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Authorization' => 'Basic '.base64_encode($token.':x'),
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'notifyme/1.0 (https://github.com/dinkbit/notifyme)',
            ],
            'json' => ['message' => $params],
        ]);

        if ($rawResponse->getStatusCode() == 201) {
            $response = [];
            $success = true;
        } elseif ($rawResponse->getStatusCode() == 404) {
            $response['error'] = 'Inválid room.';
        } elseif ($rawResponse->getStatusCode() == 400) {
            $response['error'] = 'Incorrect request values.';
        } else {
            $response['error'] = $this->responseError($rawResponse);
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
            'message' => $success ? 'Message sent' : $response['error'],
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
