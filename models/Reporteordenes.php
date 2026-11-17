<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rep Ordenes de trabajo ("Reporteorden" de asset) — MIG-REP-3.
 *
 * Todos los datos por MANDataService (ADR-014), siempre filtrados por la empresa de la sesión. Los filtros de equipo y estado viajan
 * como valor (0 = sin filtro) y las fechas como aaaa-mm-dd (0 = sin fechas; se aplican si vienen las dos).
 */
class Reporteordenes extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Órdenes de trabajo con los filtros elegidos.
	 * @param array $filtros id_equipo, estado, desde y hasta (las fechas como dd-mm-aaaa; vacío = sin filtro)
	 * @return array filas del reporte
	 */
	function getDatosReporte($filtros)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Reporteordenes | getDatosReporte(' . json_encode($filtros) . ')');
		$equipo = isset($filtros['id_equipo']) ? (int) $filtros['id_equipo'] : 0;
		$estado = isset($filtros['estado']) && preg_match('/^[A-Za-z]{1,2}$/', $filtros['estado']) ? $filtros['estado'] : '0';
		$desde = $this->fecha(isset($filtros['desde']) ? $filtros['desde'] : '');
		$hasta = $this->fecha(isset($filtros['hasta']) ? $filtros['hasta'] : '');
		if ($desde === '0' || $hasta === '0') {
			$desde = $hasta = '0';
		}
		$ruta = '/rep/ordenes/empresa/' . man_empresa() . '/equipo/' . $equipo . '/estado/' . $estado . '/desde/' . $desde . '/hasta/' . $hasta;
		$filas = man_lista(wso2(REST_MAN . $ruta));
		return $filas === null ? array() : $filas;
	}

	/**
	 * Equipos no anulados de la empresa para el desplegable.
	 * @return array filas con id_equipo, codigo y descripcion
	 */
	function equipos()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Reporteordenes | equipos()');
		$lista = array();
		foreach ((array) man_lista(wso2(REST_MAN . '/equipos/no-anulados/empresa/' . man_empresa())) as $f) {
			$lista[] = array('id_equipo' => $f['id_equipo'], 'codigo' => $f['codigo'], 'descripcion' => $f['descripcion']);
		}
		return $lista;
	}

	/**
	 * Estados que tienen órdenes en la empresa.
	 * @return array filas con estadoid, descripcion y estado
	 */
	function estados()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Reporteordenes | estados()');
		$filas = man_lista(wso2(REST_MAN . '/rep/ordenes/estados/empresa/' . man_empresa()));
		return $filas === null ? array() : $filas;
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
