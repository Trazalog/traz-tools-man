<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Preventivos: ABM de planes preventivos con herramientas e insumos.
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork). Datos por MANDataService (ADR-014), limitado a
 * la empresa de la sesión; herramientas e insumos del pañol y del almacén de tools (man_tools_helper). El alta con
 * detalle va por toolsMANAPI (regla 3). Se conservan firmas y formato de salida del model de asset. No se portan los
 * métodos que ninguna acción usada llama (E9). El calendario y la regla del contador quedan para MIG-4.
 */
class Preventivos extends CI_Model
{
	/** @var int id del último preventivo creado (lo que en asset se leía con insert_id()). */
	public $ultimo_id = 0;

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('man', 'man_tools'));
	}

	/** Listado de preventivos no anulados. @return array|null array('data' => filas) o null si no hay */
	function preventivos_List(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | preventivos_List()");
		$filas = man_lista(wso2(REST_MAN . '/preventivos/empresa/' . man_empresa()));
		return $filas ? array('data' => $filas) : null;
	}

	/** Unidades de tiempo. @return array|false objetos */
	function getUnidTiempos(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getUnidTiempos()");
		$filas = man_lista(wso2(REST_MAN . '/unidades-tiempo'));
		return $filas ? json_decode(json_encode($filas)) : false;
	}

	/** Equipo con la descripción de su marca. @return object|false */
	function getEquipoNuevoPrevent($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getEquipoNuevoPrevent(" . json_encode($data) . ")");
		$filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $data['id_equipo'] . '/con-marca/empresa/' . man_empresa()));
		if (!$filas) {
			return false;
		}
		$fila = $filas[0];
		unset($fila['marcaid'], $fila['marca_estado'], $fila['marca_empresa']);
		return (object) $fila;
	}

	/** Tareas activas (value, label). @return array|false */
	function gettarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | gettarea()");
		return $this->tareas('');
	}

	/** Tareas activas cuya descripción contiene el patrón. @return array|false */
	function gettareaxPatron($dato){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | gettareaxPatron(" . json_encode($dato) . ")");
		return $this->tareas((string) $dato);
	}

	/** Componentes activos asociados al equipo. @return array|false */
	function getcomponente($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getcomponente(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/preventivos/componentes/equipo/' . (int) $id . '/empresa/' . man_empresa()));
		return $filas ? $filas : false;
	}

	/** Herramientas disponibles del pañol de tools, con las columnas de la tabla vieja de asset. */
	function getherramienta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getherramienta()");
		return tools_herramientas_full();
	}

	/** Herramientas disponibles para el autocompletar. */
	function getHerramientasB(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getHerramientasB()");
		return tools_herramientas_autocomplete();
	}

	/** Artículos de tools para el autocompletar de insumos. */
	function getinsumo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getinsumo()");
		return tools_articulos_autocomplete();
	}

	/**
	 * Datos de una herramienta. En asset se buscaba en la tabla vieja `herramientas`, aunque desde la integración
	 * con el pañol (F4) los ids son de tools: acá se busca en el pañol de tools (decisión E18).
	 * @return array|false objetos con herrId/herrcodigo/herrmarca/herrdescrip/...
	 */
	function getdatos($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getdatos(" . json_encode($data) . ")");
		if ($data == null || !isset($data['id_herramienta'])) {
			return false;
		}
		foreach (tools_herramientas_full() as $h) {
			if ((string) $h->herrId === (string) $data['id_herramienta']) {
				return array($h);
			}
		}
		return false;
	}

	/**
	 * Alta de preventivo con herramientas e insumos (toolsMANAPI). El id queda en $this->ultimo_id.
	 * @return bool
	 */
	function insert_preventivo($data, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | insert_preventivo(" . json_encode($data) . ")");
		$id = man_alta_con_detalle('preventivo', array(
			'id_equipo'     => (string) $data['id_equipo'],
			'id_tarea'      => man_numero($data['id_tarea']),
			'perido'        => $data['perido'] === null ? null : (string) $data['perido'],
			'cantidad'      => man_numero($data['cantidad']),
			'ultimo'        => man_fecha($data['ultimo']),
			'id_componente' => man_numero($data['id_componente']),
			'critico1'      => man_numero($data['critico1']),
			'horash'        => man_numero($data['horash']),
			'prev_duracion' => man_numero($data['prev_duracion']),
			'id_unidad'     => man_numero($data['id_unidad']),
			'prev_canth'    => man_numero($data['prev_canth']),
			'id_empresa'    => man_empresa(),
			'lectura_base'  => man_numero($data['lectura_base']),
		), $herramientas, $insumos);
		$this->ultimo_id = $id ? (int) $id : 0;
		return $id !== false;
	}

	/** Guarda el nombre del adjunto. @return array el mismo $adjunto, como en asset */
	function updateAdjunto($adjunto, $ultimoId){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | updateAdjunto(" . json_encode($adjunto) . ", $ultimoId)");
		man_escribir('PUT', 'preventivo/adjunto', array(
			'prev_adjunto' => (string) $adjunto['prev_adjunto'],
			'prevId'       => (string) $ultimoId,
			'id_empresa'   => man_empresa(),
		));
		return $adjunto;
	}

	/** Cambia el estado del preventivo. @return bool */
	public function update_preventivo($data, $idprev){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | update_preventivo(" . json_encode($data) . ", $idprev)");
		$rsp = man_escribir('PUT', 'preventivo/estado', array(
			'estadoprev' => (string) $data['estadoprev'],
			'prevId'     => (string) $idprev,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Preventivo a editar. @return array|int filas o 0 */
	function getInfoPreventivo($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getInfoPreventivo(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/preventivo/' . (int) $id . '/datos/empresa/' . man_empresa()));
		return $filas ? $filas : 0;
	}

	/** Herramientas del preventivo, completadas con el pañol de tools. @return array */
	function getPreventivoHerramientas($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getPreventivoHerramientas(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/preventivo/' . (int) $id . '/herramientas/empresa/' . man_empresa()));
		return tools_merge_herramientas($filas ? $filas : array());
	}

	/** Insumos del preventivo, completados con el almacén de tools. @return array */
	function getPreventivoInsumos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getPreventivoInsumos(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/preventivo/' . (int) $id . '/insumos/empresa/' . man_empresa()));
		return tools_merge_articulos($filas ? $filas : array());
	}

	/**
	 * Edita el preventivo y, si se indica, reemplaza herramientas e insumos. La cabecera y el borrado del detalle van
	 * en un request_box; cada lista nueva, en un pedido por lotes.
	 * @param array $herramientas lista de array(herrId, cantidad) que reemplaza a las actuales (vacía = sin herramientas)
	 * @param array $insumos      lista de array(artId, cantidad) que reemplaza a los actuales
	 * @return array resPrenvent, respHerram, respInsumo (como en asset)
	 */
	function update_editar($data, $idp, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | update_editar(" . json_encode($data) . ", $idp)");
		$emp = man_empresa();
		$clave = array('prevId' => (string) $idp, 'id_empresa' => $emp);
		$rsp = requestBox(REST_MAN . '/', array(
			array('_put_preventivo' => array(
				'prevId'        => (string) $idp,
				'id_equipo'     => (string) $data['id_equipo'],
				'id_tarea'      => man_numero($data['id_tarea']),
				'perido'        => (string) $data['perido'],
				'cantidad'      => man_numero($data['cantidad']),
				'ultimo'        => man_fecha($data['ultimo']),
				'id_componente' => man_numero($data['id_componente']),
				'horash'        => man_numero($data['horash']),
				'prev_duracion' => man_numero($data['prev_duracion']),
				'id_unidad'     => man_numero($data['id_unidad']),
				'prev_canth'    => man_numero($data['prev_canth']),
				'id_empresa'    => $emp,
			)),
			array('_put_preventivo_herramientas_borrar' => $clave),
			array('_put_preventivo_insumos_borrar' => $clave),
		));
		$out = array('resPrenvent' => (bool) $rsp['status']);
		if (!$out['resPrenvent']) {
			return $out;
		}
		$out['respHerram'] = man_lote_detalle('preventivo', 'herramienta', $this->conClave($herramientas, $clave));
		$out['respInsumo'] = man_lote_detalle('preventivo', 'insumo', $this->conClave($insumos, $clave));
		return $out;
	}

	/** Borra el nombre del adjunto (el archivo queda en disco, como en asset). @return bool */
	public function eliminarAdjunto($idPreventivo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | eliminarAdjunto($idPreventivo)");
		$rsp = man_escribir('PUT', 'preventivo/adjunto', array(
			'prev_adjunto' => '',
			'prevId'       => (string) $idPreventivo,
			'id_empresa'   => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Tareas activas de la empresa (value, label), filtradas por patrón si viene. */
	private function tareas($patron){
		$filas = man_lista(wso2(REST_MAN . '/tareas/activas/empresa/' . man_empresa() . '?patron=' . rawurlencode($patron)));
		return $filas ? $filas : false;
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
