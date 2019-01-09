<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$suffix = 'api/v1';
$route[$suffix.'/test']  = 'ApiController/index';
$route[$suffix.'/orders/(:id)']['post']  = 'ApiController/posts/$1';
