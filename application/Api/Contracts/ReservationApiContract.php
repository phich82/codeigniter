<?php
namespace application\Api\Contracts;

interface ReservationApiContract
{
    public function createReservation();
    public function updateReservation();
    public function cancelReservation();
    public function getReservation();
}
