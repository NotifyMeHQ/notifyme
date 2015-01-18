<?php

namespace NotifyMeHQ\NotifyMe\Twilio;

use NotifyMeHQ\NotifyMe\AbstractGateway;
use NotifyMeHQ\NotifyMe\Contracts\Gateway;
use NotifyMeHQ\NotifyMe\Contracts\Notifier;
use NotifyMeHQ\NotifyMe\Response;

class TwilioGateway extends AbstractGateway implements Gateway, Notifier
{
    /**
     * Gateway api endpoint.
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
     * Twillio api version.
     *
     * @var string
     */
    protected $version = '2010-04-01';

    /**
     * Create a new twillo gateway instance.
     *
     * @param string[] $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->requires($config, ['from', 'client', 'token']);

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
        $this->config['client'] = array_get($options, 'client', $this->config['client']);
        $this->config['token'] = array_get($options, 'token', $this->config['token']);

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
        $params['From'] = array_get($options, 'from', $this->config['from']);
        $params['To'] = array_get($options, 'to', '');
        $params['Body'] = $message;

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
            'message' => $success ? 'Message sent' : $response['message'],
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
            'message' => $msg,
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
