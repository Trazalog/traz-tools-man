<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Familias — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Family extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Familys');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Family | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Familys->Listado_Familys();
		$data['permission'] = $permission;
		$this->load->view('familys/list', $data);
	}

	public function Obtener_Family(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Family | Obtener_Family()');
		echo json_encode($this->Familys->Obtener_Familys($this->input->post('famId')));
	}

	public function Guardar_Family(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Family | Guardar_Family()');
		$data = array(
			'famName' => $this->input->post('famName')
		);
		echo json_encode($this->Familys->Guardar_Familys($data));
	}

	public function Modificar_Family(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Family | Modificar_Family()');
		$data = array(
			'famId' => $this->input->post('famId'),
			'famName' => $this->input->post('famName')
		);
		echo json_encode($this->Familys->Modificar_Familys($data));
	}

	public function Eliminar_Family(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Family | Eliminar_Family()');
		echo json_encode($this->Familys->Eliminar_Familys($this->input->post('famId')));
	}
}
