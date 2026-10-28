<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Informes de servicio — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan la pantalla de informes y la ventana de
 * ver informe (E9). Cargar, editar y guardar el informe (cargarOrden, editarInforme, setOrdenServ) y los vales de pañol
 * se usan desde la bandeja de Bonita y van con ella (MIG-3). La verificación va por toolsMANAPI y la plantilla BPM (A5).
 */
class Ordenservicio extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Ordenservicios');
		$this->load->model('Otrabajos');
		$this->load->helper('man');
	}

	/**
	* Listado de informes de servicio
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | index($permission)");
		$data['permission'] = $permission;
		$data['list'] = $this->Ordenservicios->getOrdServiciosList();
		$this->load->view('ordenservicios/list', $data);
	}

	/**
	* Ventana de ver informe (también la usa el listado de OT)
	*/
	public function verInforme($id_ot = null, $id_eq = null, $id_solicitud = null, $idTarBonita = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | verInforme($id_ot, $id_eq, $id_solicitud, $idTarBonita)");
		$data['id_ot'] = $id_ot;
		$data['id_eq'] = $id_eq;
		$infoOt = $this->Ordenservicios->getorden($id_ot);
		$ot = $infoOt ? $infoOt[0] : array('id_tarea' => null, 'descripcion' => '', 'tareadescrip' => '', 'responsable' => '', 'usrId' => '');
		// si la tarea es opcional (sin tarea estándar) el motivo es la descripción de la OT
		$data['causa'] = (($ot['id_tarea'] < 0) || ($ot['id_tarea'] == null)) ? $ot['descripcion'] : $ot['tareadescrip'];
		$data['id_solicitudServicio'] = $id_solicitud;
		$data['id_responsable'] = $ot['descripcion'];
		$data['nom_responsable'] = $ot['responsable'];
		$data['idresponsable'] = $ot['usrId'];
		$data['idTarBonita'] = $idTarBonita;
		$data['equipos'] = $this->Ordenservicios->getEquipos(array('id_equipo' => $id_eq));
		$data['lecturas'] = $this->Ordenservicios->getLecturasOrden($id_ot);
		$data['lecturasOT'] = $this->Otrabajos->getLecturasOrden($id_ot);
		$data['tareas'] = $this->Ordenservicios->getTareasOrden($id_ot);
		$data['herramientas'] = $this->Ordenservicios->getHerramOrdenes($id_ot);
		$data['insumos'] = $this->Ordenservicios->getInsumosPorOT($id_ot);
		$data['rrhh'] = $this->Ordenservicios->getOperariosOrden($id_ot);
		$data['evidencias'] = $this->Ordenservicios->getEvidenciasPorOT($id_ot);
		$this->load->view('tareas/view_presta_presta_conf_modal', $data);
	}

	/**
	* Insumos pedidos por la OT
	*/
	public function getInsumosPorOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getInsumosPorOT()");
		echo json_encode($this->Ordenservicios->getInsumosPorOT($this->input->post('id_ot')));
	}

	/**
	* Entregas (parciales o totales) de los pedidos de la OT
	*/
	public function getEntregasPorOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getEntregasPorOT()");
		echo json_encode($this->Ordenservicios->getEntregasPorOT($this->input->post('id_ot')));
	}

	/**
	* Herramientas usadas en la OT, con días de uso
	*/
	public function getHerramientasUsadas(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getHerramientasUsadas()");
		echo json_encode($this->Ordenservicios->getHerramientasUsadas($this->input->post('id_ot')));
	}

	/**
	* Verifica los informes seleccionados: cierra la tarea "Verifica Informe de Servicio" de cada caso en Bonita, si el
	* usuario la tiene, registra la lectura de cierre y deja el informe y la OT cerrados. Si el caso no tiene esa tarea para
	* el usuario, igual cierra los estados (como asset).
	* Asset buscaba la tarea entre las 10 primeras de la bandeja del usuario (T32) y en el caso que mandaba el navegador (T34):
	* acá se busca en el caso de la OT, dentro de la empresa.
	*/
	public function cierreMasivoVerificaInforme(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | cierreMasivoVerificaInforme()");
		$selected_orders = $this->input->post('selected');
		if (empty($selected_orders) || !is_array($selected_orders)) {
			echo json_encode(array('status' => false, 'msj' => 'No se seleccionaron órdenes'));
			return;
		}
		$results = array();
		foreach ($selected_orders as $order) {
			$id_orden_servicio = isset($order['id_orden']) ? $order['id_orden'] : '';
			$rsp = $this->Ordenservicios->verificarInforme(isset($order['id_ot']) ? $order['id_ot'] : 0);
			if (!$rsp['status']) {
				$results[] = array('id_orden' => $id_orden_servicio, 'status' => 'ERROR_BPM', 'msj' => $rsp['msj']);
			} else {
				$results[] = array('id_orden' => $id_orden_servicio, 'status' => $rsp['bpm'] === 'si' ? 'OK' : 'OK_NO_BPM');
			}
		}
		echo json_encode($results);
	}
}
