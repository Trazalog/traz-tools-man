<?php defined('BASEPATH') or exit('No direct script access allowed');

class Inspeccion extends CI_Controller{
    public function __construct(){
        parent::__construct();
		$this->load->model('Sservicios');
		$this->load->model('areas');
		$this->load->model('procesos');
    }
    /**
	* Vista principal para la gestion de servicios
	* @param 
	* @return view
	*/
    public function index(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | index()");

        $this->load->view('Sservicios/list_bpm', $data);
    }
}