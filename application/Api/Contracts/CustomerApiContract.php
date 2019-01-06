<?php
namespace application\Api\Contracts;

interface CustomerApiContract
{
    public function createReservation();
    public function updateReservation();
    public function cancelReservation();
    public function getReservation();
}
