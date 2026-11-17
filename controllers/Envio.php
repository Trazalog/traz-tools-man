<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Administrar Órdenes: entregar y reprogramar órdenes terminadas — MIG-ASSET-4.
 * Portado desde asset sin el cierre de sesión (lo valida tools) y con la empresa de la sesión en cada consulta.
 */
class Envio extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper('man');
		$this->load->model('Envios');
	}

	/** Pantalla. @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3) */
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envio | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Envios->envios_List();
		$data['permission'] = $permission;
		$this->load->view('envio/list', $data);
	}

	/** Entrega una orden terminada (POST id_orden). @return json bool */
	public function entrega(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envio | entrega()");
		echo json_encode($this->Envios->entregas($this->input->post('id_orden'), date('Y-m-d')));
	}

	/** Reprograma una orden terminada parcialmente (POST ido, fe = dd-mm-aaaa). @return json bool */
	public function ModificarFecha(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envio | ModificarFecha()");
		$fe = (string) $this->input->post('fe');
		if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fe, $f) || !checkdate((int) $f[2], (int) $f[1], (int) $f[3])) {
			echo json_encode(false);
			return;
		}
		echo json_encode($this->Envios->ModificarFechas($this->input->post('ido'), $f[3] . '-' . $f[2] . '-' . $f[1]));
	}
}
