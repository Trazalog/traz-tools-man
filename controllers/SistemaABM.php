<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Sistemas — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class SistemaABM extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('SistemasABM');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemaABM | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->SistemasABM->sistemaList();
		$data['permission'] = $permission;
		$this->load->view('sistemas/list', $data);
	}

	public function agregarSistema()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemaABM | agregarSistema()');
		$datos = array(
			'descripcion' => $this->input->post('descripcion')
		);
		$result['sistema'] = $this->SistemasABM->agregarSistema($datos);
		echo json_encode($result);
	}

	public function editarSistema()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemaABM | editarSistema()');
		$datos = $this->input->post('parametros');
		$id    = $this->input->post('idsistema');
		echo json_encode($this->SistemasABM->update_editar(is_array($datos) ? $datos : array(), $id));
	}

	public function bajaSistema()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemaABM | bajaSistema()');
		$id = $this->input->post('idsistema');
		$result['isOk']    = $this->SistemasABM->bajaSistema($id);
		$result['sistema'] = $this->SistemasABM->sistemaList();
		echo json_encode($result);
	}
}
