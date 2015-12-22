<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Campfire;

use GuzzleHttp\Client;
use NotifyMeHQ\Adapters\Contracts\GatewayInterface;
use NotifyMeHQ\Support\Arr;
use NotifyMeHQ\Http\GatewayTrait;
use NotifyMeHQ\Http\Response;

class CampfireGateway implements GatewayInterface
{
    use GatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://{domain}.campfirenow.com';

    /**
     * The allowed message types.
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
     * The allowed sound types.
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
     * @return \NotifyMeHQ\Adapters\Contracts\ResponseInterface
     */
    public function notify($to, $message)
    {
        $type = Arr::get($this->config, 'type', 'TextMessage');

        if (!in_array($type, $this->allowedTypeMessages)) {
            $type = 'TextMessage';
        }

        $params = ['type' => $type];

        if ($type === 'SoundMessage') {
            $params['body'] = in_array($message, $this->allowedSounds) ? $message : 'horn';
        } else {
            $params['body'] = $message;
        }

        return $this->send($this->buildUrlFromString("room/{$to}/speak.json"), $params);
    }

    /**
     * Send the notification over the wire.
     *
     * @param string   $url
     * @param string[] $params
     *
     * @return \NotifyMeHQ\Adapters\Contracts\ResponseInterface
     */
    protected function send($url, array $params)
    {
        $success = false;

        $rawResponse = $this->client->post($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Authorization' => 'Basic '.base64_encode($this->config['token'].':x'),
                'Content-Type'  => 'application/json',
            ],
            'json' => ['message' => $params],
        ]);

        switch ($rawResponse->getStatusCode()) {
            case 201:
                $response = [];
                $success = true;
                break;
            case 400:
                $response = ['error' => 'Incorrect request values.'];
                break;
            case 404:
                $response = ['error' => 'Invalid room.'];
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
     * @return \NotifyMeHQ\Adapters\Contracts\ResponseInterface
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
     * @param \GuzzleHttp\Message\ResponseInterface $rawResponse
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
