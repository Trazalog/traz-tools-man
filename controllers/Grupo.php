<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Grupos — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Grupo extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Grupos');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Grupos->Listado_Grupos();
		$data['permission'] = $permission;
		$this->load->view('grupos/list', $data);
	}

	public function Obtener_Grupo(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | Obtener_Grupo()');
		echo json_encode($this->Grupos->Obtener_Grupos($this->input->post('id_grupo')));
	}

	public function Guardar_Grupo(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | Guardar_Grupo()');
		$data = array(
			'descripcion' => $this->input->post('descripcion'),
			'form_id' => $this->input->post('form_id')
		);
		echo json_encode($this->Grupos->Guardar_Grupos($data));
	}

	public function Modificar_Grupo(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | Modificar_Grupo()');
		$data = array(
			'id_grupo' => $this->input->post('id_grupo'),
			'descripcion' => $this->input->post('descripcion'),
			'form_id' => $this->input->post('formulario')
		);
		echo json_encode($this->Grupos->Modificar_Grupos($data));
	}

	public function Eliminar_Grupo(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | Eliminar_Grupo()');
		echo json_encode($this->Grupos->Eliminar_Grupos($this->input->post('id_grupo')));
	}

	/**
	 * Formularios de la empresa, para el desplegable del grupo
	 * @return json lista con form_id y nombre
	 */
	public function get_formularios(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupo | get_formularios()');
		echo json_encode($this->Grupos->formularios());
	}
}
