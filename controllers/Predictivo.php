<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictivo extends CI_Controller {

	function __construct(){
		
		parent::__construct();
		$this->load->model('Predictivos');
		$this->load->model('Otrabajos');
	}

	public function index($permission){

		$data = $this->session->userdata();
		log_message('DEBUG','#Main/index | Predictivo >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
	
		if(empty($data['user_data'][0]['usrName'])){
			log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
			$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
			$this->session->set_userdata($var);
			$this->session->unset_userdata(null);
			$this->session->sess_destroy();
	
			echo ("<script>location.href='login'</script>");
	
		}else{

			$data['list'] = $this->Predictivos->predictivo_List();
			$data['permission'] = $permission;
			$this->load->view('predictivo/list', $data);
		}
	}

	// Carga vista nuevo Predictivo
	public function cargarpredictivo($permission){ 
        $data['permission'] = $permission;    // envia permisos       
        $this->load->view('predictivo/view_',$data);
    }

	// Trae equipos por empresa logueada - Listo
	public function getEquipo(){
		
		$equipo = $this->Predictivos->getEquipos();

		if($equipo)
		{	
			$arre=array();
	        foreach ($equipo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Trae info de equipos por ID y por empresa logueada - Listo
	public function getInfoEquipo(){
		$id = $this->input->post('id_equipo');
		$equipo = $this->Predictivos->getInfoEquipos($id);

		if($equipo)
		{	
			$arre=array();
	        foreach ($equipo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Trae tareas por empresa logueada - Listo
	public function gettarea()
	{	
		$tareas = $this->Preventivos->gettarea();
		echo json_encode($tareas);
	}

	// Trae unidades de tiempo - Listo
	public function getUnidTiempo(){
		
		$tarea = $this->Predictivos->getUnidTiempos();

		if($tarea)
		{	
			$arre=array();
	        foreach ($tarea as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Guarda predictivos nuevos - Listo
	public function guardar_predictivo(){
		
		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa'];		

		$data     = $this->input->post();
		$eq = $this->input->post('equipo');//
		$ta = $this->input->post('id_tarea');//
		$fe = $this->input->post('vfecha');//
		$per = $this->input->post('periodo');//
		$can = $this->input->post('cantidad');//
		$hh = floatval($this->input->post('hshombre'));//
		$dur = $this->input->post('duracion');//
		$uTi = $this->input->post('unidad');//
		$op = $this->input->post('cantOper');//
		
		$uno=substr($fe, 0, 2); 
        $dos=substr($fe, 3, 2); 
        $tres=substr($fe, 6, 4); 
        $resul = ($tres."/".$dos."/".$uno); 
		
		$datos = array(	'id_equipo'=>$eq,
										'tarea_descrip'=>$ta,
										'fecha'=>$resul,
										'periodo'=>$per,
										'cantidad'=>$can,
										'horash'=>$hh,
										'estado'=>'C',
										'pred_duracion'=>$dur,
										'pred_canth'=>$op,
										'id_empresa'=>$empId,
										'id_unidad'=>$uTi
									);

		$response['respPredictivo'] = $this->Predictivos->insert_predictivo($datos);

		if($response['respPredictivo']){

			$ultimoId = $this->db->insert_id(); 	
			////////// para guardar herramientas                 
				if ( !empty($data['id_her']) ){
					//saco array con herramientas y el id de empresa
					$herr = $data["id_her"]; 
					$i = 0;
					foreach ($herr as $h) {
						$herramPred[$i]['herrId']= $h;
						$herramPred[$i]['id_empresa']= $empId;
						$i++;                                
					} 
					//saco array con cant de herramientas y el id de preventivo 
					$cantHerr = $data["cant_herr"];
					$z = 0;
					foreach ($cantHerr as $c) {
						$herramPred[$z]['cantidad']= $c;
						$herramPred[$z]['predId']= $ultimoId;
						$z++;                                
					}				
					// Guarda el bacht de datos de herramientas
					$response['respHerram'] = $this->Predictivos->insertPredHerram($herramPred);
				}else{

					$response['respHerram'] = true;	// no habia herramientas
				}	

			////////// para guardar insumos
				if ( !empty($data['id_insumo']) ){
					//saco array con herramientas y el id de empresa
					$ins = $data["id_insumo"]; 
					$j = 0;
					foreach ($ins as $in) {
						$insumoPred[$j]['artId'] = $in;
						$insumoPred[$j]['id_empresa'] = $empId;
						$j++;                                
					} 
					//saco array con cant de herramientas y el id de preventivo 
					$cantInsum = $data["cant_insumo"];
					$z = 0;
					foreach ($cantInsum as $ci) {
						$insumoPred[$z]['cantidad'] = $ci;
						$insumoPred[$z]['predId'] = $ultimoId;
						$z++;                                
					}
					// Guarda el bacht de datos de herramientas
					$response['respInsumo'] = $this->Predictivos->insertPredInsum($insumoPred);
				}else{

					$response['respInsumo'] = true;	// no habia insumos
				}	
				
			////////// Subir imagen o pdf 
				$nomcodif = $this->codifNombre($ultimoId,$empId); // codificacion de nomb  		
				$config = [
					"upload_path" => "./assets/filespredictivos",
					'allowed_types' => "png|jpg|pdf|xlsx",
					'file_name'=> $nomcodif
				];

				$this->load->library("upload",$config);
				if ($this->upload->do_upload('inputPDF')) {
					
					$data = array("upload_data" => $this->upload->data());
					$extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
					$nomcodif = $nomcodif.$extens;
					$adjunto = array('pred_adjunto' => $nomcodif);
					$response['respNomImagen'] = $this->Predictivos->updateAdjunto($adjunto,$ultimoId);
				}else{
					$response['respImagen'] = false;
				}				
		}		
		
		// si todas las inserciones se hicieron devuelve true
		if ($response['respPredictivo'] && $response['respHerram'] && $response['respInsumo']) {
			$result = true;
		} else {
			$result = false;
		}
		
		echo json_encode($result);			
	}

	// Codifica nombre de imagen para no repetir en servidor
	// formato "12_6_2018-05-21-15-26-24" idpreventivo_idempresa_fecha(año-mes-dia-hora-min-seg)
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


	// Trae info de Predictivo para editar - Listo 
	public function getEditar(){

		$id = $this->input->post('idpred');
		$ide = $this->input->post('datos');
		$result2 = $this->Predictivos->getInfopred($ide,$id);
	
		if($result2){

			$arre['datos'] = $result2;
			// trae herramientas 
			$herramientas = $this->Predictivos->getPredictivoHerramientas($id);
			if($herramientas){
				$arre['herramientas']=$herramientas;
			}
			else{ 
				$arre['herramientas']=0;
			}
			// trae insumos
			$insumos = $this->Predictivos->getPredictivoInsumos($id);
			if($insumos){
					$arre['insumos']=$insumos;
			}
			else{ $arre['insumos']=0;}

			echo json_encode($arre);
		}
		else {
			$arre['datos'] = 0;
			echo json_encode($arre);
		}	
	}	

	// Agrega adjunto desde modal edicion
	public function agregarAdjunto(){
		$userdata     = $this->session->userdata('user_data');
		$empId        = $userdata[0]['id_empresa'];

		$idPredictivo = $this->input->post('idAgregaAdjunto');

		$nomcodif = $this->codifNombre($idPredictivo, $empId); // codificacion de nomb  		
		$config   = [
			"upload_path"   => "./assets/filespredictivos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		];

		$this->load->library("upload",$config);
		if ($this->upload->do_upload('inputPDF'))
		{
			$data     = array("upload_data" => $this->upload->data());
			$extens   = $data['upload_data']['file_ext'];//guardo extension de archivo
			$nomcodif = $nomcodif.$extens;
			$adjunto  = array('pred_adjunto' => $nomcodif);
			$response = $this->Predictivos->updateAdjunto($adjunto, $idPredictivo);
		}
		else
		{
			$response = false;
		}

		echo json_encode($response);
	}

	// Guarda Predictivo editado -
	public function updatePredictivo(){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa'];
		// datos
			$id = $this->input->post('id_Predictivo');
			$ta = $this->input->post('tarea');
			$fe = $this->input->post('fecha');
			$per = $this->input->post('periodo');
			$can = $this->input->post('cantidad');
			$hh = $this->input->post('horash');
			$dur = $this->input->post('duracion');
			$uTi = $this->input->post('unidad');
			$op = $this->input->post('operarios');	
		
		$datos = array(	'tarea_descrip'=>$ta,
						'fecha'=>$fe,
						'periodo'=>$per,
						'cantidad'=>$can,
						'horash'=>$hh,
						'estado'=>'C',
						'pred_duracion'=>$dur,
						'id_unidad'=>$uTi,
						'pred_canth'=>$op						
					);

		$response['updatedatos'] = $this->Predictivos->updatePredictivos($datos,$id);
		
		if ($response['updatedatos']) {
			// caso 1: hay herram/insumos cargadas y agrego mas
			// caso 2: hay herram/insumos cargadas borra alguna
			// caso 3: hay herram/insumos cargadas borra todas
		
			/// HERRAMIENTAS	
				//saco array con herramientas y el id de empresa
				$herr = $this->input->post('idsherramienta');
				//saco array con cant de herramientas y el id de preventivo 
				$cantHerr = $this->input->post('cantHerram');		
				
				if ( !empty($herr) ) {				
					
						$respdelHerr = $this->Predictivos->deleteHerramPred($id);// borr herram ant	  		
						if ($respdelHerr) {
							$i = 0;		  			
							foreach ($herr as $h) {
								$herramPrev[$i]['herrId']= $h;
								$herramPrev[$i]['id_empresa']= $empId;
								$i++;                                
							} 
							$z = 0;
							foreach ($cantHerr as $c) {
								$herramPrev[$z]['cantidad']= $c;
								$herramPrev[$z]['predId']= $id;
								$z++;                                
							}
						// Guarda el bacht de datos de herramientas
						$response['respHerram'] = $this->Predictivos->insertPredHerram($herramPrev);
						}
				}else{
					// se borran la herram
					$respdelHerr = $this->Predictivos->deleteHerramPred($id);
					$response['respHerram'] = $respdelHerr;	// no habia herramientas
				}


			/// INSUMOS	
				//saco array con herramientas y el id de empresa
				$ins = $this->input->post('idsinsumo');
				//saco array con cant de herramientas y el id de preventivo			
				$cantInsum = $this->input->post('cantInsum');
				if ( !empty($ins) ){
					// se borran la insum anteriores
					$respdelInsum = $this->Predictivos->deleteInsumPred($id); 
					$j = 0;
					foreach ($ins as $in) {
						$insumoPrev[$j]['artId'] = $in;
						$insumoPrev[$j]['id_empresa'] = $empId;
						$j++;                                
					}
					$z = 0;
					foreach ($cantInsum as $ci) {
						$insumoPrev[$z]['cantidad'] = $ci;
						$insumoPrev[$z]['predId'] = $id;
						$z++;                                
					}
					// Guarda el bacht de datos de herramientas
					$response['respInsumo'] = $this->Predictivos->insertPredInsum($insumoPrev);
				}else{
					$respdelInsum = $this->Predictivos->deleteInsumPred($id); 
					$response['respInsumo'] = $respdelInsum ;	// no habia insumos	  			
				}	

		}	

		if ($response['updatedatos'] &&	$response['respHerram'] && $response['respInsumo']) {
			$result = true;
		} else {
			$result = fase;
		}		

		echo json_encode($result);
	}

	//Cambia de estado a "AN"
	public function baja_predictivo(){
		
		$idpre= $this->input->post('idpre');		
		$datos = array('estado'=>"AN");

		$result = $this->Predictivos->updatePredictivos($datos,$idpre);
		if ($result) {
			echo json_encode(true);
		}
		else {
			echo json_encode(false);
		}
	}

}