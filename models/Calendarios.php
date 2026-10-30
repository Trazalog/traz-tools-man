<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plan de Mantenimiento (Calendario) — lecturas y cambios simples.
 *
 * MIG-ASSET-4: reescrito (no portado). Todos los datos por MANDataService (ADR-014), limitados a la empresa de la sesión. Programar
 * órdenes y asignar responsable no están acá: son recursos de toolsMANAPI (Bonita y base en una sola operación). Especificación:
 * doc/migracion/MIG-4-CALENDARIO.md del repositorio tools.
 */
class Calendarios extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
		$this->load->model('Tareas');
	}

	/**
	* Períodos activos de la empresa, para el selector de Preventivo, Predictivo, OT y Solicitudes
	* @param array $data lo que manda el navegador; sin nada no se devuelve nada, como asset
	* @return array|false filas (idperiodo, descripcion)
	*/
	function getperiodo($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getperiodo(" . json_encode($data) . ")");
		if (empty($data)) {
			return false;
		}
		$filas = man_lista(wso2(REST_MAN . '/periodos/empresa/' . man_empresa()));
		if (!$filas) {
			return false;
		}
		$out = array();
		foreach ($filas as $f) {
			$out[] = array('idperiodo' => $f['idperiodo'], 'descripcion' => $f['descripcion']);
		}
		return $out;
	}

	/**
	* Órdenes de la empresa para el calendario en el rango [desde, hasta)
	* @param string $desde aaaa-mm-dd
	* @param string $hasta aaaa-mm-dd (no incluido)
	* @return array|false filas, o false si no hay
	*/
	function getot($desde, $hasta){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getot($desde, $hasta)");
		$filas = man_lista(wso2(REST_MAN . '/calendario/ot/empresa/' . man_empresa() . '/desde/' . $desde . '/hasta/' . $hasta));
		return $filas ? $filas : false;
	}

	/**
	* Opciones del filtro: equipos, áreas, grupos, sectores y orígenes
	* @return StdClass listas de objetos con "descripcion"
	*/
	function opcionesFiltro(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | opcionesFiltro()");
		$res = new StdClass();
		$emp = man_empresa();
		foreach (array('equipos', 'areas', 'grupos', 'sectores') as $k) {
			$res->$k = $this->opciones('/calendario/filtro/' . $k . '/empresa/' . $emp);
		}
		$res->origenes = $this->opciones('/calendario/filtro/origenes');
		return $res;
	}

	/** Lista de objetos con "descripcion" (vacía si no hay) */
	private function opciones($recurso){
		$filas = man_lista(wso2(REST_MAN . $recurso));
		return $filas ? json_decode(json_encode($filas)) : array();
	}

	/**
	* Preventivos por tiempo cuya próxima fecha cae en el mes
	* @param int $mes 1 a 12
	* @param int $year
	* @return array|false filas, o false si no hay
	*/
	function getPreventivos($mes, $year){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getPreventivos($mes, $year)");
		$filas = man_lista(wso2(REST_MAN . '/plan/preventivos/empresa/' . man_empresa() . '/mes/' . $this->ym($mes, $year)));
		return $filas ? $filas : false;
	}

	/** Preventivos por contador (horas, ciclos, kilómetros) que ya pasaron la alerta. @return array|false */
	function getPreventivosHoras(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getPreventivosHoras()");
		$filas = man_lista(wso2(REST_MAN . '/plan/preventivos-contador/empresa/' . man_empresa()));
		return $filas ? $filas : false;
	}

	/** Backlogs por programar en el mes. @return array|false */
	function getbacklog($mes, $year){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getbacklog($mes, $year)");
		$filas = man_lista(wso2(REST_MAN . '/plan/backlogs/empresa/' . man_empresa() . '/mes/' . $this->ym($mes, $year)));
		return $filas ? $filas : false;
	}

	/** Predictivos por programar en el mes. @return array|false */
	function getpredlist($mes, $year){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getpredlist($mes, $year)");
		$filas = man_lista(wso2(REST_MAN . '/plan/predictivos/empresa/' . man_empresa() . '/mes/' . $this->ym($mes, $year)));
		return $filas ? $filas : false;
	}

	/**
	* Solicitudes urgentes por programar: las de los casos de las tareas pendientes del usuario en Bonita (asset recibía esa lista del
	* navegador; acá la pide el servidor, T54). Cada fila lleva el número de la tarea de Bonita ("tarea_id").
	* @return array|false filas, o false si no hay
	*/
	function getServicioTareas($mes, $year){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getServicioTareas($mes, $year)");
		$tareas = $this->Tareas->todas();
		if (empty($tareas)) {
			return false;
		}
		$porCaso = array();
		foreach ($tareas as $t) {
			$porCaso[$t['caseId']][] = $t['id'];
		}
		$rsp = man_escribir('POST', 'plan/correctivos', array(
			'id_empresa' => man_empresa(),
			'ym'         => $this->ym($mes, $year),
			'ids'        => implode(',', array_keys($porCaso)),
		));
		$filas = man_lista($rsp);
		if (!$filas) {
			return false;
		}
		// Una fila por tarea del caso, como asset (cada tarea de Bonita llevaba los datos de su solicitud)
		$out = array();
		foreach ($filas as $f) {
			foreach ($porCaso[$f['case_id']] as $idTarea) {
				$f['id'] = $idTarea;
				$out[] = $f;
			}
		}
		return $out;
	}

	/**
	* Nueva fecha y hora de una orden de la empresa
	* @param string $diaNuevo aaaa-mm-dd hh:mm:ss
	* @return bool false si la orden no es de la empresa
	*/
	function updateDiaProgramacion($id, $diaNuevo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | updateDiaProgramacion($id, $diaNuevo)");
		$rsp = man_escribir('PUT', 'calendario/ot/fecha', array(
			'fecha_program' => (string) $diaNuevo,
			'id_orden'      => (string) (int) $id,
			'id_empresa'    => man_empresa(),
		));
		return $this->afectadas($rsp);
	}

	/**
	* Suma minutos a la duración de una orden de la empresa
	* @return bool false si la orden no es de la empresa
	*/
	function updateDuraciones($id, $minutos){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | updateDuraciones($id, $minutos)");
		$rsp = man_escribir('PUT', 'calendario/ot/duracion', array(
			'minutos'    => (string) (float) $minutos,
			'id_orden'   => (string) (int) $id,
			'id_empresa' => man_empresa(),
		));
		return $this->afectadas($rsp);
	}

	/**
	* Programa órdenes de trabajo desde un preventivo, predictivo, backlog o solicitud urgente (toolsMANAPI POST /tools/man/plan/programar).
	* La API lee el origen de la empresa, crea las órdenes (una o una serie), lanza o cierra lo que corresponde en Bonita y deja los
	* estados; si algo falla, deshace lo creado. Asset hacía todo esto desde PHP y dejaba restos si fallaba (T3, T51).
	* @param int $tipo 2 solicitud urgente, 3 preventivo, 4 backlog, 5 predictivo
	* @param string $fechaProgr dd-mm-aaaa
	* @param string $horaProgr hh:mm
	* @param int $evento 1 una orden, 2 serie (solo preventivo y predictivo)
	* @return array status (true|false) y msj si falla
	*/
	function programar($tipo, $idOrigen, $fechaProgr, $horaProgr, $evento, $cantMeses, $horas){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendarios | programar($tipo, $idOrigen, $fechaProgr, $horaProgr, $evento, $cantMeses, $horas)");
		if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fechaProgr, $f) || !checkdate((int) $f[2], (int) $f[1], (int) $f[3])) {
			return array('status' => false, 'msj' => 'La fecha no es válida');
		}
		if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $horaProgr)) {
			return array('status' => false, 'msj' => 'La hora no es válida');
		}
		$rsp = $this->rest->callAPI('POST', HOST . '/tools/man/plan/programar', array(
			'id_empresa' => man_empresa(),
			'emprId'     => (string) empresa(),
			'usrId'      => man_usuario(),
			'usrNick'    => (string) userNick(),
			'session'    => $this->session->has_userdata('bpm_token') ? $this->session->userdata('bpm_token') : '',
			'tipo'       => (string) (int) $tipo,
			'id_origen'  => (string) (int) $idOrigen,
			'fecha'      => $f[3] . '-' . $f[2] . '-' . $f[1] . ' ' . $horaProgr . ':00',
			'evento'     => (string) (int) $evento,
			'cant_meses' => (string) $cantMeses,
			'horas'      => ((int) $horas === 1) ? '1' : '0',
		));
		$aux = json_decode($rsp['data'], true);
		if (!$rsp['status'] || empty($aux['status'])) {
			log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | Calendarios | programar() >> ' . json_encode($rsp['data']));
			return array('status' => false, 'msj' => isset($aux['msj']) ? $aux['msj'] : 'No se pudo programar');
		}
		return array('status' => true, 'cuenta' => isset($aux['cuenta']) ? $aux['cuenta'] : 1);
	}

	/** "aaaa-mm" */
	private function ym($mes, $year){
		return sprintf('%04d-%02d', (int) $year, (int) $mes);
	}

	/** true si el cambio tocó alguna fila */
	private function afectadas($rsp){
		if (!$rsp['status']) {
			return false;
		}
		$d = json_decode(json_encode($rsp['data']), true);
		return is_array($d) && isset($d['filas']) && (int) $d['filas'] > 0;
	}
}
