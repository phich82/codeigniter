<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$prefix = 'api/v1';
$route[$prefix.'/test']  = 'ApiController/index';
$route[$prefix.'/orders/(:id)']['post']  = 'ApiController/posts/$1';
