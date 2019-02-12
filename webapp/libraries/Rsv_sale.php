<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\RsvSale;

require_once APPPATH.'Api/RsvSale.php';

class Rsv_sale extends RsvSale
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

        parent::__construct($CI->config->item('rsv_sale'), $CI->cache);
    }
}
