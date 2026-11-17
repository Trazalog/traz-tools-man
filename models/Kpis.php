<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reporte de Disponibilidad de Equipos ("Rep Equipo" de asset) — MIG-REP-1.
 *
 * Todos los datos por MANDataService (ADR-014). El cálculo es el de Kpi::dspRangoFecha / calcularDisponibilidad de asset, pasado
 * tal cual (incluida la cuenta de minutos de calcularMinutos, que toma 30 días por mes y suma 5 por año). Asset pedía a la base el
 * historial de cada equipo; acá se trae una vez el de toda la empresa y se reparte por equipo.
 */
class Kpis extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Equipos de la empresa de la sesión con los datos de la tabla del reporte.
	 * @return array filas (id_equipo, codigo, deeq, dear, depro, desec, decri, estadoEquipo, meta_disp); vacío si no hay
	 */
	function listaEquipos()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Kpis | listaEquipos()');
		$filas = man_lista(wso2(REST_MAN . '/rep/equipos/lista/empresa/' . man_empresa()));
		return $filas === null ? array() : $filas;
	}

	/**
	 * Disponibilidad de cada equipo entre dos fechas.
	 * @param string $fi fecha de inicio (aaaa-mm-dd hh:mm:ss)
	 * @param string $ff fecha de fin (aaaa-mm-dd hh:mm:ss)
	 * @return array|false lista de objetos con id_equipo, fi, ff y dsp (false si el equipo no tiene historial), o false si no hay equipos
	 */
	function dspRangoFecha($fi, $ff)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Kpis | dspRangoFecha(' . $fi . ', ' . $ff . ')');
		$emp = man_empresa();
		$equipos = man_lista(wso2(REST_MAN . '/rep/equipos/disponibles/empresa/' . $emp));
		if ($equipos === null) {
			return false;
		}
		$historial = $this->historialPorEquipo(man_lista(wso2(REST_MAN . '/rep/equipos/historial/empresa/' . $emp)));
		$salida = array();
		foreach ($equipos as $e) {
			$id = $e['id_equipo'];
			$his = isset($historial[$id]) ? $historial[$id] : array();
			$fi1 = $this->estadoEquipoAlta($his, $fi);
			$ff2 = $this->estadoEquipoBaja($his, $ff);
			$o = new stdClass();
			$o->id_equipo = $id;
			$o->fi = $fi1;
			$o->ff = $ff2;
			$o->dsp = $this->calcularDisponibilidad($his, $fi1, $ff2);
			$salida[] = $o;
		}
		return $salida;
	}

	/** Reparte las filas de historial (ordenadas por equipo y fecha) en una lista por equipo. */
	private function historialPorEquipo($filas)
	{
		$por = array();
		if ($filas === null) {
			return $por;
		}
		foreach ($filas as $f) {
			$por[$f['id_equipo']][] = $f;
		}
		return $por;
	}

	/** Si el equipo se activó después de $fecha, arranca en su primera fecha de historial. */
	private function estadoEquipoAlta($his, $fecha)
	{
		if (!$his) {
			return $fecha;
		}
		$fechaAlta = $his[0]['fecha'];
		return (strtotime($fechaAlta) > strtotime($fecha)) ? $fechaAlta : $fecha;
	}

	/** Si el equipo se dio de baja (último estado IN) antes de $fecha, termina en esa fecha. */
	private function estadoEquipoBaja($his, $fecha)
	{
		if (!$his) {
			return $fecha;
		}
		$ultimo = $his[count($his) - 1];
		if ($ultimo['estado'] != 'IN') {
			return $fecha;
		}
		return (strtotime($ultimo['fecha']) < strtotime($fecha)) ? $ultimo['fecha'] : $fecha;
	}

	/**
	 * Disponibilidad (%) de un equipo entre dos fechas, como Kpi::calcularDisponibilidad de asset.
	 * @return string|int|false porcentaje con dos decimales; 100 o 0 si no hubo cambios en el rango; false si no hay historial anterior
	 */
	private function calcularDisponibilidad($his, $fi, $ff)
	{
		$disp = 0;
		$tc = $this->calcularMinutos($fi, $ff);

		$data = array();
		foreach ($his as $h) {
			if (strtotime($h['fecha']) >= strtotime($fi) && strtotime($h['fecha']) < strtotime($ff)) {
				$data[] = $h;
			}
		}

		// Sin cambios en el rango: vale el último estado anterior a $fi
		if (count($data) == 0) {
			$antes = array();
			foreach ($his as $h) {
				if (strtotime($h['fecha']) < strtotime($fi)) {
					$antes[] = $h;
				}
			}
			if (!$antes) {
				return false;
			}
			return (end($antes)['estado'] == 'AC') ? 100 : 0;
		}

		if ($data[0]['estado'] == 'RE') {
			$disp += $this->calcularMinutos($fi, $data[0]['fecha']);
			unset($data[0]);
		}

		$fechaActivo = null;
		foreach ($data as $o) {
			// Los registros con obs = 1 son mantenimientos que no cambiaron el estado del equipo
			if ($o['obs'] == 1) {
				continue;
			}
			if ($o['estado'] == 'AC') {
				$fechaActivo = $o['fecha'];
			}
			if ($o['estado'] == 'RE') {
				// Asset hacía new DateTime(null) (= ahora) cuando no había un AC anterior; se conserva
				$disp += $this->calcularMinutos($fechaActivo === null ? 'now' : $fechaActivo, $o['fecha']);
				$fechaActivo = null;
			}
		}
		if ($fechaActivo) {
			$disp += $this->calcularMinutos($fechaActivo, $ff);
		}

		if ($tc == 0) {
			return '0.00';
		}
		return number_format(($disp * 100) / $tc, 2);
	}

	/** Minutos entre dos fechas con la cuenta de asset: 30 días por mes y 5 días más por año. */
	private function calcularMinutos($fi, $ff)
	{
		$intervalo = date_create($fi)->diff(date_create($ff));
		$anos = (int) $intervalo->format('%Y');
		$meses = $anos * 12;
		$dias = (($meses + (int) $intervalo->format('%m')) * 30) + ($anos * 5);
		$horas = ($dias + (int) $intervalo->format('%d')) * 24;
		$minutos = ($horas + (int) $intervalo->format('%H')) * 60;
		return $minutos + (int) $intervalo->format('%i');
	}
}
