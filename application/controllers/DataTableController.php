<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataTableController extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data = $this->mockData(10000);
		$this->load->view('datatable/index', compact('data'));
	}

	private function mockData($totalRecords = 100)
	{
		$data = [];
		for ($s=1; $s <= $totalRecords; $s++) {
			$seats = ['Walk-in', 'tel-user'];
			$data[] = [
				'name' => 'Name '.$s,
				'age'  => 10 + $s,
				'seat' => $seats[array_rand($seats)]
			];
		}
		return $data;
	}
}
