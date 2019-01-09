<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Exceptions;

use Exception;
use App\Api\Helpers\Constant;

class RsvSalesException extends Exception //HttpException
{
    /**
     * Constructor
     *
     * @param int    $statusCode []
     * @param string $endPoint   []
     * @param string $errorCode  []
     * @return void
     */
    public function __construct($statusCode, $endPoint = null, $errorCode = null)
    {
        parent::__construct($statusCode);

        parent::setHeaders([
            Constant::EXCEPTION_TYPE_KEY => Constant::EXCEPTION_TYPE_RSV_SALES,
            'end-point' => $endPoint,
            'error-code' => $errorCode,
        ]);
    }
}
