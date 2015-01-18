<?php

namespace NotifyMeHQ\NotifyMe\PagerDuty;

use NotifyMeHQ\NotifyMe\AbstractGateway;
use NotifyMeHQ\NotifyMe\Contracts\Gateway;
use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class PagerDutyGateway extends AbstractGateway implements Gateway, Notifier
{
    /**
     * Gateway api endpoint.
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
     * PagerDuty api version.
     *
     * @var string
     */
    protected $version = '2010-04-15';

    /**
     * Create a new pagerduty gateway instance.
     *
     * @param string[] $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->requires($config, ['token']);

        $config['from'] = array_get($config, 'from', '');

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
        $params['service_key'] = array_get($options, 'token', $this->config['token']);
        $params['incident_key'] = array_get($options, 'to', 'NotifyMe');
        $params['event_type'] = array_get($options, 'event_type', 'trigger');
        $params['client'] = array_get($options, 'client', null);
        $params['client_url'] = array_get($options, 'client_url', null);
        $params['details'] = array_get($options, 'details', null);
        $params['description'] = $message;

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

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'headers'         => [
                'Content-Type' => 'application/json',
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
            'message' => $success ? 'Message sent' : $response['error']['message'],
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
