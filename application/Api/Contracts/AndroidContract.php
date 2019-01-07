<?php
namespace App\Api\Contracts;

interface AndroidContract
{
    public function login($urlFail = null, $urlOk = null);
}
