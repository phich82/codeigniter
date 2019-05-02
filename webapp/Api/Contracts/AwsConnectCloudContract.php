<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Contracts;

/**
 * Get the resource from the restful APIs (providers)
 */
interface AwsConnectCloudContract
{
    public function request($path, $params = []);
    public function requestCache($path, $params = [], $keyCache = null);
    public function cacheKey($path, $params = []);
}
