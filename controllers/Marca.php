<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Marcas — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Marca extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Marcas');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marca | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Marcas->Listado_Marcas();
		$data['permission'] = $permission;
		$this->load->view('marca/list', $data);
	}

	public function Obtener_Marca(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marca | Obtener_Marca()');
		echo json_encode($this->Marcas->Obtener_Marcas($this->input->post('marcaid')));
	}

	public function Guardar_Marca(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marca | Guardar_Marca()');
		$data = array(
			'marcadescrip' => $this->input->post('marcadescrip')
		);
		echo json_encode($this->Marcas->Guardar_Marcas($data));
	}

	public function Modificar_Marca(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marca | Modificar_Marca()');
		$data = array(
			'marcaid' => $this->input->post('marcaid'),
			'marcadescrip' => $this->input->post('marcadescrip')
		);
		echo json_encode($this->Marcas->Modificar_Marcas($data));
	}

	public function Eliminar_Marca(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marca | Eliminar_Marca()');
		echo json_encode($this->Marcas->Eliminar_Marcas($this->input->post('marcaid')));
	}
}
