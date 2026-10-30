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
		$this->load->helper('man_tarea');
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
	* Pantalla de una tarea: datos del equipo, solicitud y OT, comentarios, línea de tiempo y la vista propia de cada tipo de tarea
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	* @param int $idTarBonita id de la tarea en Bonita
	*/
	public function detaTarea($permission = '', $idTarBonita = 0){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | detaTarea($permission, $idTarBonita)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$det = $this->Tareas->detalle($idTarBonita);
		if ($det === null) {
			echo '<div class="alert alert-danger">No se pudo abrir la tarea: no existe o no es de tu empresa.</div>';
			return;
		}
		$tarea = $det['tarea'];
		$idOt = ctype_digit((string) $det['id_ot']) ? (int) $det['id_ot'] : 0;
		// Solicitud de servicio del caso: asset la leía de la variable del caso en Bonita; acá sale de asset (la del caso, o la del backlog)
		$idSs = ctype_digit((string) $det['id_ss']) ? (int) $det['id_ss'] : (ctype_digit((string) $det['sore_id']) ? (int) $det['sore_id'] : 0);
		$ot = $idOt ? $this->Tareas->cabeceraOt($idOt) : null;
		$solicitud = $idSs ? $this->Tareas->cabeceraSolicitud($idSs) : null;
		$idEq = $ot ? $ot['id_equipo'] : ($solicitud ? $this->idEquipoDeSolicitud($idSs) : null);
		$equipo = ($idEq !== null && $idEq !== '') ? $this->Tareas->cabeceraEquipo($idEq) : null;

		$data['permission'] = $permission;
		$data['device'] = man_dispositivo();
		$data['TareaBPM'] = $tarea;
		$data['idTarBonita'] = (int) $idTarBonita;
		$data['id_OT'] = $idOt;
		$data['id_SS'] = $idSs;
		$data['id_EQ'] = $idEq;
		$data['cabecera'] = man_cabecera($equipo, $solicitud, $ot);
		$data['lineaTiempo'] = man_timeline($this->Tareas->lineaTiempo($idTarBonita));
		$data['comentarios'] = $this->load->view('tareas/componentes/comentarios', array(
			'case_id' => $tarea['caseId'],
			'idTarBonita' => (int) $idTarBonita,
			'comentarios' => $this->Tareas->comentarios($idTarBonita),
		), true);
		// Informe de servicio ya hecho: muestra el motivo del último rechazo
		$data['idOServ'] = $ot && $ot['tiene_informe'] > 0;
		$data['justificacion'] = $ot ? $ot['justificacion'] : '';
		if ($idOt) {
			$data['subtareas'] = $this->Tareas->getSubtareas($idOt);
		}
		// Las pantallas de asset leen estos tres datos, que asset nunca cargaba: sin formulario ni tarea estándar
		$data['idForm'] = 0;
		$data['infoTarea'] = array('visible' => '');
		$data['datos'] = array(array('id_listarea' => '', 'id_orden' => $idOt, 'duracion_std' => ''));

		$vista = array(
			'Analisis de Solicitud de Servicio' => 'tareas/view_analisisSServicios',
			'Planificar Solicitud' => 'tareas/view_planificar',
			'Asignar Responsable OT Urgente ' => 'tareas/view_asignar',
			'Asignar Resonsable OT' => 'tareas/view_asignar',
			'Planificar Backlog' => 'tareas/view_planificar',
			'Ejecutar OT' => 'tareas/view_ejecutarOT',
			'Esperando cambio estado "a Ejecutar"' => 'tareas/view_cambio_estado',
			'Esperando cambio estado "a Ejecutar" 2' => 'tareas/view_cambio_estado',
			'Confecciona informe servicio' => 'tareas/view_informe_servicio',
			'Verifica Informe de Servicio' => 'tareas/view_verifica_informe',
			'Presta conformidad' => 'tareas/view_presta_conformidad',
		);
		$nombre = $tarea['displayName'];
		$this->load->view(isset($vista[$nombre]) ? $vista[$nombre] : 'tareas/view_', $data);
		$this->load->view('tareas/scripts/tarea_std', array('permission' => $permission));
	}

	/**
	* Agrega un comentario al caso de la tarea
	* @return json status y msj
	*/
	public function GuardarComentario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | GuardarComentario()");
		echo json_encode($this->Tareas->comentar($this->input->post('idTarBonita'), $this->input->post('content')));
	}

	/**
	* Archivo del procedimiento (adjunto del preventivo) de la OT de una tarea
	* @return json filas con prev_adjunto
	*/
	public function procedimiento(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | procedimiento()");
		echo json_encode($this->Tareas->procedimiento($this->input->post('ot')));
	}

	/**
	* Marca una subtarea de la OT como terminada (T) o en curso (C)
	* @return json status
	*/
	public function cambiarEstadoSubtask(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | cambiarEstadoSubtask()");
		echo json_encode(array('status' => $this->Tareas->cambiarEstadoSubtask($this->input->post('idTarBonita'),
			$this->input->post('idListarea'), $this->input->post('estado'))));
	}

	/**
	* Comentarios del caso de la tarea (la vista los recarga después de agregar uno)
	* @param int $idTarBonita id de la tarea en Bonita
	*/
	public function ObtenerComentariosBPM($idTarBonita = 0){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Tarea | ObtenerComentariosBPM($idTarBonita)");
		$this->load->view('tareas/componentes/comentarios', array(
			'case_id' => '',
			'idTarBonita' => (int) $idTarBonita,
			'comentarios' => $this->Tareas->comentarios($idTarBonita),
		));
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
