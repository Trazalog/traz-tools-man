<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Equipos: listado (list), alta (view_), edición, estados, lecturas, adjuntos, contratistas y metas — MIG-ASSET-1.
 *
 * Portado desde asset solo con las acciones que usan las vistas (decisión E9). Los adjuntos se guardan
 * en la carpeta de asset (decisión E11, man_asset_path()). El mantenimiento autónomo de las lecturas
 * (campo Falla, que crea una solicitud y lanza Bonita, y el formulario dinámico) queda apagado hasta
 * portarlo con su motor (decisión E14).
 */
class Equipo extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Equipos');
		$this->load->helper('man');
	}

	/**
	* Listado de equipos
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | index($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['empresa']    = man_empresa();
		$data['permission'] = $permission;
		$this->load->view('Equipo/list', $data);
	}

	/**
	* Página del listado (DataTables del lado del servidor)
	* @return json draw, recordsTotal, recordsFiltered y data
	*/
	public function paginado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | paginado()");
		$search = $this->input->post('search');
		$r = $this->Equipos->equiposPaginados($this->input->post('start'), $this->input->post('length'),
			isset($search['value']) ? $search['value'] : '');
		echo json_encode(array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => count($r['datos']),
			"recordsFiltered" => intval($r['numDataTotal']),
			"data"            => $r['datos']
		));
	}

	/**
	* Baja lógica del equipo (AN)
	*/
	public function baja_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | baja_equipo()");
		$result = $this->Equipos->baja_equipos(array('estado' => "AN"), $this->input->post('idEquipo'));
		print_r(json_encode($result));
	}

	/**
	* Equipo a editar
	* @return json datos del equipo, o "nada"
	*/
	public function getEditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getEditar()");
		$info = $this->Equipos->getEquipoId($this->input->post('idEquipo'));
		if ($info) {
			echo json_encode($info);
		}
		else echo "nada";
	}

	public function getarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getarea()");
		$this->lista($this->Equipos->getareas());
	}

	public function getproceso(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getproceso()");
		$this->lista($this->Equipos->getprocesos());
	}

	public function getcriti(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcriti()");
		$this->lista($this->Equipos->getcriti());
	}

	/** Sectores (etapas) */
	public function getetapa(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getetapa()");
		$this->lista($this->Equipos->getetapas());
	}

	public function getgrupo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getgrupo()");
		$this->lista($this->Equipos->getgrupos());
	}

	public function getmarca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getmarca()");
		$this->lista($this->Equipos->getmarcas());
	}

	public function getcliente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcliente()");
		$this->lista($this->Equipos->getclientes());
	}

	/**
	* Guarda el equipo editado
	*/
	public function editar_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | editar_equipo()");
		$datos = $this->input->post('data');
		echo json_encode($this->Equipos->update_editar(is_array($datos) ? $datos : array(), $this->input->post('idEquipo')));
	}

	/**
	* Contratistas asignados al equipo
	*/
	public function getContratistasEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getContratistasEquipo()");
		echo json_encode($this->Equipos->getContratistasEquipo($this->input->post('id_equipo')));
	}

	/**
	* Contratistas de la empresa
	*/
	public function getcontra(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcontra()");
		$this->lista($this->Equipos->getcontra());
	}

	/**
	* Formulario de alta de equipo
	*/
	public function cargarequipo($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | cargarequipo($permission)");
		$data['empresa']    = man_empresa();
		$data['permission'] = $permission;
		$this->load->view('Equipo/view_', $data);
	}

	// Altas rápidas desde el formulario: devuelven el id creado, o 0.
	public function agregar_area(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_area()");
		$this->alta('agregar_area');
	}

	public function agregar_criti(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_criti()");
		$this->alta('agregar_criti');
	}

	public function agregar_proceso(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_proceso()");
		$this->alta('agregar_proceso');
	}

	/** Sector (etapa) */
	public function agregar_etapa(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_etapa()");
		$this->alta('agregar_etapa');
	}

	public function agregar_grupo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_grupo()");
		$this->alta('agregar_grupos');
	}

	public function agregar_marca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_marca()");
		$this->alta('agregar_marcas');
	}

	public function agregar_cliente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_cliente()");
		$this->alta('agregar_clientes');
	}

	/**
	* Alta de equipo, con adjuntos opcionales (inputPDF[])
	* @return json status, code y msj
	*/
	public function guardar_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | guardar_equipo()");
		$codigo = $this->input->post("codigo");
		if ($this->Equipos->validaUnicidadCodigo($codigo)) {
			echo json_encode(['status'=>'error','code'=>'201','msj'=>'El código ingresado se encuentra en uso']);
			return;
		}

		$data = array(
			'descripcion'         => $this->input->post("descripcion"),
			'fecha_ingreso'       => $this->input->post("fecha_ingreso"),
			'fecha_garantia'      => $this->input->post("fecha_garantia"),
			'marca'               => $this->input->post("marca"),
			'codigo'              => $codigo,
			'id_hubicacion'       => $this->input->post("ubicacion"),
			'id_sector'           => $this->input->post("etapa"),
			'id_grupo'            => $this->input->post("grupo"),
			'id_customer'         => $this->input->post("cliente"),
			'id_criticidad'       => $this->input->post("criticidad"),
			'fecha_ultimalectura' => $this->input->post("fecha_ultima"),
			'ultima_lectura'      => $this->input->post("ultima_lectura"),
			'descrip_tecnica'     => $this->input->post("destec"),
			'id_unidad'           => $this->input->post("unin"),
			'id_area'             => $this->input->post("area"),
			'id_proceso'          => $this->input->post("proceso"),
			'numero_serie'        => $this->input->post("numse")
		);

		if ($this->Equipos->insert_equipo($data)) {
			$this->subirAdjuntos($this->Equipos->ultimo_id);
			echo json_encode(['status'=>'ok','code'=>'200','msj'=>'Guardado exitoso de nuevo equipo']);
		} else {
			echo json_encode(['status'=>'ok','code'=>'500','msj'=>'Error al guardar equipo']);
		}
	}

	/**
	* Inhabilita el equipo (IN)
	*/
	public function cambio_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | cambio_equipo()");
		print_r($this->Equipos->update_cambio(array('estado' => "IN"), $this->input->post('idequipo')));
	}

	/**
	* Equipo con su marca (modal de contratistas)
	*/
	public function getco(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getco()");
		$this->lista($this->Equipos->getco($this->input->post()));
	}

	/**
	* Asigna un contratista al equipo
	*/
	public function guardarcontra(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | guardarcontra()");
		echo json_encode($this->Equipos->insert_contratista(array(
			'id_equipo'      => $this->input->post('id_equipo'),
			'id_contratista' => $this->input->post('id_contratista')
		)));
	}

	/**
	* Quita un contratista del equipo
	*/
	public function delContratista(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | delContratista()");
		echo json_encode($this->Equipos->delContratista($this->input->post('id_contratista'), $this->input->post('id_equipo')));
	}

	/**
	* Registra una lectura. Con "falla" (mantenimiento autónomo) asset además creaba una solicitud de
	* servicio y lanzaba Bonita; eso va con Solicitudes en MIG-2 (decisión E14) y acá se rechaza sin guardar.
	*/
	public function setLectura(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | setLectura()");
		if ($this->input->post('falla')) {
			echo json_encode(['status' => false, 'msj' => 'La lectura con falla todavía no está disponible en este módulo']);
			return;
		}
		echo json_encode($this->Equipos->setLecturas($this->input->post()));
	}

	/**
	* Edita valor y observación de una lectura
	*/
	public function setLecturaObservacionEdit(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | setLecturaObservacionEdit()");
		echo json_encode($this->Equipos->setLecturaObservacionEdit($this->input->post()));
	}

	/**
	* Historial de lecturas del equipo
	*/
	public function getHistoriaLect(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getHistoriaLect()");
		echo json_encode($this->Equipos->getHistoriaLecturas($this->input->post()));
	}

	/**
	* Última lectura del equipo
	*/
	public function getEqPorId(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getEqPorId()");
		echo json_encode($this->Equipos->getEqPorIds($this->input->post('idEquipo')));
	}

	/**
	* Habilita el equipo (AC) con su lectura de alta
	*/
	public function estado_alta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | estado_alta()");
		echo json_encode($this->Equipos->estado_alta($this->input->post('idequipo')));
	}

	/**
	* Marca un adjunto como eliminado
	* @return json adjuntos que quedan
	*/
	public function eliminaAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | eliminaAdjunto()");
		if ($this->Equipos->eliminaAdjunto($this->input->post('idAdjunto'))) {
			echo json_encode($this->Equipos->getAdjuntos($this->input->post('idEquipo')));
		} else {
			echo json_encode(['msj'=>'Error al eliminar archivo']);
		}
	}

	/**
	* Agrega adjuntos al equipo (inputPDF[])
	* @return json adjuntos del equipo
	*/
	public function agregarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregarAdjunto()");
		$idEquipo = $this->input->post('idAgregaAdjunto');
		if (!empty($_FILES['inputPDF']['name'])) {
			$this->subirAdjuntos($idEquipo);
			echo json_encode($this->Equipos->getAdjuntos($idEquipo));
		}
	}

	/**
	* Reemplaza el archivo de un adjunto (inputEditarPDF)
	* @return json adjuntos del equipo
	*/
	public function EditarAdjunto(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | EditarAdjunto()");
		$idEquipo = $this->input->post('idEquipo');
		$responseArchivos = null;
		if (isset($_FILES['inputEditarPDF'])) {
			$nombreArchivo = $_FILES['inputEditarPDF']['name'];
			$nomcodif = $this->codificaNombreV2($nombreArchivo, pathinfo($nombreArchivo, PATHINFO_EXTENSION));
			$this->iniciarUpload($nomcodif);
			if ($this->upload->do_upload('inputEditarPDF')) {
				$this->Equipos->updateAdjunto(array('adjunto' => $this->upload->data('file_name')), $this->input->post('id_adjunto'));
				$responseArchivos = $this->Equipos->getAdjuntos($idEquipo);
			}
		}
		echo json_encode($responseArchivos);
	}

	/**
	* Meta de disponibilidad del equipo
	*/
	public function getMeta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getMeta()");
		$response = $this->Equipos->getMeta($this->input->post());
		if (!$response) {
			echo json_encode(['msj'=>'Error al extraer Meta']);
		} else {
			echo json_encode($response);
		}
	}

	/**
	* Asigna la meta de disponibilidad
	*/
	public function asignarMeta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | asignarMeta()");
		if (!$this->Equipos->asignarMeta($this->input->post())) {
			echo json_encode(['msj'=>'Error al asinar Meta']);
		} else {
			echo json_encode(['msj'=>'OK']);
		}
	}

	/**
	 * Sube los archivos de inputPDF[] a la carpeta de adjuntos de equipos de asset y los registra.
	 * Asset cargaba la librería una sola vez y todos los archivos tomaban la configuración del primero,
	 * así que a partir del segundo el nombre en disco no coincidía con el de la base; acá se reinicia
	 * por archivo y se registra el nombre con el que quedó guardado.
	 */
	private function subirAdjuntos($idEquipo){
		if (empty($_FILES['inputPDF']['name'])) {
			return;
		}
		$files = $_FILES['inputPDF'];
		$cantidad = is_array($files['name']) ? count($files['name']) : 0;
		for ($i = 0; $i < $cantidad; $i++) {
			if ($files['name'][$i] === '') {
				continue;
			}
			$nomcodif = $this->codificaNombreV2($files['name'][$i], pathinfo($files['name'][$i], PATHINFO_EXTENSION));
			$_FILES['inputPDF_' . $i] = array(
				'name'     => $nomcodif,
				'type'     => $files['type'][$i],
				'tmp_name' => $files['tmp_name'][$i],
				'error'    => $files['error'][$i],
				'size'     => $files['size'][$i],
			);
			$this->iniciarUpload($nomcodif);
			if ($this->upload->do_upload('inputPDF_' . $i)) {
				$this->Equipos->guardaAdjuntoEquipo(array('id_equipo' => $idEquipo, 'adjunto' => $this->upload->data('file_name')));
			}
		}
	}

	private function iniciarUpload($nombre){
		$config = array(
			"upload_path"   => man_asset_path() . "assets/filesequipos",
			'allowed_types' => "*",
			'file_name'     => $nombre
		);
		if (isset($this->upload)) {
			$this->upload->initialize($config);
		} else {
			$this->load->library("upload", $config);
		}
	}

	/**
	 * Nombre de archivo sin espacios y con un número al azar, para no pisar otros en el servidor
	 */
	private function codificaNombreV2($nombre, $extension){
		$nombreSinEspacios = strtr($nombre, " ", "_");
		$long = strlen($extension) + 1;
		return substr($nombreSinEspacios, 0, -$long) . rand(1, 3000) . "." . $extension;
	}

	/** Alta rápida: llama al método del model con "parametros" y devuelve el id, o 0. */
	private function alta($metodo){
		$datos = $this->input->post('parametros');
		if ($datos) {
			$id = $this->Equipos->$metodo($datos);
			echo $id ? $id : 0;
		}
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
