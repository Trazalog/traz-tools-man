<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Clientes — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Cliente extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Clientes');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Cliente | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Clientes->Listado_Clientes();
		$data['permission'] = $permission;
		$this->load->view('cliente/view_', $data);
	}

	public function Obtener_Cliente(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Cliente | Obtener_Cliente()');
		echo json_encode($this->Clientes->Obtener_Clientes($this->input->post('cliId')));
	}

	public function Guardar_Cliente(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Cliente | Guardar_Cliente()');
		$data = array(
			'cliName' => $this->input->post('cliName'),
			'cliLastName' => $this->input->post('cliLastName'),
			'cliDni' => $this->input->post('cliDni'),
			'cliAddress' => $this->input->post('cliAddress'),
			'cliPhone' => $this->input->post('cliPhone'),
			'cliEmail' => $this->input->post('cliEmail'),
			'cliRazonSocial' => $this->input->post('cliRazonSocial')
		);
		echo json_encode($this->Clientes->Guardar_Clientes($data));
	}

	public function Modificar_Cliente(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Cliente | Modificar_Cliente()');
		$data = array(
			'cliId' => $this->input->post('cliId'),
			'cliName' => $this->input->post('cliName'),
			'cliDni' => $this->input->post('cliDni'),
			'cliAddress' => $this->input->post('cliAddress'),
			'cliPhone' => $this->input->post('cliPhone'),
			'cliEmail' => $this->input->post('cliEmail'),
			'cliRazonSocial' => $this->input->post('cliRazonSocial')
		);
		echo json_encode($this->Clientes->Modificar_Clientes($data));
	}

	public function Eliminar_Cliente(){
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Cliente | Eliminar_Cliente()');
		echo json_encode($this->Clientes->Eliminar_Clientes($this->input->post('cliId')));
	}
}
