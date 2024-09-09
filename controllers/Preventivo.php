<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Preventivo extends CI_Controller {

	function __construct()
        {
		parent::__construct();
		$this->load->model('Preventivos');
		
	}
	/**
    * Carga vista principal de Preventivos
    * @param 
    * @return view listado Preventivos
    */
	public function index($permission = "Add-Edit-Del-"){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Preventivo | index()");
		$data['list'] = $this->Preventivos->preventivos_List();
		$data['permission'] = $permission;
		$this->load->view('preventivo/list', $data);
	}	
	/**
	* Trae equipos por empresa logueada
	* @param 
	* @return array lista de equipos
	*/
	public function getequipo(){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getequipo()");
		$equipo = $this->Preventivos->getequipo();

		if($equipo){	
			$arre=array();
	        foreach ($equipo as $row ){   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae unidades de tiempo
	* @param 
	* @return array lista de unidades de tiempo
	*/
	public function getUnidTiempo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getUnidTiempo()");
		$tarea = $this->Preventivos->getUnidTiempos();

		if($tarea){	
			$arre=array();
	        foreach ($tarea as $row ){   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae datos de equipo por ID para nuevo preventivo 
	* @param array $id_equipo
	* @return array lista de equipos para ID seleccionado
	*/
	public function getEquipoNuevoPrevent(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getEquipoNuevoPrevent()");
		$res = $this->Preventivos->getEquipoNuevoPrevent($this->input->post());

		echo json_encode($res);
	}
	/**
	* Trae tareas por empresa logueada 
	* @param 
	* @return array lista de tareas
	*/
	public function gettarea(){	
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | gettarea()");
		$tareas = $this->Preventivos->gettarea();
		echo json_encode($tareas);
	}
	
	//Trae insumo por id 
	public function traerinsumo(){

		$ins = $this->Preventivos->traerinsumo($this->input->post());
		if($ins)
		{	
			$arre=array();
	        foreach ($ins as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Trae info de Preventivo a Editar -Listo
	public function geteditar(){
		
		$id = $this->input->post('idprev');
		
		//info de preventivo
		$info = $this->Preventivos->getInfoPreventivo($id); 
		
		if($info){	

			$arre['datos']=$info;			
			// trae herramientas 
			$herramientas = $this->Preventivos->getPreventivoHerramientas($id);
			if($herramientas){
				$arre['herramientas']=$herramientas;
			}
			else $arre['herramientas']=0;
			// trae insumos
			$insumos = $this->Preventivos->getPreventivoInsumos($id);
			if($insumos){
					$arre['insumos']=$insumos;
			}
			else $arre['insumos']=0;

			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae componentes segun id de equipo
	* @param 
	* @return array lista de componentes
	*/
	public function getcomponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getcomponente()");
		$idEquipo   = $_POST['id_equipo']; 
		$componente = $this->Preventivos->getcomponente($idEquipo);
		echo json_encode($componente);
	}
	/**
	* Trae herramientas segun empresa logueada
	* @param 
	* @return array lista de herramientas
	*/
	public function getherramienta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getherramienta()");
		$herramienta = $this->Preventivos->getherramienta();		
		if($herramienta){	
			$arre=array();
	        foreach ($herramienta as $row ){   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae herramientas por empresa logueada 
	* @param 
	* @return array lista de herramientas
	*/
	public function getHerramientasB(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getHerramientasB()");
        $herramientas = $this->Preventivos->getHerramientasB();     
        echo json_encode($herramientas);
    }
	/**
	* Trae insumos por empresa logueada 
	* @param 
	* @return array lista de insumos
	*/
	public function getinsumo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | getinsumo()");
		$insumo = $this->Preventivos->getinsumo();		
		if($insumo){	
			$arre=array();
	        foreach ($insumo as $row ){   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Guarda preventivo segun empresa logueada
	public function guardar_preventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | guardar_preventivo()");
		$empId    = empresa();

		$eq         =$this->input->post('id_equipo');
		$ta         =$this->input->post('tarea');
		$com        =$this->input->post('id_componente');
		$ultimo     =$this->input->post('ultimo');
		$pe         =$this->input->post('periodo');
		$can        =$this->input->post('cantidad');
		$oper       =$this->input->post('cantOper');
		$com        =$this->input->post('id_componente');
		$durac      =$this->input->post('duracion');
		$unidad     =$this->input->post("unidad");
		$canhm      =$this->input->post('hshombre');
		$critico1   =$this->input->post('alerta');
		$lectbase   =$this->input->post('lectura_base');	
		$herr		=$this->input->post('id_her');
		$insumos	=$this->input->post('id_insumo');
		$cantInsum	=$this->input->post('cant_insumo');
		$datos = array(
			'id_equipo'     => $eq,
			'id_tarea'      => $ta,
			'perido'        => $pe,
			'cantidad'      => $can,
			'ultimo'        => $ultimo,
			'id_componente' => $com,
			'critico1'      => $critico1,
			'horash'        => $canhm,
			'estadoprev'    => 'C',
			'prev_duracion' => $durac,
			'id_unidad'     => $unidad,
			'prev_canth'    => $oper,
			'id_empresa'    => $empId,
			'lectura_base'  => $lectbase
		);
		$response = $this->Preventivos->insert_preventivo($datos);
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Preventivo | insert_preventivo>> ' . json_encode($response));
		if($response['status']){
			$ultimoId = $response['id']; // id de preventivo insertado
			////////// para guardar herramientas                 
			if ( !empty($herr) ){
				//saco array con herramientas y el id de empresa
				// $herr = $herr;
				$i = 0;
				foreach ($herr as $h) {
					$herramPrev[$i]['herrId']= $h;
					$herramPrev[$i]['id_empresa']= $empId;
					$i++;                                
				} 
				//saco array con cant de herramientas y el id de preventivo 
				$cantHerr = $data["cant_herr"];
				$z = 0;
				foreach ($cantHerr as $c) {
					$herramPrev[$z]['cantidad']= $c;
					$herramPrev[$z]['PrevId']= $ultimoId;
					$z++;                                
				}
				// Guarda el bacht de datos de herramientas
				$response['respHerram'] = $this->Preventivos->insertPrevHerram($herramPrev);
			}else{

				$response['respHerram'] = "vacio";	// no habia herramientas
			}
	  		////////// para guardar insumos
	  		if ( !empty($insumos) ){
		  		//saco array con herramientas y el id de empresa
		  		// $ins = $data["id_insumo"];
		  		$j = 0;
		  		foreach ($insumos as $in) {
		  			$insumoPrev[$j]['artId'] = $in;
		  			$insumoPrev[$j]['id_empresa'] = $empId;
		  			$j++;                                
		  		} 
		  		//saco array con cant de herramientas y el id de preventivo 
		  		// $cantInsum = $data["cant_insumo"];
		  		$z = 0;
		  		foreach ($cantInsum as $ci) {
		  			$insumoPrev[$z]['cantidad'] = $ci;
		  			$insumoPrev[$z]['PrevId'] = $ultimoId;
		  			$z++;                                
		  		}
		  		// Guarda el bacht de datos de herramientas
		  		$response['respInsumo'] = $this->Preventivos->insertPrevInsum($insumoPrev);
	  		}else{
	  			$response['respInsumo'] = "vacio";	// no habia insumos
			}

			////////// Subir imagen o pdf 
			$nomcodif = $this->codifNombre($ultimoId,$empId); // codificacion de nomb  	
			$nomcodif = 'preventivo'.$nomcodif;	
			//'allowed_types' => "png|jpg|pdf|xlsx", Inicialmente guarda asi -> 26102023
			$config = [
				"upload_path" => "./assets/filespreventivos",
				'allowed_types' => "png|jpg|pdf",					
				'file_name'=> $nomcodif
			];

			$this->load->library("upload",$config);
			if ($this->upload->do_upload('inputPDF')) {
				
				$data = array("upload_data" => $this->upload->data());
				$extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
				$nomcodif = $nomcodif.$extens;
				$adjunto = array('prev_adjunto' => $nomcodif);
				$response['respNomImagen'] = $this->Preventivos->updateAdjunto($adjunto,$ultimoId);
			}else{
				$response['respImagen'] = false;
			}
		}
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Preventivo | guardar_preventivo('. json_encode($response).')');
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

	// Guarda edicion de Preventivo
	public function editar_preventivo(){
		
		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa'];
		
		$id_preventivo = $this->input->post('id_prevent');		
		$eq = $this->input->post('id_equipo');///
		$ta = $this->input->post('id_tarea');/// 
		$pe = $this->input->post('perido');;///	
		$can = $this->input->post('cantidad');///		
		$ultm = $this->input->post('ultimo');///
		$com = $this->input->post('id_componente');///
		$hshom = $this->input->post('horash');	///
		$durac = $this->input->post('prev_duracion');///	
		$oper = $this->input->post('cantOper');///
		$unidad = $this->input->post("unidad");///

		$datos = array('id_equipo'=>$eq,
						'id_tarea'=>$ta,
						'perido'=>$pe,
						'cantidad'=>$can,
						'ultimo' => $ultm,
						'id_componente'=>$com,
						'horash'=>$hshom,
						'estadoprev'=>'C',
						'prev_duracion'=> $durac,
						'id_unidad'=> $unidad,
						'prev_canth'=> $oper,
						'id_empresa'=> $empId
						);
		//echo "datos en controller: ";
			//dump_exit($datos);				
		$response['resPrenvent'] = $this->Preventivos->update_editar($datos,$id_preventivo);
		
		if ($response) {
			// caso 1: hay herram/insumos cargadas y agrego mas
			// caso 2: hay herram/insumos cargadas borra alguna
			// caso 3: hay herram/insumos cargadas borra todas
		
		/// HERRAMIENTAS	
			//saco array con herramientas y el id de empresa
			$herr = $this->input->post('idsherramienta');
			//saco array con cant de herramientas y el id de preventivo 
		  	$cantHerr = $this->input->post('cantHerram');		  	
		  	
			if ( !empty($herr) ) {				
				
		  		$respdelHerr = $this->Preventivos->deleteHerramPrev($id_preventivo);// borr herram ant	  		
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
			  			$herramPrev[$z]['PrevId']= $id_preventivo;
			  			$z++;                                
			  		}
			  	// Guarda el bacht de datos de herramientas
		  		$response['respHerram'] = $this->Preventivos->insertPrevHerram($herramPrev);
		  		}
			}else{
				// se borran la herram
				$respdelHerr = $this->Preventivos->deleteHerramPrev($id_preventivo);
	  			$response['respHerram'] = $respdelHerr;	// no habia herramientas
	  	}

		/// INSUMOS	
			//saco array con herramientas y el id de empresa
			$ins = $this->input->post('idsinsumo');
			//saco array con cant de herramientas y el id de preventivo			
			$cantInsum = $this->input->post('cantInsum');
	  		if ( !empty($ins) ){
	  			// se borran la insum anteriores
	  			$respdelInsum = $this->Preventivos->deleteInsumPrev($id_preventivo); 
		  		$j = 0;
		  		foreach ($ins as $in) {
		  			$insumoPrev[$j]['artId'] = $in;
		  			$insumoPrev[$j]['id_empresa'] = $empId;
		  			$j++;                                
		  		}
		  		$z = 0;
		  		foreach ($cantInsum as $ci) {
		  			$insumoPrev[$z]['cantidad'] = $ci;
		  			$insumoPrev[$z]['PrevId'] = $id_preventivo;
		  			$z++;                                
		  		}
		  		// Guarda el bacht de datos de herramientas
		  		$response['respInsumo'] = $this->Preventivos->insertPrevInsum($insumoPrev);
	  		}else{
	  			$respdelInsum = $this->Preventivos->deleteInsumPrev($id_preventivo); 
	  			$response['respInsumo'] = $respdelInsum ;	// no habia insumos	  			
	  		}
		}
		echo json_encode($response);
		//echo json_encode(true);
	}
	/**
    * Da de baja Preventivo
    * @param integer $idprev id de preventivo
    * @return bool true or false segun resultado de la operacion
    */
	public function baja_preventivo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivo | baja_preventivo()");
		$idprev = $this->input->post('idprev');		
		$datos = array('estadoprev'=>"AN");		
		$response = $this->Preventivos->update_preventivo($datos, $idprev);		
		echo json_encode($response);
	}
	/**
    * Carga vista para creacion de Preventivos
    * @param 
    * @return view agregar Preventivos
    */
	public function cargarpreventivo($permission){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Preventivo | cargarpreventivo()");
        $data['permission'] = $permission;    // envia permisos       
        $this->load->view('preventivo/view_',$data);
    }

	public function volver($permission){ 
		$data['list'] = $this->Otrabajos->otrabajos_List();
			$data['permission'] = $permission;    // envia permisos       
			$this->load->view('otrabajos/list',$data);
	}

	public function getProducto (){
    	$response = $this->Preventivos->getProductos($this->input->post());
    	echo json_encode($response);
    }

	public function agregar_componente()
	{

	    if($_POST)
	    {
	    	$datos=$_POST['datos'];

	     	$result = $this->Preventivos->agregar_componente($datos);
	      	//print_r($this->db->insert_id());
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}

  	public function agregar_insumo()
	{

	    if($_POST)
	    {
	    	$datos=$_POST['datos'];

	     	$result = $this->Equipos->agregar_insumo($datos);
	      	//print_r($this->db->insert_id());
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}

  	
		


	
	public function guardar_agregar(){
			
			$datos=$_POST['data'];
			
			$result = $this->Preventivos->insert_herramienta($datos);

			//log_message('DEBUG', '#PREVENTIVO >> guardar_preventivo()  $result>> ' . json_encode($result));

			echo json_encode($result);
				
		}
	public function getperiodo(){
		$this->load->model('Preventivos');
		$periodo = $this->Preventivos->getperiodo();
		//echo json_encode($Customers);

		if($periodo)
		{	
			$arre=array();
	        foreach ($periodo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	

	public function getdatos(){
		$this->load->model('Preventivos');
		$datos = $this->Preventivos->getdatos($this->input->post());
		if($datos)
		{	
			$arre=array();
	        foreach ($datos as $row ) 
	        {   
	           $arre[]=$row;
	        }
			//echo
			print_r(json_encode($arre));
		}
		else echo "nada";
	}


	//GUARDAR PEDIDO
	public function guardarorden()
	{
		
		$datos=$_POST['datos'];

		$result = $this->Otrabajos->insert_herramienta($datos);
		
		$id=$this->db->insert_id();
		
		$result2 = $this->Otrabajos->get_pedido($id);

		echo json_encode($result2);

	}

	

	

	public function getpreventivo(){

		$idpe=$_POST['idp'];
		$ideq=$_POST['datos'];
		$datos=	$this->Preventivos->getpreventivos($idpe,$ideq);

		if($datos){	
				$arre=array();
		        foreach ($datos as $row ) 
		        {   
		           $arre[]=$row;
		        }
				echo json_encode($arre);
			}
		else echo "nada";

	}

	

	public function preventivoinertot(){

		$eq=$_POST['equipo'];
		$tar=$_POST['tarea'];
		$fe=$_POST['fecha'];
		$idp=$_POST['idp'];
		$userdata = $this->session->userdata('user_data');
		$usrId = $userdata[0]['usrId'];

		$insert = array(
					'id_tarea' => $tar,
				   'nro' => $idp,
				   'fecha_inicio' => $fe,
				   'descripcion' => 'Preventivo',			   
				   'estado' => 'C',
				   'id_usuario' => $usrId,
				   'id_usuario_a' => $usrId,
				   'id_sucursal' => 1,
				   'id_equipo' => $eq,
				   'tipo'=> 5
				   );

		$result = $this->Preventivos->insert_preventivoorden($insert);			
		print_r($result);

		
	}


	/**
	 * Preventivo:eliminarAdjunto();
	 *
	 * @return Bool 	True si se eliminó el archivo o false si hubo error
	 */
	public function eliminarAdjunto()
	{
	    $idPreventivo = $this->input->post('idprev');
	    $response     = $this->Preventivos->eliminarAdjunto($idPreventivo);
		echo json_encode($response);
	}
	
	/**
	 * Preventivo:agregarAdjunto();
	 *
	 * @param 
	 * @return String nomre de archivo adjunto
	 */
	public function agregarAdjunto()
	{
		$userdata     = $this->session->userdata('user_data');
		$empId        = $userdata[0]['id_empresa'];

		$idPreventivo = $this->input->post('idAgregaAdjunto');

		$nomcodif = $this->codifNombre($idPreventivo, $empId); // codificacion de nomb  		
		$config   = [
			"upload_path"   => "./assets/filespreventivos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		];

		$this->load->library("upload",$config);
		if ($this->upload->do_upload('inputPDF'))
		{
			$data     = array("upload_data" => $this->upload->data());
			$extens   = $data['upload_data']['file_ext'];//guardo extension de archivo
			$nomcodif = $nomcodif.$extens;
			$adjunto  = array('prev_adjunto' => $nomcodif);
			$response = $this->Preventivos->updateAdjunto($adjunto, $idPreventivo);
		}
		else
		{
			$response = false;
		}

		echo json_encode($response);
	}
	
}
