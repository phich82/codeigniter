<?php
namespace App\Api\Contracts;

interface RsvCloudContract
{
    public function createReservation();
    public function updateReservation();
    public function cancelReservation();
    public function getReservation();
}
