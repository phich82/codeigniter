<?php
/**
* @author Long Nguyen <long.nguyen2@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;

/**
 * Provide the restful APIs
 */
class UploadService
{
    use FactoryTrait;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        // Load model to working with database
        $this->UploadModel = $this->model('api/upload_logs', 'UploadModel');
    }


    /**
     * Receive Logs File and Move to path in server defined
     *
     * @param array $params
     * @param headers $headers
     */
    public function doUploadLogs($headers = [], $params = [])
    {
        $rsvCompany = $this->UploadModel->getCompanyByPOS($headers['company_code'], $headers['store_code']);

        // if company_code & store_no empty then return error message now
        if(empty($rsvCompany)) {
            return ['error' => translate('lang_api.pos_company_not_sync')];
        }
        $logFile = $params['log_file_name'];
        $params['file_name'] = $params['rename'] ?: date('YmdHis').'_'.substr($headers['device_id'], 0, 4).$params['ext'];
        $subPath = DIRECTORY_SEPARATOR.$rsvCompany['company_code'].DIRECTORY_SEPARATOR.$rsvCompany['store_no'].DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR;
        $fullPath = rtrim($params['upload_path'], DIRECTORY_SEPARATOR).$subPath;

        // create path for store logs file
        $this->createPaths($fullPath, $params['mode_path']);
        $params['upload_path'] = $fullPath;

        // Load Library Upload of CodeIgniter
        $doUpload = $this->make('upload', null, $params);
        return (!$doUpload->do_upload($logFile)) ? ['error' => $doUpload->display_errors()] : ['data' => $doUpload->data()];
    }

    /**
     * Check and create folder before store files
     */
    private function createPaths($path, $mode = 0777, $recursive = TRUE) {
        if(!is_dir($path)) {
            return mkdir($path, $mode, $recursive);
        }
    }


}
