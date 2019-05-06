<?php

/*
|--------------------------------------------------------------------------
| The config parameters for Connect Cloud
|--------------------------------------------------------------------------
|
*/
$config['aws_connect_cloud'] = [
    'base_uri' => 'http://192.168.123.238:8282'
];

/*
|--------------------------------------------------------------------------
| API Key Name
|--------------------------------------------------------------------------
|
*/
$config['api_key_name'] = 'X-API-KEY';

/*
|--------------------------------------------------------------------------
| Response Key Names
|--------------------------------------------------------------------------
|
*/
$config['response_result_key'] = 'result_status';
$config['response_error_key'] = 'error_message';
$config['response_message_key'] = 'message';
$config['response_data_key']  = 'data';
$config['response_error_code_key'] = 'error_key';
$config['response_success_status'] = 0;
$config['response_warning_status'] = 5;
$config['response_error_status'] = 9;
$config['response_success_text'] = 'success';
$config['response_failed_text']  = 'failed';

$config['response_order_result_key']  = 'order_result';
$config['response_failed_orders_key'] = 'failed_orders';
$config['validation_error_message_key'] = 'messages';
$config['validation_errors_key'] = 'validation_errors';
$config['api_default_error_message'] = 'Error occured.';
$config['api_default_success_message'] = 'Success.';
$config['api_default_exist_message'] = 'Already existed.';
$config['api_response_format'] = 'application/json';
$config['response_update_status_key'] = 'update_status';


/*
|--------------------------------------------------------------------------
| The path to the validation files
|--------------------------------------------------------------------------
|
*/
$config['requests_folder_path'] = APPPATH.'controllers/apis/requests/';
$config['request_class_suffix'] = 'Request';

/*
|--------------------------------------------------------------------------
| The maximum records will returned
|--------------------------------------------------------------------------
|
*/
$config['max_page_total'] = 100;
$config['default_page'] = 1;

/*
|--------------------------------------------------------------------------
| HTTP protocol
|--------------------------------------------------------------------------
|
| Set to force the use of HTTPS for REST API calls
|
*/
$config['api_force_https'] = FALSE;

/*
|--------------------------------------------------------------------------
| CORS Check
|--------------------------------------------------------------------------
|
| Set to TRUE to enable Cross-Origin Resource Sharing (CORS). Useful if you
| are hosting your API on a different domain from the application that
| will access it through a browser
|
*/
$config['api_check_cors'] = TRUE;

/*
|--------------------------------------------------------------------------
| CORS Allowable Headers
|--------------------------------------------------------------------------
|
| If using CORS checks, set the allowable headers here
|
*/
$config['api_allowed_cors_headers'] = [
    'Origin',
    'X-Requested-With',
    'Content-Type',
    'Accept',
    'Access-Control-Request-Method',
    'header',
    'company_code',
    'store_code',
    'X-API-KEY'
];

/*
|--------------------------------------------------------------------------
| CORS Allowable Methods
|--------------------------------------------------------------------------
|
| If using CORS checks, you can set the methods you want to be allowed
| List methods: 'GET', 'POST', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'
|
*/
$config['api_allowed_cors_methods'] = ['GET', 'POST', 'OPTIONS'];

/*
|--------------------------------------------------------------------------
| CORS Allow Any Domain
|--------------------------------------------------------------------------
|
| Set to TRUE to enable Cross-Origin Resource Sharing (CORS) from any
| source domain
|
*/
$config['api_allow_any_cors_domain'] = TRUE;

/*
|--------------------------------------------------------------------------
| CORS Allowable Domains
|--------------------------------------------------------------------------
|
| Used if $config['check_cors'] is set to TRUE and $config['allow_any_cors_domain']
| is set to FALSE. Set all the allowable domains within the array
|
| e.g. $config['allowed_origins'] = ['http://www.example.com', 'https://spa.example.com']
|
*/
$config['api_allowed_cors_origins'] = [];

$config['file_validate_rules'] = [
    'upload_path'   => APPPATH.'..'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'rsv'.DIRECTORY_SEPARATOR,
    'allowed_types' => 'zip|gz',
    'remove_spaces' => TRUE,
    'rename'        => '', // if want fixed new name for all file, such as time()
    'ext'           => '.zip', // use this for extension rename
    'log_file_name' => 'logs_file',
    'max_size'      => (1024 * 2), // 2M
    'mode_path'     => DIR_READ_MODE
];

$config['ignore_token'] = false;
//$config['api_existed_record_value'] = EXISTED_RECORD_VALUE;
