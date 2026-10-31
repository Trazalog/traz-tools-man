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

	/**
	* Alta de OT desde el formulario nuevo (fechas dd/mm/aaaa, tarea estándar o texto libre, herramientas, insumos y
	* adjunto inputPDF). La OT, el caso de Bonita y el detalle los crea la API (A5); el adjunto se sube después.
	* Asset siempre respondía false (nunca asignaba respOT); la vista no mira la respuesta. Acá: true si se creó.
	*/
	public function guardar_agregar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | guardar_agregar()");
		$data = $this->input->post();
		$idTarea = $this->input->post('id_tarea');
		$herramientas = array();
		if (!empty($data['id_her']) && is_array($data['id_her'])) {
			foreach ($data['id_her'] as $k => $h) {
				$herramientas[] = array(
					'herrId'            => (string) $h,
					'cantidad'          => isset($data['cant_herr'][$k]) ? man_numero($data['cant_herr'][$k]) : '1',
					'descripcion_libre' => isset($data['desc_libre'][$k]) ? (string) $data['desc_libre'][$k] : '',
				);
			}
		}
		$result = $this->Otrabajos->lanzarOT(array(
			'id_tarea'      => !empty($idTarea) ? (string) $idTarea : '0',
			'descripcion'   => !empty($idTarea) ? '' : (string) $this->input->post('tareacustom'),
			'fecha_program' => $this->fechaIso($this->input->post('fechaInicio')),
			'fecha_entrega' => $this->fechaIso($this->input->post('fechaEntrega')),
			'id_sucursal'   => man_numero((string) $this->input->post('suci')),
			'id_proveedor'  => man_numero((string) $this->input->post('prov')),
			'id_equipo'     => (string) $this->input->post('equipo'),
		), $herramientas, man_detalle(isset($data['id_insumo']) ? $data['id_insumo'] : null, isset($data['cant_insumo']) ? $data['cant_insumo'] : null, 'artId'));
		if (!$result['status']) {
			echo json_encode($result);
			return;
		}
		$nomcodif = $this->codifNombre($result['id'], man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filesOTrabajos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		if ($this->upload->do_upload('inputPDF')) {
			$this->Otrabajos->setAdjunto(array('ot_adjunto' => "assets/filesOTrabajos/" . $nomcodif . $this->upload->data('file_ext'), 'otId' => $result['id']));
		}
		echo json_encode(true);
	}

	/**
	* Asigna responsable desde el modal "Asignar OT": cierra la tarea de Bonita, asigna "Ejecutar OT" y graba la OT.
	* Responde 1 o el mensaje de error, como asset.
	*/
	public function guardar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | guardar()");
		$fee = (string) $this->input->post('fecha_entrega');
		echo $this->Otrabajos->asignar($this->input->post('id_orden'), $this->input->post('usuario'),
			substr($fee, 6, 4) . "-" . substr($fee, 3, 2) . "-" . substr($fee, 0, 2),
			$this->input->post('case_id'), $this->input->post('task_id'));
	}

	/**
	* Asigna responsable y tarea desde la ventana de la orden del Plan de Mantenimiento (EjecutarOT de asset).
	* POST ot, responsable, tareastd, tareaOpcional, latitud, longitud. La tarea de Bonita, el tipo y el origen salen de la orden.
	* @return json status y msj si falla
	*/
	public function EjecutarOT(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | EjecutarOT()");
		echo json_encode($this->Otrabajos->asignarPlan($this->input->post('ot'), $this->input->post('responsable'), $this->input->post('tareastd'),
			$this->input->post('tareaOpcional'), $this->input->post('latitud'), $this->input->post('longitud')));
	}

	/** Cambia el responsable de "Ejecutar OT" (POST ot, responsable). @return json status y msj si falla */
	public function reasignaOt(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | reasignaOt()");
		echo json_encode($this->Otrabajos->reasignarPlan($this->input->post('ot'), $this->input->post('responsable')));
	}

	/**
	* Lanza el caso de Bonita de una orden programada sin lanzar (botón "Lanzar" de la lista de órdenes, MIG-8)
	* @return json status, id_orden y case_id, o status false y msj
	*/
	public function lanzar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | lanzar()");
		echo json_encode($this->Otrabajos->lanzarPlan($this->input->post('ot')));
	}

	/**
	* Anula una orden programada sin lanzar (opción "Anular" de la lista de órdenes y del calendario, MIG-8)
	* @return json status e id_orden, o status false y msj
	*/
	public function anular(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | anular()");
		echo json_encode($this->Otrabajos->anularPlan($this->input->post('ot')));
	}

	/** Guarda el responsable elegido al cerrar la ventana de la orden (POST idOt, id_usuario_a). @return json bool */
	public function updateResponsable(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | updateResponsable()");
		echo json_encode($this->Otrabajos->updateResponsablePlan($this->input->post('idOt'), $this->input->post('id_usuario_a')));
	}

	/**
	* Guarda la edición de la OT y reemplaza herramientas e insumos (asset no responde nada)
	*/
	public function guardar_editar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | guardar_editar()");
		$datos = $this->input->post('parametros');
		$this->Otrabajos->update_edita($this->input->post('idOT'), is_array($datos) ? $datos : array(),
			man_detalle($this->input->post('idsherramienta'), $this->input->post('cantHerram'), 'herrId'),
			man_detalle($this->input->post('idsinsumo'), $this->input->post('cantInsum'), 'artId'));
	}

	/**
	* Agrega un adjunto (inputPDF) a la OT; se verifica que la OT sea de la empresa antes de subir
	* @return json ot_adjunto e id, o false
	*/
	public function agregarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | agregarAdjunto()");
		$id = $this->input->post('idAgregaAdjunto');
		if (!$this->Otrabajos->getasigna($id)) {
			echo json_encode(false);
			return;
		}
		$nomcodif = $this->codifNombre($id, man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filesOTrabajos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		$result = false;
		if ($this->upload->do_upload('inputPDF')) {
			$ruta = "assets/filesOTrabajos/" . $nomcodif . $this->upload->data('file_ext');
			$idAdj = $this->Otrabajos->setAdjunto(array('ot_adjunto' => $ruta, 'otId' => $id));
			if ($idAdj) {
				$result = array('ot_adjunto' => $ruta, 'id' => $idAdj);
			}
		}
		echo json_encode($result);
	}

	public function eliminarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | eliminarAdjunto()");
		echo json_encode($this->Otrabajos->eliminarAdjunto($this->input->post('id_adjunto')));
	}

	/**
	* El botón "Total" dispara también este alta/edición del ABM viejo de OT, cuyo formulario (otrabajos/view_) no
	* existe: llega sin acción y asset no hace nada. Acá tampoco (E9).
	*/
	public function setotrabajo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | setotrabajo() >> sin acción, no hace nada");
		echo json_encode(true);
	}

	/** Termina la OT con la fecha de hoy */
	public function FinalizaOt(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | FinalizaOt()");
		print_r($this->Otrabajos->update_cambio($this->input->post('idfin'), date("Y-m-d")));
	}

	/** Anula la OT (AN). Asset no responde nada. */
	public function baja_predictivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | baja_predictivo()");
		$this->Otrabajos->update_predictivo(array('estado' => "AN"), $this->input->post('gloid'));
	}

	/**
	* Tareas de la OT (asignación)
	*/
	public function cargartarea($permission = '', $idglob = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | cargartarea($permission, $idglob)");
		$data['list']       = $this->Otrabajos->cargartareas($idglob);
		$data['permission'] = $permission;
		$data['id_orden']   = $idglob;
		$this->load->view('otrabajos/asignacion', $data);
	}

	public function agregar_tarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | agregar_tarea()");
		$datos = $this->input->post('parametros');
		$id = is_array($datos) ? $this->Otrabajos->agregar_tareas($datos) : false;
		echo $id ? $id : 0;
	}

	public function EliminarTarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | EliminarTarea()");
		print_r($this->Otrabajos->cambiarTarea($this->input->post('idtarea'), 'estado', 'IN'));
	}

	/** Marca la tarea realizada (la vista manda el id de la tarea como id_orden) */
	public function TareaRealizada(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | TareaRealizada()");
		print_r($this->Otrabajos->cambiarTarea($this->input->get('id_orden'), 'estado', 'RE'));
	}

	public function ModificarUsuario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | ModificarUsuario()");
		print_r($this->Otrabajos->cambiarTarea($this->input->post('idtarea'), 'usuario', man_numero($this->input->post('idusu'))));
	}

	/** Cambia la fecha de la tarea (llega dd/mm/aaaa) */
	public function ModificarFecha(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | ModificarFecha()");
		$fe = (string) $this->input->post('fe');
		$fecha = trim($fe) === '' ? '' : substr($fe, 6, 4) . "/" . substr($fe, 3, 2) . "/" . substr($fe, 0, 2);
		print_r($this->Otrabajos->cambiarTarea($this->input->post('idtarea'), 'fecha', man_fecha($fecha)));
	}

	/**
	* Nombre codificado del archivo: id_empresa_fecha (año-mes-dia-hora-min-seg)
	*/
	private function codifNombre($ultimoId, $empId){
		$hora = date('Y-m-d H:i:s');
		$delimiter = array(" ", ",", ".", "'", "\"", "|", "\\", "/", ";", ":");
		$explode = explode($delimiter[0], str_replace($delimiter, $delimiter[0], $hora));
		return $ultimoId . '_' . $empId . '_' . $explode[0] . '-' . $explode[1] . '-' . $explode[2] . '-' . $explode[3];
	}

	/** dd/mm/aaaa → aaaa-mm-dd, como asset (vacío queda vacío) */
	private function fechaIso($fecha){
		$f = explode('/', (string) $fecha);
		return count($f) == 3 ? $f[2] . '-' . $f[1] . '-' . $f[0] : '';
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
