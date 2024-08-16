<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backlog extends CI_Controller {

	function __construct()
        {
		parent::__construct();
		$this->load->model('Backlogs');
		$this->load->model('Tareas');
	}

	public function index($permission){
		$data = $this->session->userdata();
		log_message('DEBUG','#Main/index | Backlog >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
	
		if(empty($data['user_data'][0]['usrName'])){
			log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
			$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
			$this->session->set_userdata($var);
			$this->session->unset_userdata(null);
			$this->session->sess_destroy();
	
			echo ("<script>location.href='login'</script>");
	
		}else{

			$data['list'] = $this->Backlogs->backlog_List();		
			$data['permission'] = $permission;
			$this->load->view('backlog/list', $data);
		}
	}

	// Trae equipos para llenar select vista - Listo
	public function getequipo(){
		
		$equipo = $this->Backlogs->getequipo();		

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

	// Trae info de equipo por id
	public function getInfoEquipo(){
			
		$res = $this->Backlogs->getInfoEquipos($this->input->post());
		 echo json_encode($res);
	}

	// Trae tareas por empresa logueada - Listo
	public function gettarea(){
	
		$tarea = $this->Backlogs->gettareas();
		
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
	
	// Trae datos de backlog para editar - Listo
	public function geteditar(){

		$id=$_GET['idpred'];
		$ide=$_GET['datos'];

		$result = $this->Backlogs->geteditar($id);
		//dump($result, 'back sin tarea: ');
		if($result){	
			$arre['datos'] = $result;
			$result2 = $this->Backlogs->traerequiposBack($ide,$id);
	//	dump($result2, 'equipos: ');
		
			//dump($result2, 'equipos: ');
			//if($result2){
				$arre['equipo']=$result2;
				//dump($result2, 'equipos: ');
			//}
			// else {
			// 	$arre['equipo']=0;
			// }
			
			$herramientas = $this->Backlogs->getBacklogHerramientas($id);
			if($herramientas){
				$arre['herramientas']=$herramientas;
			}
			else{ 
				$arre['herramientas']=0;
			}
			// trae insumos
			$insumos = $this->Backlogs->getBacklogInsumos($id);
			if($insumos){
					$arre['insumos']=$insumos;
			}
			else{ $arre['insumos']=0;}

			echo json_encode($arre);	
		
		}else {
			
			$arre['datos'] = 0;
			echo json_encode($arre);
		}	
	}
	
/* Funciones para BPM */
	// al editar cambia a estado 'S' (solicitado)
	public function editarNuevo(){ 
		
		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa']; 
    $idce = $this->input->post('idce');
		$eq = $this->input->post('equipo');
		$fe = $this->input->post('fecha');
		$ta = $this->input->post('tarea');
		$hs = $this->input->post('horas');
		$tarOpc = $this->input->post('tarea_opcional');
		$idBacklog = $this->input->post('idBacklog');
		$idTarBonita = $this->input->post('idTarBonita');
	
		$datos = array('id_equipo'     			=> $eq,
									'id_tarea' 						=> $ta,						
									'fecha'         			=> $fe,
									'estado'       				=> 'S',
									'back_duracion' 			=> $hs,
									'id_unidad'						=> 1,			// 1 xq se carga en minutos 
									'id_empresa'    			=> $empId,
									'idcomponenteequipo' 	=> $idce,
									'tarea_opcional'			=> $tarOpc
								);
	
		$result = $this->Backlogs->editar_backlogs($datos,$idBacklog);		
	
		$result = $this->bpm->cerrarTarea($idTarBonita);

		echo json_encode($result);
	}

/* Fin Funciones para BPM */

	public function editar_backlog(){

		log_message('DEBUG', '#BACKLOG >> editar_backlog POST: ' . json_encode($this->input->post()));


		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa'];

		$datos 					= $this->input->post();	
		//dump_exit($datos);

		$idTarBonita		= $this->input->post('idTarBonita');
		$id_back 				= $this->input->post('backid');//
		$tarea 					= $this->input->post('tarea');
		$fecha 					= $this->input->post('fecha');//
		$duracion 			= $this->input->post('duracion');//
		$back_canth			= $this->input->post('back_canth');
		$hshombre 			= $this->input->post('hshombre');//
		$id_unidad 			= $this->input->post('id_unidad');//
		$tareaOpcional 	= $this->input->post('tareaOpcional');//
		$tipo 					= $this->input->post('tipo'); 
		$idcomponenteequipo = $this->input->post('idcomponenteequipo'); 

		// $uno		=  substr($fecha, 0, 2); 
		// $dos		=  substr($fecha, 3, 2); 
		// $tres		=  substr($fecha, 6, 4); 
		// $resul 	=  ($tres."-".$dos."-".$uno); 

		$resul 	= $fecha ;
			

		// SI ES TAREA BPM (BACK POR PROCESO)							
		if ($tipo == 'editNuevo') {
				
				$response = $this->bpm->cerrarTarea($idTarBonita);	

				log_message('DEBUG', '#BACKLOG >> editar_backlog >> editNuevo rsp: ' . json_encode($response));

				
				// Si cerro la tarea
				if ($response['status']) {	
					
						// al editar cambia a estado 'S' (solicitado)
						$datos = array('id_tarea' => $tarea,
													'fecha' => $resul,		// poner fecha actual
													'estado' => 'S',
													'horash' => $hshombre,
													'back_duracion' => $duracion,
													'back_canth'=> $back_canth,
													'id_unidad' => $id_unidad,
													'id_empresa'=> $empId,
													'tarea_opcional'=>$tareaOpcional,
													'idcomponenteequipo'=>	$idcomponenteequipo								
													);
						$result = $this->Backlogs->editar_backlogs($datos,$id_back); 

				}else{
					// error cerrando la tarea
					$respuesta['status'] = false;
					$respuesta['msj'] = 'ERROR';
					$respuesta['code'] = 'ASP_0200, Error ASP_0200: Comunicarse con el Proveedor de Servicio';
					echo json_encode($respuesta);
					return;
				}	

		}else{
				// sino es nuevo soloo edita el backlog
				$datos = array('id_tarea' => $tarea,
												'fecha' => $resul,
												'horash' => $hshombre,
												'back_duracion' => $duracion,
												'back_canth'=> $back_canth,
												'id_unidad' => $id_unidad,
												'tarea_opcional'=>$tareaOpcional									
												);	
				$result = $this->Backlogs->editar_backlogs($datos,$id_back);	
			
				log_message('DEBUG', '#BACKLOG >> editar_backlog >> editNuevo ELSE datos: ' . json_encode($datos). 'res:'. json_encode($result));

		}							
		
		
		// Si cerro tareas o edito exitossamente
		if ($result) {
			
			/// HERRAMIENTAS	
				//saco array con herramientas y el id de empresa
				$herr = $this->input->post('idsherramienta');
				//saco array con cant de herramientas y el id de preventivo 
				$cantHerr = $this->input->post('cantHerram');		
				
				if ( !empty($herr) ) {				
					
						$respdelHerr = $this->Backlogs->deleteHerramBack($id_back);// 	  		
						if ($respdelHerr) {
							$i = 0;		  			
							foreach ($herr as $h) {
								$herram[$i]['herrId']= $h;
								$herram[$i]['id_empresa']= $empId;
								$i++;                                
							} 
							$z = 0;
							foreach ($cantHerr as $c) {
								$herram[$z]['cantidad']= $c;
								$herram[$z]['backId']= $id_back;
								$z++;                                
							}
						// Guarda el bacht de datos de herramientas
						$response['respHerram'] = $this->Backlogs->insertBackHerram($herram);
						}
				}else{
					// se borran la herram
					$respdelHerr = $this->Backlogs->deleteHerramBack($id_back);
					$response['respHerram'] = $respdelHerr;	// no habia herramientas
				}

			/// INSUMOS	
				//saco array con herramientas y el id de empresa
				$ins = $this->input->post('idsinsumo');
				//saco array con cant de herramientas y el id de preventivo			
				$cantInsum = $this->input->post('cantInsum');
				if ( !empty($ins) ){
					// se borran la insum anteriores
					$respdelInsum = $this->Backlogs->deleteInsumBack($id_back); 
					$j = 0;
					foreach ($ins as $in) {
						$insumo[$j]['artId'] = $in;
						$insumo[$j]['id_empresa'] = $empId;
						$j++;                                
					}
					$z = 0;
					foreach ($cantInsum as $ci) {
						$insumo[$z]['cantidad'] = $ci;
						$insumo[$z]['backId'] = $id_back;
						$z++;                                
					}
					// Guarda el bacht de datos de herramientas
					$response['respInsumo'] = $this->Backlogs->insertBackInsum($insumo);
				}else{
					$respdelInsum = $this->Backlogs->deleteInsumBack($id_back); 
					$response['respInsumo'] = $respdelInsum ;	// no habia insumos	  			
				}	

				echo json_encode(['status'=>true, 'msj'=>'OK']);
				return;
		
		}else{
			// respuesta de error de BD
			echo json_encode(['status'=>false, 'msj'=> ASP_0100.' | Error en Base de Datos']);
			return;
		}

	}
	//Cambia de estado a "AN" - Listo
	public function baja_backlog(){
	
		$idpre=$_POST['gloid'];
		
		$datos = array('estado'=>"AN");
		$result = $this->Backlogs->update_back($datos, $idpre);
		echo json_encode($result);	
	}

	//Cambia de estado a "B" - BORRADO LOGICO
	public function baja_backlog_estado_Borrado(){
	
		$idpre=$_POST['gloid'];
		
		$datos = array('estado'=>"B");
		$result = $this->Backlogs->update_back($datos, $idpre);
		echo json_encode($result);	
	}

	// Carga vista para backolg nuevo - Listo
	public function cargarback($permission){ 
		$data['permission'] = $permission;       
		$this->load->view('backlog/view_',$data);
	}
  	
  //Inserta  Backlog nuevo - Listo
	public function guardar_backlog(){
		
		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa'];
		$data = $this->input->post();		     
		$ideq=$data['equipo'];
		$idce=$data['idcomponenteequipo'];
		$fe=$data['vfecha'];
		$ta=$data['id_tarea'];
		$hs=$data['hshombre'];		
		$back_dur = $data['duracion'];
		$id_unidad = $data['unidad'];
		$back_canth = $data['cantOper'];
		$tarOpc = $data['tareaOpcional'];
		// fecha convertida		
			$uno=substr($fe, 0, 2); 
			$dos=substr($fe, 3, 2); 
			$tres=substr($fe, 6, 4); 
			$resul = ($tres."/".$dos."/".$uno); 

		$datos = array('id_equipo'     			=> $ideq,//
										'id_tarea' 						=> $ta,		//				
										'fecha'         			=> $resul, //
										'horash'							=> $hs, //
										'estado'        			=> 'C',//				
										'back_duracion' 			=> $back_dur,
										'id_unidad'						=> $id_unidad,
										'back_canth'					=> $back_canth,
										'id_empresa'    			=> $empId,
										'idcomponenteequipo' 	=> $idce,//	
										'tarea_opcional'			=> $tarOpc
								);		
	
		$response['respBacklog'] = $this->Backlogs->insert_backlog($datos);	

		if($response['respBacklog']){

			$ultimoId = $this->db->insert_id(); 
			
			////////// para guardar herramientas                 
				if ( !empty($data['id_her']) ){
					//saco array con herramientas y el id de empresa
					$herr = $data["id_her"]; 
					$i = 0;
					foreach ($herr as $h) {
						$herram[$i]['herrId']= $h;
						$herram[$i]['id_empresa']= $empId;
						$i++;                                
					} 
					//saco array con cant de herramientas y el id de preventivo 
					$cantHerr = $data["cant_herr"];
					$z = 0;
					foreach ($cantHerr as $c) {
						$herram[$z]['cantidad']= $c;
						$herram[$z]['backId']= $ultimoId;
						$z++;                                
					}				
					// Guarda el bacht de datos de herramientas
					$response['respHerram'] = $this->Backlogs->insertBackHerram($herram);
				}else{

					$response['respHerram'] = true;	// no habia herramientas
				}	


			////////// para guardar insumos
				if ( !empty($data['id_insumo']) ){
					//saco array con herramientas y el id de empresa
					$ins = $data["id_insumo"]; 
					$j = 0;
					foreach ($ins as $in) {
						$insumo[$j]['artId'] = $in;
						$insumo[$j]['id_empresa'] = $empId;
						$j++;                                
					} 
					//saco array con cant de herramientas y el id de preventivo 
					$cantInsum = $data["cant_insumo"];
					$z = 0;
					foreach ($cantInsum as $ci) {
						$insumo[$z]['cantidad'] = $ci;
						$insumo[$z]['backId'] = $ultimoId;
						$z++;                                
					}
					// Guarda el bacht de datos de herramientas
					$response['respInsumo'] = $this->Backlogs->insertBackInsum($insumo);
				}else{

					$response['respInsumo'] = true;	// no habia insumos
				}	

			////////// Subir imagen o pdf 
				$nomcodif = $this->codifNombre($ultimoId,$empId); // codificacion de nomb  		
				$config = [
					"upload_path" => "./assets/filesbacklog",
					'allowed_types' => "png|jpg|pdf|xlsx",
					'file_name'=> $nomcodif
				];

				$this->load->library("upload",$config);
				
				if ($this->upload->do_upload('inputPDF')) {					
					$data = array("upload_data" => $this->upload->data());
					$extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
					$nomcodif = $nomcodif.$extens;
					$adjunto = array('back_adjunto' => $nomcodif);
					$response['respNomImagen'] = $this->Backlogs->updateAdjunto($adjunto,$ultimoId);
				}else{
					$response['respImagen'] = false;
				}							
		}		
		
		// si todas las inserciones se hicieron devuelve true
		if ($response['respBacklog'] && $response['respHerram'] && $response['respInsumo']) {
			$result = true;
		} else {
			$result = false;
		}
		echo json_encode($result);
	}

	// Agrega adjunto desde modal edicion
	public function agregarAdjunto(){
		$userdata     = $this->session->userdata('user_data');
		$empId        = $userdata[0]['id_empresa'];

		$idPredictivo = $this->input->post('idAgregaAdjunto');

		log_message('DEBUG', '#BACKLOG >> agregarAdjunto >> userdata ' . json_encode($userdata). ' empId:'. json_encode($empId));


		$nomcodif = $this->codifNombre($idPredictivo, $empId); // codificacion de nomb  		
		$config   = [
			"upload_path"   => "./assets/filesbacklog",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		];

		log_message('DEBUG', '#BACKLOG >> agregarAdjunto >> nomcodif ' . json_encode($nomcodif));
		log_message('DEBUG', '#BACKLOG >> agregarAdjunto >> config ' . json_encode($config));

		$this->load->library("upload",$config);
		if ($this->upload->do_upload('inputPDF'))
		{
			$data     = array("upload_data" => $this->upload->data());
			$extens   = $data['upload_data']['file_ext'];//guardo extension de archivo
			$nomcodif = $nomcodif.$extens;
			$adjunto  = array('back_adjunto' => $nomcodif);
			$response = $this->Backlogs->updateAdjunto($adjunto, $idPredictivo);
		}
		else
		{
			$response = false;
		}

		log_message('DEBUG', '#BACKLOG >> agregarAdjunto >> response ' . json_encode($response));

		echo json_encode($response);
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

	public function getComponente()
	{
		$idEquipo = $this->input->post('idEquipo');
		$componentes = $this->Backlogs->getComponentes($idEquipo);
		if($componentes)
		{	
			$arre = array();$i=0;
	        foreach ($componentes as $valor ){   
						$valorS = (array)$valor;
						$arre[$i]['value']   = $valorS['codigo'];
						$arre[$i]['label']   = $valorS['codigo'];
						$arre[$i]['descrip'] = $valorS['descripcion'];
						$arre[$i]['sistema'] = $valorS['sistema'];
						$arre[$i]['idce']    = $valorS['idce'];
						$i++;
	        }
			echo json_encode($arre);
		}
		else echo json_encode(0);
	}
}