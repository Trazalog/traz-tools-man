<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Informes de servicio — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) con las acciones de la pantalla de informes, la ventana de ver informe (E9)
 * y, desde la bandeja de Bonita (MIG-3), cargar, editar y guardar el informe (cargarOrden, editarInforme, setOrdenServ). La OT, el
 * equipo y la solicitud salen de la tarea de Bonita, no de la dirección. Los vales de pañol, los insumos y las herramientas del informe
 * no se migran (almacenes y pañol). La verificación va por toolsMANAPI y la plantilla BPM (A5).
 */
class Ordenservicio extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Ordenservicios');
		$this->load->model('Otrabajos');
		$this->load->model('Tareas');
		$this->load->helper('man');
	}

	/**
	* Listado de informes de servicio
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | index($permission)");
		$data['permission'] = $permission;
		$data['list'] = $this->Ordenservicios->getOrdServiciosList();
		$this->load->view('ordenservicios/list', $data);
	}

	/**
	* Ventana de ver informe (también la usa el listado de OT)
	*/
	public function verInforme($id_ot = null, $id_eq = null, $id_solicitud = null, $idTarBonita = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | verInforme($id_ot, $id_eq, $id_solicitud, $idTarBonita)");
		$data['id_ot'] = $id_ot;
		$data['id_eq'] = $id_eq;
		$infoOt = $this->Ordenservicios->getorden($id_ot);
		$ot = $infoOt ? $infoOt[0] : array('id_tarea' => null, 'descripcion' => '', 'tareadescrip' => '', 'responsable' => '', 'usrId' => '');
		// si la tarea es opcional (sin tarea estándar) el motivo es la descripción de la OT
		$data['causa'] = (($ot['id_tarea'] < 0) || ($ot['id_tarea'] == null)) ? $ot['descripcion'] : $ot['tareadescrip'];
		$data['id_solicitudServicio'] = $id_solicitud;
		$data['id_responsable'] = $ot['descripcion'];
		$data['nom_responsable'] = $ot['responsable'];
		$data['idresponsable'] = $ot['usrId'];
		$data['idTarBonita'] = $idTarBonita;
		$data['equipos'] = $this->Ordenservicios->getEquipos(array('id_equipo' => $id_eq));
		$data['lecturas'] = $this->Ordenservicios->getLecturasOrden($id_ot);
		$data['lecturasOT'] = $this->Otrabajos->getLecturasOrden($id_ot);
		$data['tareas'] = $this->Ordenservicios->getTareasOrden($id_ot);
		$data['herramientas'] = $this->Ordenservicios->getHerramOrdenes($id_ot);
		$data['insumos'] = $this->Ordenservicios->getInsumosPorOT($id_ot);
		$data['rrhh'] = $this->Ordenservicios->getOperariosOrden($id_ot);
		$data['evidencias'] = $this->Ordenservicios->getEvidenciasPorOT($id_ot);
		$this->load->view('tareas/view_presta_presta_conf_modal', $data);
	}

	/**
	* Ventana para cargar el informe de servicio de la tarea; si la OT ya tiene uno (la verificación lo devolvió), abre el de edición
	* @param int $idTarBonita id de la tarea "Confecciona informe servicio" (los otros datos de la dirección los trae asset por compatibilidad)
	*/
	public function cargarOrden($id_ot = null, $id_eq = null, $id_solicitud = null, $idTarBonita = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | cargarOrden($idTarBonita)");
		$data = $this->datosTarea($idTarBonita);
		if ($data === null) {
			return;
		}
		$this->load->view($data['tiene_informe'] ? 'tareas/view_edicion_inf_servicio_modal' : 'tareas/view_inf_servicio_modal', $data);
	}

	/** Ventana para editar el informe de servicio de la tarea. */
	public function editarInforme($id_ot = null, $id_eq = null, $id_solicitud = null, $idTarBonita = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | editarInforme($idTarBonita)");
		$data = $this->datosTarea($idTarBonita);
		if ($data !== null) {
			$this->load->view('tareas/view_edicion_inf_servicio_modal', $data);
		}
	}

	/**
	* Datos de la OT y el equipo de la tarea de Bonita (solo si es de la empresa) para las ventanas del informe
	* @return array|null datos para la vista; null (y mensaje en pantalla) si la tarea no es de la empresa
	*/
	private function datosTarea($idTarBonita){
		$det = $this->Tareas->detalle($idTarBonita);
		if ($det === null || !ctype_digit((string) $det['id_ot'])) {
			echo '<div class="alert alert-danger">No se pudo abrir el informe: la tarea no existe o no es de tu empresa.</div>';
			return null;
		}
		$idOt = (int) $det['id_ot'];
		$cab = $this->Tareas->cabeceraOt($idOt);
		$idEq = $cab ? $cab['id_equipo'] : 0;
		$infoOt = $this->Ordenservicios->getorden($idOt);
		$ot = $infoOt ? $infoOt[0] : array('id_tarea' => null, 'descripcion' => '', 'tareadescrip' => '', 'responsable' => '', 'usrId' => '');
		$ss = ctype_digit((string) $det['id_ss']) ? $det['id_ss'] : (ctype_digit((string) $det['sore_id']) ? $det['sore_id'] : 0);
		return array(
			'permission'           => preg_replace('/[^A-Za-z-]/', '', (string) $this->input->get('permission')),
			'tiene_informe'        => $cab && $cab['tiene_informe'] > 0,
			'id_ot'                => $idOt,
			'id_eq'                => $idEq,
			// si la tarea es opcional (sin tarea estándar) el motivo es la descripción de la OT
			'causa'                => (($ot['id_tarea'] < 0) || ($ot['id_tarea'] == null)) ? $ot['descripcion'] : $ot['tareadescrip'],
			'id_solicitudServicio' => $ss,
			'id_responsable'       => $ot['descripcion'],
			'nom_responsable'      => $ot['responsable'],
			'idresponsable'        => $ot['usrId'],
			'idTarBonita'          => (int) $idTarBonita,
			'equipos'              => $this->Ordenservicios->getEquipos(array('id_equipo' => $idEq)),
			'lecturas'             => $this->Ordenservicios->getLecturasOrden($idOt),
			'tareas'               => $this->Ordenservicios->getTareasOrden($idOt),
			'rrhh'                 => $this->Ordenservicios->getOperariosOrden($idOt),
		);
	}

	/** Datos del equipo del informe (autocompletado de la ventana) */
	public function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getEquipo()");
		echo json_encode($this->Ordenservicios->getEquipos($this->input->post()));
	}

	/** Usuarios de la empresa para elegir operarios */
	public function getOperario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getOperario()");
		echo json_encode($this->Ordenservicios->getOperarios());
	}

	/** Usuarios de las subtareas de la OT, propuestos como recursos humanos */
	public function getRRHHOrdenTrabajo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getRRHHOrdenTrabajo()");
		echo json_encode(array('recursos' => $this->Ordenservicios->getRRHHOrdenTrabajo($this->input->post('idOT'))));
	}

	/**
	* Guarda el informe de servicio y cierra la tarea. Al editar, las evidencias ya cargadas se conservan (asset las perdía: la pantalla
	* de edición no las muestra y el guardado borraba todo).
	* @return json status, msj y code
	*/
	public function setOrdenServ(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | setOrdenServ()");
		$d = $this->input->post('datosInfoServicio');
		$d = is_array($d) ? $d : array();
		$idTarBonita = isset($d['idTarBonita']) ? $d['idTarBonita'] : 0;
		$evidencias = array();
		if ($this->input->post('func') == 'editar') {
			$det = $this->Tareas->detalle($idTarBonita);
			foreach ($det !== null && ctype_digit((string) $det['id_ot']) ? $this->Ordenservicios->getEvidenciasPorOT($det['id_ot']) : array() as $e) {
				$evidencias[] = array('valor_base64' => $e['valor_base64'], 'nombre' => $e['nombre']);
			}
		} elseif (!empty($d['evidencias']) && is_array($d['evidencias'])) {
			foreach ($d['evidencias'] as $e) {
				$evidencias[] = array('valor_base64' => isset($e['valor_base64']) ? $e['valor_base64'] : '',
					'nombre' => isset($e['nombre_archivo']) ? $e['nombre_archivo'] : '');
			}
		}
		$r = $this->Tareas->guardarInforme($idTarBonita, $d, $this->input->post('tarea'), $this->input->post('operario'), $evidencias);
		echo json_encode(array('status' => $r['status'], 'msj' => $r['status'] ? 'OK' : $r['msj'], 'code' => $r['status'] ? 'Exito' : $r['msj']));
	}

	/**
	* Insumos pedidos por la OT
	*/
	public function getInsumosPorOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getInsumosPorOT()");
		echo json_encode($this->Ordenservicios->getInsumosPorOT($this->input->post('id_ot')));
	}

	/**
	* Entregas (parciales o totales) de los pedidos de la OT
	*/
	public function getEntregasPorOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getEntregasPorOT()");
		echo json_encode($this->Ordenservicios->getEntregasPorOT($this->input->post('id_ot')));
	}

	/**
	* Herramientas usadas en la OT, con días de uso
	*/
	public function getHerramientasUsadas(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getHerramientasUsadas()");
		echo json_encode($this->Ordenservicios->getHerramientasUsadas($this->input->post('id_ot')));
	}

	/**
	* Verifica los informes seleccionados: cierra la tarea "Verifica Informe de Servicio" de cada caso en Bonita, si el
	* usuario la tiene, registra la lectura de cierre y deja el informe y la OT cerrados. Si el caso no tiene esa tarea para
	* el usuario, igual cierra los estados (como asset).
	* Asset buscaba la tarea entre las 10 primeras de la bandeja del usuario (T32) y en el caso que mandaba el navegador (T34):
	* acá se busca en el caso de la OT, dentro de la empresa.
	*/
	public function cierreMasivoVerificaInforme(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | cierreMasivoVerificaInforme()");
		$selected_orders = $this->input->post('selected');
		if (empty($selected_orders) || !is_array($selected_orders)) {
			echo json_encode(array('status' => false, 'msj' => 'No se seleccionaron órdenes'));
			return;
		}
		$results = array();
		foreach ($selected_orders as $order) {
			$id_orden_servicio = isset($order['id_orden']) ? $order['id_orden'] : '';
			$rsp = $this->Ordenservicios->verificarInforme(isset($order['id_ot']) ? $order['id_ot'] : 0);
			if (!$rsp['status']) {
				$results[] = array('id_orden' => $id_orden_servicio, 'status' => 'ERROR_BPM', 'msj' => $rsp['msj']);
			} else {
				$results[] = array('id_orden' => $id_orden_servicio, 'status' => $rsp['bpm'] === 'si' ? 'OK' : 'OK_NO_BPM');
			}
		}
		echo json_encode($results);
	}
}
