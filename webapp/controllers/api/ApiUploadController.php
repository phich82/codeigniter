<?php

require_once(dirname(__FILE__) . '/../../controllers/apis/ApiController.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/HttpCode.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/Response.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/ApiLog.php');

use App\Api\Helpers\HttpCode;
use App\Api\Helpers\Response;
use App\Api\Helpers\ApiLog;

class ApiUploadController extends ApiController
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('api/Upload_Files_service', null, 'UploadFilesService');
    }

    /**
     * Upload Logs file from Android to tracking later
     */
    public function uploadLogsPost() {
        $params = $this->config->item('file_validate_rules');
        $headers = $this->_headers();
        // Load lang file
        $langCode = $headers['lang_code'] ?? $this->config->item('language');
        $this->lang->load($langCode);

        // start execute check and upload file
        $result = $this->UploadFilesService->doUploadLogs($headers, $params);
        $response = [
            $this->response_result_key => $this->success_status
        ];

        // error
        if (isset($result['error'])) {
            $error = $result['error'];
            $response = [
                $this->response_result_key => $this->error_status,
                $this->response_error_key  => [
                    $this->response_error_code          => $this->api_default_error_message,
                    $this->validation_error_message_key => (is_string($error) ? strip_tags($error) : $error)
                ]
            ];
        }

        // response
        return $this->response->json($response, HttpCode::HTTP_OK);
    }

}
