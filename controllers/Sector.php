<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Sectores — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Sector extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Sectores');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sector | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Sectores->Listado_Sectores();
		$data['permission'] = $permission;
		$this->load->view('sectores/list', $data);
	}

	public function Obtener_Sector(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sector | Obtener_Sector()');
		echo json_encode($this->Sectores->Obtener_Sectores($this->input->post('id_sector')));
	}

	public function Guardar_Sector(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sector | Guardar_Sector()');
		$data = array(
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Sectores->Guardar_Sectores($data));
	}

	public function Modificar_Sector(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sector | Modificar_Sector()');
		$data = array(
			'id_sector' => $this->input->post('id_sector'),
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Sectores->Modificar_Sectores($data));
	}

	public function Eliminar_Sector(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sector | Eliminar_Sector()');
		echo json_encode($this->Sectores->Eliminar_Sectores($this->input->post('id_sector')));
	}
}
