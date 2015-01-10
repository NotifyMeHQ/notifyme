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
     * Campfire allowed message types.
     *
     * @var string
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
     * @var string
     */
    protected $allowedSounds = [
        // hard to keep this list up-to-date
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

        $type = $this->array_get($options, 'type', 'TextMessage');

        if (! in_array($type, $this->allowedTypeMessages)) {
            $type = 'TextMessage';
        }

        $params['body'] = $message;

        if ($type == 'SoundMessage') {
            $params['body'] = in_array($message, $this->allowedSounds) ? $message: 'horn';
        }

        $params['type'] = $type;

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
        unset($params['from']);

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Authorization' => 'Basic ' . base64_encode($token . ':x'),
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
