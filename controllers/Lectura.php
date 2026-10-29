<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Carga de lecturas de parámetros de equipo — MIG-ASSET-3.
 *
 * Portado desde asset sin las acciones comentadas. La sesión la valida tools (no se repite el cierre de
 * sesión de asset) y la vista no usa el listado general, así que index no lo trae.
 */
class Lectura extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Lecturas');
		$this->load->helper('man');
	}

	/**
	* Pantalla de carga de lecturas
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['permission'] = $permission;
		$this->load->view('lectura/lect', $data);
	}

	/** Equipos de la empresa con parámetros asociados */
	public function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | getEquipo()");
		echo json_encode($this->Lecturas->getEquipo());
	}

	/** Parámetros asociados al equipo */
	public function getParametrosAsoc(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | getParametrosAsoc()");
		echo json_encode($this->Lecturas->getParametrosAsoc($this->input->post('equipoId')));
	}

	/** Guarda la lectura de un parámetro del equipo */
	public function guardar_lectura(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | guardar_lectura()");
		$insert = array(
			'paramId'   => $this->input->post('id_parametro'),
			'id_equipo' => $this->input->post('id_equipo'),
			'valor'     => $this->input->post('valor'),
			'fechahora' => $this->input->post('fecha'),
		);
		echo json_encode($this->Lecturas->guardar_lectura($insert));
	}

	/** Borra una lectura por id */
	public function deleteLectura(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | deleteLectura()");
		echo json_encode($this->Lecturas->deleteLectura($this->input->post('id')));
	}

	/** Lecturas del equipo en la fecha */
	public function getLecturasCargadas(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lectura | getLecturasCargadas()");
		echo json_encode($this->Lecturas->getLecturasCargadas($this->input->post('fecha'), $this->input->post('idequipo')));
	}
}
