<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Procesos — MIG-ASSET-1.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Proceso extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Procesos');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas, como en asset ("Add-Edit-Del"). Hasta MIG-5 llegan
	 *                           en la URL; sin parámetro la pantalla queda de solo lectura.
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Proceso | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Procesos->Listado_procesos();
		$data['permission'] = $permission;
		$this->load->view('proceso/view_', $data);
	}

	/**
	* Trae el proceso por ID
	* @param integer $id_proceso (POST)
	* @return array lista con el proceso seleccionado
	*/
	public function Obtener_proceso(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Proceso | Obtener_proceso()');
		$id = $this->input->post('id_proceso');
		$result = $this->Procesos->Obtener_procesos($id);
		echo json_encode($result);
	}

	public function Guardar_proceso(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Proceso | Guardar_proceso()');
		$data = array(
			'descripcion' => $this->input->post('descripcion'),
			'estado'      => 'AC'
		);
		$sql = $this->Procesos->Guardar_procesos($data);
		echo json_encode($sql);
	}

	public function Modificar_proceso(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Proceso | Modificar_proceso()');
		$data = array(
			'id_proceso'     => $this->input->post('id_proceso'),
			'descripcion' => $this->input->post('descripcion'),
		);
		$sql = $this->Procesos->Modificar_procesos($data);
		echo json_encode($sql);
	}

	public function Eliminar_proceso(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Proceso | Eliminar_proceso()');
		$id = $this->input->post('id_proceso');
		$result = $this->Procesos->Eliminar_procesos($id);
		echo json_encode($result);
	}
}
