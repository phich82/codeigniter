<?php

require_once(dirname(__FILE__) . '/../../Api/Helpers/HttpCode.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/Response.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/ApiLog.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/VerifyAuth.php');

use App\Api\Helpers\HttpCode;
use App\Api\Helpers\Response;
use App\Api\Helpers\ApiLog;
use App\Api\Helpers\VerifyAuth;

abstract class ApiController extends CI_Controller
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->config('api');

        $this->response = new Response();

        $this->response_error_key   = $this->config->item('response_error_key');
        $this->response_message_key = $this->config->item('response_message_key');
        $this->response_result_key  = $this->config->item('response_result_key');
        $this->response_data_key    = $this->config->item('response_data_key');
        $this->response_error_code  = $this->config->item('response_error_code_key');
        $this->response_order_result_key  = $this->config->item('response_order_result_key');
        $this->response_failed_orders_key = $this->config->item('response_failed_orders_key');
        $this->response_update_status_key = $this->config->item('response_update_status_key');
        $this->success_status = $this->config->item('response_success_status');
        $this->warning_status = $this->config->item('response_warning_status');
        $this->error_status   = $this->config->item('response_error_status');
        $this->response_success_text = $this->config->item('response_success_text');
        $this->response_failed_text  = $this->config->item('response_failed_text');
        $this->validation_error_message_key = $this->config->item('validation_error_message_key');
        $this->validation_errors_key        = $this->config->item('validation_errors_key');
        $this->api_default_error_message    = $this->config->item('api_default_error_message');
        $this->api_default_success_message  = $this->config->item('api_default_success_message');
        $this->api_default_exist_message    = $this->config->item('api_default_exist_message');
        $this->api_mapping_dynamic_keys     = $this->config->item('api_mapping_dynamic_keys');
        $this->api_existed_record_value     = $this->config->item('api_existed_record_value');
        $this->requests_folder_path         = $this->config->item('requests_folder_path');

        // Check for CORS access request
        if ($this->config->item('api_check_cors') === true) {
            $this->_cors();
        }
    }

    /**
     * @override
     * _remap
     *
     * Notes: The request class must be the method name without suffix (the request method) plus 'Request'
     * Ex: If the method name in controller is getOrdersPost, the name of the request class will be getOrdersRequest
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function _remap($method, $args = []) {
        $segments = $this->uri->segment_array();
        // remove the method suffix if any
        $methodNoSuffix = preg_replace('/(.*)(post|get|put|delete|patch|head|options)/i', '$1', $method);

        // append the new suffix to it if it has the suffix
        if (strlen($methodNoSuffix) !== strlen($method)) {
            $method = $methodNoSuffix.ucfirst($this->input->method());
        }

        // the request method not allowed, show the error
        if (!method_exists($this, $method)) {
            ApiLog::info('Method ['.$method.'] not exist');
            return $this->response->json([
                $this->response_result_key => $this->error_status,
                $this->response_error_key  => HttpCode::message(HttpCode::HTTP_METHOD_NOT_ALLOWED),
            ], HttpCode::HTTP_OK);
        }

        // track headers of the incoming request
        ApiLog::info('Headers parameters from the incoming request: '.json_encode($this->_headers()));

        // validate json format from the incomming request
        if ($segments[3] != 'upload_logs') {
            $errorsJson = $this->_validateJsonFormat($this->input->raw_input_stream);
            // if ($errorsJson !== true) {
            //     return $this->response->json([
            //         $this->response_result_key => $this->error_status,
            //         $this->response_error_key  => [
            //             $this->response_message_key  => HttpCode::message(HttpCode::HTTP_BAD_REQUEST),
            //             $this->validation_errors_key => $errorsJson
            //         ],
            //     ], HttpCode::HTTP_OK);
            // }
        }

        $ignoreToken = isset($segments[4]) && ( $segments[4] == 'orders_list' || $segments[3] == 'upload_logs');
        // verify the authentication
        // if (!$ignoreToken && !$this->config->item('ignore_token') && !VerifyAuth::verify(array_merge($this->_body(), $this->_headers()))) {
        //     return $this->response->json([
        //         $this->response_result_key => $this->error_status,
        //         $this->response_error_key  => HttpCode::message(HttpCode::HTTP_UNAUTHORIZED)
        //     ], HttpCode::HTTP_OK);
        // }

        // validate the request
        $class_request = ucfirst($methodNoSuffix.$this->config->item('request_class_suffix'));
        $file_request  = $this->requests_folder_path.$class_request.'.php';


        // track the incoming request
        if ($segments[3] != 'upload_logs') {
            ApiLog::info('The incoming request: '.$this->input->raw_input_stream);
        }

        // check the exist of the request class if any
        if (file_exists($file_request)) {
            // import the request class
            include_once $file_request;

            $data = $this->input->raw_input_stream;
            // validate the input
            $validator = new $class_request(array_merge(
                json_decode($data, true) ?: [],
                $this->_headers()
            ));
            if ($segments[3] != 'upload_logs') {
                ApiLog::info('Receive data:', $data);
            }

            // error
            if ($validator->hasError()) {
                ApiLog::info('['.__FUNCTION__.'] Validation errors: ===================>'.json_encode($validator->error()));

                return $this->response->json([
                    $this->response_result_key => $this->error_status,
                    $this->response_error_key  => [
                        $this->response_message_key  => HttpCode::message(HttpCode::HTTP_BAD_REQUEST),
                        $this->validation_errors_key => $validator->error()
                    ],
                ], HttpCode::HTTP_OK);
            }
        } else {
            ApiLog::info('File ['.$file_request.'] not exist. But it is ignored.');
        }

        // the request method allowed
        return call_user_func_array([$this, $method], $args);
    }

    /**
     * Checks allowed domains, and adds appropriate headers for HTTP access control (CORS)
     *
     * @access protected
     * @return void
     */
    private function _cors()
    {
        // Convert the config items into strings
        $allowed_headers = implode(', ', $this->config->item('api_allowed_cors_headers'));
        $allowed_methods = implode(', ', $this->config->item('api_allowed_cors_methods'));

        // If we want to allow any domain to access the API
        if ($this->config->item('api_allow_any_cors_domain') === true) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: '.$allowed_headers);
            header('Access-Control-Allow-Methods: '.$allowed_methods);
        } else {
            // We're going to allow only certain domains access
            // Store the HTTP Origin header
            $origin = $this->input->server('HTTP_ORIGIN');
            if ($origin === null) {
                $origin = '';
            }

            // If the origin domain is in the allowed_cors_origins list, then add the Access Control headers
            if (in_array($origin, $this->config->item('api_allowed_cors_origins'))) {
                header('Access-Control-Allow-Origin: '.$origin);
                header('Access-Control-Allow-Headers: '.$allowed_headers);
                header('Access-Control-Allow-Methods: '.$allowed_methods);
            }
        }

        // If the request HTTP method is 'OPTIONS', kill the response and send it to the client
        if ($this->input->method() === 'options') {
            exit;
        }
    }

    /**
     * Get all parameters from the request header
     *
     * @return array
     */
    protected function _headers()
    {
        $headers = $this->input->request_headers();
        $keyApi  = $this->config->item('api_key_name');
        $keyApiLower = strtolower($keyApi);
        $keyApiFirstCase = implode('-', array_map('ucfirst', explode('-', $keyApiLower)));
        if (isset($headers[$keyApiLower])) {
            $headers[$keyApi] = $headers[$keyApiLower];
        } elseif (isset($headers[$keyApiFirstCase])) {
            $headers[$keyApi] = $headers[$keyApiFirstCase];
        }
        return $headers;
    }

    /**
     * Get all parameters from the request body
     *
     * @return array
     */
    protected function _body()
    {
        return json_decode($this->input->raw_input_stream, true) ?: [];
    }

    /**
     * Create array of the arguments
     *
     * @return array
     */
    protected function _arguments()
    {
        return [$this->_body(), $this->_headers()];
    }

    /**
     * Validate the json format
     *
     * @param  string $input
     * @return bool|array
     */
    private function _validateJsonFormat($input)
    {
        $input = $input ?: $this->input->raw_input_stream;
        json_decode($input);

        if (json_last_error() == JSON_ERROR_NONE) {
            return true;
        }

        $errors = [];
        foreach ($this->patterns_json_error() as $pattern) {
            preg_match_all($pattern, $input, $matches);
            foreach ($matches as $match) {
                $errors = array_unique(array_merge($errors, $match));
            }
            if (!empty($errors)) {
                $errorKeys = array_map(function ($error) {
                    return trim(preg_replace('/\s\s+/', '', $error));
                }, $errors);

                return [
                    'error_message' => $this->getJsonErrorMessage(),
                    'error_keys'    => $errorKeys
                ];
            }
        }
        return [
            'error_message' => $this->getJsonErrorMessage(),
            'error_keys'    => $errors
        ];
    }

    /**
     * Get the json error message
     *
     * @return string
     */
    private function getJsonErrorMessage()
    {
        // get the error message
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $message = '';
                break;
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or the modes mismatch.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found.';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error: format of JSON string is wrong.';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            default:
                $message = 'Unknown error.';
                break;
        }
        return $message;
    }

    /**
     * Patterns for wrong json format
     *
     * @return array
     */
    private function patterns_json_error()
    {
        return [
            'has_double_quotes'  => '#(\s*[a-zA-Z0-9\"\_\-]+\:\s*\”.*\”)#',
            'has_special_quotes' => '#(\s*[a-zA-Z0-9\_\-\.]+\:\s*\`.*\`)#',
            'miss_colon_first'   => '#(\s*\{\s*[^\:]*\s*\,)#',
            'miss_colon_another' => '#(\s*\,\s*[^\:]*\s*\,\s*)#',
            'miss_colon_last'    => '#(\s*\,\s*[^\:]*\s*\}\s*)#',
            'has_comma_at_first' => '#(^\s*\{\s*\,\s*$)#',
            'has_comma_at_end'   => '#(^\s*\,\s*\}\s*$)#',
        ];
    }

}
