<?php
namespace application\Api\Contracts;

interface VipApiContract
{
    public function login($urlFail = null, $urlOk = null);
}
