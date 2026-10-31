<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Contratistas — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Contratista extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Contratistas');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratista | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Contratistas->Listado_Contratistas();
		$data['permission'] = $permission;
		$this->load->view('contratistas/list', $data);
	}

	public function Obtener_Contratista(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratista | Obtener_Contratista()');
		echo json_encode($this->Contratistas->Obtener_Contratistas($this->input->post('id_contratista')));
	}

	public function Guardar_Contratista(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratista | Guardar_Contratista()');
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'contradireccion' => $this->input->post('contradireccion'),
			'contramail' => $this->input->post('contramail'),
			'contramail1' => $this->input->post('contramail1'),
			'contracelular1' => $this->input->post('contracelular1'),
			'contracelular2' => $this->input->post('contracelular2'),
			'contratelefono' => $this->input->post('contratelefono'),
			'contracontacto' => $this->input->post('contracontacto')
		);
		echo json_encode($this->Contratistas->Guardar_Contratistas($data));
	}

	public function Modificar_Contratista(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratista | Modificar_Contratista()');
		$data = array(
			'id_contratista' => $this->input->post('id_contratista'),
			'nombre' => $this->input->post('nombre'),
			'contradireccion' => $this->input->post('contradireccion'),
			'contramail' => $this->input->post('contramail'),
			'contramail1' => $this->input->post('contramail1'),
			'contracelular1' => $this->input->post('contracelular1'),
			'contracelular2' => $this->input->post('contracelular2'),
			'contratelefono' => $this->input->post('contratelefono'),
			'contracontacto' => $this->input->post('contracontacto')
		);
		echo json_encode($this->Contratistas->Modificar_Contratistas($data));
	}

	public function Eliminar_Contratista(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratista | Eliminar_Contratista()');
		echo json_encode($this->Contratistas->Eliminar_Contratistas($this->input->post('id_contratista')));
	}
}
