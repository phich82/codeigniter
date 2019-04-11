<?php
namespace App\Api\Contracts;

interface AwsCloudContract
{
    public function request($path, $params = []);
    public function requestCache($path, $params = [], $keyCache = null);
    public function cacheKey($path, $params = []);
}
