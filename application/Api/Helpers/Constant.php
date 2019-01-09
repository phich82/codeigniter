<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Helpers;

class Constant
{
    const EXCEPTION_TYPE_KEY = 'exception-type';
    const EXCEPTION_TYPE_ANDROID = 'android';
    const EXCEPTION_TYPE_AWS_CLOUD = 'aws cloud';
    const EXCEPTION_TYPE_RSV_CLOUD = 'rsv cloud';
    const EXCEPTION_TYPE_RSV_SALES = 'rsv sales';

    const API_CACHE_EXPIRE = 1440; // 1 day

}
