<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rep Informe de Servicios — MIG-REP-2 (Reporte de asset).
 *
 * Portado de asset sin lo que no usa la pantalla: `ejemplo` y `consultaConFiltros` (el reporte de consumo, que es de pañol),
 * `getCliente`, `getArticulo`, `getSupervisor` (nadie los llama desde la pantalla) y `tiempo` (un "Holis Mundo").
 */
class Reporte extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Reportes');
		$this->load->helper('man');
	}

	/**
	* Pantalla del reporte
	* @param string $permission acciones habilitadas (el reporte es de solo lectura; se pasa igual que en el resto de MAN)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporte | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['permission'] = $permission;
		$this->load->view('reportes/rep_informes', $data);
	}

	/**
	* Solicitudes, preventivos y backlog con los filtros elegidos (POST id_sector, id_equipo, desde y hasta)
	* @return json lista de filas, o 0 si no hay ninguna (como asset)
	*/
	public function getReporte(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporte | getReporte()");
		$filas = $this->Reportes->getReporte($this->input->post());
		echo json_encode($filas ? $filas : 0);
	}

	/**
	* Equipos para el desplegable del filtro
	* @return json lista con id_equipo, descripcion y codigo
	*/
	public function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporte | getEquipo()");
		echo json_encode($this->Reportes->equipos());
	}
}
