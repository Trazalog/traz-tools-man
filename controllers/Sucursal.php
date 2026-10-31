<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ABM de Sucursales — MIG-ASSET-5a.
 * La sesión la valida tools (DNATO); los datos van por el model, que usa MANDataService (ADR-014).
 */
class Sucursal extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Sucursales');
		$this->load->helper('man');
	}

	/**
	 * Pantalla del ABM.
	 * @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro la pantalla queda de solo lectura
	 */
	public function index($permission = '')
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursal | index(' . $permission . ')');
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Sucursales->sucursalesList();
		$data['permission'] = $permission;
		$this->load->view('sucursales/list', $data);
	}

	public function agregarSucursal()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursal | agregarSucursal()');
		$datos = array(
			'descripc' => $this->input->post('descripcion'),
			'dire' => $this->input->post('direccion'),
			'zonas' => $this->input->post('zona'),
			'telefono' => $this->input->post('telefono')
		);
		$result['sucursal'] = $this->Sucursales->agregarSucursal($datos);
		echo json_encode($result);
	}

	public function editarSucursal()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursal | editarSucursal()');
		$datos = $this->input->post('parametros');
		$id    = $this->input->post('idsucursal');
		echo json_encode($this->Sucursales->update_editar(is_array($datos) ? $datos : array(), $id));
	}

	public function bajaSucursal()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursal | bajaSucursal()');
		$id = $this->input->post('idSucursal');
		$result['isOk']    = $this->Sucursales->bajaSucursal($id);
		$result['sucursal'] = $this->Sucursales->sucursalesList();
		echo json_encode($result);
	}
}
