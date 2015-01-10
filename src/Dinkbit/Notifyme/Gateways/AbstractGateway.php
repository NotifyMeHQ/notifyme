<?php 

namespace Dinkbit\Notifyme\Gateways;

use Dinkbit\Notifyme\Contracts\Gateway as GatewayContract;

abstract class AbstractGateway implements GatewayContract {

    /**
     * Configuration options.
     * 
     * @var string[]
     */
    protected $config;

    /**
     * Inject the configuration for a Gateway.
     * 
     * @param $config
     */
    abstract public function __construct($config);

    /**
     * Commit a HTTP request.
     * 
     * @param  string   $method
     * @param  string   $url
     * @param  string[] $params
     * @param  string[] $options
     * @return mixed
     */
    abstract protected function commit($method = 'post', $url, $params = [], $options = []);

    /**
     * Map HTTP response to response object.
     * 
     * @param  bool  $success
     * @param  array $response
     * 
     * @return \Dinkbit\Notifyme\Response
     */
    abstract public function mapResponse($success, $response);

    /**
     * Get the gateway request url.
     * 
     * @return mixed
     */
    abstract protected function getRequestUrl();

    /**
     * Get gateway display name.
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return property_exists($this->displayName) ? $this->displayName : '';
    }

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        return new \GuzzleHttp\Client;
    }

    /**
     * Build requirest url from string.
     * 
     * @param  string $endpoint
     * 
     * @return string
     */
    protected function buildUrlFromString($endpoint)
    {
        return $this->getRequestUrl() . '/' . $endpoint;
    }

    /**
     * Get value from array or provide default.
     * 
     * @param  array  $array
     * @param  string $key
     * @param  null   $default
     * 
     * @return mixed
     */
    public function array_get($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Require specific config values.
     * 
     * @param  string[] $options
     * @param  string[] $required
     * 
     * @return bool
     */
    protected function requires($options, array $required = [])
    {
        foreach ($required as $key) {
            if ( ! array_key_exists(trim($key), $options)) {
                throw new \InvalidArgumentException("Missing required parameter: {$key}");
                break;
                return false;
            }
        }

        return true;
    }
}