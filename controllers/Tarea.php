<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bandeja de tareas de Bonita de AssetPlanner — MIG-ASSET-3.
 *
 * Portada desde el asset actual por partes. Esta es la parte que muestra la bandeja (lista paginada y actualizar). El detalle de la
 * tarea, tomar y soltar, cerrar cada tipo de tarea, comentarios y formularios se agregan con el resto de la Bandeja. No se portan
 * el ABM de tareas estándar de asset (`index2`, `*_Tarea`: ninguna vista lo llama; las tareas estándar están en Área > Tareas) ni
 * los tres botones de la bandeja que asset muestra solo a la empresa 6 (cierre masivo de "Confecciona informe", de "Verifica informe"
 * y "Asignar Bonita": usan una fecha de corte fija y una variable de sesión que nadie carga). Verificar en bloque está en Informes.
 */
class Tarea extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Tareas');
		$this->load->helper('man');
	}

	/**
	* Bandeja de entrada del usuario
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		// Se limpia el cache para que la tabla pida todo de nuevo
		$this->Tareas->reiniciarCache();
		$data['permission'] = $permission;
		$data['empr_id'] = empresa();
		$data['tiempoRecarga'] = man_tiempo_recarga();
		$data['device'] = man_dispositivo();
		$this->load->view('tareas/list', $data);
	}

	/**
	* Página de tareas para la tabla (DataTables del lado del servidor)
	* @return json draw, recordsTotal, recordsFiltered y data
	*/
	public function paginado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | paginado()");
		$search = $this->input->post('search');
		$r = $this->Tareas->tareaspaginadas($this->input->post('start'), $this->input->post('length'),
			isset($search['value']) ? $search['value'] : '', $this->input->post('order'));
		echo json_encode(array(
			'draw'            => intval($this->input->post('draw')),
			'recordsTotal'    => intval($r['numDataTotal']),
			'recordsFiltered' => intval($r['numDataTotal']),
			'data'            => $r['datos'],
		));
	}

	/**
	* Vacía el cache de la bandeja (botón Actualizar y recarga automática)
	*/
	public function actualizaTareas(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | actualizaTareas()");
		$this->Tareas->reiniciarCache();
	}
}
