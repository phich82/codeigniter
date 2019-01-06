<?php
namespace application\Api\Exceptions;

require_once APPPATH.'helpers/constant.php';

//use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomerException extends Exception //HttpException
{
    /**
     * Constructor.
     *
     * @param int $statusCode
     * @param string $endPoint
     * @param string $errorCode
     */
    public function __construct($statusCode, $endPoint = null, $errorCode = null)
    {
        parent::__construct($statusCode);

        parent::setHeaders([
            Constant::EXCEPTION_TYPE_KEY => Constant::EXCEPTION_TYPE_CUSTOMER,
            'end-point' => $endPoint,
            'error-code' => $errorCode,
        ]);
    }
}
