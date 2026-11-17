<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backlog: listado, alta, edición y baja — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan las vistas (E9). Las vistas usan
 * además acciones de Preventivo y Predictivo (herramientas, insumos, tareas, unidades, adjuntos). El alta con
 * herramientas e insumos es una operación de toolsMANAPI (ADR-014, regla 3); los adjuntos van a la carpeta de asset
 * (E11). La edición que además cierra una tarea de Bonita (tipo "editNuevo", desde la bandeja) toma el backlog de la tarea,
 * lo deja Solicitado y cierra la tarea (MIG-3, decisión E19).
 */
class Backlog extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Backlogs');
		$this->load->helper('man');
	}

	/**
	* Listado de backlog
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | index($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['list']       = $this->Backlogs->backlog_List();
		$data['permission'] = $permission;
		$this->load->view('backlog/list', $data);
	}

	public function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | getequipo()");
		$equipo = $this->Backlogs->getequipo();
		if ($equipo) {
			echo json_encode(array_values($equipo));
		}
		else echo "nada";
	}

	public function getInfoEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | getInfoEquipo()");
		echo json_encode($this->Backlogs->getInfoEquipos($this->input->post()));
	}

	/**
	* Backlog a editar, con equipo, herramientas e insumos
	*/
	public function geteditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | geteditar()");
		$id  = $this->input->get('idpred');
		$ide = $this->input->get('datos');
		$result = $this->Backlogs->geteditar($id);
		if ($result) {
			$arre['datos']  = $result;
			$arre['equipo'] = $this->Backlogs->traerequiposBack($ide, $id);
			$herramientas = $this->Backlogs->getBacklogHerramientas($id);
			$arre['herramientas'] = $herramientas ? $herramientas : 0;
			$insumos = $this->Backlogs->getBacklogInsumos($id);
			$arre['insumos'] = $insumos ? $insumos : 0;
		} else {
			$arre['datos'] = 0;
		}
		echo json_encode($arre);
	}

	/**
	* Edita el backlog y reemplaza herramientas e insumos
	* @return json status y msj
	*/
	public function editar_backlog(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | editar_backlog()");
		$editNuevo = $this->input->post('tipo') == 'editNuevo';
		$idBack = $this->input->post('backid');
		if ($editNuevo) {
			// Desde la bandeja: el backlog sale de la tarea (no del número que manda el navegador) y la tarea debe ser "Editar Backlog"
			$this->load->model('Tareas');
			$det = $this->Tareas->detalle($this->input->post('idTarBonita'));
			$info = ($det !== null && $det['tarea']['displayName'] == 'Editar Backlog') ? $this->Tareas->backlogDeTarea($det['id_ss']) : 0;
			if (!$info) {
				echo json_encode(['status' => false, 'msj' => 'La tarea no es de la empresa o no es Editar Backlog']);
				return;
			}
			$idBack = $info[0]['backId'];
		}
		$datos = array(
			'id_tarea'       => $this->input->post('tarea'),
			'fecha'          => $this->input->post('fecha'),
			'horash'         => $this->input->post('hshombre'),
			'back_duracion'  => $this->input->post('duracion'),
			'back_canth'     => $this->input->post('back_canth'),
			'id_unidad'      => $this->input->post('id_unidad'),
			'tarea_opcional' => $this->input->post('tareaOpcional')
		);
		$herramientas = man_detalle($this->input->post('idsherramienta'), $this->input->post('cantHerram'), 'herrId');
		$insumos      = man_detalle($this->input->post('idsinsumo'), $this->input->post('cantInsum'), 'artId');
		$r = $this->Backlogs->editar_backlogs($datos, $idBack, $herramientas, $insumos);
		if ($r['editado'] && $editNuevo) {
			// Vuelve a quedar Solicitado y con el componente elegido; recién después se cierra la tarea (si Bonita falla, al reintentar se repite igual)
			if (!$this->Backlogs->reenviar($idBack, $this->input->post('idcomponenteequipo'))) {
				echo json_encode(['status' => false, 'msj' => 'ASP_0100 | Error en Base de Datos']);
				return;
			}
			$c = $this->Tareas->cerrarEditarBacklog($this->input->post('idTarBonita'));
			echo json_encode($c['status'] ? ['status' => true, 'msj' => 'OK'] : ['status' => false, 'msj' => 'El backlog se guardó pero no se pudo cerrar la tarea: ' . $c['msj']]);
			return;
		}
		if ($r['editado']) {
			echo json_encode(['status' => true, 'msj' => 'OK']);
		} else {
			echo json_encode(['status' => false, 'msj' => 'ASP_0100 | Error en Base de Datos']);
		}
	}

	/**
	* Borrado lógico (B)
	*/
	public function baja_backlog_estado_Borrado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | baja_backlog_estado_Borrado()");
		echo json_encode($this->Backlogs->update_back(array('estado' => "B"), $this->input->post('gloid')));
	}

	/**
	* Formulario de alta
	*/
	public function cargarback($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | cargarback($permission)");
		$data['permission'] = $permission;
		$this->load->view('backlog/view_', $data);
	}

	/**
	* Alta de backlog con herramientas, insumos y adjunto (inputPDF). La fecha llega como dd/mm/aaaa.
	* @return json true si se guardó todo
	*/
	public function guardar_backlog(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | guardar_backlog()");
		$data = $this->input->post();
		$fe = $data['vfecha'];
		$datos = array(
			'id_equipo'          => $data['equipo'],
			'id_tarea'           => $data['id_tarea'],
			'fecha'              => trim((string) $fe) === '' ? '' : substr($fe, 6, 4) . "/" . substr($fe, 3, 2) . "/" . substr($fe, 0, 2),
			'horash'             => $data['hshombre'],
			'back_duracion'      => $data['duracion'],
			'id_unidad'          => $data['unidad'],
			'back_canth'         => $data['cantOper'],
			'idcomponenteequipo' => $data['idcomponenteequipo'],
			'tarea_opcional'     => $data['tareaOpcional']
		);
		$herramientas = man_detalle(isset($data['id_her']) ? $data['id_her'] : null, isset($data['cant_herr']) ? $data['cant_herr'] : null, 'herrId');
		$insumos      = man_detalle(isset($data['id_insumo']) ? $data['id_insumo'] : null, isset($data['cant_insumo']) ? $data['cant_insumo'] : null, 'artId');

		$result = $this->Backlogs->insert_backlog($datos, $herramientas, $insumos);
		if ($result) {
			$ultimoId = $this->Backlogs->ultimo_id;
			$nomcodif = $this->codifNombre($ultimoId, man_empresa());
			$this->load->library("upload", array(
				"upload_path"   => man_asset_path() . "assets/filesbacklog",
				'allowed_types' => "png|jpg|pdf|xlsx",
				'file_name'     => $nomcodif
			));
			if ($this->upload->do_upload('inputPDF')) {
				$this->Backlogs->updateAdjunto(array('back_adjunto' => $nomcodif . $this->upload->data('file_ext')), $ultimoId);
			}
		}
		echo json_encode($result);
	}

	/**
	* Borra el nombre del adjunto del backlog. En asset la vista llamaba a Preventivo/eliminarAdjunto con el id del
	* backlog, que borraba el adjunto del preventivo con ese número (T20); acá se borra el del backlog (E12).
	*/
	public function eliminarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | eliminarAdjunto()");
		echo json_encode($this->Backlogs->eliminarAdjunto($this->input->post('idprev')));
	}

	/**
	* Agrega o reemplaza el adjunto (inputPDF)
	* @return json array('back_adjunto' => nombre) o false
	*/
	public function agregarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | agregarAdjunto()");
		$idBacklog = $this->input->post('idAgregaAdjunto');
		// Solo registros de la empresa: si no, no se sube nada (asset no lo verificaba).
		if (!$this->Backlogs->geteditar($idBacklog)) {
			echo json_encode(false);
			return;
		}
		$nomcodif = $this->codifNombre($idBacklog, man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filesbacklog",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		if ($this->upload->do_upload('inputPDF')) {
			$response = $this->Backlogs->updateAdjunto(array('back_adjunto' => $nomcodif . $this->upload->data('file_ext')), $idBacklog);
		} else {
			$response = false;
		}
		echo json_encode($response);
	}

	/**
	* Componentes del equipo para el autocompletar
	* @return json lista (value, label, descrip, sistema, idce) o 0
	*/
	public function getComponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Backlog | getComponente()");
		$componentes = $this->Backlogs->getComponentes($this->input->post('idEquipo'));
		if (!$componentes) {
			echo json_encode(0);
			return;
		}
		$arre = array();
		foreach ($componentes as $valor) {
			$arre[] = array(
				'value'   => $valor->codigo,
				'label'   => $valor->codigo,
				'descrip' => $valor->descripcion,
				'sistema' => $valor->sistema,
				'idce'    => $valor->idce,
			);
		}
		echo json_encode($arre);
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

	/**
	 * Si la empresa no está vinculada con asset, muestra el aviso y devuelve false.
	 */
	private function empresaVinculada(){
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return false;
		}
		return true;
	}
}
