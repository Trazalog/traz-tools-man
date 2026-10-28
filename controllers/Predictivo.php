<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Predictivos: listado, alta, edición y baja de planes predictivos — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan las vistas (E9). Las vistas usan
 * además acciones de Preventivo (herramientas, insumos, tareas, adjuntos). El alta con herramientas e insumos es una
 * operación de toolsMANAPI (ADR-014, regla 3); los adjuntos van a la carpeta de asset (E11).
 */
class Predictivo extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Predictivos');
		$this->load->helper('man');
	}

	/**
	* Listado de predictivos
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | index($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['list']       = $this->Predictivos->predictivo_List();
		$data['permission'] = $permission;
		$this->load->view('predictivo/list', $data);
	}

	/**
	* Formulario de alta
	*/
	public function cargarpredictivo($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | cargarpredictivo($permission)");
		$data['permission'] = $permission;
		$this->load->view('predictivo/view_', $data);
	}

	public function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | getEquipo()");
		$this->lista($this->Predictivos->getEquipos());
	}

	public function getInfoEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | getInfoEquipo()");
		$this->lista($this->Predictivos->getInfoEquipos($this->input->post('id_equipo')));
	}

	public function getUnidTiempo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | getUnidTiempo()");
		$this->lista($this->Predictivos->getUnidTiempos());
	}

	/**
	* Alta de predictivo con herramientas, insumos y adjunto (inputPDF). La fecha llega como dd-mm-aaaa.
	* @return json true si se guardó todo
	*/
	public function guardar_predictivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | guardar_predictivo()");
		$data = $this->input->post();
		$fe = $this->input->post('vfecha');
		$datos = array(
			'id_equipo'     => $this->input->post('equipo'),
			'tarea_descrip' => $this->input->post('id_tarea'),
			'fecha'         => substr($fe, 6, 4) . "/" . substr($fe, 3, 2) . "/" . substr($fe, 0, 2),
			'periodo'       => $this->input->post('periodo'),
			'cantidad'      => $this->input->post('cantidad'),
			'horash'        => floatval($this->input->post('hshombre')),
			'pred_duracion' => $this->input->post('duracion'),
			'pred_canth'    => $this->input->post('cantOper'),
			'id_unidad'     => $this->input->post('unidad')
		);
		$herramientas = man_detalle(isset($data['id_her']) ? $data['id_her'] : null, isset($data['cant_herr']) ? $data['cant_herr'] : null, 'herrId');
		$insumos      = man_detalle(isset($data['id_insumo']) ? $data['id_insumo'] : null, isset($data['cant_insumo']) ? $data['cant_insumo'] : null, 'artId');

		$result = $this->Predictivos->insert_predictivo($datos, $herramientas, $insumos);
		if ($result) {
			$ultimoId = $this->Predictivos->ultimo_id;
			$nomcodif = $this->codifNombre($ultimoId, man_empresa());
			$this->load->library("upload", array(
				"upload_path"   => man_asset_path() . "assets/filespredictivos",
				'allowed_types' => "png|jpg|pdf|xlsx",
				'file_name'     => $nomcodif
			));
			if ($this->upload->do_upload('inputPDF')) {
				$this->Predictivos->updateAdjunto(array('pred_adjunto' => $nomcodif . $this->upload->data('file_ext')), $ultimoId);
			}
		}
		echo json_encode($result);
	}

	/**
	* Predictivo a editar, con herramientas e insumos
	*/
	public function getEditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | getEditar()");
		$id = $this->input->post('idpred');
		$result2 = $this->Predictivos->getInfopred($this->input->post('datos'), $id);
		if ($result2) {
			$arre['datos'] = $result2;
			$herramientas = $this->Predictivos->getPredictivoHerramientas($id);
			$arre['herramientas'] = $herramientas ? $herramientas : 0;
			$insumos = $this->Predictivos->getPredictivoInsumos($id);
			$arre['insumos'] = $insumos ? $insumos : 0;
		} else {
			$arre['datos'] = 0;
		}
		echo json_encode($arre);
	}

	/**
	* Borra el nombre del adjunto del predictivo. En asset la vista llamaba a Preventivo/eliminarAdjunto con el id del
	* predictivo, que borraba el adjunto del preventivo con ese número (T20); acá se borra el del predictivo (E12).
	*/
	public function eliminarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | eliminarAdjunto()");
		echo json_encode($this->Predictivos->eliminarAdjunto($this->input->post('idprev')));
	}

	/**
	* Agrega o reemplaza el adjunto (inputPDF)
	* @return json array('pred_adjunto' => nombre) o false
	*/
	public function agregarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | agregarAdjunto()");
		$idPredictivo = $this->input->post('idAgregaAdjunto');
		$nomcodif = $this->codifNombre($idPredictivo, man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filespredictivos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		if ($this->upload->do_upload('inputPDF')) {
			$response = $this->Predictivos->updateAdjunto(array('pred_adjunto' => $nomcodif . $this->upload->data('file_ext')), $idPredictivo);
		} else {
			$response = false;
		}
		echo json_encode($response);
	}

	/**
	* Edita el predictivo y reemplaza herramientas e insumos
	* @return json true si se guardó todo (asset devolvía la constante inexistente "fase" si fallaba, T10)
	*/
	public function updatePredictivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | updatePredictivo()");
		$datos = array(
			'tarea_descrip' => $this->input->post('tarea'),
			'fecha'         => $this->input->post('fecha'),
			'periodo'       => $this->input->post('periodo'),
			'cantidad'      => $this->input->post('cantidad'),
			'horash'        => $this->input->post('horash'),
			'pred_duracion' => $this->input->post('duracion'),
			'id_unidad'     => $this->input->post('unidad'),
			'pred_canth'    => $this->input->post('operarios')
		);
		$herramientas = man_detalle($this->input->post('idsherramienta'), $this->input->post('cantHerram'), 'herrId');
		$insumos      = man_detalle($this->input->post('idsinsumo'), $this->input->post('cantInsum'), 'artId');
		$r = $this->Predictivos->updatePredictivos($datos, $this->input->post('id_Predictivo'), $herramientas, $insumos);
		echo json_encode($r['updatedatos'] && $r['respHerram'] && $r['respInsumo']);
	}

	/**
	* Baja lógica (AN)
	*/
	public function baja_predictivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Predictivo | baja_predictivo()");
		echo json_encode($this->Predictivos->baja_predictivos(array('estado' => "AN"), $this->input->post('idpre')));
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
