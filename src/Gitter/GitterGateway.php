<?php

namespace NotifyMeHQ\NotifyMe\Gitter;

use NotifyMeHQ\NotifyMe\AbstractGateway;
use NotifyMeHQ\NotifyMe\Arr;
use NotifyMeHQ\NotifyMe\GatewayInterface;
use NotifyMeHQ\NotifyMe\Response;

class GitterGateway extends AbstractGateway implements GatewayInterface
{
    /**
     * Gateway api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.gitter.im';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'gitter';

    /**
     * Gitter api version.
     *
     * @var string
     */
    protected $version = 'v1';

    /**
     * Create a new gitter gateway instance.
     *
     * @param string[] $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->requires($config, ['token']);

        $config['from'] = Arr::get($config, 'from', '');

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
        $room = Arr::get($options, 'to', '');

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString("rooms/{$room}/chatMessages"), $params);
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
        $params['token'] = Arr::get($options, 'token', $this->config['token']);

        $params['text'] = $message;

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

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $params,
        ]);

        if ($rawResponse->getStatusCode() == 200) {
            $response = [];
            $success = true;
        } elseif ($rawResponse->getStatusCode() == 404) {
            $response['error'] = 'Inválid room.';
        } elseif ($rawResponse->getStatusCode() == 400) {
            $response['error'] = 'Incorrect request values.';
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
        return $this->endpoint.'/'.$this->version;
    }
}
