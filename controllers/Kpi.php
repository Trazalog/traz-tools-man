<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reportes de indicadores de MAN — MIG-REP-1.
 *
 * Por ahora, el Reporte de Disponibilidad de Equipos (opción "Rep Equipo" de asset, Kpi/index). La pantalla de KPIs con
 * MTTR/MTTF/MTBF de asset (Escritorio "Otrabajo") se migró como cajas del tablero de tools, no como pantalla de MAN.
 */
class Kpi extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Kpis');
		$this->load->helper('man');
	}

	/**
	* Reporte de Disponibilidad de Equipos: tabla de equipos y, al elegir fechas, su % de disponibilidad
	* @param string $permission acciones habilitadas (el reporte es de solo lectura; se pasa igual que en el resto de MAN)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Kpi | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list'] = $this->Kpis->listaEquipos();
		$data['permission'] = $permission;
		$this->load->view('kpis/rep_equipos', $data);
	}

	/**
	* Disponibilidad de cada equipo entre dos fechas (GET fi y ff, formato dd-mm-aaaa)
	* @return json lista con id_equipo, fi, ff y dsp (false si el equipo no tiene historial); false si no hay equipos
	*/
	public function dspRangoFecha(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Kpi | dspRangoFecha()");
		$fi = $this->fecha($this->input->get('fi'));
		$ff = $this->fecha($this->input->get('ff'));
		if ($fi === null || $ff === null) {
			echo json_encode(false);
			return;
		}
		echo json_encode($this->Kpis->dspRangoFecha($fi . ' 00:00:00', $ff . ' 00:00:00'));
	}

	/** De dd-mm-aaaa a aaaa-mm-dd; null si no tiene ese formato o no es una fecha real. */
	private function fecha($valor){
		if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', (string) $valor, $m) || !checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
			return null;
		}
		return $m[3] . '-' . $m[2] . '-' . $m[1];
	}
}
