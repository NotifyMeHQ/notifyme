<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Adapters\Ballou;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\Support\Arr;
use NotifyMeHQ\Http\Response;

class BallouGateway implements GatewayInterface
{
    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://sms.ballou.se';

    /**
     * The http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The configuration options.
     *
     * @var string[]
     */
    protected $config;

    /**
     * Create a new ballou gateway instance.
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
            'UN'      => Arr::get($this->config, 'username', ''),
            'PW'      => Arr::get($this->config, 'password', ''),
            'O'       => urlencode(Arr::get($this->config, 'sender', '')),
            'D'       => Arr::get($this->config, 'D', $to),
            'LONGSMS' => Arr::get($this->config, 'LONGSMS', ''),
            'M'       => $message,
        ];

        return $this->send($this->buildUrlFromString('http/get/SendSms.php'), array_filter($params));
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

        $rawResponse = $this->client->get($url.'?'.http_build_query($params), [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Accept'       => 'application/xml',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        if ($rawResponse->getStatusCode() == 200) {
            $success = false;
            $response = $rawResponse->xml();

            if (isset($response->error)) {
                $response = $this->responseError($rawResponse);
            } else {
                $success = $response->response->attributes()->type == 'status';
            }
        } else {
            $response = $this->responseError($rawResponse);
        }

        return $this->mapResponse($success, $response);
    }

    /**
     * Map the raw response to our response object.
     *
     * @param bool              $success
     * @param \SimpleXMLElement $response
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    protected function mapResponse($success, $response)
    {
        return (new Response())->setRaw((array) $response)->map([
            'success' => $success,
            'message' => $success ? 'Message sent' : $response->attributes()->error,
        ]);
    }

    /**
     * Get error response from server or fallback to general error.
     *
     * @param \GuzzleHttp\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function responseError($rawResponse)
    {
        return $rawResponse->xml() ?: $this->xmlError($rawResponse);
    }

    /**
     * Get the default xml response.
     *
     * @param \GuzzleHttp\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function xmlError($rawResponse)
    {
        $msg = 'API Response not valid.';
        $msg .= " (Raw response API {$rawResponse->getBody()})";

        return [
            'error' => $msg,
        ];
    }

    /**
     * Build request url from string.
     *
     * @param string|null $endpoint
     *
     * @return string
     */
    protected function buildUrlFromString($endpoint = null)
    {
        if ($endpoint) {
            return $this->getRequestUrl().'/'.$endpoint;
        }

        return $this->getRequestUrl();
    }

    /**
     * Get the request url.
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint;
    }
}
