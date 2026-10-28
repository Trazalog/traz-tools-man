<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Solicitudes de servicio: listado, conformidad, anulación y datos para ver e imprimir — MIG-ASSET-2.
 *
 * Portado desde el asset actual (diverge del fork) solo con las acciones que usan las vistas (E9). Crear una solicitud
 * (con su caso de Bonita) y eliminarla son operaciones de toolsMANAPI, que habla con Bonita por toolsbpmAPI (A5, E22).
 */
class Sservicio extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('Sservicios', 'Areas', 'Procesos'));
		$this->load->helper('man');
	}

	/**
	* Listado de solicitudes
	* @param string $permission acciones habilitadas; sin parámetro, solo lectura (E3)
	*/
	public function index($permission = '')
	{
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['permission'] = $permission;
		$this->load->view('Sservicios/list_bpm', $data);
	}

	/**
	* Solicitud con equipo, sector, área, proceso y adjuntos
	*/
	public function getSS(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getSS()");
		$idSS = $this->input->post('idSS');
		$data["SS"] = $this->Sservicios->getSSs($idSS);
		if (empty($data["SS"])) {
			echo json_encode($data);
			return;
		}
		$data["EQ_SEC"] = $this->Sservicios->getEquipoSector($data["SS"][0]->id_equipo);
		$area = isset($data["EQ_SEC"][0]) ? $this->Areas->Obtener_areas($data["EQ_SEC"][0]->area) : null;
		$data["AR"] = $area ? $area[0]["descripcion"] : null;
		$proceso = isset($data["EQ_SEC"][0]) ? $this->Procesos->Obtener_procesos($data["EQ_SEC"][0]->proceso) : null;
		$data["PR"] = $proceso ? $proceso[0]["descripcion"] : null;
		$data['adjuntos'] = $this->Sservicios->getAdjuntosSolServicio($idSS);
		echo json_encode($data);
	}

	public function getSector(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getSector()");
		echo json_encode($this->Sservicios->getSectores());
	}

	/** Anula la solicitud (AN) */
	public function elimSolicitud(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | elimSolicitud()");
		echo json_encode($this->Sservicios->elimSolicitudes($this->input->post('id_solic')));
	}

	public function getEquipSector(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getEquipSector()");
		echo json_encode($this->Sservicios->getEquipSectores($this->input->post()));
	}

	/** Datos para imprimir */
	public function getsolImp(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getsolImp()");
		$result = $this->Sservicios->getsolImps($this->input->post('idservicio'));
		if ($result) {
			echo json_encode(array('datos' => $result));
		}
		else echo "nada";
	}

	/**
	* Crea la solicitud con su caso de Bonita y, si viene, sube el adjunto (inputPDF) a la carpeta de asset (E11)
	* @return json status y msj
	*/
	function lanzarProcesoBPM(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | lanzarProcesoBPM()");
		$r = $this->Sservicios->lanzarSolicitud($this->input->post());
		if (!$r['status']) {
			echo json_encode($r);
			return;
		}
		$nomcodif = $this->codifNombre($r['id'], man_empresa());
		$this->load->library("upload", array(
			"upload_path"   => man_asset_path() . "assets/filesSS",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		));
		if ($this->upload->do_upload('inputPDF')) {
			$this->Sservicios->setAdjunto(array('sol_adjunto' => "assets/filesSS/" . $nomcodif . $this->upload->data('file_ext')), $r['id']);
		}
		echo json_encode(['status' => true, 'msj' => 'OK']);
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
	* Formulario de nueva solicitud
	*/
	public function nuevaSS($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | nuevaSS($permission)");
		$data['permission'] = $permission;
		$this->load->view('Sservicios/view_', $data);
	}

	/**
	* Solicitudes terminadas (para reactivarlas)
	*/
	public function get_SolicTerminada($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | get_SolicTerminada($permission)");
		$data['list'] = $this->Sservicios->get_SolicTerminadas();
		$data['permission'] = $permission;
		$this->load->view('Sservicios/list_term', $data);
	}

	/** Registra la conformidad */
	public function confSolicitud(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | confSolicitud()");
		echo json_encode((bool) $this->Sservicios->confSolicitudes($this->input->post()));
	}

	/**
	* Vuelve una solicitud terminada a Solicitada. Asset respondía la vista vieja Sservicios/list cuando la que llama
	* espera JSON, así que siempre mostraba "Error en cambio de estado"; acá responde el resultado (E12).
	*/
	public function activSolicitud(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | activSolicitud()");
		echo json_encode($this->Sservicios->activSolicitudes($this->input->post()));
	}

	/**
	* Página de la tabla (DataTables del lado del servidor)
	*/
	public function paginado(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | paginado()");
		$search = $this->input->post('search');
		$r = $this->Sservicios->solicitudespaginadas($this->input->post('start'), $this->input->post('length'),
			isset($search['value']) ? $search['value'] : '', $this->input->post('order'), $this->input->post('showConformes'));
		echo json_encode(array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => intval($r['numDataTotal']),
			"recordsFiltered" => intval($r['numDataTotal']),
			"data"            => $r['datos']
		));
	}

	/**
	* Marca la solicitud y su OT como eliminadas y borra el caso de Bonita (mismos textos de respuesta que asset)
	*/
	public function eliminar_solicitud(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | eliminar_solicitud()");
		$id_solicitud = $this->input->post('id_solicitud');
		$motivo = $this->input->post('motivo');
		if (empty($id_solicitud) || empty($motivo)) {
			echo "ID de solicitud o motivo no proporcionado.";
			return;
		}
		$r = $this->Sservicios->eliminarSolicitud($id_solicitud, $motivo, $this->input->post('case_id'));
		if ($r['status']) {
			echo "Solicitud de servicio y orden de trabajo eliminadas exitosamente.";
			echo json_encode($r);
		} else {
			echo "Error al eliminar solicitud de servicio y orden de trabajo.";
		}
	}

	/**
	* ¿El usuario está en alguno de los grupos de asset indicados? (provisorio hasta MIG-5)
	*/
	public function validaUsuario(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | validaUsuario()");
		echo json_encode($this->Sservicios->validaUsuario(man_usuario(), man_empresa(), $this->input->post('filtro1'), $this->input->post('filtro2')));
	}
}
