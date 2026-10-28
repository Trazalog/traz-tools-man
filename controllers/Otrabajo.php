<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Órdenes de trabajo — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan las vistas (E9). Lo que habla con
 * Bonita va por toolsMANAPI y la plantilla BPM (A5). Los adjuntos van a la carpeta de asset (E11).
 */
class Otrabajo extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Otrabajos');
		$this->load->helper('man');
	}

	/**
	* En asset abre el tablero de indicadores, que todavía no está portado: en MAN va al listado (E24)
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | index($permission)");
		$this->listOrden($permission);
	}

	/**
	* Formulario de nueva OT
	*/
	public function nuevaOT($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | nuevaOT($permission)");
		$data['permission'] = $permission;
		$this->load->view('otrabajos/view_agregarOT', $data);
	}

	/**
	* Listado de OTs (las filas llegan después por filtrarListado). Asset además pedía la lista de usuarios de Bonita,
	* que la vista no usa: acá no se pide.
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function listOrden($permission = '', $ot = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | listOrden($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['list']       = false;
		$data['equipos']    = $this->Otrabajos->getEquiposNuevaOT();
		$data['permission'] = $permission;
		$data['opciones']   = $this->load->view('otrabajos/tabla_opciones', array('permission' => $permission), true);
		$this->load->view('otrabajos/list', $data);
	}

	public function getproveedor(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getproveedor()");
		$this->lista($this->Otrabajos->getproveedor());
	}

	public function traer_sucursal(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | traer_sucursal()");
		$this->lista($this->Otrabajos->traer_sucursal());
	}

	public function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getequipo()");
		$this->lista($this->Otrabajos->getequipo());
	}

	/**
	* OT a editar, con herramientas, insumos y adjuntos
	*/
	public function getpencil(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getpencil()");
		$id = $this->input->post('idp');
		$result = $this->Otrabajos->getpencil($id);
		if ($result) {
			$arre['datos'] = $result;
			$herramientas = $this->Otrabajos->getOTHerramientas($id);
			$arre['herramientas'] = $herramientas ? $herramientas : 0;
			$insumos = $this->Otrabajos->getOTInsumos($id);
			$arre['insumos'] = $insumos ? $insumos : 0;
			$adjuntos = $this->Otrabajos->getOTadjuntos($id);
			$arre['adjunto'] = $adjuntos ? $adjuntos : 0;
		} else {
			$arre['datos'] = 0;
		}
		echo json_encode($arre);
	}

	public function getasigna(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getasigna()");
		$result = $this->Otrabajos->getasigna($this->input->get('id_orden'));
		if ($result) {
			echo json_encode(array('datos' => $result));
		}
		else echo "nada";
	}

	public function getusuario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getusuario()");
		$this->lista($this->Otrabajos->getusuario());
	}

	public function getEquiposNuevaOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getEquiposNuevaOT()");
		$this->lista($this->Otrabajos->getEquiposNuevaOT());
	}

	public function getInfoEquipoNuevaOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getInfoEquipoNuevaOT()");
		$this->lista($this->Otrabajos->getInfoEquiposNuevaOT($this->input->post('id_equipo')));
	}

	public function traer_cli(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | traer_cli()");
		$this->lista($this->Otrabajos->traer_cli());
	}

	public function getpedidos(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getpedidos()");
		$result = $this->Otrabajos->getpedidos($this->input->get('fechai'));
		if ($result) {
			echo json_encode(array('datos' => $result));
		}
		else echo "nada";
	}

	public function getcliente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getcliente()");
		$this->lista($this->Otrabajos->getcliente($this->input->post()));
	}

	/** Tipo de origen de la OT */
	public function getOrigenOt(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getOrigenOt()");
		$response = $this->Otrabajos->getOrigenOt($this->input->post('idot'));
		echo json_encode(isset($response[0]) ? $response[0] : null);
	}

	/** Vista de una OT común (asset usa la consulta de preventivo, se conserva) */
	public function getViewDataOt(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getViewDataOt()");
		$idOt = $this->input->post('idOt');
		$this->vista('otrabajo', $this->Otrabajos->getViewDataPreventivo($idOt), $idOt);
	}

	public function getViewDataSolServicio(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getViewDataSolServicio()");
		$idOt = $this->input->post('idOt');
		$this->vista('solicitud', $this->Otrabajos->getViewDataSolServicio($idOt, $this->input->post('idSolServicio')), $idOt);
	}

	public function getViewDataPreventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getViewDataPreventivo()");
		$idOt = $this->input->post('idOt');
		$this->vista('preventivo', $this->Otrabajos->getViewDataPreventivo($idOt, $this->input->post('idPreventivo')), $idOt);
	}

	public function getViewDataBacklog(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getViewDataBacklog()");
		$idOt = $this->input->post('idOt');
		$this->vista('backlog', $this->Otrabajos->getViewDataBacklog($idOt, $this->input->post('idBacklog')), $idOt);
	}

	public function getViewDataPredictivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getViewDataPredictivo()");
		$idOt = $this->input->post('idOt');
		$this->vista('predictivo', $this->Otrabajos->getViewDataPredictivo($idOt, $this->input->post('idPredictivo')), $idOt);
	}

	/**
	* Impresión según el origen de la OT. La firma se muestra para la empresa 15 de asset, como en asset.
	*/
	public function printOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | printOT()");
		$datos = $this->input->post('datos');
		$datos = is_array($datos) ? $datos : array();
		switch ($this->input->post('tipo')) {
			case '1':
				$this->load->view('otrabajos/printot', $datos);
				break;
			case '2':
				if (man_empresa() === '15') $datos['muestraFirma'] = true;
				$this->load->view('otrabajos/printotsolserv', $datos);
				break;
			case '3':
				if (man_empresa() === '15') $datos['muestraFirma'] = true;
				$this->load->view('otrabajos/printotprev', $datos);
				break;
			case '4':
				$this->load->view('otrabajos/printotback', $datos);
				break;
			case '5':
				$this->load->view('otrabajos/printotpred', $datos);
				break;
			default:
				break;
		}
	}

	/**
	* Filas del listado con los filtros de la pantalla (sin filtros no devuelve nada, como en asset)
	*/
	public function filtrarListado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | filtrarListado()");
		$data = array();
		foreach (array('fec_desde', 'fec_hasta', 'estadoFilt', 'equipoFilt') as $campo) {
			if (!empty($this->input->post($campo))) {
				$data[$campo] = $this->input->post($campo);
			}
		}
		if (empty($data)) {
			echo json_encode(null);
			return;
		}
		echo json_encode($this->Otrabajos->filtrarListado($data, 2));
	}

	/** Respuesta de las vistas de OT: datos, herramientas, insumos y adjuntos (0 si no hay). */
	private function vista($clave, $datos, $idOt){
		$response[$clave] = $datos;
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);
		$response['herramientas'] = $herramientas ? $herramientas : 0;
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		$response['insumos'] = $insumos ? $insumos : 0;
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);
		$response['adjunto'] = $adjuntos ? $adjuntos : 0;
		echo json_encode($response);
	}

	/** Lista como json (array de filas), o "nada" si no hay. */
	private function lista($filas){
		if ($filas) {
			echo json_encode(array_values($filas));
		}
		else echo "nada";
	}
}
