<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api;

require_once APPPATH.'helpers/constant.php';
require_once APPPATH.'Api/Contracts/RsvSaleContract.php';
require_once APPPATH.'Api/Exceptions/RsvSalesException.php';

use GuzzleHttp\Client;
use App\Api\Contracts\RsvSaleContract;
use App\Api\Exceptions\RsvSalesException;

class RsvSale implements RsvSaleContract
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var array
     */
    protected $params;
    /**
     * The cache instance
     * @var CI_Cache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param array    $config []
     * @param CI_Cache $cache  []
     */
    public function __construct($config, $cache = null)
    {
        $this->client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com',//$config['base_uri'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
            'timeout' => 60.0,
            'verify' => FCPATH.'cert/cacert.pem',
        ]);

        $this->setDefaultParams($config['common']);
        $this->cache = $cache;
    }

    /**
     * @implement
     * Request the resource without cache
     *
     * @param  string $path   []
     * @param  array  $params []
     * @return object         []
     * @throws GuzzleException
     */
    public function request($path, $params = [])
    {
        $params = array_merge($this->params, $params);
        log_message('debug', 'API Request: ' . $path . PHP_EOL . json_encode($params));
        $response = $this->client->request('POST', $path, ['json' => $params]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (400 <= $statusCode = $response->getStatusCode()) {
            log_message('error', 'API Response: ' . $statusCode . PHP_EOL . $body);

            throw new RsvSalesException($statusCode, $path, $result->common->error_code ?? null);
        }

        return $result;
    }

    /**
     * @implement
     * Request the resource and cache it
     *
     * @param string $path     []
     * @param array  $params   []
     * @param string $cacheKey []
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
     * Get the resource by path
     *
     * @param string $path []
     * @return object
     */
    public function get($path)
    {
        $response = $this->client->get($path);
        return json_decode($response->getBody(), true);
    }

    /**
     * Get the resource by path
     *
     * @param string $path []
     * @return object
     */
    public function post($path, $params = [])
    {
        $params = array_merge($this->params, $params);
        log_message('debug', 'API Request: ' . $path . PHP_EOL . json_encode($params));
        $response = $this->client->post($path, ['json' => $params]);
        return json_decode($response->getBody()->getContents(), true);
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
}
