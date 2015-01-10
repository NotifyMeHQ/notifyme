<?php 

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Notifier;
use Dinkbit\Notifyme\Response;

class Slack extends AbstractGateway implements Notifier {

    protected $endpoint = 'https://slack.com/api';
    protected $displayName = 'slack';

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->requires($config, ['token']);

        $config['token'] = $config['token'];
        $config['username'] = $this->array_get($config, 'username', '');

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($message, $options = [])
    {
        $params = [];

        $params['unfurl_links'] = true;

        $params = $this->addMessage($message, $params, $options);

        return $this->commit('post', $this->buildUrlFromString('chat.postMessage'), $params);
    }

    /**
     * Add a message to the request.
     * 
     * @param  string   $message
     * @param  string[] $params
     * @param  string[] $options
     * @return array
     */
    protected function addMessage($message, array $params, array $options)
    {
        $params['token'] = $this->array_get($options, 'token', $this->config['token']);
        $params['username'] = $this->array_get($options, 'username', $this->config['username']);
        $params['channel'] = $this->array_get($options, 'channel', '');
        $params['text'] = $this->formatMessage($message);

        return $params;
    }

    /**
     * Formats a string for Slack.
     *
     * @param  string $string
     * 
     * @return string
     */
    public function formatMessage($string)
    {
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    protected function commit($method = 'post', $url, $params = [], $options = [])
    {
        $success = false;

        $rawResponse = $this->getHttpClient()->{$method}($url, [
            'exceptions' => false,
            'timeout' => '80',
            'connect_timeout' => '30',
            'body' => $params
        ]);

        if ($rawResponse->getStatusCode() == 200)
        {
            $response = $this->parseResponse($rawResponse->getBody());
            $success = $response['ok'];
        }
        else
        {
            $response = $this->responseError($rawResponse);
        }

        return $this->mapResponse($success, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function mapResponse($success, $response)
    {
        return (new Response)->setRaw($response)->map([
            'success'       => $success,
            'message'       => $success ? 'Message sent' : $response['error'],
        ]);
    }

    /**
     * @param $body
     * @return array
     */
    protected function parseResponse($body)
    {
        return json_decode($body, true);
    }

    /**
     * @param $rawResponse
     * @return array
     */
    protected function responseError($rawResponse)
    {
        if ( ! $this->isJson($rawResponse->getBody()))
        {
            return $this->jsonError($rawResponse);
        }

        return $this->parseResponse($rawResponse->getBody());
    }

    /**
     * @param $rawResponse
     * @return array
     */
    public function jsonError($rawResponse)
    {
        $msg = 'API Response not valid.';
        $msg .= " (Raw response API {$rawResponse->getBody()})";

        return [
            'error' => $msg
        ];
    }

    /**
     * @param $string
     * @return bool
     */
    protected function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint;
    }
}