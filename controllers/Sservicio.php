<?php defined('BASEPATH') or exit('No direct script access allowed');

class Sservicio extends CI_Controller{
    public function __construct(){
        parent::__construct();
		$this->load->model('Sservicios');
		$this->load->model('Areas');
		$this->load->model('Procesos');
    }
    /**
	* Vista principal para la gestion de solicitudes de servicios
	* @param 
	* @return view
	*/
    public function index($permission = "Add-Edit-Del-"){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | index()");

		$data['list'] = $this->Sservicios->getServiciosList('false');
		$data['permission'] = $permission;

        $this->load->view('Sservicios/list_bpm', $data);
    }
	/**
	* Levanta vista para la creacion de una solicitud de servicios
	* @param 
	* @return view
	*/
	public function nuevaSS($permission){
		// $data['list']       = $this->Otrabajos->otrabajos_List();
		$data['permission'] = $permission;
		$this->load->view('Sservicios/view_', $data);
	}
	public function getSS(){

		$data["SS"] = $this->Sservicios->getSSs($_POST['idSS']);
		$id_equipo = $data["SS"][0]->id_equipo;
		$data["EQ_SEC"] = $this->Sservicios->getEquipoSector($id_equipo);
		$id_area = $data["EQ_SEC"][0]->area;
		$id_proceso = $data["EQ_SEC"][0]->proceso;
		$area = $this->areas->Obtener_areas($id_area);
		$data["AR"] = $area[0]["descripcion"];
		$proceso = $this->procesos->Obtener_procesos($id_proceso);
		$data["PR"] = $proceso[0]["descripcion"];

		echo json_encode($data);
	}
	// Trae sectores por empresa logueada - Listo
	public function getSector(){
		$data = $this->session->userdata();
		log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getSector() |  data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
		
		// if(empty($data['user_data'][0]['usrName'])){
		// 	log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
		// 	$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
		// 	$this->session->set_userdata($var);
		// 	$this->session->unset_userdata(null);
		// 	$this->session->sess_destroy();

		// 	echo ("<script>location.href='login'</script>");

		// }else{
			$response = $this->Sservicios->getSectores($this->input->post());
			echo json_encode($response);
		// }
	}
	public function getEquipo(){
		$response = $this->Sservicios->getEquipos($this->input->post());
		echo json_encode($response);
	}
	// Elimina solicitud - Listo 
	public function elimSolicitud(){
		$id = $this->input->post('id_solic');
		$response = $this->Sservicios->elimSolicitudes($id);
		echo json_encode($response);
	}
	/**
	* Trae equipos segun sector de empresa logueada
	* @param 
	* @return array lsita equipos del sector seleccionado
	*/
	public function getEquipSector(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getEquipSector()");
		
		$response = $this->Sservicios->getEquipSectores($this->input->post());
		echo json_encode($response);
	}
	//Trae datos para impresion de solicitud - Listo
	public function getsolImp(){
		$id = $_POST['idservicio'];

		$result = $this->Sservicios->getsolImps($id);

		if ($result) {

			$arre['datos'] = $result;
			print_r(json_encode($arre));
		} else echo "nada";
	}
	// Trae usuarios registrados (para solicitantes de la SServicio) OK
	public function getOperario(){
		$response = $this->Sservicios->getOperarios($this->input->post());
		echo json_encode($response);
	}
	/* FUNCIONES ORIGINALES DE ASSET	*/

	// Codifica nombre de imagen para no repetir en servidor
	// formato "12_6_2018-05-21-15-26-24" idpreventivo_idempresa_fecha(a�o-mes-dia-hora-min-seg)
	function codifNombre($ultimoId,$empId){

		$guion = '_';
		$guion_medio = '-';
		$hora = date('Y-m-d H:i:s');// hora actual del sistema	
		$delimiter = array(" ",",",".","'","\"","|","\\","/",";",":");
		$replace = str_replace($delimiter, $delimiter[0], $hora);
		$explode = explode($delimiter[0], $replace);
		
		$strigHora = $explode[0].$guion_medio.$explode[1].$guion_medio.$explode[2].$guion_medio.$explode[3];
		
		$nomImagen = $ultimoId.$guion.$empId.$guion.$strigHora;
		
		return $nomImagen;
  	}
	/* INTEGRACION CON BPM */
	// trae tareas estandar para select de nueva solicitud
	public function getTareasStandar(){
		$result = $this->Sservicios->getTareasStandar();
		echo json_encode($result);
	}
	/**
	* GUARDA SSERVICIOS y lanza proceso en BPM (inspección)
	* @param 
	* @return array json con respuesta de la transaccion
	*/
	function lanzarProcesoBPM(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | lanzarProcesoBPM()");
		$userdata = $this->session->userdata('user_data');
		$empId = empresa();
		// inserta registro en  tabla solicitud de reparacion
		$id_solServicio  = $this->Sservicios->setservicios($this->input->post());

		if ($id_solServicio) {
			//Contrato para lanzar Solcitud de Servicio
			$contract = array(
				"idSolicitudServicio"	=>	$id_solServicio,
				"idOT"  => 	0
			);			

			//Lanzar Proceso
			$responce  = $this->bpm->lanzarProceso(BPM_PROCESS_ID_MANTENIMIENTO, $contract);
			
			if(!$responce['status']){
				$this->Sservicios->eliminar($id_solServicio);
				echo json_encode($responce); 
				return;
			}

			//Validar Resultado
		
			if ($responce['data']['caseId']) {
				////////// Subir imagen o pdf 
				$nomcodif = $this->codifNombre($id_solServicio, $empId); // codificacion de nomb  		
				$config = [
					"upload_path" => "./assets/filesSS",
					'allowed_types' => "png|jpg|pdf|xlsx",
					'file_name'=> $nomcodif
				];
				$this->load->library("upload",$config);
				if ($this->upload->do_upload('inputPDF')) {
					$urlfile = "assets/filesSS/";
					$data = array("upload_data" => $this->upload->data());
					$extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
					$nomcodif = $urlfile.$nomcodif.$extens;
					$adjunto = array('sol_adjunto' => $nomcodif);
					$result['respNomImagen'] = $this->Sservicios->setAdjunto($adjunto,$id_solServicio);
				}else{
					$result['respImagen'] = false;
				}	
				//update de solic de servicio concaseid
				if($this->Sservicios->setCaseId($responce['data']['caseId'],$id_solServicio)){

					echo json_encode(['status'=> true, 'msj'=>'OK']);return;

				}else{

					echo json_encode(['status'=> false, 'msj'=>ASP_0200]);return;

				}				

			} else{ 

				//Falla Lanzar el Procesoy elimina la solicitud
				$result = $this->Sservicios->elimSolicitudes($id_solServicio);
				echo json_encode(['status'=> false, 'msj'=>ASP_0100]);
				return;
			}
	
		}else{ 

			//Falla Lanzar el Procesoy elimina la solicitud
			$result = $this->Sservicios->elimSolicitudes($id_solServicio);
			echo json_encode(['status'=> false, 'msj'=>ASP_0100]);
			return;
		}
	}
	/*	./INTEGRACION CON BPM */

	public function getInfoEquipo(){
		$data = $this->Sservicios->getInfoEquipos($this->input->post());
		if ($data  == false) {
			echo json_encode(false);
		} else {
			echo json_encode($data);
		}
	}
	// Guarda solicitud de Servicio - Listo
	public function setservicios(){

		$data = $this->Sservicios->setservicios($this->input->post());
		if($data  == false)
		{
			echo json_encode(false);
		}
		else
		{
			echo json_encode(true);	
		}
	}
	//////// no vistas
	public function get_SolicTerminada(){

		$data['list'] = $this->Sservicios->get_SolicTerminadas();
		$data['permission'] = $permission;
		$this->load->view('Sservicios/list_term', $data);
	}
	public function confSolicitud()
	{
		$data = $this->Sservicios->confSolicitudes($this->input->post());
		if ($data  == false) {
			echo json_encode(false);
		} else {
			echo json_encode(true);
		}
	}
	public function activSolicitud()
	{
		$data = $this->input->post();
		$response = $this->Sservicios->activSolicitudes($data);
		$this->load->view('Sservicios/list', $data);
	}
	/**
	* Devuelve el listado de solicitudes de servicio con aquellas en estado Conforme
	* @param string $showConforme
	* @return array json con listado
	*/
	public function getServiciosConformes(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicio | getServiciosConformes()");

		$showConformes = $_GET['showConformes'];
		$data['list'] = $this->Sservicios->getServiciosList($showConformes);
		echo json_encode($data['list']);
	}
}