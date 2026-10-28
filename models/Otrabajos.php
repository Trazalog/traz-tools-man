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
