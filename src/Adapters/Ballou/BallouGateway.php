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

use Exception;
use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\Http\GatewayTrait;
use NotifyMeHQ\Http\Response;
use NotifyMeHQ\Support\Arr;
use SimpleXMLElement;
use Throwable;

class BallouGateway implements GatewayInterface
{
    use GatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://sms.ballou.se';

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
            $response = $this->parse($rawResponse->getBody());

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
     * Parse an xml string to an array.
     *
     * @param string $body
     *
     * @return array
     */
    protected function parse($body)
    {
        $disableEntities = libxml_disable_entity_loader(true);
        $internalErrors = libxml_use_internal_errors(true);

        try {
            $xml = new SimpleXMLElement((string) $body ?: '<root />', LIBXML_NONET);

            return json_decode(json_encode($xml), true);
        } catch (Exception $e) {
            //
        } catch (Throwable $e) {
            //
        } finally {
            libxml_disable_entity_loader($disableEntities);
            libxml_use_internal_errors($internalErrors);
        }
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
            'message' => $success ? 'Message sent' : $response['error'],
        ]);
    }

    /**
     * Get error response from server or fallback to general error.
     *
     * @param \GuzzleHttp\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function responseError($rawResponse)
    {
        return $this->parse($rawResponse->getBody()) ?: $this->buildError($rawResponse);
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
