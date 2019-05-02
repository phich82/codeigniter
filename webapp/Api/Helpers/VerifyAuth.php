<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Helpers;

class VerifyAuth
{
    /**
     * Verify the autentication for restful api
     *
     * @param array $params
     *
     * @return bool
     */
    public static function verify($params = [])
    {
        if (empty($params) || !isset($params['company_code']) || !isset($params['store_code'])) {
            ApiLog::info('['.__FUNCTION__.'] ===> Invalid parameters: '.json_encode($params));
            return false;
        }

        $CI =& get_instance();
        $CI->load->library('rsv/Rsvencryption');
        $CI->load->config('api');
        $apiKeyName = $CI->config->item('api_key_name');

        if (!isset($params[$apiKeyName])) {
            ApiLog::info('['.__FUNCTION__.'] ===> Invalid parameters: the key ['.$apiKeyName.'] does not exist.');
            return false;
        }

        try {
            $hashed = $CI->rsvencryption->createApiKey($params['company_code'], $params['store_code']);
            return $hashed === $params[$apiKeyName];
        } catch (Exception $e) {
            ApiLog::info('['.__FUNCTION__.'] ===> Encyption failed: ['.$e->getMessage().']');
            return false;
        }
    }

}
