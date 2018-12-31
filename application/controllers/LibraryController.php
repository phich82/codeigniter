<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LibraryController extends CI_Controller
{
    /**
     * Index Page for this controller.
     *
     * @return object|string
     */
    public function index()
    {
        return $this->load->view('library/index');
    }
}
