<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use application\Api\ApiBase;

require_once APPPATH.'Api/Api.php';

class Api extends ApiBase
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

        parent::__construct($CI->config->item('customer'), $CI->cache);
    }
}
