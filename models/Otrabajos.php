<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Órdenes de trabajo.
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork). Datos por MANDataService (ADR-014), limitado a la
 * empresa de la sesión (asset no filtraba getpencil, getOrigenOt, getcliente, getpedidos ni las vistas de la OT);
 * herramientas e insumos del pañol y del almacén de tools (man_tools_helper). Lo que habla con Bonita va por
 * toolsMANAPI y la plantilla BPM (A5). Se conservan firmas y formato de salida del model de asset (E9).
 */
class Otrabajos extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('man', 'man_tools'));
	}

	/** Proveedores activos. @return array|false */
	function getproveedor(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getproveedor()");
		return $this->filas('/ots/proveedores/empresa/' . man_empresa());
	}

	/** Sucursales. @return array|false objetos */
	function traer_sucursal(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | traer_sucursal()");
		return $this->objetos('/ots/sucursales/empresa/' . man_empresa());
	}

	/** Equipos no anulados (todas las columnas). @return array|false objetos */
	function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getequipo()");
		return $this->objetos('/equipos/no-anulados/empresa/' . man_empresa());
	}

	/** Equipos no anulados (id y código). @return array|false */
	function getEquiposNuevaOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getEquiposNuevaOT()");
		return $this->filas('/equipos/no-anulados/codigos/empresa/' . man_empresa());
	}

	/** Equipo con marca y cliente. @return array|false */
	function getInfoEquiposNuevaOT($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getInfoEquiposNuevaOT(" . json_encode($id) . ")");
		return $this->filas('/ots/equipo/' . (int) $id . '/info/empresa/' . man_empresa());
	}

	/** OT a editar. @return array|int filas o 0 */
	function getpencil($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getpencil(" . json_encode($id) . ")");
		$filas = $this->filas('/ot/' . (int) $id . '/editar/empresa/' . man_empresa());
		return $filas ? $filas : 0;
	}

	/** Herramientas de la OT: las del pañol de tools completadas, más las de texto libre. @return array */
	function getOTHerramientas($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTHerramientas(" . json_encode($id) . ")");
		$rows = $this->filas('/ot/' . (int) $id . '/herramientas/empresa/' . man_empresa());
		$libres = array();
		$catalogo = array();
		foreach ((array) $rows as $r) {
			if ((int) $r['herrId'] === 0 && !empty($r['descripcion_libre'])) {
				$libres[] = array(
					'herrId'      => 0,
					'cantidad'    => $r['cantidad'],
					'codigo'      => '(texto libre)',
					'marca'       => '',
					'descripcion' => $r['descripcion_libre'],
				);
			} else {
				$catalogo[] = $r;
			}
		}
		return array_merge(tools_merge_herramientas($catalogo), $libres);
	}

	/** Insumos de la OT, completados con el almacén de tools. @return array */
	function getOTInsumos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTInsumos(" . json_encode($id) . ")");
		$rows = $this->filas('/ot/' . (int) $id . '/insumos/empresa/' . man_empresa());
		return tools_merge_articulos($rows ? $rows : array());
	}

	/** Adjuntos de la OT. @return array|int filas o 0 */
	function getOTadjuntos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTadjuntos(" . json_encode($id) . ")");
		$filas = $this->filas('/ot/' . (int) $id . '/adjuntos/empresa/' . man_empresa());
		return $filas ? $filas : 0;
	}

	/** Datos de la OT para asignar. @return array|false */
	function getasigna($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getasigna(" . json_encode($id) . ")");
		return $this->filas('/ot/' . (int) $id . '/asigna/empresa/' . man_empresa());
	}

	/** Usuarios de asset de la empresa (usrId, usrName, usrLastName). @return array|null */
	function getusuario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getusuario()");
		$filas = $this->filas('/usuarios/empresa/' . man_empresa());
		return $filas ? $filas : null;
	}

	/** Clientes en estado C. @return array|false */
	function traer_cli(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | traer_cli()");
		return $this->filas('/ots/clientes/empresa/' . man_empresa());
	}

	/** OT con los datos de su cliente (SELECT * de las dos tablas, como en asset). @return array|int filas o 0 */
	function getpedidos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getpedidos(" . json_encode($id) . ")");
		$filas = $this->filas('/ot/' . (int) $id . '/cliente/empresa/' . man_empresa());
		if (!$filas) {
			return 0;
		}
		foreach ($filas as &$f) {
			unset($f['cliId2']);
		}
		return $filas;
	}

	/** Un cliente de la empresa. @return array|false objetos */
	function getcliente($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getcliente(" . json_encode($data) . ")");
		if ($data == null) {
			return false;
		}
		return $this->objetos('/ots/cliente/' . (int) $data['idcliente'] . '/empresa/' . man_empresa());
	}

	/** Tipo de origen e id de la solicitud de origen. @return array objetos */
	function getOrigenOt($idot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOrigenOt(" . json_encode($idot) . ")");
		$filas = $this->objetos('/ot/' . (int) $idot . '/origen/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Vista de una OT que viene de una solicitud de servicio, con los datos de la solicitud. @return array */
	function getViewDataSolServicio($idOt, $idSolicitud = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getViewDataSolServicio($idOt)");
		$datos = $this->filas('/ot/' . (int) $idOt . '/vista/solicitud/empresa/' . man_empresa());
		if (!$datos) {
			return array();
		}
		$sol = $this->filas('/ots/vista/solicitud/' . (int) $datos[0]['id_solicitud'] . '/empresa/' . man_empresa());
		$datos[0]['solServicio'] = $sol ? $sol[0] : null;
		return $datos;
	}

	/** Vista de una OT de preventivo, con la tarea del preventivo y su detalle. @return array */
	function getViewDataPreventivo($idOt, $idSolicitud = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getViewDataPreventivo($idOt)");
		$datos = $this->filas('/ot/' . (int) $idOt . '/vista/preventivo/empresa/' . man_empresa());
		if (!$datos) {
			return array();
		}
		$datos[0]['tarea'] = $this->getViewDataTareaPreventivo($datos[0]['id_solicitud']);
		return $datos;
	}

	/**
	 * Tarea del preventivo. Asset arma herramientas e insumos como arreglos indexados por la posición del preventivo
	 * ($herramientas[0] = lista); se conserva esa forma.
	 * @return array|null
	 */
	function getViewDataTareaPreventivo($id_solicitud){
		$filas = $this->filas('/ots/vista/preventivo/' . (int) $id_solicitud . '/empresa/' . man_empresa());
		if (!$filas) {
			return null;
		}
		$h = man_lista(wso2(REST_MAN . '/preventivo/' . (int) $filas[0]['prevId'] . '/herramientas/empresa/' . man_empresa()));
		$i = man_lista(wso2(REST_MAN . '/preventivo/' . (int) $filas[0]['prevId'] . '/insumos/empresa/' . man_empresa()));
		$filas[0]['herramientas'] = array(tools_merge_herramientas($h ? $h : array()));
		$filas[0]['insumos'] = array(tools_merge_articulos($i ? $i : array()));
		return $filas[0];
	}

	/** Vista de una OT de backlog, con la tarea y el componente del backlog. @return array */
	function getViewDataBacklog($idOt, $idSolicitud = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getViewDataBacklog($idOt)");
		$datos = $this->filas('/ot/' . (int) $idOt . '/vista/backlog/empresa/' . man_empresa());
		if (!$datos) {
			return array();
		}
		$tarea = $this->filas('/ots/vista/backlog/' . (int) $datos[0]['id_solicitud'] . '/empresa/' . man_empresa());
		if ($tarea) {
			$comp = $this->filas('/ots/vista/componente-equipo/' . (int) $tarea[0]['idcomponenteequipo'] . '/empresa/' . man_empresa());
			$tarea[0]['compEquipo'] = $comp ? $comp[0] : null;
		}
		$datos[0]['tarea'] = $tarea ? $tarea[0] : null;
		return $datos;
	}

	/** Vista de una OT de predictivo, con la tarea del predictivo. @return array */
	function getViewDataPredictivo($idOt, $idSolicitud = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getViewDataPredictivo($idOt)");
		$datos = $this->filas('/ot/' . (int) $idOt . '/vista/predictivo/empresa/' . man_empresa());
		if (!$datos) {
			return array();
		}
		$tarea = $this->filas('/ots/vista/predictivo/' . (int) $datos[0]['id_solicitud'] . '/empresa/' . man_empresa());
		$datos[0]['tarea'] = $tarea ? $tarea[0] : null;
		return $datos;
	}

	/**
	 * Listado de OTs con los filtros de la pantalla. $tipo 1 = solo OTs (tipo 1); cualquier otro valor, todas.
	 * @return array|false
	 */
	public function filtrarListado($data, $tipo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | filtrarListado(" . json_encode($data) . ", $tipo)");
		$q = http_build_query(array(
			'solo_tipo1' => $tipo == 1 ? '1' : '',
			'fec_desde'  => isset($data['fec_desde']) ? $data['fec_desde'] : '',
			'fec_hasta'  => isset($data['fec_hasta']) ? $data['fec_hasta'] : '',
			'estado'     => isset($data['estadoFilt']) ? $data['estadoFilt'] : '',
			'equipo'     => isset($data['equipoFilt']) ? $data['equipoFilt'] : '',
		));
		return $this->filas('/ots/listado/empresa/' . man_empresa() . '?' . $q);
	}

	/**
	 * Edita la OT con los campos del formulario (asset aceptaba cualquier columna que llegara; acá solo las del
	 * formulario) y reemplaza herramientas e insumos. La cabecera y el borrado del detalle van en un request_box y
	 * cada lista nueva en un pedido por lotes (E20).
	 * @return bool
	 */
	function update_edita($id, $data, $herramientas = array(), $insumos = array()){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | update_edita($id, " . json_encode($data) . ")");
		$emp = man_empresa();
		$clave = array('id_orden' => (string) $id, 'id_empresa' => $emp);
		$rsp = requestBox(REST_MAN . '/', array(
			array('_put_ot_editar' => array(
				'fecha_program' => man_fecha(isset($data['fecha_program']) ? $data['fecha_program'] : null, true),
				'fecha_entrega' => man_fecha(isset($data['fecha_entrega']) ? $data['fecha_entrega'] : null, true),
				'id_tarea'      => man_numero(isset($data['id_tarea']) ? $data['id_tarea'] : null),
				'descripcion'   => isset($data['descripcion']) ? (string) $data['descripcion'] : '',
				'id_sucursal'   => man_numero(isset($data['id_sucursal']) ? $data['id_sucursal'] : ''),
				'id_proveedor'  => man_numero(isset($data['id_proveedor']) ? $data['id_proveedor'] : ''),
				'id_equipo'     => (string) (isset($data['id_equipo']) ? $data['id_equipo'] : ''),
				'id_orden'      => (string) $id,
				'id_empresa'    => $emp,
			)),
			array('_put_ot_herramientas_borrar' => $clave),
			array('_put_ot_insumos_borrar' => $clave),
		));
		if (!$rsp['status']) {
			return false;
		}
		$h = array();
		foreach ($herramientas as $f) {
			$h[] = array_merge($f, $clave, array('descripcion_libre' => null));
		}
		$i = array();
		foreach ($insumos as $f) {
			$i[] = array_merge($f, $clave);
		}
		return man_lote_detalle('ot', 'herramienta', $h) !== false && man_lote_detalle('ot', 'insumo', $i) !== false;
	}

	/** Registra un adjunto de la OT. @return string|false id del adjunto */
	function setAdjunto($adjunto){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | setAdjunto(" . json_encode($adjunto) . ")");
		$rsp = man_escribir('POST', 'ot/adjunto', array(
			'ot_adjunto' => (string) $adjunto['ot_adjunto'],
			'id_orden'   => (string) $adjunto['otId'],
			'id_empresa' => man_empresa(),
		));
		return ($rsp['status'] && is_scalar($rsp['data']) && (int) $rsp['data'] > 0) ? (string) $rsp['data'] : false;
	}

	/** Borra un adjunto de la OT. @return bool */
	function eliminarAdjunto($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | eliminarAdjunto($id)");
		$rsp = man_escribir('PUT', 'ot/adjunto/borrar', array('id' => (string) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** Cambia el estado de la OT. @return bool */
	function update_predictivo($data, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | update_predictivo(" . json_encode($data) . ", $id)");
		$rsp = man_escribir('PUT', 'ot/estado', array('estado' => (string) $data['estado'], 'id_orden' => (string) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** Termina la OT (T) con la fecha indicada. @return bool */
	function update_cambio($ido, $fecha){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | update_cambio($ido, $fecha)");
		$rsp = man_escribir('PUT', 'ot/finalizar', array('fecha' => (string) $fecha, 'id_orden' => (string) $ido, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/**
	 * Tareas de la OT con el nombre del usuario. Asset devolvía todas las columnas de sisusers, incluida la
	 * contraseña cifrada; acá solo usrId, usrName y usrLastName, que es lo que usa la vista (E25).
	 * @return array|false
	 */
	function cargartareas($idglob){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | cargartareas(" . json_encode($idglob) . ")");
		return $this->filas('/ot/' . (int) $idglob . '/tareas/empresa/' . man_empresa());
	}

	/** Agrega una tarea a la OT. @return string|false id */
	function agregar_tareas($data){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | agregar_tareas(" . json_encode($data) . ")");
		$rsp = man_escribir('POST', 'ot/tarea', array(
			'id_orden'     => (string) $data['id_orden'],
			'tareadescrip' => isset($data['tareadescrip']) ? (string) $data['tareadescrip'] : null,
			'estado'       => isset($data['estado']) ? (string) $data['estado'] : null,
			'id_empresa'   => man_empresa(),
		));
		return ($rsp['status'] && is_scalar($rsp['data']) && (int) $rsp['data'] > 0) ? (string) $rsp['data'] : false;
	}

	/** Cambia un campo de una tarea de la OT (estado, usuario o fecha). @return bool */
	function cambiarTarea($idListarea, $campo, $valor){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | cambiarTarea($idListarea, $campo, " . json_encode($valor) . ")");
		$params = array('estado' => 'estado', 'usuario' => 'id_usuario', 'fecha' => 'fecha');
		$rsp = man_escribir('PUT', 'ot/tarea/' . $campo, array(
			$params[$campo] => $valor === null ? null : (string) $valor,
			'id_listarea'   => (string) $idListarea,
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Filas de un recurso de MANDataService (result_array()), o false si no hay. */
	private function filas($recurso){
		$filas = man_lista(wso2(REST_MAN . $recurso));
		return $filas ? $filas : false;
	}

	/** Filas como objetos (result()), o false si no hay. */
	private function objetos($recurso){
		$filas = man_lista(wso2(REST_MAN . $recurso));
		return $filas ? json_decode(json_encode($filas)) : false;
	}
}
