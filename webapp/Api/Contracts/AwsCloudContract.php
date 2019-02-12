<?php
namespace App\Api\Contracts;

interface AwsCloudContract
{
    public function createReservation();
    public function updateReservation();
    public function cancelReservation();
    public function getReservation();
}
