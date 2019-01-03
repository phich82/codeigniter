<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataTableController extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * @return void
     */
    public function index()
    {
        $data = $this->_mockData(10000);
        $this->load->view('datatable/index', compact('data'));
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
