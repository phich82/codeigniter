<?php
/**
 * @author Huynh Phat <phat.nguyen@persol.co.jp>
 * @license [v1]
 */
namespace App\Api;

use GuzzleHttp\Client;
use App\Api\Helpers\Constant;
use App\Api\Contracts\AwsConnectCloudContract;
use App\Api\Exceptions\AwsConnectCloudException;

/**
 * Get the resource from the restful APIs (providers)
 */
class AwsConnectCloud implements AwsConnectCloudContract
{
    /**
     * The Client instance
     *
     * @var Client
     */
    protected $client;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * The cache instance
     * @var CI_Cache
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param array    $config
     * @param CI_Cache $cache
     */
    public function __construct($config, $cache = null)
    {
        $this->client = new Client([
            'base_uri' => $config['base_uri'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
            'timeout' => 60.0,
            'verify' => false,
        ]);
        $this->cache = $cache;
    }

    /**
     * @implement
     * Request the resource without cache
     *
     * @param  string $path
     * @param  array  $params
     * @return object
     * @throws GuzzleException
     */
    public function request($path, $params = [])
    {
        return $this->_request('POST', $path, $params);
    }

    /**
     * @implement
     * Request the resource and cache it
     *
     * @param string $path
     * @param array  $params
     * @param string $cacheKey
     * @return mixed
     * @throws GuzzleException
     */
    public function requestCache($path, $params = [], $cacheKey = null)
    {
        if ($cacheKey === null) {
            $cacheKey = $this->cacheKey($path, $params);
        }

        // get from cache if any
        if (!$cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        $result = $this->request($path, $params);

        // store data to cache
        $this->cache->save($cacheKey, $result, Constant::API_CACHE_EXPIRE);

        return $result;
    }

    /**
     * Get the resource by the get method
     *
     * @param string $path []
     * @return object
     */
    public function get($path, $params = [])
    {
        return $this->_request('GET', $path, $params);
    }

    /**
     * Get the resource by the post method
     *
     * @param string $path []
     * @return object
     */
    public function post($path, $params = [])
    {
        return $this->_request('POST', $path, $params);
    }

    /**
     * Get the resource by the put method
     *
     * @param string $path []
     * @return object
     */
    public function put($path, $params = [])
    {
        return $this->_request('PUT', $path, $params);
    }

    /**
     * Request resource by the request method
     *
     * @param string $method
     * @param string $path
     * @param array  $params
     * @param array  $options
     *
     * @return object
     * @throws AwsConnectCloudException
     */
    private function _request($method, $path, $params = [], $options = [])
    {
        $params  = array_merge($this->params, is_array($params) ? $params : []);
        $options = !empty($options) ? $options : ['json' => $params];

        log_message('debug', 'API Request: ' . $path . PHP_EOL . json_encode($params + $options));

        $response = $this->client->request($method, $path, $options);

        $body = $response->getBody();
        $result = json_decode($body);

        if (400 <= $statusCode = $response->getStatusCode()) {
            log_message('error', 'API Response: ' . $statusCode . PHP_EOL . $body);

            $errorCode = isset($result->common) && isset($result->common->error_code) ? $result->common->error_code : null;
            throw new AwsConnectCloudException($statusCode, $path, $errorCode);
        }

        return $result;
    }

    /**
     * Get the cache key by path and parameters
     *
     * @param string $path   []
     * @param array  $params []
     * @return string
     */
    public function cacheKey($path, $params = [])
    {
        return md5(sprintf("%s.%s", $path, json_encode($params)));
    }

    /**
     * Set the default parameters
     *
     * @param array $params []
     * @return void
     */
    private function setDefaultParams($params)
    {
        $this->params = [
            'common' => $params,
        ];
    }

    /**
     * TODO: Mock from the post method from Connect Cloud
     *
     * @param  string $path
     * @param  array $params
     * @param  bool $ok
     * @return array
     */
    public function mockPost($path, $params = [], $isFailed = true)
    {
        log_message('info', 'Connect API Request: ' . $path . PHP_EOL . json_encode($params));

        // success
        return [
            'headers' => [
                "Content-Type"        => "application/json",
                "cache-control"       => "no-cache",
                "Connect-Cloud-Token" => "fdc617fa-8518-4af9-9220-b2647c08e72c",
                "User-Agent"          => "User Agent Connect Cloud",
                "Accept"              => "*/*",
                "Host"                => "host_connect_cloud",
                "cookie"              => "cookie_connect_cloud=pvhmlp0auicrtfbcuc6hqsm0tuv6hvti",
                "accept-encoding"     => "gzip,deflate",
                "Connection"          => "keep-alive",
            ],
            'response' => [
                'status'  => $isFailed === false,
                'message' => $isFailed ? 'Push notification to Connect Cloud failed.' : 'This is the stub message from Connect Cloud',
            ]
        ];
    }
}
