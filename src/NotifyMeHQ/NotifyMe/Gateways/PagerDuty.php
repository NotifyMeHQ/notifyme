<?php

namespace NotifyMeHQ\NotifyMe\Gateways;

use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class PagerDuty extends AbstractGateway implements Notifier
{
    /**
     * Gateway API endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://events.pagerduty.com/generic/{version}';

    /**
     * Gateway display name.
     *
     * @var string
     */
    protected $displayName = 'pagerduty';

    /**
     * Gitter API version.
     *
     * @var string
     */
    protected $version = '2010-04-15';

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['token']);

        $config['from'] = $this->array_get($config, 'from', '');

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $params = [];

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString("create_event.json"), $params);
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
        $params['service_key'] = $this->array_get($options, 'token', $this->config['token']);
        $params['incident_key'] = $this->array_get($options, 'to', 'NotifyMe');
        $params['event_type'] = $this->array_get($options, 'event_type', 'trigger');
        $params['client'] = $this->array_get($options, 'client', null);
        $params['client_url'] = $this->array_get($options, 'client_url', null);
        $params['details'] = $this->array_get($options, 'details', null);
        $params['description'] = $message;

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
            'headers'         => [
                'Content-Type'  => 'application/json',
            ],
            'json' => $params,
        ]);

        if ($rawResponse->getStatusCode() == 200) {
            $response = [];
            $success = true;
        } elseif ($rawResponse->getStatusCode() == 404) {
            $response['error'] = 'Inválid service.';
        } elseif ($rawResponse->getStatusCode() == 400) {
            $response['error'] = 'Incorrect request values.';
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
            'message'       => $success ? 'Message sent' : $response['error']['message'],
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
        return str_replace('{version}', $this->version, $this->endpoint);
    }
}
