<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$prefix = 'api/v1';
$route[$prefix.'/test']  = 'ApiController/index';
$route[$prefix.'/orders/(:id)']['post']  = 'ApiController/posts/$1';
$route[$prefix.'/posts']  = 'ApiController/posts';
$route[$prefix.'/posts_async']  = 'ApiController/post_async';
$route[$prefix.'/all']  = 'ApiController/all';

/**
 * The Restful APIs of RSV Cloud provider
 */
$route['api/v2/orderlist/orders_sales'] = 'api/ApiRsvCloudController/createOrdersPost';
$route['api/v2/orderlist/orders_list'] = 'api/ApiRsvCloudController/getOrdersPost';
$route['api/v2/orderlist/store_pos_info'] = 'api/ApiRsvCloudController/savePosInfoPost';
$route['api/v2/orderlist/push_info'] = 'api/ApiRsvCloudController/createPushPost';
$route['api/v2/upload_logs'] = 'api/ApiUploadController/uploadLogsPost';

$route['api/v2/orderlist/add_reserve'] = 'api/ApiRsvCloudController/addReservePost'; // for test
$route['api/v2/orderlist/update_status'] = 'api/ApiRsvCloudController/updateOrderStatusPost'; // for test
$route['api/v2/orderlist/kitchen_print_result'] = 'api/ApiRsvCloudController/getKitchenPrintResultPost'; // for test

$route['api/v2/orderlist/healthcheck'] = 'api/ApiRsvCloudController/checkHealthPost';
// $route['api/v2/orderlist/push_order'] = 'api/ApiRsvCloudController/sendOrdersToServicesPost';
// $route['api/v2/orderlist/order_update'] = 'api/ApiRsvCloudController/updateOrdersPost';
