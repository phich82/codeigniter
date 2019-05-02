<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Helpers;

use App\Api\Helpers\HttpCode;

class Response
{
    private $output;
    private $config;

    public function __construct()
    {
        $this->output = load_class('Output', 'core');
        $this->config = load_class('Config', 'core');
    }

    /**
     * HTTP status codes and their respective description
     * Note: Only the widely used HTTP status codes are used
     *
     * @var array
     * @link http://www.restapitutorial.com/httpstatuscodes.html
     */
    protected $http_status_codes = [
        HttpCode::HTTP_OK                    => 'OK',
        HttpCode::HTTP_CREATED               => 'CREATED',
        HttpCode::HTTP_NO_CONTENT            => 'NO CONTENT',
        HttpCode::HTTP_NOT_MODIFIED          => 'NOT MODIFIED',
        HttpCode::HTTP_BAD_REQUEST           => 'BAD REQUEST',
        HttpCode::HTTP_UNAUTHORIZED          => 'UNAUTHORIZED',
        HttpCode::HTTP_FORBIDDEN             => 'FORBIDDEN',
        HttpCode::HTTP_NOT_FOUND             => 'NOT FOUND',
        HttpCode::HTTP_METHOD_NOT_ALLOWED    => 'METHOD NOT ALLOWED',
        HttpCode::HTTP_NOT_ACCEPTABLE        => 'NOT ACCEPTABLE',
        HttpCode::HTTP_CONFLICT              => 'CONFLICT',
        HttpCode::HTTP_INTERNAL_SERVER_ERROR => 'INTERNAL SERVER ERROR',
        HttpCode::HTTP_NOT_IMPLEMENTED       => 'NOT IMPLEMENTED'
    ];

    /**
     * Takes mixed data and optionally a status code, then creates the response
     *
     * @param array|null $data      [Data to output to the user]
     * @param int|null   $http_code [HTTP status code]
     * @param bool       $continue  [TRUE to flush response to client and continue running the script; otherwise, exit]
     */
    public function json($data = null, $http_code = null, $continue = false)
    {
        ob_start();
        // cast as an integer
        if ($http_code !== null) {
            $http_code = (int) $http_code;
        }
        // Set the output as NULL by default
        $output = null;
        // If data is NULL and no HTTP status code provided, then display, error and exit
        if ($data === null && $http_code === null) {
            $http_code = Constant::HTTP_NOT_FOUND;
        } elseif ($data !== null) { // If data is not NULL and a HTTP status code provided, then continue
            if (is_array($data) || is_object($data)) { // set the format header
                $mimeType = $this->config->item('api_response_format');
                $charset = strtolower($this->config->item('charset'));
                $this->output->set_content_type($mimeType, $charset);
                $data = $this->_toJson($data);
            }
            // format is not supported, so output as a string
            $output = $data;
        }

        // if not greater than zero, then set the HTTP status code as 200 by default
        // if it is the 500 error, should pass a correct HTTP status code
        $http_code > 0 || $http_code = Constant::HTTP_OK;

        $this->output->set_status_header($http_code);

        // output the data
        $this->output->set_output($output);

        if ($continue === false) {
            // display the data and exit execution
            $this->output->_display();
            exit;
        } else {
			ob_end_flush();
        }
    }

    /**
     * Convert data to json
     *
     * @param array $data
     * @return string
     */
    private function _toJson($data = [])
    {
        return json_encode($data ?: []);
    }
}
