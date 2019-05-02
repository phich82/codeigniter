<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\AwsConnectCloud;

require_once APPPATH.'Api/AwsConnectCloud.php';

/**
 * For dependency injection (IoC)
 */
class Aws_connect_cloud extends AwsConnectCloud
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->driver('cache');

        parent::__construct($CI->config->item('aws_connect_cloud'), $CI->cache);
    }
}
