<?php

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Notifier;
use Dinkbit\Notifyme\Response;

class Twilio extends AbstractGateway implements Notifier
{
    /**
     * Gateway API endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.twilio.com';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'twilio';

    /**
     * Twillio API version.
     *
     * @var string
     */
    protected $version = '2010-04-01';

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['from', 'client', 'token']);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $params = [];

        $this->config['client'] = $this->array_get($options, 'client', $this->config['client']);
        $this->config['token'] = $this->array_get($options, 'token', $this->config['token']);

        unset($options['client']);
        unset($options['token']);

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString('Accounts/'.$this->config['client'].'/SMS/Messages.json'), $params);
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
        $params['From'] = $this->array_get($options, 'from', $this->config['from']);
        $params['To'] = $this->array_get($options, 'to', '');
        $params['Body'] = $message;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'verify'          => true,
            'auth'            => [
                $this->config['client'],
                $this->config['token'],
            ],
            'headers' => [
                'Accept-Charset' => 'utf-8',
                'Content-Type'   => 'application/x-www-form-urlencoded',
                'User-Agent'     => 'notifyme/3.12.8 (php '.phpversion().')',
            ],
            'body' => $params,
        ]);

        if ($rawResponse->getStatusCode() == 201) {
            $response = $this->parseResponse($rawResponse->getBody());
            $success = true;
        } else {
            $response = $this->responseError($rawResponse);
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
            'message'       => $success ? 'Message sent' : $response['message'],
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
        if (! $this->isJson($rawResponse->getBody())) {
            return $this->jsonError($rawResponse);
        }

        return $this->parseResponse($rawResponse->getBody());
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
            'message' => $msg,
        ];
    }

    /**
     * Check if string is a valid JSON.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
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
