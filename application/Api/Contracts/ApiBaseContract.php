<?php
namespace application\Api\Contracts;

interface ApiBaseContract
{
    public function request($path, $params = []);
    public function requestCache($path, $params = [], $keyCache = null);
    public function cacheKey($path, $params = []);
}
