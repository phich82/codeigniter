<?php

namespace application\Api\Exceptions;

//use Symfony\Component\HttpKernel\Exception\HttpException;

class VipException extends Exception //HttpException
{
    /**
     * AnaException constructor.
     *
     * @param int $statusCode
     */
    public function __construct($statusCode)
    {
        parent::__construct($statusCode);

        parent::setHeaders([Constant::EXCEPTION_TYPE_KEY => Constant::EXCEPTION_TYPE_VIP]);
    }
}
