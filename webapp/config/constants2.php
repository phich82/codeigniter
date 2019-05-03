<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/* define HTTP Prototype */
define('RSV_HTTP_REQUEST_TYPE_GET',     'GET');
define('RSV_HTTP_REQUEST_TYPE_POST',    'POST');
define('RSV_HTTP_REQUEST_TYPE_PUT',     'PUT');
define('RSV_HTTP_REQUEST_TYPE_DELETE',  'DELETE');
define('RSV_HTTP_HEADER_TYPE_JSON',     'json');
define('RSV_HTTP_HEADER_TYPE_HTML',     'html');
define('RSV_HTTP_HEADER_TYPE_XML',      'xml');

/*Mobile scheme*/
define('MOBILE_TO_ACTIVATE',       'pos-activate://');
define('MOBILE_TO_RECONNECT',      'pos-reconnect://');
define('MOBILE_DOWNLOAD_PDF',      'pos-download-pdf://');
define('MOBILE_TO_LOGIN',          'pos-to-login://');

/*Mobile Methods - Javascript */
define('M_NOTIFY_LOGGED_IN', 'rsv-notify-logged-in');
define('M_NOTIFY_LOGGED_OUT', 'rsv-notify-logged-out');

define('RSV_API_ACTIVATION_STATUS_ACTIVE' , 1);
define('RSV_API_ACTIVATION_STATUS_NOT_ACTIVE' , 0);

define('RSV_ERROR_COMPANY_CODE_NOT_FOUND',              1);
define('RSV_ERROR_STORE_NO_NOT_FOUND',                  2);
define('RSV_ERROR_POS_DATA_NOT_FOUND',                  3);

/* define Error code Activation */
define('RSV_ERROR_ACTIVATION_DATA_NOT_FOUND',           4);
define('RSV_ERROR_OPE_ID_NOT_FOUND',                    5);
define('RSV_ERROR_ACTIVATION_IS_EXPIRED',               6);
define('RSV_ERROR_INVALID_DATA',                        7);
define('RSV_ERROR_DEACTIVATION_FAILED',                 8);
define('RSV_ERROR_ACTIVATION_FAILED',                   9);
define('RSV_ERROR_ACTIVATION_FOR_SAME_STORE_EXIST',     10);
define('RSV_ERROR_ACTIVATION_BY_ANOTHER_STORE',     15);

/*Refresh Token*/
define('RSV_ERROR_COULD_NOT_REFRESH_TOKEN',             11);
/*RSV Table*/
define('RSV_ERROR_TABLE_NAME_NOT_FOUND',                12);
/*RSV Master*/
define('RSV_ERROR_COULD_NOT_GET_MASTER',                13);
/*RSV Notification*/
define('RSV_ERROR_COULD_NOT_SEND_NOTIFICATION',         14);

/* Define API status */
define('RSV_API_STATUS_SUCCESS',                    0);
define('RSV_API_STATUS_FAILED_NOT_UPDATED',         5);
define('RSV_API_STATUS_FAILED_OTHERS',              9);

define('RSV_API_RESPONSE_SUCCESS',                  200);
define('RSV_API_ERROR_CODE_NO_COOPERATION',         1);
define('RSV_API_ERROR_CODE_INVALID_DATA',           2);
define('RSV_API_ERROR_CODE_UNDEFINED_CODE',         3);
define('RSV_API_ERROR_CODE_SYSTEM_EXCEPTION',       99);
define('RSV_API_USER_PFR',                          'PFR');

/* SQL Execution type */
define('RSV_SQL_EXECUTION_TYPE_SELECT_SINGLE_ROW' ,                     1);         // apc cache
define('RSV_SQL_EXECUTION_TYPE_SELECT_MULTIPLE_ROWS' ,                  2);         // memcached cache

/* SQL Definition */
define('RSV_DATABASE_TABLE_COLUMN_TYPE_AUTO_INCREMENT',                 1);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_UNIQUE',                         2);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_EXISTED',                        3);         // use to check if value of column is existed
define('RSV_DATABASE_TABLE_ROW_INSERT_DELETE_FLAG_DEFAULT_VALUE',       0);
define('RSV_DATABASE_TABLE_ROW_SAFE_DELETE',                            1);
define('RSV_DATABASE_TABLE_ROW_INSERT_DATETIME_NOW',                    1);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_NOT_NULL',                       3);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_ALLOW_ZERO',                     4);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_INTEGER',                        5);
define('RSV_DATABASE_TABLE_COLUMN_TYPE_STRING',                         6);
define('RSV_DB_DEFAULT_VALUE_MENU_PRICE',                               0);
define('RSV_DB_DEFAULT_VALUE_VISIT_COUNT',                              0);
define('RSV_DB_DEFAULT_VALUE_AVG_SALES',                                0);
define('RSV_DB_DEFAULT_VALUE_SMOKING_TYPE',                             0);
define('RSV_DB_DEFAULT_VALUE_WALKIN_FLG',                               0);
define('RSV_DB_DEFAULT_VALUE_STAR_FLG',                                 0);
define('RSV_DB_DEFAULT_VALUE_GENDER_NO',                                3);
define('RSV_DB_DEFAULT_VALUE_PAYMENT_CODE',                          '00');
define('RSV_DB_DEFAULT_VALUE_PRIV_ROOM_TYPE',                           0);
define('RSV_DB_DONE_SYNC_FLG',                                          1);
define('RSV_DB_AUTO_SEAT_FLG_ON',                                       1);

// define error code
define('RSV_ERROR_CODE_OK', 0);
define('RSV_ERROR_CODE_TABLE_COLUMN_NOT_EXISTED',           1);
define('RSV_ERROR_CODE_COLUMN_NOT_PRE_DEFINED_GENERIC_DAO', 2);
define('RSV_ERROR_CODE_TABLE_COLUMN_NOT_ALLOW_NULL',        3);
define('RSV_ERROR_CODE_TABLE_COLUMN_ACCEPT_INTEGER',        4);
define('RSV_ERROR_CODE_TABLE_COLUMN_ACCEPT_STRING',         5);
define('RSV_ERROR_CODE_USER_NOT_EXISTED',                   6);
define('RSV_ERROR_CODE_PASSWORD_NOT_MATCH',                 7);
define('RSV_ERROR_CODE_CONFIRM_PASSWORD_NOT_MATCH',         8);
define('RSV_ERROR_CODE_USER_EMPTY',                         9);
define('RSV_ERROR_CODE_TABLE_COLUMN_NOT_UNIQUE',            10);
define('RSV_ERROR_CODE_USER_PASS_EMPTY',                    11);
define('RSV_ERROR_CODE_FAILED_TO_DELETE',                   12);
define('RSV_ERROR_CODE_USER_ID_NOT_AVAILABLE',              13);

// define common constants
define('RSV_COMMON_DATATYPE_STRING',            'string');
define('RSV_COMMON_DATATYPE_INTEGER',           'integer');
define('RSV_COMMON_USER_GENDER_MALE',           1);
define('RSV_COMMON_USER_GENDER_FEMALE',         2);
define('RSV_COMMON_USER_GENDER_UNKNOWN',        3);
define('RSV_SQL_BLACKLIST_CHARACTERS',          '<>\'&|%');
define('RSV_UAC_NOT_ALLOW_ALL_ACTIONS',            '*');


/* Language Translator Configuration */
define('RSV_CONFIG_LANGUAGE_PROVIDER_FILE',                             1);
define('RSV_CONFIG_LANGUAGE_PROVIDER_DATABASE',                         2);

define('RSV_CONFIG_LANGUAGE_SUPPORT_ENGLISH',                           'eng');
define('RSV_CONFIG_LANGUAGE_SUPPORT_JAPANESE',                          'jpn');

define('RSV_STATUS_AWAITING', 1);
define('RSV_STATUS_OCCUPIED', 2);
define('RSV_STATUS_PAID', 4);
define('RSV_STATUS_APPEARED', 2); // Not use
define('RSV_STATUS_FINISHED', 4); // Not use
define('RSV_STATUS_CANCELLED', 6); // Not use
define('RSV_STATUS_NOTSHOW', 7); // Not use

define('RSV_ACTION_STATUS_NEW', 'new');
define('RSV_ACTION_STATUS_EDIT', 'edit');
define('RSV_ACTION_STATUS_DELETE', 'delete');
define('RSV_ACTION_STATUS_UPDATE', 'update');
define('RSV_ACTION_STATUS_CANCEL', 'cancel');
define('RSV_ACTION_STATUS_REMOVE', 'remove');
define('ONE_WEEK_DURATION_TIME', '+1 week');

define('RESULT_CODE_FAIL', '1');
define('RESULT_CODE_SUCCESS', '0');

/* business date of reservation */
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_HOLIDAY_VALUE', '-1');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_MONDAY_VALUE', '0');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_TUESDAY_VALUE', '1');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_WEDNESDAY_VALUE', '2');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_THURSDAY_VALUE', '3');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_FRIDAY_VALUE', '4');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_SATURDAY_VALUE', '5');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_SUNDAY_VALUE', '6');

define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_HOLIDAY', 'HOLIDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_MONDAY', 'MONDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_TUESDAY', 'TUESDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_WEDNESDAY', 'WEDNESDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_THURSDAY', 'THURSDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_FRIDAY', 'FRIDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_SATURDAY', 'SATURDAY');
define('RSV_CONFIG_PHP_JS_RSV_BUSINESS_DAY_SUNDAY', 'SUNDAY');

define('RSV_STATUS_DAY_BUSSINESS', '1'); // same 'code' value on mst_multilingual_sys with code_status='DAY_STATUS'
define('RSV_STATUS_DAY_NONE_BUSSINESS', '2'); // same 'code' value on mst_multilingual_sys with code_status='DAY_STATUS'

// define maximum Store, Store Group, Floor, Table, Table Group, Staff, Course, Role
define('STORE_MAX', 500);
define('STORE_GROUP_MAX', 100);
define('FLOOR_MAX', 50);
define('TABLE_MAX', 500);
define('TABLE_GROUP_MAX', 100);
define('STAFF_MAX', 500);
define('COURSE_MAX', 200);
define('ROLE_MAX', 50);
define('CUSTOMER_MAX',1000);

define('G_SYNC_FLG_STOP' , 0);
define('G_SYNC_FLG_START' , 1);
define('NO_STORE' , -1);

define('DATE_FORMAT', 'Y/m/d');

// define default information expiration
define('INFORMATION_DEFAULT_PERIOD_END', '2999/12/31');

// define default user expiration
define('STAFF_DEFAULT_EXPIRATION', '29991231');

// define company rank
define('COMPANY_RANK_SUPER', '0');
define('COMPANY_RANK_ADMIN', '1');
define('COMPANY_RANK_AGENCY', '2');
define('COMPANY_RANK_CLIENT', '3');

define('CUSTOMER_HEADER_COUNT', 20);
define('STAFF_HEADER_COUNT', 10);
//define('TABLE_HEADER_COUNT', 9);
define('TABLE_HEADER_COUNT', 8); //for hide auto_seat_flg

//Static path
define('PATH_CUSTOMER_UPLOAD','customer/customer_upload');
//$_distPath = (defined('ENV_PRODUCT') && (ENV_PRODUCT == 1)) ? '/dist' : '';
$_distPath = '';
define('PATH_CSS', $_distPath . "/css/");
define('PATH_JS', $_distPath . "/js/");
define('PATH_IMG', $_distPath . "/img/");
// Rsv history remark
define('RSV_REMARK_LIMIT', 40);

// define default business hours time
define('LUNCH_START_TIME', '1000');
define('LUNCH_END_TIME', '1400');
define('DINNER_START_TIME', '1700');
define('DINNER_END_TIME', '2200');

// define default line to load more
define('LIMIT_LOAD_MORE', 30);
define('OFFSET_LOAD_MORE', 0);
// paging
define('DEFAULT_PAGING_LIMIT', 30);
define('DEFAULT_PAGING_OFFSET', 0);

define('FILTER_ALL',0);
define('FILTER_WAITING_LIST',1);

define('LOGIN_RETRY_LIMIT_FOR_ADMIN', 10);
define('ACCOUNT_LOCK_TIME_ADMIN', 15);
define('APP_STATUS_ADMIN', '1');

define('USER_ID_OWNER', 'owner');
define('ROLE_NO_OWNER', '1');
define('SYSTEM_ID', 'SYSTEM');
define('POS_ID', 'POS');
define('MAINTE_USER_ID', 'mainte');
define('MAINTE_USER_NAME', '保守者');
define('MAINTE_USER_PASS', 'Regi1802');

define('DEFAULT_PASSWORD', 'rsv12345678');

define('USER_ID_MGR_PREFIX', 'mgr');
define('ROLE_NO_STORE_MANAGER', '2');

define('USER_ID_DEFAULT_STAFF_PREFIX', 'staff');
define('ROLE_NO_DEFAULT_STAFF', '4');

#For Remove Cache Css and Js
define('DEPLOY_VER',"v1.6.5");
define('SESSION_DEBUG', '0');
define('DEBUG', '0');

define('RSV_VERSION', '1.0.0');
define('MEDIA_URL', 'http://designstub.com/demos/rooky/');

define('UNDELETE_FLAG_VALUE', 0);
define('DELETE_FLAG_VALUE', 1);
define('LUNCH_TIME_VALUE', 0);
define('DINNER_TIME_VALUE', 1);
define('DEFAULT_SENT_STATUS', 1);
define('UNSENT_TO_VALUE', 0);
define('DEFAULT_SENT_STATUS_VALUE', 0);
define('DB_USER_VALUE', 'SYSTEM');
define('DEFAULT_DELIVERY_STATUS', 0);
define('DEFAULT_ORDER_STATUS', 0);
define('DEFAULT_PAGE', 1);
define('NO_IS_TODAY', 0);
define('DEFAULT_CONNECT_POS_STATUS', 1); // 1: disconnected
define('NO_SENT_TO', 0); // Not sent
define('SENT_TO_POS', 1); // Sent to POS+
define('SENT_TO_POS_FAILED', 2); // Sent to POS+ failed
define('SENT_TO_CONNECT_CLOUD', 1);
define('SENT_TO_POS_PLUS', 2);
define('SENT_TO_KITCHEN_PRINTER', 3);
define('CONNECT_CLOUD_NOT_RESPONSE', 0);
define('CONNECT_CLOUD_RESPONSE_FAILED', 1);
define('CONNECT_CLOUD_RESPONSE_SUCCESS', 2);
define('ORDER_STATUS_RESERVED', 0);
define('ORDER_STATUS_CONFIRMED', 1);
define('ORDER_STATUS_COOKING', 2);
define('ORDER_STATUS_COMPLETED', 3);
define('ORDER_STATUS_UNRECEIVED', 4);
define('ORDER_STATUS_CANCELLED', 5);
define('POS_RELEASE_VERSION', '1');
define('ORDER_SALES_TEXT', 'order_sales');
define('RSV_SALES_VALUE', '01');
define('DEMAEKAN_VALUE', '02');
define('FROM_RSV_TEXT', 'RSV');
define('ADD_RESERVE_TEXT', 'add_reserve');
define('UPDATE_STATUS_TEXT', 'status');
define('UPDATE_RECEIVE_DATE_TEXT', 'receive_date');
define('PRINT_STATUS_NO', 0);
define('PRINT_STATUS_SUCCESS', 1);
define('PRINT_STATUS_FAIL', 2);
define('DEFAULT_VOUCHER_FROM_SYS', 1);
define('RSV_ORDER_STATUS_RESERVED', '00');
define('RSV_ORDER_STATUS_ORDERED', '10');
define('RSV_ORDER_STATUS_SERVED', '20');
define('RSV_ORDER_STATUS_CANCELLED', '30');
define('DEFAULT_STORE_LANG_CODE', 'eng');
define('PUSH_NOTIFICATION_TITLE_EN', 'There is a new message');
define('PUSH_NOTIFICATION_TITLE_JP', '新しいメッセージがあります');
define('PUSH_NOTIFICATION_DESC_EN', 'It\'s almost time to receive food or beverages.');
define('PUSH_NOTIFICATION_DESC_JP', '商品の受け取り時間が近づいています');

/**
 * Define constant for APIs | Self Orders
 */
define('ROWS_PER_PAGE', 1000);
define('BEFORE_RECEIVING_TIME', 15);
