<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Tareas estándar — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Tareaestandar extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Tareasestandar');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareaestandar | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Tareasestandar->Listado_Tareas();
		$data['permission'] = $permission;
		$this->load->view('tareaestandar/list', $data);
	}

	public function Obtener_Tarea(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareaestandar | Obtener_Tarea()');
		echo json_encode($this->Tareasestandar->Obtener_Tareas($this->input->post('id_tarea')));
	}

	public function Guardar_Tarea(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareaestandar | Guardar_Tarea()');
		$data = array(
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Tareasestandar->Guardar_Tareas($data));
	}

	public function Modificar_Tarea(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareaestandar | Modificar_Tarea()');
		$data = array(
			'id_tarea' => $this->input->post('id_tarea'),
			'descripcion' => $this->input->post('descripcion')
		);
		echo json_encode($this->Tareasestandar->Modificar_Tareas($data));
	}

	public function Eliminar_Tarea(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareaestandar | Eliminar_Tarea()');
		echo json_encode($this->Tareasestandar->Eliminar_Tareas($this->input->post('id_tarea')));
	}
}
