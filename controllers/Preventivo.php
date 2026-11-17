<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Preventivos: listado, alta, edición y baja de planes preventivos — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan las vistas (E9), incluidas las que
 * usan Predictivo y Backlog (herramientas, insumos, adjuntos). Los adjuntos van a la carpeta de asset (E11). El alta
 * con herramientas e insumos es una operación de toolsMANAPI (ADR-014, regla 3).
 */
class Preventivo extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Preventivos');
		$this->load->helper('man');
	}

	/**
	* Listado de preventivos
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | index($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['list']       = $this->Preventivos->preventivos_List();
		$data['permission'] = $permission;
		$this->load->view('preventivo/list', $data);
	}

	public function getUnidTiempo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getUnidTiempo()");
		$this->lista($this->Preventivos->getUnidTiempos());
	}

	public function getEquipoNuevoPrevent(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getEquipoNuevoPrevent()");
		echo json_encode($this->Preventivos->getEquipoNuevoPrevent($this->input->post()));
	}

	public function gettarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | gettarea()");
		echo json_encode($this->Preventivos->gettarea());
	}

	public function gettareaxPatron(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | gettareaxPatron()");
		echo json_encode($this->Preventivos->gettareaxPatron($this->input->get('patron')));
	}

	/**
	* Preventivo a editar, con herramientas e insumos
	*/
	public function geteditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | geteditar()");
		$id   = $this->input->post('idprev');
		$info = $this->Preventivos->getInfoPreventivo($id);
		if ($info) {
			$arre['datos'] = $info;
			$herramientas = $this->Preventivos->getPreventivoHerramientas($id);
			$arre['herramientas'] = $herramientas ? $herramientas : 0;
			$insumos = $this->Preventivos->getPreventivoInsumos($id);
			$arre['insumos'] = $insumos ? $insumos : 0;
			echo json_encode($arre);
		}
		else echo "nada";
	}

	public function getcomponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getcomponente()");
		echo json_encode($this->Preventivos->getcomponente($this->input->post('id_equipo')));
	}

	public function getherramienta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getherramienta()");
		$this->lista($this->Preventivos->getherramienta());
	}

	public function getHerramientasB(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getHerramientasB()");
		echo json_encode($this->Preventivos->getHerramientasB());
	}

	public function getinsumo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getinsumo()");
		$this->lista($this->Preventivos->getinsumo());
	}

	/** Datos de una herramienta del pañol de tools (E18) */
	public function getdatos(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getdatos()");
		$this->lista($this->Preventivos->getdatos($this->input->post()));
	}

	/**
	* Alta de preventivo con herramientas, insumos y adjunto (inputPDF)
	* @return json resPrenvent, respHerram, respInsumo y respNomImagen o respImagen (como en asset)
	*/
	public function guardar_preventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | guardar_preventivo()");
		$data = $this->input->post();
		$datos = array(
			'id_equipo'     => $this->input->post('id_equipo'),
			'id_tarea'      => $this->input->post('tarea'),
			'perido'        => $this->input->post('periodo'),
			'cantidad'      => $this->input->post('cantidad'),
			'ultimo'        => $this->input->post('ultimo'),
			'id_componente' => $this->input->post('id_componente'),
			'critico1'      => $this->input->post('alerta'),
			'horash'        => $this->input->post('hshombre'),
			'prev_duracion' => $this->input->post('duracion'),
			'id_unidad'     => $this->input->post('unidad'),
			'prev_canth'    => $this->input->post('cantOper'),
			'lectura_base'  => $this->input->post('lectura_base')
		);
		$herramientas = man_detalle(isset($data['id_her']) ? $data['id_her'] : null, isset($data['cant_herr']) ? $data['cant_herr'] : null, 'herrId');
		$insumos      = man_detalle(isset($data['id_insumo']) ? $data['id_insumo'] : null, isset($data['cant_insumo']) ? $data['cant_insumo'] : null, 'artId');

		$response['resPrenvent'] = $this->Preventivos->insert_preventivo($datos, $herramientas, $insumos);
		if ($response['resPrenvent']) {
			$ultimoId = $this->Preventivos->ultimo_id;
			// Como insert_batch() en asset: cantidad de filas grabadas, o "vacio" si no había.
			$response['respHerram'] = $herramientas ? count($herramientas) : "vacio";
			$response['respInsumo'] = $insumos ? count($insumos) : "vacio";
			$nomcodif = 'preventivo' . $this->codifNombre($ultimoId, man_empresa());
			$this->load->library("upload", array(
				"upload_path"   => man_asset_path() . "assets/filespreventivos",
				'allowed_types' => "png|jpg|pdf",
				'file_name'     => $nomcodif
			));
			if ($this->upload->do_upload('inputPDF')) {
				$adjunto = array('prev_adjunto' => $nomcodif . $this->upload->data('file_ext'));
				$response['respNomImagen'] = $this->Preventivos->updateAdjunto($adjunto, $ultimoId);
			} else {
				$response['respImagen'] = false;
			}
		}
		echo json_encode($response);
	}

	/**
	* Edita el preventivo y reemplaza herramientas e insumos
	*/
	public function editar_preventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | editar_preventivo()");
		$datos = array(
			'id_equipo'     => $this->input->post('id_equipo'),
			'id_tarea'      => $this->input->post('id_tarea'),
			'perido'        => $this->input->post('perido'),
			'cantidad'      => $this->input->post('cantidad'),
			'ultimo'        => $this->input->post('ultimo'),
			'id_componente' => $this->input->post('id_componente'),
			'horash'        => $this->input->post('horash'),
			'prev_duracion' => $this->input->post('prev_duracion'),
			'id_unidad'     => $this->input->post("unidad"),
			'prev_canth'    => $this->input->post('cantOper')
		);
		$herramientas = man_detalle($this->input->post('idsherramienta'), $this->input->post('cantHerram'), 'herrId');
		$insumos      = man_detalle($this->input->post('idsinsumo'), $this->input->post('cantInsum'), 'artId');
		echo json_encode($this->Preventivos->update_editar($datos, $this->input->post('id_prevent'), $herramientas, $insumos));
	}

	/**
	* Baja lógica (AN)
	*/
	public function baja_preventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | baja_preventivo()");
		echo json_encode($this->Preventivos->update_preventivo(array('estadoprev' => "AN"), $this->input->post('idprev')));
	}

	/**
	* Formulario de alta
	*/
	public function cargarpreventivo($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | cargarpreventivo($permission)");
		$data['permission'] = $permission;
		$this->load->view('preventivo/view_', $data);
	}

	/**
	* Borra el nombre del adjunto del preventivo
	*/
	public function eliminarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | eliminarAdjunto()");
		echo json_encode($this->Preventivos->eliminarAdjunto($this->input->post('idprev')));
	}

	/**
	* Agrega o reemplaza el adjunto del preventivo (inputPDF)
	* @return json array('prev_adjunto' => nombre) o false
	*/
	public function agregarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | agregarAdjunto()");
		$idPreventivo = $this->input->post('idAgregaAdjunto');
		// Solo registros de la empresa: si no, no se sube nada (asset no lo verificaba).
		if (!$this->Preventivos->getInfoPreventivo($idPreventivo)) {
			echo json_encode(false);
			return;
		}
		$nomcodif = $this->codifNombre($idPreventivo, man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filespreventivos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		if ($this->upload->do_upload('inputPDF')) {
			$adjunto  = array('prev_adjunto' => $nomcodif . $this->upload->data('file_ext'));
			$response = $this->Preventivos->updateAdjunto($adjunto, $idPreventivo);
		} else {
			$response = false;
		}
		echo json_encode($response);
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

	/** Lista como json (array de filas), o "nada" si no hay. */
	private function lista($filas){
		if ($filas) {
			echo json_encode(array_values($filas));
		}
		else echo "nada";
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
