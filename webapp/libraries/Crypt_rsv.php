<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'Api/CryptRsv.php';

use App\Api\CryptRsv;


class Crypt_rsv extends CryptRsv
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $CI =& get_instance();

        if (empty($key = $CI->config->item('app_key'))) {
            throw new RuntimeException('No application encryption key has been specified.');
        }

        parent::__construct(base64_decode($key), $CI->config->item('cipher'));
    }
}
