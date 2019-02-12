<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Exceptions;

use Exception;
use App\Api\Helpers\Constant;

defined('BASEPATH') OR exit('No direct script access allowed');

class AwsCloudException extends Exception //HttpException
{
    /**
     * Constructor
     *
     * @param int $statusCode []
     * @return void
     */
    public function __construct($statusCode)
    {
        parent::__construct($statusCode);

        parent::setHeaders([Constant::EXCEPTION_TYPE_KEY => Constant::EXCEPTION_TYPE_AWS_CLOUD]);
    }
}
