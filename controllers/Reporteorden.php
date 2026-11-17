<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rep Ordenes de trabajo — MIG-REP-3 (Reporte de asset, controller Reporteorden).
 *
 * Portado de asset sin lo que la pantalla no usa: getOTequipo, traerot, traerOTestado (listados por orden, equipo o estado que nadie llama)
 * y getfecha, getarticulo, traerArticulo, calestado (copiados del reporte de pedidos de pañol; en asset ni siquiera funcionaban).
 */
class Reporteorden extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Reporteordenes');
		$this->load->helper('man');
	}

	/**
	* Pantalla del reporte
	* @param string $permission acciones habilitadas (el reporte es de solo lectura; se pasa igual que en el resto de MAN)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporteorden | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['permission'] = $permission;
		$this->load->view('reporteorden/rep_ordenes', $data);
	}

	/**
	* Órdenes con los filtros elegidos (POST id_equipo, estado, desde y hasta; vacío = sin filtro)
	* @return json lista de filas (vacía si no hay ninguna)
	*/
	public function getDatosReporte(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporteorden | getDatosReporte()");
		echo json_encode($this->Reporteordenes->getDatosReporte($this->input->post()));
	}

	/**
	* Equipos para el desplegable del filtro
	* @return json lista con id_equipo, codigo y descripcion
	*/
	public function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporteorden | getEquipo()");
		echo json_encode($this->Reporteordenes->equipos());
	}

	/**
	* Estados para el desplegable del filtro
	* @return json lista con estadoid, descripcion y estado
	*/
	public function getestado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Reporteorden | getestado()");
		echo json_encode($this->Reporteordenes->estados());
	}
}
