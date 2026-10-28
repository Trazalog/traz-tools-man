<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Solicitudes de servicio.
 *
 * MIG-ASSET-2: portado desde el asset actual (diverge del fork). Datos por MANDataService (ADR-014), limitado a la
 * empresa de la sesión (asset no filtraba getSSs, getEquipSectores ni get_SolicTerminadas). Lo que lanza o borra
 * casos de Bonita (setservicios + lanzarProceso, eliminar_solicitud) espera la decisión A5 y no está en este model.
 * Se conservan firmas y formato de salida del model de asset (E9: solo lo que usan las acciones portadas).
 */
class Sservicios extends CI_Model
{
	/**
	 * Columnas del SELECT de solicitudespaginadas() de asset, en orden: la vista ordena por número de columna y asset
	 * pasaba ese número tal cual a ORDER BY (orden posicional, 1 = primera columna).
	 */
	private $columnasPaginado = array('id_solicitud', 'numero', 'id_tipo', 'nivel', 'solicitante', 'f_solicitado', 'f_sugerido', 'hora_sug',
		'id_equipo', 'correctivo', 'causa', 'observaciones', 'estado', 'usrId', 'fecha_conformidad', 'observ_conformidad', 'foto1', 'foto2',
		'foto3', 'foto', 'id_empresa', 'case_id', 'urgente', 'sol_adjunto', 'id_usuario_eliminada', 'fecha_eliminada', 'motivo_eliminada',
		'equipo', 'sector', 'grupo', 'ubicacion', 'fecha_terminada', 'f_inicio', 'descripcion', 'f_asignacion', 'f_solicitado', 'case_id',
		'id_orden', 'id_usuario_a', 'mantenedor');

	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/** Equipo, sector, área y proceso de un equipo de la empresa. @return array objetos */
	function getEquipoSector($idequipo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getEquipoSector(" . json_encode($idequipo) . ")");
		$filas = man_lista(wso2(REST_MAN . '/solicitudes/equipo/' . (int) $idequipo . '/sector/empresa/' . man_empresa()));
		return $filas ? json_decode(json_encode($filas)) : array();
	}

	/** Anula la solicitud (AN). Asset no devolvía nada. */
	function elimSolicitudes($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | elimSolicitudes(" . json_encode($id) . ")");
		$this->estado($id, 'AN');
	}

	/** Equipos activos de un sector de la empresa. @return array|null descripcion (código) e id_equipo */
	function getEquipSectores($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getEquipSectores(" . json_encode($data) . ")");
		$filas = man_lista(wso2(REST_MAN . '/solicitudes/sector/' . (int) $data['id_sector'] . '/equipos/empresa/' . man_empresa()));
		if (!$filas) {
			return null;
		}
		$out = array();
		foreach ($filas as $f) {
			$out[] = array('descripcion' => $f['codigo'], 'id_equipo' => $f['id_equipo']);
		}
		return $out;
	}

	/** Solicitud con el nombre de quien la eliminó. @return array objetos */
	function getSSs($idSS){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getSSs(" . json_encode($idSS) . ")");
		$filas = man_lista(wso2(REST_MAN . '/solicitud/' . (int) $idSS . '/datos/empresa/' . man_empresa()));
		return $filas ? json_decode(json_encode($filas)) : array();
	}

	/** Sectores no anulados de la empresa para el autocompletar. @return array label/value */
	function getSectores(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getSectores()");
		$filas = man_lista(wso2(REST_MAN . '/solicitudes/sectores/empresa/' . man_empresa()));
		$sectores = array();
		foreach ((array) $filas as $f) {
			$sectores[] = array('label' => $f['descripcion'], 'value' => $f['id_Sector']);
		}
		return $sectores;
	}

	/** Datos para imprimir la solicitud. @return array|null */
	function getsolImps($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getsolImps(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/solicitud/' . (int) $id . '/impresion/empresa/' . man_empresa()));
		if (!$filas) {
			return null;
		}
		$r = $filas[0];
		return array(
			'f_solicitado' => $r['f_solicitado'],
			'solicitante'  => $r['solicitante'],
			'causa'        => $r['causa'],
			'hora_sug'     => $r['hora_sug'],
			'codigo'       => $r['codigo'],
			'ubicacion'    => $r['ubicacion'],
			'degr'         => $r['degr'],
			'sec'          => $r['sec'],
		);
	}

	/** Solicitudes terminadas (T) de la empresa. @return array|false */
	function get_SolicTerminadas(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | get_SolicTerminadas()");
		$filas = man_lista(wso2(REST_MAN . '/solicitudes/terminadas/empresa/' . man_empresa()));
		return $filas ? $filas : false;
	}

	/** Vuelve la solicitud a Solicitada (S). @return bool */
	function activSolicitudes($data){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | activSolicitudes(" . json_encode($data) . ")");
		return $this->estado($data['id_solicitud'], 'S');
	}

	/** Registra la conformidad y deja la solicitud terminada (T). @return bool */
	function confSolicitudes($data){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | confSolicitudes(" . json_encode($data) . ")");
		$rsp = man_escribir('PUT', 'solicitud/conformidad', array(
			'fecha_conformidad'  => man_fecha($data['fecha_conformidad']),
			'observ_conformidad' => (string) $data['observ_conformidad'],
			'id_solicitud'       => (string) $data['id_sol'],
			'id_empresa'         => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Página de solicitudes para la tabla (DataTables del lado del servidor), con el mismo filtro, conteo y orden
	 * posicional que asset. El DataService devuelve las filas filtradas y acá se ordenan y se corta la página.
	 * @return array datos (filas de la página), numDataTotal
	 */
	public function solicitudespaginadas($start, $length, $search, $ordering, $showConformes){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | solicitudespaginadas($start, $length, " . json_encode($search) . ")");
		$conformes = ($showConformes == 'false') ? 'false' : 'true';
		$q = '?conformes=' . $conformes . '&buscar=' . rawurlencode((string) $search);
		$cant = man_lista(wso2(REST_MAN . '/solicitudes/cantidad/empresa/' . man_empresa() . $q));
		$filas = man_lista(wso2(REST_MAN . '/solicitudes/empresa/' . man_empresa() . $q));
		$filas = $filas ? $filas : array();
		if (!empty($ordering) && is_array($ordering)) {
			$this->ordenar($filas, $ordering);
		}
		return array(
			'datos'        => array_slice($filas, (int) $start, (int) $length),
			'numDataTotal' => $cant ? $cant[0]['cant'] : 0,
		);
	}

	/**
	 * ¿El usuario de la sesión está en alguno de los grupos de asset? (provisorio hasta MIG-5: los permisos siguen
	 * siendo los de asset, P3).
	 * @return bool
	 */
	public function validaUsuario($id_usuario, $empresaId, $filtro1 = null, $filtro2 = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | validaUsuario($id_usuario, " . json_encode($filtro1) . ", " . json_encode($filtro2) . ")");
		$filas = man_lista(wso2(REST_MAN . '/usuario/' . (int) $id_usuario . '/grupos/empresa/' . $empresaId
			. '?filtro1=' . rawurlencode((string) $filtro1) . '&filtro2=' . rawurlencode((string) $filtro2)));
		return (bool) $filas;
	}

	/** Adjuntos de la solicitud (ya era REST en asset). */
	public function getAdjuntosSolServicio($id_solicitud){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getAdjuntosSolServicio($id_solicitud)");
		$aux = $this->rest->callAPI('GET', REST_MAN . '/solicitudServicio/adjuntos/solicitud/' . (int) $id_solicitud);
		$resp = json_decode($aux['data']);
		return isset($resp->adjuntos->adjunto) ? $resp->adjuntos->adjunto : null;
	}

	/** Cambia el estado de una solicitud de la empresa. @return bool */
	private function estado($id, $estado){
		$rsp = man_escribir('PUT', 'solicitud/estado', array(
			'estado'       => (string) $estado,
			'id_solicitud' => (string) $id,
			'id_empresa'   => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Orden posicional como el ORDER BY de asset: cada criterio trae el número de columna de la tabla, que asset usaba
	 * como posición del SELECT. Números con números, el resto sin distinguir mayúsculas; NULL primero en ascendente.
	 */
	private function ordenar(&$filas, $ordering){
		$criterios = array();
		foreach ($ordering as $o) {
			$pos = isset($o['column']) ? (int) $o['column'] : 0;
			if ($pos < 1 || $pos > count($this->columnasPaginado)) {
				continue;
			}
			$criterios[] = array($this->columnasPaginado[$pos - 1], (isset($o['dir']) && strtolower($o['dir']) === 'desc') ? -1 : 1);
		}
		if (!$criterios) {
			return;
		}
		usort($filas, function ($a, $b) use ($criterios) {
			foreach ($criterios as $c) {
				$x = isset($a[$c[0]]) ? $a[$c[0]] : null;
				$y = isset($b[$c[0]]) ? $b[$c[0]] : null;
				if ($x === $y) {
					continue;
				}
				if ($x === null) {
					$r = -1;
				} elseif ($y === null) {
					$r = 1;
				} elseif (is_numeric($x) && is_numeric($y)) {
					$r = ($x + 0 < $y + 0) ? -1 : (($x + 0 > $y + 0) ? 1 : 0);
				} else {
					$r = strcasecmp($x, $y);
				}
				if ($r !== 0) {
					return $r * $c[1];
				}
			}
			return 0;
		});
	}
}
