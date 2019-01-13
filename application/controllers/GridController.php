<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GridController extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * @return void
     */
    public function index()
    {
        $timeListHeader = $this->getTimeListHeader();
        $tableList = $this->getTableList(100);
        $reservations = $this->getReservation();
        $data = array_reduce($reservations, function ($carry, $reservation) {
            $list = explode(',', str_replace(' ', '', $reservation['table_list']));
            foreach ($list as $tableId) {
                $carry[$tableId][$tableId.'_'.str_replace(':', '', $reservation['start_time'])] = $reservation;
            }
            return $carry;
        }, []);
        $this->load->view('grid/index', compact('timeListHeader', 'tableList', 'data'));
    }

    private function getReservation()
    {
        return [
            [
                'id' => 1,
                'start_time' => '00:30',
                'end_time' => '01:30',
                'table_list' => '1, 3, 5'
            ],
            [
                'id' => 2,
                'start_time' => '10:30',
                'end_time' => '13:30',
                'table_list' => '1,7,20'
            ],
            [
                'id' => 3,
                'start_time' => '20:30',
                'end_time' => '23:30',
                'table_list' => '3, 9, 10'
            ],
            [
                'id' => 4,
                'start_time' => '09:30',
                'end_time' => '11:30',
                'table_list' => '3, 11, 15'
            ],
            [
                'id' => 5,
                'start_time' => '00:30',
                'end_time' => '01:30',
                'table_list' => '10, 13'
            ],
            [
                'id' => 6,
                'start_time' => '00:30',
                'end_time' => '01:30',
                'table_list' => '2'
            ],
        ];
    }

    private function getTimeListHeader()
    {
        return [
            '00:00' => '0000',
            '00:30' => '0030',
            '01:00' => '1000',
            '01:30' => '1030',
            '02:00' => '0200',
            '02:30' => '0230',
            '03:00' => '0300',
            '03:30' => '3030',
            '04:00' => '0400',
            '04:30' => '0430',
            '05:00' => '5000',
            '05:30' => '0530',
            '06:00' => '0600',
            '06:30' => '0630',
            '07:00' => '0700',
            '07:30' => '0730',
            '08:00' => '8000',
            '08:30' => '0830',
            '09:00' => '0900',
            '09:30' => '0930',
            '10:00' => '1000',
            '10:30' => '1030',
            '11:00' => '1100',
            '11:30' => '1130',
            '12:00' => '1200',
            '12:30' => '1230',
            '13:00' => '1300',
            '13:30' => '1330',
            '14:00' => '1400',
            '14:30' => '1430',
            '15:00' => '1500',
            '15:30' => '1530',
            '16:00' => '1600',
            '16:30' => '1630',
            '17:00' => '1700',
            '17:30' => '1730',
            '18:00' => '1800',
            '18:30' => '1830',
            '19:00' => '1900',
            '19:30' => '1930',
            '20:00' => '2000',
            '20:30' => '2030',
            '21:00' => '2100',
            '21:30' => '2130',
            '22:00' => '2200',
            '22:30' => '2230',
            '23:00' => '2300',
            '23:30' => '2330',
            '24:00' => '2400',
            '24:30' => '2430',
            '25:00' => '2500',
            '25:30' => '2530',
            '26:00' => '2600',
            '26:30' => '2630',
            '27:00' => '2700',
            '27:30' => '2730',
            '28:00' => '2800',
            '28:30' => '2830',
            '29:00' => '2900',
            '29:30' => '2930',
            '30:00' => '3000',
        ];
    }

    private function getTableList($total = 100)
    {
        $tableList = [];
        for ($s=1; $s <= $total; $s++) {
            $tableList[] = [
                'id'   => $s,
                'name' => "Table $s"
            ];
        }
        return $tableList;
    }

    /**
     * Mock data
     *
     * @param int $totalRecords []
     *
     * @return array
     */
    private function _mockData($totalRecords = 100)
    {
        $data = [];
        for ($s=1; $s <= $totalRecords; $s++) {
            $seats = ['Walk-in', 'tel-user'];
            $data[] = [
                'id'   => $s,
                'name' => 'Name '.$s,
                'age'  => 10 + $s,
                'seat' => $seats[array_rand($seats)]
            ];
        }
        return $data;
    }
}
