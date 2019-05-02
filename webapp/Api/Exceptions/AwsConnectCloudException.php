<?php
/**
 * @author Huynh Phat <phat.nguyen@persol.co.jp>
 * @license [v1]
 */
namespace App\Api\Exceptions;

defined('BASEPATH') OR exit('No direct script access allowed');

use Exception;
use App\Api\Helpers\Constant;

class AwsConnectCloudException extends Exception
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

        // parent::setHeaders([Constant::EXCEPTION_TYPE_KEY => Constant::EXCEPTION_TYPE_AWS_CLOUD]);
    }
}
