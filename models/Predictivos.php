<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Predictivos: ABM de planes predictivos con herramientas e insumos.
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork). Datos por MANDataService (ADR-014), limitado a
 * la empresa de la sesión; herramientas e insumos del pañol y del almacén de tools (man_tools_helper). El alta con
 * detalle va por toolsMANAPI (regla 3). Se conservan firmas y formato de salida del model de asset (E9: solo lo que
 * usan las acciones portadas).
 */
class Predictivos extends CI_Model
{
	/** @var int id del último predictivo creado (lo que en asset se leía con insert_id()). */
	public $ultimo_id = 0;

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('man', 'man_tools'));
	}

	/** Listado de predictivos no anulados. @return array array('data' => filas o 0) */
	function predictivo_List(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | predictivo_List()");
		$filas = man_lista(wso2(REST_MAN . '/predictivos/empresa/' . man_empresa()));
		return array('data' => $filas ? $filas : 0);
	}

	/** Equipos no anulados (id y código). @return array|false */
	function getEquipos(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getEquipos()");
		$filas = man_lista(wso2(REST_MAN . '/equipos/no-anulados/codigos/empresa/' . man_empresa()));
		return $filas ? $filas : false;
	}

	/** Equipo con su marca (solo si tiene marca, como en asset). @return array|false filas */
	function getInfoEquipos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getInfoEquipos(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $id . '/con-marca/empresa/' . man_empresa()));
		if (!$filas || $filas[0]['marcaid'] === null) {
			return false;
		}
		unset($filas[0]['marca_estado'], $filas[0]['marca_empresa']);
		return $filas;
	}

	/** Unidades de tiempo. @return array|false objetos */
	function getUnidTiempos(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getUnidTiempos()");
		$filas = man_lista(wso2(REST_MAN . '/unidades-tiempo'));
		return $filas ? json_decode(json_encode($filas)) : false;
	}

	/**
	 * Alta de predictivo con herramientas e insumos (toolsMANAPI). El id queda en $this->ultimo_id.
	 * @return bool
	 */
	function insert_predictivo($data, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | insert_predictivo(" . json_encode($data) . ")");
		$id = man_alta_con_detalle('predictivo', array(
			'id_equipo'     => (string) $data['id_equipo'],
			'tarea_descrip' => $data['tarea_descrip'] === null ? null : (string) $data['tarea_descrip'],
			'fecha'         => man_fecha($data['fecha']),
			'periodo'       => $data['periodo'] === null ? null : (string) $data['periodo'],
			'cantidad'      => man_numero($data['cantidad']),
			'horash'        => man_numero($data['horash']),
			'pred_duracion' => man_numero($data['pred_duracion']),
			'pred_canth'    => man_numero($data['pred_canth']),
			'id_empresa'    => man_empresa(),
			'id_unidad'     => man_numero($data['id_unidad']),
		), $herramientas, $insumos);
		$this->ultimo_id = $id ? (int) $id : 0;
		return $id !== false;
	}

	/** Guarda el nombre del adjunto. @return array el mismo $adjunto, como en asset */
	function updateAdjunto($adjunto, $ultimoId){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | updateAdjunto(" . json_encode($adjunto) . ", $ultimoId)");
		man_escribir('PUT', 'predictivo/adjunto', array(
			'pred_adjunto' => (string) $adjunto['pred_adjunto'],
			'predId'       => (string) $ultimoId,
			'id_empresa'   => man_empresa(),
		));
		return $adjunto;
	}

	/** Borra el nombre del adjunto (el archivo queda en disco, como en asset). @return bool */
	function eliminarAdjunto($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | eliminarAdjunto($id)");
		$rsp = man_escribir('PUT', 'predictivo/adjunto', array('pred_adjunto' => '', 'predId' => (string) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** ¿El predictivo es de la empresa de la sesión? */
	function esDeLaEmpresa($id){
		return (bool) man_lista(wso2(REST_MAN . '/predictivos/de-empresa/' . man_empresa() . '/predictivo/' . (int) $id));
	}

	/** Predictivo a editar. @return array|false objetos */
	function getInfopred($ide, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getInfopred(" . json_encode($ide) . ", " . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/predictivo/' . (int) $id . '/equipo/' . (int) $ide . '/empresa/' . man_empresa()));
		return $filas ? json_decode(json_encode($filas)) : false;
	}

	/** Herramientas del predictivo, completadas con el pañol de tools. @return array */
	function getPredictivoHerramientas($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getPredictivoHerramientas(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/predictivo/' . (int) $id . '/herramientas/empresa/' . man_empresa()));
		return tools_merge_herramientas($filas ? $filas : array());
	}

	/** Insumos del predictivo, completados con el almacén de tools. @return array */
	function getPredictivoInsumos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | getPredictivoInsumos(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/predictivo/' . (int) $id . '/insumos/empresa/' . man_empresa()));
		return tools_merge_articulos($filas ? $filas : array());
	}

	/**
	 * Edita el predictivo y reemplaza herramientas e insumos. La cabecera y el borrado del detalle van en un
	 * request_box; cada lista nueva, en un pedido por lotes.
	 * @return array updatedatos, respHerram, respInsumo
	 */
	function updatePredictivos($datos, $id, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | updatePredictivos(" . json_encode($datos) . ", $id)");
		$emp = man_empresa();
		$clave = array('predId' => (string) $id, 'id_empresa' => $emp);
		$rsp = requestBox(REST_MAN . '/', array(
			array('_put_predictivo' => array(
				'predId'        => (string) $id,
				'tarea_descrip' => (string) $datos['tarea_descrip'],
				'fecha'         => man_fecha($datos['fecha']),
				'periodo'       => (string) $datos['periodo'],
				'cantidad'      => man_numero($datos['cantidad']),
				'horash'        => man_numero($datos['horash']),
				'pred_duracion' => man_numero($datos['pred_duracion']),
				'id_unidad'     => man_numero($datos['id_unidad']),
				'pred_canth'    => man_numero($datos['pred_canth']),
				'id_empresa'    => $emp,
			)),
			array('_put_predictivo_herramientas_borrar' => $clave),
			array('_put_predictivo_insumos_borrar' => $clave),
		));
		$out = array('updatedatos' => (bool) $rsp['status']);
		if (!$out['updatedatos']) {
			return $out;
		}
		$out['respHerram'] = man_lote_detalle('predictivo', 'herramienta', $this->conClave($herramientas, $clave));
		$out['respInsumo'] = man_lote_detalle('predictivo', 'insumo', $this->conClave($insumos, $clave));
		return $out;
	}

	/** Cambia el estado del predictivo. @return bool */
	function baja_predictivos($datos, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivos | baja_predictivos(" . json_encode($datos) . ", $id)");
		$rsp = man_escribir('PUT', 'predictivo/estado', array(
			'estado'     => (string) $datos['estado'],
			'predId'     => (string) $id,
			'id_empresa' => man_empresa(),
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
