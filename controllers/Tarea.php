<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bandeja de tareas de Bonita de AssetPlanner — MIG-ASSET-3.
 *
 * Portada desde el asset actual por partes. Hasta ahora: la lista (paginada y actualizar) y las acciones sobre la tarea (tomar, soltar,
 * urgencia, inicio, ejecutar OT, verificar informe, conformidad). El detalle de la tarea, comentarios y formularios se agregan con el
 * resto de la Bandeja. No se portan
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

	/**
	* Toma la tarea en Bonita
	* @return json status y msj
	*/
	public function tomarTarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | tomarTarea()");
		echo json_encode($this->Tareas->tomarTarea($this->input->post('idTarBonita')));
	}

	/**
	* Suelta la tarea en Bonita
	* @return json status y msj
	*/
	public function soltarTarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | soltarTarea()");
		echo json_encode($this->Tareas->soltarTarea($this->input->post('idTarBonita')));
	}

	/**
	* Termina el análisis de la solicitud con la decisión de urgencia
	* @return json status y msj
	*/
	public function decidirUrgencia(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | decidirUrgencia()");
		echo json_encode($this->Tareas->decidirUrgencia($this->input->post('idTarBonita'), $this->input->post('opcion')));
	}

	/**
	* ¿La OT ya tiene fecha de inicio?
	* @return json true o false
	*/
	public function confInicioTarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | confInicioTarea()");
		echo json_encode($this->Tareas->inicioConfirmado($this->input->get('id_OT')));
	}

	/**
	* Marca el inicio de la OT con la posición del dispositivo
	* @return json true o false
	*/
	public function inicioTarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | inicioTarea()");
		echo json_encode($this->Tareas->inicioTarea($this->input->post('id_OT'), $this->input->post('lat'), $this->input->post('lon')));
	}

	/**
	* Termina la tarea "Ejecutar OT"
	* @return json status y msj
	*/
	public function ejecutarOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | ejecutarOT()");
		echo json_encode($this->Tareas->ejecutarOT($this->input->post('idTarBonita')));
	}

	/**
	* Verifica el informe de servicio (conforme o no, con la justificación)
	* @return json status y msj
	*/
	public function verificarInforme(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | verificarInforme()");
		echo json_encode($this->Tareas->verificarInforme($this->input->post('idTarBonita'), $this->input->post('opcion'),
			$this->input->post('justificacion')));
	}

	/**
	* Presta (o no) conformidad con el trabajo
	* @return json status y msj
	*/
	public function prestarConformidad(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | prestarConformidad()");
		echo json_encode($this->Tareas->prestarConformidad($this->input->post('idTarBonita'), $this->input->post('opcion')));
	}
}
