<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Informes de servicio (ex órdenes de servicio).
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork) con lo que usan la pantalla de informes y la ventana de
 * ver informe (E9). Cargar, editar y guardar el informe, y los vales de pañol, se hacen desde la bandeja: van con ella
 * (MIG-3). Datos por MANDataService (ADR-014), limitado a la empresa de la sesión (asset no filtraba getorden, getEquipos
 * ni el detalle del informe); pedidos, entregas y herramientas del almacén y el pañol de tools (man_tools_helper). La
 * verificación habla con Bonita por toolsMANAPI y la plantilla BPM (A5). Se conservan firmas y formato de salida.
 */
class Ordenservicios extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('man', 'man_tools'));
	}

	/** Informes de la empresa, el último primero. @return array */
	function getOrdServiciosList(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOrdServiciosList()");
		$filas = $this->filas('/informes/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** OT del informe, con la tarea estándar y el responsable. @return array|int filas o 0 */
	function getorden($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getorden(" . json_encode($id) . ")");
		$filas = $this->filas('/informe/ot/' . (int) $id . '/empresa/' . man_empresa());
		return $filas ? $filas : 0;
	}

	/**
	 * Datos del equipo para el informe.
	 * @param array|string $data array con id_equipo (como en asset) o el id
	 * @return array|null
	 */
	function getEquipos($data){
		$id = is_array($data) ? (isset($data['id_equipo']) ? $data['id_equipo'] : null) : $data;
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getEquipos(" . json_encode($id) . ")");
		$filas = $this->filas('/informe/equipo/' . (int) $id . '/empresa/' . man_empresa());
		return $filas ? $filas[0] : null;
	}

	/** Horómetros y fechas del informe de la OT. @return array|false */
	function getLecturasOrden($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getLecturasOrden(" . json_encode($id_ot) . ")");
		return $this->filas('/informe/ot/' . (int) $id_ot . '/lecturas/empresa/' . man_empresa());
	}

	/** Tareas del informe de la OT. @return array|false */
	function getTareasOrden($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getTareasOrden(" . json_encode($id_ot) . ")");
		return $this->filas('/informe/ot/' . (int) $id_ot . '/tareas/empresa/' . man_empresa());
	}

	/** Herramientas del vale de salida del informe (pañol viejo de asset). @return array */
	function getHerramOrdenes($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getHerramOrdenes(" . json_encode($id_ot) . ")");
		$filas = $this->filas('/informe/ot/' . (int) $id_ot . '/herramientas/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Recursos humanos del informe de la OT. @return array */
	function getOperariosOrden($id_orden){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOperariosOrden(" . json_encode($id_orden) . ")");
		$filas = $this->filas('/informe/ot/' . (int) $id_orden . '/operarios/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Usuarios activos de la empresa para elegir los operarios del informe. @return array filas (label, value) */
	function getOperarios(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOperarios()");
		$filas = $this->filas('/informe/operarios/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Usuarios de las subtareas de la OT, propuestos como recursos humanos del informe. @return array filas (label, value) */
	function getRRHHOrdenTrabajo($idOT){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getRRHHOrdenTrabajo(" . json_encode($idOT) . ")");
		$filas = $this->filas('/informe/ot/' . (int) $idOT . '/rrhh/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Evidencias (imágenes y archivos) del último informe de la OT: getIdOrdenPorOT + getEvidenciasOrden de asset. @return array */
	function getEvidenciasPorOT($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getEvidenciasPorOT(" . json_encode($id_ot) . ")");
		$filas = $this->filas('/informe/ot/' . (int) $id_ot . '/evidencias/empresa/' . man_empresa());
		return $filas ? $filas : array();
	}

	/** Insumos pedidos por la OT al almacén de tools, con lo pedido, entregado y pendiente. @return array */
	function getInsumosPorOT($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getInsumosPorOT(" . json_encode($id_ot) . ")");
		$out = array();
		foreach (tools_pedidos_ot($id_ot) as $p) {
			foreach (tools_pedido_detalle($p['pema_id']) as $d) {
				// El detalle trae la cantidad pedida y el resto pendiente: lo entregado (el consumo real) es la diferencia.
				$pedido    = (float) $d['cantidad'];
				$pendiente = isset($d['resto']) ? (float) $d['resto'] : $pedido;
				$out[] = array(
					'pema_id'     => $p['pema_id'],
					'ortr_id'     => $p['ortr_id'],
					'barcode'     => $d['barcode'],
					'descripcion' => $d['descripcion'],
					'fecha'       => $p['fecha'],
					'estado'      => $p['estado'],
					'cantidad'    => $d['cantidad'],
					'pedido'      => $pedido,
					'entregado'   => $pedido - $pendiente,
					'pendiente'   => $pendiente,
				);
			}
		}
		return $out;
	}

	/** Entregas (parciales o totales) de los pedidos de la OT. @return array */
	function getEntregasPorOT($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getEntregasPorOT(" . json_encode($id_ot) . ")");
		$out = array();
		foreach (tools_pedidos_ot($id_ot) as $p) {
			$pema = isset($p['pema_id']) ? $p['pema_id'] : null;
			$entregas = tools_entregas_pedido($pema);
			foreach ($entregas as $k => $e) {
				$entregas[$k]['detalle'] = tools_entrega_detalle(isset($e['enma_id']) ? $e['enma_id'] : null);
			}
			$out[] = array(
				'pema_id'  => $pema,
				'estado'   => isset($p['estado']) ? $p['estado'] : '',
				'fecha'    => isset($p['fecha']) ? $p['fecha'] : '',
				'entregas' => $entregas,
			);
		}
		return $out;
	}

	/** Herramientas usadas en la OT (vales de pañol asociados), con días de uso. @return array */
	function getHerramientasUsadas($id_ot){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getHerramientasUsadas(" . json_encode($id_ot) . ")");
		if (tools_empr_id() === null) {
			return array();
		}
		$rows = $this->filas('/ot/' . (int) $id_ot . '/herramientas-usadas/empresa/' . man_empresa());
		if (!$rows) {
			return array();
		}
		$cat = array();
		foreach (tools_herramientas() as $h) {
			if (isset($h['herr_id'])) {
				$cat[(string) $h['herr_id']] = $h;
			}
		}
		$salidas = array();
		$out = array();
		foreach ($rows as $r) {
			$sapa = $r['sapa_id'];
			if (!isset($salidas[$sapa])) {
				$salidas[$sapa] = tools_salida_fecha($sapa);
			}
			$fsalida = isset($salidas[$sapa]['fecha']) ? $salidas[$sapa]['fecha'] : null;
			$fdevol  = tools_devolucion_herramienta($r['herrId'], $fsalida);
			$h = isset($cat[(string) $r['herrId']]) ? $cat[(string) $r['herrId']] : array();
			$out[] = array(
				'herrId'      => $r['herrId'],
				'sapa_id'     => $sapa,
				'origen'      => $r['origen'] ? $r['origen'] : 'MANTENEDOR',
				'activa'      => (int) $r['activa'],
				'codigo'      => isset($h['codigo']) ? $h['codigo'] : '',
				'descripcion' => isset($h['descripcion']) ? $h['descripcion'] : ('herr ' . $r['herrId']),
				'marca'       => isset($h['marca']) ? $h['marca'] : '',
				'salida'      => $fsalida,
				'devolucion'  => $fdevol,
				'dias'        => tools_dias_entre($fsalida, $fdevol),
			);
		}
		return $out;
	}

	/**
	 * Verifica un informe: cierra en Bonita la tarea "Verifica Informe de Servicio" del caso de la OT, si el usuario la tiene,
	 * y deja el informe y la OT cerrados (con la lectura de cierre del equipo si cerró la tarea). Todo en toolsMANAPI (A5).
	 * El caso lo toma el API de la OT de la empresa: no se acepta uno de afuera (T34).
	 * @return array status (true|false), bpm ('si' si cerró la tarea en Bonita), msj si falla
	 */
	function verificarInforme($idOt){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | verificarInforme($idOt)");
		$rsp = $this->rest->callAPI('POST', HOST . '/tools/man/informe/verificar', array(
			'id_ot'      => (string) (int) $idOt,
			'usrNick'    => (string) userNick(),
			'usrId'      => man_usuario(),
			'id_empresa' => man_empresa(),
			'session'    => $this->session->has_userdata('bpm_token') ? $this->session->userdata('bpm_token') : '',
		));
		$aux = json_decode($rsp['data'], true);
		if ($rsp['status'] && !empty($aux['status'])) {
			return array('status' => true, 'bpm' => isset($aux['bpm']) ? $aux['bpm'] : 'no');
		}
		log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | verificarInforme() >> ' . json_encode($rsp['data']));
		return array('status' => false, 'msj' => isset($aux['msj']) ? $aux['msj'] : ASP_104);
	}

	/** Filas de MANDataService, o false si no hay. */
	private function filas($recurso){
		$filas = man_lista(wso2(REST_MAN . $recurso));
		return $filas ? $filas : false;
	}
}
