<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Pagerduty;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\Http\GatewayTrait;
use NotifyMeHQ\Http\Response;
use NotifyMeHQ\Support\Arr;

class PagerdutyGateway implements GatewayInterface
{
    use GatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://events.pagerduty.com/generic';

    /**
     * The api version.
     *
     * @var string
     */
    protected $version = '2010-04-15';

    /**
     * Create a new pagerduty gateway instance.
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
            'to'          => $to,
            'service_key' => $this->config['token'],
            'event_type'  => Arr::get($this->config, 'event_type', 'trigger'),
            'client'      => Arr::get($this->config, 'client', null),
            'client_url'  => Arr::get($this->config, 'client_url', null),
            'details'     => Arr::get($this->config, 'details', null),
            'description' => $message,
        ];

        return $this->send($this->buildUrlFromString('create_event.json'), $params);
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
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
        ]);

        switch ($rawResponse->getStatusCode()) {
            case 200:
                $response = [];
                $success = true;
                break;
            case 400:
                $response = ['error' => 'Incorrect request values.'];
                break;
            case 404:
                $response = ['error' => 'Invalid service.'];
                break;
            default:
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
            'message' => $success ? 'Message sent' : $response['error']['message'],
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
        return ['error' => ['message' => "API Response not valid. (Raw response API {$rawResponse->getBody()})"]];
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
