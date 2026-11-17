<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Backlogs: ABM de backlog con herramientas e insumos.
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork). Datos por MANDataService (ADR-014), limitado a
 * la empresa de la sesión; herramientas e insumos del pañol y del almacén de tools (man_tools_helper). El alta con
 * detalle va por toolsMANAPI (regla 3). Se conservan firmas y formato de salida del model de asset (E9: solo lo que
 * usan las acciones portadas).
 */
class Backlogs extends CI_Model
{
	/** @var int id del último backlog creado (lo que en asset se leía con insert_id()). */
	public $ultimo_id = 0;

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('man', 'man_tools'));
	}

	/** Listado de backlogs no borrados. @return array array('data' => filas o 0) */
	function backlog_List(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | backlog_List()");
		$filas = man_lista(wso2(REST_MAN . '/backlogs/empresa/' . man_empresa()));
		return array('data' => $filas ? $filas : 0);
	}

	/** Equipos no inhabilitados de la empresa (todas las columnas). @return array|false */
	function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getequipo()");
		$filas = man_lista(wso2(REST_MAN . '/equipos/no-inhabilitados/empresa/' . man_empresa()));
		return $filas ? $filas : false;
	}

	/** Componentes de un equipo (o de todos, si no viene) para el autocompletar. @return array|false objetos */
	function getComponentes($idEquipo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getComponentes(" . json_encode($idEquipo) . ")");
		$filas = man_lista(wso2(REST_MAN . '/backlogs/componentes/empresa/' . man_empresa() . '?id_equipo=' . rawurlencode((string) $idEquipo)));
		return $filas ? json_decode(json_encode($filas)) : false;
	}

	/**
	 * Equipo con su marca (E.*, M.*): las columnas de la marca pisan estado e id_empresa del equipo, y quedan en
	 * null si el equipo no tiene marca, como en asset.
	 * @return object|false
	 */
	function getInfoEquipos($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getInfoEquipos(" . json_encode($data) . ")");
		if ($data == null) {
			return false;
		}
		$filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $data['id_equipo'] . '/con-marca/empresa/' . man_empresa()));
		if (!$filas) {
			return false;
		}
		$fila = $filas[0];
		$fila['estado'] = $fila['marca_estado'];
		$fila['id_empresa'] = $fila['marca_empresa'];
		unset($fila['marca_estado'], $fila['marca_empresa']);
		return (object) $fila;
	}

	/** Cambia el estado del backlog. @return bool */
	function update_back($data, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | update_back(" . json_encode($data) . ", $id)");
		$rsp = man_escribir('PUT', 'backlog/estado', array(
			'estado'     => (string) $data['estado'],
			'backId'     => (string) $id,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Alta de backlog con herramientas e insumos (toolsMANAPI). El id queda en $this->ultimo_id.
	 * @return bool
	 */
	function insert_backlog($data, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | insert_backlog(" . json_encode($data) . ")");
		$id = man_alta_con_detalle('backlog', array(
			'id_equipo'          => (string) $data['id_equipo'],
			'id_tarea'           => man_numero($data['id_tarea']),
			'fecha'              => man_fecha($data['fecha'], true),
			'horash'             => man_numero($data['horash']),
			'back_duracion'      => man_numero($data['back_duracion']),
			'id_unidad'          => man_numero($data['id_unidad']),
			'back_canth'         => man_numero($data['back_canth']),
			'id_empresa'         => man_empresa(),
			'idcomponenteequipo' => man_numero($data['idcomponenteequipo']),
			'tarea_opcional'     => $data['tarea_opcional'] === null ? null : (string) $data['tarea_opcional'],
		), $herramientas, $insumos);
		$this->ultimo_id = $id ? (int) $id : 0;
		return $id !== false;
	}

	/** Guarda el nombre del adjunto. @return array el mismo $adjunto, como en asset */
	function updateAdjunto($adjunto, $ultimoId){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | updateAdjunto(" . json_encode($adjunto) . ", $ultimoId)");
		man_escribir('PUT', 'backlog/adjunto', array(
			'back_adjunto' => (string) $adjunto['back_adjunto'],
			'backId'       => (string) $ultimoId,
			'id_empresa'   => man_empresa(),
		));
		return $adjunto;
	}

	/** Borra el nombre del adjunto (el archivo queda en disco, como en asset). @return bool */
	function eliminarAdjunto($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | eliminarAdjunto($id)");
		$rsp = man_escribir('PUT', 'backlog/adjunto', array('back_adjunto' => '', 'backId' => (string) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** Backlog a editar. @return array|int filas o 0 */
	function geteditar($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | geteditar(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/backlog/' . (int) $id . '/datos/empresa/' . man_empresa()));
		return $filas ? $filas : 0;
	}

	/** Equipo, marca y componente del backlog. @return array|int filas o 0 */
	function traerequiposBack($ide, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | traerequiposBack(" . json_encode($ide) . ", " . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/backlog/' . (int) $id . '/equipo/' . (int) $ide . '/empresa/' . man_empresa()));
		return $filas ? $filas : 0;
	}

	/** Herramientas del backlog, completadas con el pañol de tools. @return array */
	function getBacklogHerramientas($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getBacklogHerramientas(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/backlog/' . (int) $id . '/herramientas/empresa/' . man_empresa()));
		return tools_merge_herramientas($filas ? $filas : array());
	}

	/** Insumos del backlog, completados con el almacén de tools. @return array */
	function getBacklogInsumos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getBacklogInsumos(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/backlog/' . (int) $id . '/insumos/empresa/' . man_empresa()));
		return tools_merge_articulos($filas ? $filas : array());
	}

	/**
	 * Edita el backlog y reemplaza herramientas e insumos. La cabecera y el borrado del detalle van en un
	 * request_box; cada lista nueva, en un pedido por lotes.
	 * @return array editado, respHerram, respInsumo
	 */
	function editar_backlogs($datos, $id_back, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | editar_backlogs(" . json_encode($datos) . ", $id_back)");
		$emp = man_empresa();
		$clave = array('backId' => (string) $id_back, 'id_empresa' => $emp);
		$rsp = requestBox(REST_MAN . '/', array(
			array('_put_backlog' => array(
				'backId'         => (string) $id_back,
				'id_tarea'       => man_numero($datos['id_tarea']),
				'fecha'          => man_fecha($datos['fecha'], true),
				'horash'         => man_numero($datos['horash']),
				'back_duracion'  => man_numero($datos['back_duracion']),
				'back_canth'     => man_numero($datos['back_canth']),
				'id_unidad'      => man_numero($datos['id_unidad']),
				'tarea_opcional' => (string) $datos['tarea_opcional'],
				'id_empresa'     => $emp,
			)),
			array('_put_backlog_herramientas_borrar' => $clave),
			array('_put_backlog_insumos_borrar' => $clave),
		));
		$out = array('editado' => (bool) $rsp['status']);
		if (!$out['editado']) {
			return $out;
		}
		$out['respHerram'] = man_lote_detalle('backlog', 'herramienta', $this->conClave($herramientas, $clave));
		$out['respInsumo'] = man_lote_detalle('backlog', 'insumo', $this->conClave($insumos, $clave));
		return $out;
	}

	/**
	 * Deja el backlog Solicitado (S) y con el componente elegido: lo que hace asset al editarlo desde la tarea Editar Backlog.
	 * @return bool
	 */
	function reenviar($id_back, $idcomponenteequipo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlogs | reenviar($id_back, " . json_encode($idcomponenteequipo) . ")");
		$componente = ctype_digit((string) $idcomponenteequipo) ? (string) $idcomponenteequipo : '0';
		$rsp = man_escribir('PUT', 'backlog/reenviar', array(
			'backId'              => (string) (int) $id_back,
			'idcomponenteequipo'  => $componente,
			'idcomponenteequipo2' => $componente,
			'id_empresa'          => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Agrega la clave de la cabecera a cada fila de detalle. */
	private function conClave($filas, $clave){
		$out = array();
		foreach ((array) $filas as $f) {
			$out[] = array_merge($f, $clave);
		}
		return $out;
	}
}
