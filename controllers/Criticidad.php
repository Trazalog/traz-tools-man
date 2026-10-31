<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Criticidad — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Criticidad extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Criticidades');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidad | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Criticidades->Listado_criticidades();
		$data['permission'] = $permission;
		$this->load->view('criticidad/view_', $data);
	}

	public function Obtener_criticidad(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidad | Obtener_criticidad()');
		echo json_encode($this->Criticidades->Obtener_criticidades($this->input->post('id_criti')));
	}

	public function Guardar_criticidad(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidad | Guardar_criticidad()');
		$data = array(
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Criticidades->Guardar_criticidades($data));
	}

	public function Modificar_criticidad(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidad | Modificar_criticidad()');
		$data = array(
			'id_criti' => $this->input->post('id_criti'),
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Criticidades->Modificar_criticidades($data));
	}

	public function Eliminar_criticidad(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidad | Eliminar_criticidad()');
		echo json_encode($this->Criticidades->Eliminar_criticidades($this->input->post('id_criti')));
	}
}
