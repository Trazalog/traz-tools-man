<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Parámetros — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Altparametro extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Altparametros');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametro | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Altparametros->Listado_Altparametros();
		$data['permission'] = $permission;
		$this->load->view('altparametros/list', $data);
	}

	public function Obtener_Altparametro(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametro | Obtener_Altparametro()');
		echo json_encode($this->Altparametros->Obtener_Altparametros($this->input->post('paramId')));
	}

	public function Guardar_Altparametro(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametro | Guardar_Altparametro()');
		$data = array(
			'paramdescrip' => $this->input->post('paramdescrip'),
			'min' => $this->input->post('min')
		);
		echo json_encode($this->Altparametros->Guardar_Altparametros($data));
	}

	public function Modificar_Altparametro(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametro | Modificar_Altparametro()');
		$data = array(
			'paramId' => $this->input->post('paramId'),
			'paramdescrip' => $this->input->post('paramdescrip'),
			'min' => $this->input->post('min')
		);
		echo json_encode($this->Altparametros->Modificar_Altparametros($data));
	}

	public function Eliminar_Altparametro(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametro | Eliminar_Altparametro()');
		echo json_encode($this->Altparametros->Eliminar_Altparametros($this->input->post('paramId')));
	}
}
