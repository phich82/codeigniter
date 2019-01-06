<?php

namespace App\Api;

use GuzzleHttp\Client;
use application\Api\Contracts\VipApiContract;

class Vip implements VipApiContract
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * Ana constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'http_errors' => false,
            'timeout' => 30.0,
            'auth' => [
                $this->config['basic_user'],
                $this->config['basic_pwd'],
            ],
            'verify' => false,
        ]);
    }

    /**
     * Redirect to login page
     *
     * @param string $urlFail
     * @param string $urlOk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login($urlFail = null, $urlOk = null)
    {
        $url = $this->config['base_uri'] . 'login_cooperation/psgwLoginJa.jsp';

        $authUrl = $url . '?' . http_build_query([
                'ssoProduct' => $this->config['sso_product'],
                'url-ok' => $urlOk ?: url($this->config['url_ok']),
                'url-fail' => $urlFail ?: url($this->config['url_fail']),
            ]);

        return redirect($authUrl);
    }
}
