<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Pushover;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\Http\GatewayTrait;
use NotifyMeHQ\Http\Response;
use NotifyMeHQ\Support\Arr;

class PushoverGateway implements GatewayInterface
{
    use GatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.pushover.net';

    /**
     * The api version.
     *
     * @var string
     */
    protected $version = '1';

    /**
     * The allowed sounds.
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
        'none',
    ];

    /**
     * Create a new pushover gateway instance.
     *
     * @param \GuzzleHttp\Client $client
     * @param string[]           $config
     *
     * @return void
     */
    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Send a notification.
     *
     * @param string $to
     * @param string $message
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function notify($to, $message)
    {
        $params = [
            'token'   => $this->config['token'],
            'user'    => $to,
            'device'  => Arr::get($this->config, 'device', ''),
            'title'   => Arr::get($this->config, 'title', ''),
            'message' => $message,
        ];

        if (isset($this->config['sound'])) {
            $params['sound'] = in_array($this->config['sound'], $this->allowedSounds) ? $$this->config['sound'] : 'pushover';
        }

        return $this->send($this->buildUrlFromString('messages.json'), $params);
    }

    /**
     * Send the notification over the wire.
     *
     * @param string   $url
     * @param string[] $params
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    protected function send($url, array $params)
    {
        $success = false;

        $rawResponse = $this->client->post($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => $params,
        ]);

        if (substr((string) $rawResponse->getStatusCode(), 0, 1) === '2') {
            $response = json_decode($rawResponse->getBody(), true);
            $success = (bool) $response['status'];
        } else {
            $response = $this->responseError($rawResponse);
        }

        return $this->mapResponse($success, $response);
    }

    /**
     * Map the raw response to our response object.
     *
     * @param bool  $success
     * @param array $response
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    protected function mapResponse($success, array $response)
    {
        return (new Response())->setRaw($response)->map([
            'success' => $success,
            'message' => $success ? 'Message sent' : implode(', ', $response['errors']),
        ]);
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
        return ['errors' => "API Response not valid. (Raw response API {$rawResponse->getBody()})"];
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
