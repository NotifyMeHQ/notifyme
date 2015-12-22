<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Hipchat;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\Http\GatewayTrait;
use NotifyMeHQ\Http\Response;
use NotifyMeHQ\Support\Arr;

class HipchatGateway implements GatewayInterface
{
    use GatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.hipchat.com';

    /**
     * The api version.
     *
     * @var string
     */
    protected $version = 'v2';

    /**
     * The allowed message background colours.
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
            'id'             => $to,
            'from'           => $this->config['from'],
            'message'        => $message,
            'notify'         => Arr::get($this->config, 'notify', false),
            'message_format' => Arr::get($this->config, 'format', 'text'),
        ];

        $color = Arr::get($this->config, 'color', 'yellow');

        if (!in_array($color, $this->colours)) {
            $color = 'yellow';
        }

        $params['color'] = $color;

        $type = Arr::get($this->config, 'type', 'message');

        return $this->send($this->buildUrlFromString("room/{$to}/{$type}"), $params);
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
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$this->config['token'],
            ],
            'json' => $params,
        ]);

        if (substr((string) $rawResponse->getStatusCode(), 0, 1) === '2') {
            $response = [];
            $success = true;
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
     * @param \GuzzleHttp\Message\ResponseInterface $rawResponse
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
