<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Componentes: catálogo (index/listabm) y asignación a equipos (asigna/list, cargarcomp/view_) — MIG-ASSET-1.
 *
 * Los archivos (PDF) se guardan en la carpeta de asset (decisión E11, man_asset_path()). Cambios
 * respecto de asset: el nombre del archivo lleva la empresa (asset la dejaba vacía), y no se imprimen
 * mensajes de depuración en las respuestas.
 */
class Componente extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Componentes');
		$this->load->helper('man');
	}

	/**
	* Catálogo de componentes
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = '')
	{
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | index($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['list']       = $this->Componentes->listadoABM();
		$data['permission'] = $permission;
		$this->load->view('componente/listabm', $data);
	}

	/**
	* Componentes asignados a equipos
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function asigna($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | asigna($permission)");
		if (!$this->empresaVinculada()) {
			return;
		}
		$data['list']       = $this->Componentes->componentes_List();
		$data['permission'] = $permission;
		$this->load->view('componente/list', $data);
	}

	/**
	* Formulario para asignar componentes a un equipo
	*/
	public function cargarcomp($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | cargarcomp($permission)");
		$data['permission'] = $permission;
		$this->load->view('componente/view_', $data);
	}

	/**
	* Equipos de la empresa
	* @return json lista de equipos, o "nada"
	*/
	public function traerequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | traerequipo()");
		$equipo = $this->Componentes->traerequipo();
		if ($equipo) {
			echo json_encode(array_values($equipo));
		}
		else echo "nada";
	}

	/**
	* Baja de la asociación equipo/componente
	*/
	public function baja_comp(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | baja_comp()");
		$idequip = $this->input->post('idequipo');
		$idcomp  = $this->input->post('datos');
		$result  = $this->Componentes->delete_asociacion($idequip, $idcomp);
		print_r($result);
	}

	/**
	* Descripción del equipo
	* @return json status y datos
	*/
	public function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getequipo()");
		$id     = $this->input->post('idequipo');
		$equipo = $this->Componentes->getequipo($id);
		if ($equipo) {
			$arre = array('status' => true);
			foreach ($equipo as $row) {
				$arre['datos'] = $row;
			}
			print_r(json_encode($arre));
		}
		else echo json_encode(array('status' => false, 'datos' => ''));
	}

	/**
	* Marcas para el modal de agregar componente
	* @return json lista de marcas, o "nada"
	*/
	public function getmarca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getmarca()");
		$marca = $this->Componentes->getmarca();
		if ($marca) {
			echo json_encode(array_values($marca));
		}
		else echo "nada";
	}

	/**
	* Alta de componente desde la asignación, con el PDF en base64
	*/
	public function agregar_componente()
	{
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | agregar_componente()");
		$datos = $this->input->post('parametros');
		if ($datos) {
			$insert = array(
				'descripcion' => $datos['descripcion'],
				'informacion' => $datos['informacion'],
				'marcaid'     => $datos['marcaid'],
			);
			$result = $this->Componentes->agregar_componente($insert);
			print_r(json_encode($result));
			if ($result) {
				$ultimoId = $this->Componentes->ultimo_id;
				$path = "assets/files/equipos/" . $ultimoId . ".pdf";
				file_put_contents(man_asset_path() . $path, base64_decode($datos['pdf']));
				return $this->Componentes->updatecomp($ultimoId, array('pdf' => $path));
			}
		}
	}

	/**
	* Nombre codificado del archivo: idcomponente_idempresa_fecha (año-mes-dia-hora-min-seg)
	*/
	private function codifNombre($ultimoId, $empId){
		$hora = date('Y-m-d H:i:s');
		$delimiter = array(" ", ",", ".", "'", "\"", "|", "\\", "/", ";", ":");
		$explode = explode($delimiter[0], str_replace($delimiter, $delimiter[0], $hora));
		$strigHora = $explode[0] . '-' . $explode[1] . '-' . $explode[2] . '-' . $explode[3];
		return $ultimoId . '_' . $empId . '_' . $strigHora;
	}

	/**
	* Asigna componentes a un equipo. La vista manda arrays indexados desde 1 y la cantidad en x.
	*/
	public function guardar_componente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | guardar_componente()");
		$idequipo = $this->input->post('idequipo');
		$compo    = $this->input->post('comp');
		$codigo   = $this->input->post('codigo');
		$sistema  = $this->input->post('sistemaid');
		$ba       = (int) $this->input->post('x');
		$res      = false;
		for ($j = 1; $j <= $ba; $j++) {
			if (!empty($compo[$j])) {
				$res = $this->Componentes->insert_componente(array(
					'id_equipo'     => $idequipo,
					'id_componente' => $compo[$j],
					'codigo'        => isset($codigo[$j]) ? $codigo[$j] : '',
					'sistemaid'     => isset($sistema[$j]) ? $sistema[$j] : '',
				));
			}
		}
		return $res;
	}

	/**
	* Componentes asociados a un equipo
	*/
	public function getcompo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getcompo()");
		$id    = $this->input->post('idequipo');
		$compo = $this->Componentes->getcompo($id);
		if ($compo != 0) {
			echo json_encode(array_values($compo));
		}
		return $compo;
	}

	/**
	* Datos de una asociación para el modal de editar
	*/
	public function getEditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getEditar()");
		$dataCompEq = $this->Componentes->getEditar($this->input->post('idCompEq'));
		$data = array();
		if ($dataCompEq) {
			foreach ($dataCompEq[0] as $clave => $valor) {
				$data[$clave] = $valor;
			}
		}
		echo json_encode($data);
	}

	/**
	* Componentes para autocompletar
	*/
	public function getcomponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getcomponente()");
		echo json_encode($this->Componentes->getcomponente());
	}

	/**
	* Sistemas de la empresa
	*/
	public function getsistema(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getsistema()");
		$compo = $this->Componentes->getsistema();
		if ($compo) {
			echo json_encode(array_values($compo));
		}
		else echo "nada";
	}

	/**
	* Modifica una asociación equipo/componente
	*/
	public function editarCompEq(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | editarCompEq()");
		$result = $this->Componentes->updateEditar($this->input->post('data'), $this->input->post('idCompEq'));
		echo json_encode($result);
	}

	/**
	* Alta de componente en el catálogo, con archivo adjunto (inputPDF)
	*/
	public function agregarComponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | agregarComponente()");
		$datos = array(
			"descripcion" => $this->input->post("descrip1"),
			"informacion" => $this->input->post("info"),
			"marcaid"     => $this->input->post("ma"),
			"estado"      => "AC"
		);
		$response = $this->Componentes->agregar_componente($datos);
		if ($response) {
			$ultimoId = $this->Componentes->ultimo_id;
			$nomcodif = $this->codifNombre($ultimoId, man_empresa());
			$this->load->library("upload", array(
				"upload_path"   => man_asset_path() . "assets/files/equipos",
				'allowed_types' => "png|jpg|pdf|xlsx",
				'file_name'     => $nomcodif
			));
			if ($this->upload->do_upload('inputPDF')) {
				$adjunto  = array('pdf' => $nomcodif . $this->upload->data('file_ext'));
				$response = $this->Componentes->updatecomp($ultimoId, $adjunto);
			} else {
				$response = false;
			}
		}
		echo json_encode($response);
	}

	/**
	* Baja de un componente del catálogo
	*/
	public function bajaComponente()
	{
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | bajaComponente()");
		echo json_encode($this->Componentes->bajaComponente($this->input->post('idComponente')));
	}

	/**
	* Modifica un componente del catálogo; si llega archivo (pdfE), lo reemplaza
	*/
	public function editarComponente()
	{
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | editarComponente()");
		$id_componente = $this->input->post("idComponenteE");
		$datos = array(
			"descripcion" => $this->input->post("descripcionE"),
			"informacion" => $this->input->post("informacionE"),
			"marcaid"     => $this->input->post("marcaE")
		);
		if (isset($_FILES['pdfE']) && $_FILES['pdfE']['size'] > 0) {
			$this->load->library("upload", array(
				"overwrite"     => true,
				"upload_path"   => man_asset_path() . "assets/files/equipos",
				'allowed_types' => "pdf",
				'file_name'     => "comp" . $id_componente
			));
			if (!$this->upload->do_upload('pdfE')) {
				echo json_encode(false);
				return;
			}
			$datos['pdf'] = "comp" . $id_componente . $this->upload->data('file_ext');
		}
		echo json_encode($this->Componentes->editarComponente($datos, $id_componente) == true);
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
