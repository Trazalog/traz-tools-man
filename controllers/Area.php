<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Áreas — MIG-ASSET-1.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Area extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Areas');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas, como en asset ("Add-Edit-Del"). Hasta MIG-5 llegan
	 *                           en la URL; sin parámetro la pantalla queda de solo lectura.
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Area | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Areas->Listado_areas();
		$data['permission'] = $permission;
		$this->load->view('area/view_', $data);
	}

	/**
	* Trae el área por ID
	* @param integer $id_area (POST)
	* @return array lista con el área seleccionada
	*/
	public function Obtener_area(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Area | Obtener_area()');
		$id = $this->input->post('id_area');
		$result = $this->Areas->Obtener_areas($id);
		echo json_encode($result);
	}

	public function Guardar_area(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Area | Guardar_area()');
		$data = array(
			'descripcion' => $this->input->post('descripcion'),
			'estado'      => 'AC'
		);
		$sql = $this->Areas->Guardar_areas($data);
		echo json_encode($sql);
	}

	public function Modificar_area(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Area | Modificar_area()');
		$data = array(
			'id_area'     => $this->input->post('id_area'),
			'descripcion' => $this->input->post('descripcion'),
		);
		$sql = $this->Areas->Modificar_areas($data);
		echo json_encode($sql);
	}

	public function Eliminar_area(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Area | Eliminar_area()');
		$id = $this->input->post('id_area');
		$result = $this->Areas->Eliminar_areas($id);
		echo json_encode($result);
	}
}
