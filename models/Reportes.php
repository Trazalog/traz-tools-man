<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reporte de Informes de Servicio ("Rep Informe de Servicios" de asset, Reporte/getReporte) — MIG-REP-2.
 *
 * Todos los datos por MANDataService (ADR-014). Las tres consultas (solicitudes, preventivos y backlog) son las del model Reportes de asset,
 * con los mismos JOIN y el mismo GROUP BY, y siempre filtradas por la empresa de la sesión. Los filtros de sector y equipo viajan como
 * número (0 = sin filtro) y las fechas como aaaa-mm-dd (0 = sin fechas; se aplican si vienen las dos).
 */
class Reportes extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Solicitudes de servicio, preventivos y backlog con su orden y su informe de servicio.
	 * @param array $filtros id_sector, id_equipo, desde y hasta (las fechas como dd-mm-aaaa)
	 * @return array filas (asset las devolvía en este orden: solicitudes, preventivos y backlog)
	 */
	function getReporte($filtros)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Reportes | getReporte(' . json_encode($filtros) . ')');
		$sector = isset($filtros['id_sector']) ? (int) $filtros['id_sector'] : 0;
		$equipo = isset($filtros['id_equipo']) ? (int) $filtros['id_equipo'] : 0;
		$desde = $this->fecha(isset($filtros['desde']) ? $filtros['desde'] : '');
		$hasta = $this->fecha(isset($filtros['hasta']) ? $filtros['hasta'] : '');
		if ($desde === '0' || $hasta === '0') {
			$desde = $hasta = '0';
		}
		$ruta = '/empresa/' . man_empresa() . '/sector/' . $sector . '/equipo/' . $equipo . '/desde/' . $desde . '/hasta/' . $hasta;

		$todo = array();
		foreach (array('solicitudes', 'preventivos', 'backlog') as $origen) {
			$filas = man_lista(wso2(REST_MAN . '/rep/informes/' . $origen . $ruta));
			$todo = array_merge($todo, $filas === null ? array() : $filas);
		}
		return $todo;
	}

	/**
	 * Equipos no anulados de la empresa para el desplegable.
	 * @return array filas con id_equipo, descripcion y codigo
	 */
	function equipos()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Reportes | equipos()');
		$filas = man_lista(wso2(REST_MAN . '/equipos/no-anulados/empresa/' . man_empresa()));
		$lista = array();
		foreach ((array) $filas as $f) {
			$lista[] = array('id_equipo' => $f['id_equipo'], 'descripcion' => $f['descripcion'], 'codigo' => $f['codigo']);
		}
		return $lista;
	}

	/** De dd-mm-aaaa a aaaa-mm-dd; '0' si está vacía o no es una fecha real (sin filtro). */
	private function fecha($valor)
	{
		if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', (string) $valor, $m) || !checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
			return '0';
		}
		return $m[3] . '-' . $m[2] . '-' . $m[1];
	}
}
