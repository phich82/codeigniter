<?php defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\Services\UploadService;

require_once APPPATH.'Api/Services/UploadService.php';

/**
 * For dependency injection (IoC)
 */
class Upload_Files_service extends UploadService
{
    //
}
