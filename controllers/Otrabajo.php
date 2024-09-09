<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Otrabajo extends CI_Controller {

	function __construct(){
		parent::__construct();
		
		$this->load->model('Otrabajos');
		$this->load->model('Equipos');
		$this->load->library('upload');
	}
	/**
	 * Muestra pantalla de listado de Ordenes de Trabajo.
	 *
	 * @param 	String 	$permission 	Permisos de ejecuci�n.
	 */
	public function index($permission) // Ok
	{
		$data = $this->session->userdata();
		log_message('DEBUG','#Main/index | OTrabajo >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
	
		if(empty($data['user_data'][0]['usrName'])){
			log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
			$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
			$this->session->set_userdata($var);
			$this->session->unset_userdata(null);
			$this->session->sess_destroy();
	
			echo ("<script>location.href='login'</script>");
	
		}else{
			$data['list']       = $this->Otrabajos->otrabajos_List(null,0);
			$data['kpi'] = $this->Equipos->informe_equipos();
			$data['permission'] = $permission;
			$this->load->view('otrabajos/dashOriginal', $data);
		}
	}  
	/**
	 * Muestra pantalla de Nueva Orden de Trabajo.
	 * @param 	String 	$permission 	Permisos de ejecuci�n.
     * @return view pantalla creacion nueva OT
	 */
	public function nuevaOT($permission = "Add-Edit-Del-"){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | nuevaOT()");
		$data['permission'] = $permission;
		$this->load->view('otrabajos/view_agregarOT', $data);
	}
    /**
     * Muestra pantalla de listado de Ordenes de Trabajo de distintos origenes.
     * @param 	String 	$permission 	Permisos de ejecuci�n.
     * @return view listado de OT
    */
	public function listOrden($permission = "Add-Edit-Del-",$ot=null){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | listOrden()");
        $data['list'] = false;
        $data['equipos'] = $this->Otrabajos->getEquiposNuevaOT();
        $data['permission'] = $permission;
        $rsp = $this->bpm->getUsuariosBPM();
        $data['list_usuarios'] = $rsp['data'];
        $data['opciones'] = $this->load->view('otrabajos/tabla_opciones',['permission'=>$permission],true);

        $this->load->view('otrabajos/list', $data);
	}  
	/**
	 * Muestra pantalla de listado de Ordenes de Trabajo generada poritem de menu.
	 *
	 * @param 	String 	$permission 	Permisos de ejecuci�n.
	 */
	public function listOTestandar($permission,$ot=null){
		$this->load->library('BPM',null);
		$data['list']    = $this->Otrabajos->otrabajos_List($ot, 1);
		//dump($data['list'], 'listado');
		$data['permission'] = $permission;
		$data['list_usuarios'] = $this->bpm->ObtenerUsuarios();	
		//log_message('DEBUG', 'listado de usr en BPM: '.json_encode($data['list_usuarios']));

		$data['opciones'] = $this->load->view('otrabajos/tabla_opciones',['permission'=>$permission],true);
		
		$this->load->view('otrabajos/list', $data);
	}
  /**
   * Traer proveedores de empresa con estado AC.
   *
   */
  public function getproveedor() // Ok
  {	
    $proveedores = $this->Otrabajos->getproveedor();
    if($proveedores)
    {	
      $arre=array();
          foreach ($proveedores as $row ) 
          {   
              $arre[] = $row;
          }
      echo json_encode($arre);
    }
    else echo "nada";
  }  
  /**
  	 * Traer Sucursales de empresa con estado AC.
  	 *
  	 * @return 	String 	Arreglo con sucursales.
  	 */
	public function traer_sucursal() // Ok
	{
		$sucursales = $this->Otrabajos->traer_sucursal();
		if($sucursales)
		{	
			$arre = array();
	        foreach ($sucursales as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
  	 * Traer Equipos de empresa con estado AC.
  	 *
  	 * @return 	String 	Arreglo con equipos.
  	 */
	public function getequipo() // Ok
	{
		$equipos = $this->Otrabajos->getequipo();
		if($equipos)
		{
			$arre = array();
	        foreach ($equipos as $row )
	        {
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
  }
  
  
     /**
	 * Agrega nueva OTs.
	 * @param Array	$data 	datos cargados para la nueva OT.
	 * @return Array resultado de la operacion.
	 */
	public function guardar_agregar() {
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | guardar_agregar()");
        $cic =& get_instance();
        $userdata = $cic->session->userdata();
		$usrId = $userdata['id'];
		$empresaId = empresa();
	
		$data = $this->input->post();
		
		$id_tareaestd	 = $this->input->post('id_tarea');	
		if(!empty($id_tareaestd)){
			$id_tar	 = $id_tareaestd;
			$descripcion	=	$this->Otrabajos->getDescTareaSTD($id_tar);		
		}else{
			$id_tar = 0;
			$descripcion   = $this->input->post('tareacustom');
		}		
		$f_inicio	 = $this->input->post('fechaInicio');
		$f_entrega = $this->input->post('fechaEntrega');		
		$f_inicio      = explode('/', $f_inicio);
		$fecha_inicio = 	$f_inicio[2].'-'.$f_inicio[1].'-'.$f_inicio[0];		
		$f_entrega      = explode('/', $f_entrega);
		$fecha_entrega = 	$f_entrega[2].'-'.$f_entrega[1].'-'.$f_entrega[0];
		$equipo        = $this->input->post('equipo');
		$sucursal      = $this->input->post('suci');
		$proveedor     = $this->input->post('prov');

        $datos2 = array(
			'id_tarea'			=> $id_tar,
			'fecha_program' => $fecha_inicio,
			//'fecha_inicio'  => $fecha_inicio,
			'fecha_entrega' => $fecha_entrega,
			'descripcion'   => $descripcion,
			'estado'        => 'PL',
			'id_usuario'    => $usrId,
			//'id_usuario_a'  => 1,
			'id_sucursal'   => $sucursal,
			'id_proveedor'  => $proveedor,
			'id_equipo'     => $equipo,
			'tipo'          => 1,
			'id_empresa'    => $empresaId
		);

		// guarda OT y devuelve el id de insercion
		$ultimoId = $this->Otrabajos->guardar_agregar($datos2);	
		
		$contract = array(
            "idSolicitudServicio"	=> 0,
            "idOT"  => 	$ultimoId
		);

		$result = $this->bpm->lanzarProceso(BPM_PROCESS_ID_MANTENIMIENTO, $contract);
		//elimina OT si no se pudo lanzar proceso y devuelve mensaje de error
		if(!$result['status']){
			$this->Otrabajos->eliminar($ultimoId);
			echo json_encode($result);
		}
		// guarda case id generado el lanzar proceso				
		$respcaseOT = $this->Otrabajos->setCaseidenOTNueva($result['data']['caseId'], $ultimoId);

		if($ultimoId){
			////////// para guardar herramientas                 
            if ( !empty($data['id_her']) ){
                //saco array con herramientas y el id de empresa
                $herr = $data["id_her"]; 
                $i = 0;
                foreach ($herr as $h) {
                    $herram[$i]['herrId']= $h;
                    $herram[$i]['id_empresa']= $empresaId;
                    $i++;                                
                } 
                //saco array con cant de herramientas y el id de preventivo 
                $cantHerr = $data["cant_herr"];
                $z = 0;
                foreach ($cantHerr as $c) {
                    $herram[$z]['cantidad']= $c;
                    $herram[$z]['otId']= $ultimoId;
                    $z++;                                
                }				
                // Guarda el bacht de datos de herramientas
                $result['respHerram'] = $this->Otrabajos->insertOTHerram($herram);
            }else{
                $result['respHerram'] = "vacio";	// no habia herramientas
            }	

			////////// para guardar insumos
            if ( !empty($data['id_insumo']) ){
                //saco array con herramientas y el id de empresa
                $ins = $data["id_insumo"]; 
                $j = 0;
                foreach ($ins as $in) {
                    $insumo[$j]['artId'] = $in;
                    $insumo[$j]['id_empresa'] = $empresaId;
                    $j++;                                
                } 
                //saco array con cant de herramientas y el id de preventivo 
                $cantInsum = $data["cant_insumo"];
                $z = 0;
                foreach ($cantInsum as $ci) {
                    $insumo[$z]['cantidad'] = $ci;
                    $insumo[$z]['otId'] = $ultimoId;
                    $z++;                                
                }
                // Guarda el bacht de datos de herramientas
                $result['respInsumo'] = $this->Otrabajos->insertOTInsum($insumo);
            }else{
                $result['respInsumo'] = "vacio";	// no habia insumos
            }	
			
			////////// Subir imagen o pdf 
            $nomcodif = $this->codifNombre($ultimoId,$empresaId); // codificacion de nomb  		
            
            $upload_path = "./assets/filesOTrabajos";
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            $config = [
                "upload_path" => $upload_path,
                'allowed_types' => "png|jpg|pdf|xlsx",
                'file_name'=> $nomcodif
            ];
            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputPDF')) {
                $urlfile = "assets/filesOTrabajos/";
                $data = array("upload_data" => $this->upload->data());
                $extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
                $nomcodif = $urlfile.$nomcodif.$extens;
                $adjunto = array('ot_adjunto' => $nomcodif,'otId' => $ultimoId);
                $result['respNomImagen'] = $this->Otrabajos->setAdjunto($adjunto);
            }else{
                $result['respImagen'] = false;
            }			
		}		
		if($result['respOT']){
			$result = true;
			echo json_encode($result);
		}else{
			$result = false;
			echo json_encode($result);
		}
		
	}
    /**
	 * Codifica nombre de imagen para no repetir en servidor
     * formato "12_6_2018-05-21-15-26-24" idpreventivo_idempresa_fecha(a�o-mes-dia-hora-min-seg)
	 * @param Integer $ultimoId id generado en la insercion de la OT @param Integer	$empId id de empresa
	 * @return String string concatenada con la codificacion especificada en descripcion
	 */
	function codifNombre($ultimoId,$empId){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | codifNombre()");
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
	/**
	 * Actualiza responsable en tabla Orden_trabajo
	 *  al ser seleccionado
	 *  en modalejecutar ot 
	 */
	public function updateResponsable(){
		log_message('DEBUG', "#TRAZA | #ASSET | Sservicio | updateResponsable()");
		$id_responsable = $this->input->post('id_usuario_a');
		$id_orden = $this->input->post('idOt');
		$response = $this->Otrabajos->updateResponsables($id_orden, $id_responsable);
		echo json_encode($response);
	}
	/**
	 * Actualiza en tabla Orden_trabajo al ser 
	 * seleccionado tarea en modal ejecutar ot 
	 * 
	 */
	public function updateTarea(){
		
		$idTarea = $this->input->post('idTarea');
		$tarea = $this->input->post('tarea');
		$idOt = $this->input->post('idOt');
		$response = $this->Otrabajos->updateTarea($idOt, $idTarea, $tarea);
		echo json_encode($response);
	}


    /**
  	 * Trae datos para editar por ID de orden de trabajo.
  	 *  @param Integer $idp id de la orden de trabajo
     *  @return Array datos de la OT
    */
	public function getpencil(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getpencil(id OT: $id");
		$id = $this->input->post('idp');
		$result = $this->Otrabajos->getpencil($id);		
		//dump($result, 'info de OT: ');
		if($result){
			$arre['datos'] = $result;
			// trae herramientas 
			$herramientas = $this->Otrabajos->getOTHerramientas($id);
			if($herramientas){
				$arre['herramientas']=$herramientas;
			}
			else{ 
				$arre['herramientas']=0;
			}
			// trae insumos
			$insumos = $this->Otrabajos->getOTInsumos($id);
			if($insumos){
					$arre['insumos']=$insumos;
			}
			else{ $arre['insumos']=0;}

			// trae adjuntos
			$adjuntos = $this->Otrabajos->getOTadjuntos($id);
			if($adjuntos){
					$arre['adjunto']=$adjuntos;
			}
			else{ $arre['adjunto']=0;}

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

		$id = $this->input->post('idAgregaAdjunto');
		
		$nomcodif = $this->codifNombre($id, $empId); // codificacion de nomb  		
		$urlfile = "assets/filesOTrabajos/";
		
		$config   = [
			"upload_path"   => "./assets/filesOTrabajos",
			'allowed_types' => "png|jpg|pdf|xlsx",
			'file_name'     => $nomcodif
		];

		$this->load->library("upload",$config);
		if ($this->upload->do_upload('inputPDF'))
		{
			$data     = array("upload_data" => $this->upload->data());
			$extens   = $data['upload_data']['file_ext'];//guardo extension de archivo
			$nomcodif = $urlfile.$nomcodif.$extens;
			$adjunto  = array('ot_adjunto' => $nomcodif,
												'otId' => $id);
			$response = $this->Otrabajos->setAdjunto($adjunto);
			//$response = $this->Otrabajos->updateAdjunto($adjunto, $id);
			if($response){
				$idAdj = $this->db->insert_id();
				$result['ot_adjunto'] = $nomcodif;
				$result['id'] = $idAdj;
			}
		}
		else
		{
			$result = false;
		}

		echo json_encode($result);
	}
	public function eliminarAdjunto(){
		
		$id = $this->input->post('id_adjunto');
		$response = $this->Otrabajos->eliminarAdjunto($id);		
		echo json_encode($response);
  }  
  /**
  	 * Actualiza la OT.
  	 *
  	 */
	public function guardar_editar() // Ok
	{		
		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa'];

		$data     = $this->input->post();
		// dump($data, 'datos en controller: ');
		
		// dump_exit($data);
		$id = $this->input->post('idOT');	
		$datos    = $this->input->post('parametros');
		$result   = $this->Otrabajos->update_edita($id, $datos);

		/// HERRAMIENTAS	
			//saco array con herramientas y el id de empresa
			$herr = $this->input->post('idsherramienta');
			//saco array con cant de herramientas y el id de preventivo 
			$cantHerr = $this->input->post('cantHerram');		
			
			if ( !empty($herr) ) {				
				
				$respdelHerr = $this->Otrabajos->deleteHerramOT($id);// borr herram ant	  		
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
						$herramPrev[$z]['otId']= $id;
						$z++;                                
					}
				// Guarda el bacht de datos de herramientas
				$response['respHerram'] = $this->Otrabajos->insertOTHerram($herramPrev);
				}
			}else{
				// se borran la herram
				$respdelHerr = $this->Otrabajos->deleteHerramOT($id);
				$response['respHerram'] = $respdelHerr;	// no habia herramientas
			}


		/// INSUMOS	
			//saco array con herramientas y el id de empresa
			$ins = $this->input->post('idsinsumo');
			//saco array con cant de herramientas y el id de preventivo			
			$cantInsum = $this->input->post('cantInsum');
			if ( !empty($ins) ){
				// se borran la insum anteriores
				$respdelInsum = $this->Otrabajos->deleteInsumOT($id); 
				$j = 0;
				foreach ($ins as $in) {
					$insumoPrev[$j]['artId'] = $in;
					$insumoPrev[$j]['id_empresa'] = $empId;
					$j++;                                
				}
				$z = 0;
				foreach ($cantInsum as $ci) {
					$insumoPrev[$z]['cantidad'] = $ci;
					$insumoPrev[$z]['otId'] = $id;
					$z++;                                
				}
				// Guarda el bacht de datos de herramientas
				$response['respInsumo'] = $this->Otrabajos->insertOTInsum($insumoPrev);
			}else{
				$respdelInsum = $this->Otrabajos->deleteInsumOT($id); 
				$response['respInsumo'] = $respdelInsum ;	// no habia insumos	  			
			}	
		//print_r($result);
	}
	/**
	 * Muestra la vista de Asignar Tarea
	 *
	 * @param 	String 	$permission 	Permisos de ejecuci�n.
	 * @param 	Int 	$idglob 		Id de orden de trabajo.
	 */
	public function cargartarea($permission, $idglob) // Ok
	{ 
		$data['list']       = $this->Otrabajos->cargartareas($idglob);
		$data['permission'] = $permission;
		$data['id_orden']   = $idglob; 
        $this->load->view('otrabajos/asignacion',$data);
 }

		/**
			* Trae datos a mostrar en el Modal Asignar OT.
			*
			* @return 	String 	Arreglo con datos a mostrar en Modal Asignar OT.
			*/
		public function getasigna() // Ok
		{
			$id     = $_GET['id_orden'];
			$result = $this->Otrabajos->getasigna($id);
			if($result)
			{
				$arre['datos'] = $result;
				echo json_encode($arre);
			}
			else echo "nada";
		}

	/**
	 * Trae usuarios por id de empresa logueada.
	 *
	 * @return 	String 	Arreglo con usuarios.
	 */
	public function getusuario() // Ok
	{	
		$usuarios = $this->Otrabajos->getusuario();
		if($usuarios)
		{
			$arre = array();
	        foreach ($usuarios as $row )
	        {
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	// Trae equipos por empresa logueada - Listo
	public function getEquiposNuevaOT(){
		
		$equipo = $this->Otrabajos->getEquiposNuevaOT();
		if($equipo){	
			$arre=array();
	        foreach ($equipo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
    /**
    * Trae info de equipos por ID y por empresa logueada
    * @param Integer $id_equipo id del equipo
    * @return Array data del equipo
    */
	public function getInfoEquipoNuevaOT(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getInfoEquipoNuevaOT()");
		$id = $this->input->post('id_equipo');
		$equipo = $this->Otrabajos->getInfoEquiposNuevaOT($id);

		if($equipo){	
			$arre=array();
            foreach ($equipo as $row ) {   
                $arre[]=$row;
            }
			echo json_encode($arre);
		}
		else echo "nada";
	}	
	
	/**
	 *
	 *
	 */
	public function traer_cli()
	{
		$usuario = $this->Otrabajos->traer_cli();
		if($usuario)
		{	
			$arre = array();
	        foreach ($usuario as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}


	// Cargar orden de servicios(Informe de servicios)
	public function cargarOrden($permission, $id_sol = null, $id_eq = null, $causa = null){
			$data['permission'] = $permission;    // envia permisos
			$data['id_solicitud'] = $id_sol;      // id de O.T.
			$data['id_eq'] = $id_eq;              // id de equipo
			$data['causa'] = $causa;              // motivo de la O.T.

			$this->load->view('ordenservicios/view_',$data);
	}




	
	public function getotrabajo(){
		$data['data'] = $this->Otrabajos->getotrabajos($this->input->post());
		$response['html'] = $this->load->view('otrabajos/view_', $data, true);

		echo json_encode($response);
	}
	
	public function setotrabajo(){

		$data = $this->Otrabajos->setotrabajos($this->input->post());
		if($data  == false)
		{
			echo json_encode(false);
		}
		else
		{
			echo json_encode(true);	
		}
	}

	public function getprint(){

		$id=$_POST['ido'];
		//print_r($id);
		$result = $this->Otrabajos->getprint($id);
		 print_r(json_encode($result));

	}

	
	//pedidos
	public function getorden(){

		$id=$_POST['id_orden'];
		$result = $this->Otrabajos->getorden($id);
		if($result)
		{	
			$arre['datos']=$result;

			echo json_encode($arre);
		}
		else echo "nada";
	}



	//pedidos a entregar x fecha
	public function getpedidos(){

		$id=$_GET['fechai'];
		$result = $this->Otrabajos->getpedidos($id);
		if($result)
		{	
			$arre['datos']=$result;

			echo json_encode($arre);
		}
		else echo "nada";
	}
	// boton agregar

	public function agregar(){//ajax

	    if($_POST){
	      $agregar = $this->Otrabajos->agregar($_POST);
	      echo ($agregar===true)?"bien":"mal";
	    }
    }
    
 public function guardar(){

		$case_id = $this->input->post('case_id');
		$task_id = $this->input->post('task_id');
		$user_id = $this->input->post('usuario');

		//CERRAR TAREA BPM
		$result = $this->bpm->cerrarTarea($task_id);

		if(!$result['status']) {
			echo $result['msj'];return;
		}

		//OBTENER TASK_ID EJECTURAR OT
		$task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Ejecutar OT');

		if($task_id == 0) {echo 'No existe Tarea Ejecutar OT'; return;}

		//ASIGNO USUARIO A TAREA EJECTURA OT
		$result = $this->bpm->setUsuario($task_id, $user_id);

		if(!$result['status']) {echo $result['msj'];return;}
		
		//GUARDAR DATOS DE OT EN BD MYSQL	
		$id = $_POST['id_orden'];
		$fee = $_POST['fecha_entrega'];
	
		$uno=substr($fee, 0, 2); 
        $dos=substr($fee, 3, 2); 
        $tres=substr($fee, 6, 4); 
        $resul = ($tres."-".$dos."-".$uno); 
		$datos = array(	'fecha_entrega'=>$resul,
						'estado'=>'As',
						'id_usuario_a'=>$user_id
						);
		$result = $this->Otrabajos->update_guardar($id, $datos);		
		
		if($result >0)
		{   echo 1;
		}
		else echo "error al insertar";
	}

	public function getcliente(){
		
		$cliente = $this->Otrabajos->getcliente($this->input->post());
		if($cliente)
		{	
			$arre=array();
	        foreach ($cliente as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	//nuevo

	//traer grupo
	public function getgrupo(){
				
		$grupo = $this->Otrabajos->getgrupo();
		
		if($grupo)
		{	
			$arre=array();
	        foreach ($grupo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	

	public function getnum(){
	
		$id=$_POST['id_orden'];
		
		$grupo = $this->Otrabajos->getnums();
		
		echo json_encode($grupo);
	}

	//GUARDAR PEDIDO
	public function guardarorden(){
		
		$datos=$_POST['datos'];
		$result = $this->Otrabajos->insert_pedido($datos);	
		$id=$this->db->insert_id();
		$result2 = $this->Otrabajos->get_pedido($id);

		echo json_encode($result2);

	}



	public function agregar_usuario(){

	    if($_POST) {
	    	$datos=$_POST['datos'];

	     	$result = $this->Otrabajos->agregar_usuario($datos);
	      	//print_r($this->db->insert_id());
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}



	//argegar un proveedor
	public function agregar_proveedor()
	{
			if($_POST)
			{
				$datos  = $_POST['datos'];
					$result = $this->Otrabajos->agregar_proveedor($datos);
						if($result)
							echo $this->db->insert_id();
						else echo 0;
			}
	}

	public function agregar_pedido(){

			$datos=$_POST['data'];
			$idot=$_POST['ido'];

			$result = $this->Otrabajos->agregar_pedidos($datos);
						//print_r($this->db->insert_id());
			if($result){
					
				
				$id= $this->db->insert_id();
				$fec= date("Y-m-d H:i:s");
				
				$fecha = array(
											'fecha'=>$fec

										);
				$result1 = $this->Otrabajos->agregar_pedidos_fecha($fecha,$id);

				$arre=array();
				$datos2 = array(
											'id_orden'=>$idot, 
											'estado'=>'P'			        	
											
										);

				$result2 = $this->Otrabajos->update_ordtrab($idot, $datos2);
			}
		return $result2; 		
		
	}
  	
	public function agregar_tarea(){

			$datos=$_POST['parametros'];
			$result = $this->Otrabajos->agregar_tareas($datos);
						//print_r($this->db->insert_id());
		
			if($result)
							echo $this->db->insert_id();
			else echo 0;	

		}
    
	// trae detalle de nota de pedido
	public function getmostrar(){

			$idm=$_POST['id'];
			$dat= $this->Otrabajos->getdatos($idm); //traigo todos los datos
			echo json_encode($dat);
	}

	public function baja_orden(){

			$idO=$_POST['idord'];
			$result = $this->Otrabajos->eliminacion($idO);
			print_r($result);
	}



	

	public function getArticulo (){
      $response = $this->Otrabajos->getArticulos($this->input->post());
      echo json_encode($response);
    }

 //mdificado

 public function EliminarTarea(){
	
		$idord=$_POST['idtarea'];	
		$datos = array('estado'=>'IN');
		$result = $this->Otrabajos->EliminarTareas($idord, $datos);
		print_r($result);
	
	}
		
	//modificada
	public function TareaRealizada(){
	
		$idord=$_GET['id_orden'];	
		$datos = array('estado'=>'RE');
		//$datos = array('estado'=>8);
		$result = $this->Otrabajos->TareaRealizadas($idord, $datos);
		print_r($result);
	
	}

	public function ModificarUsuario(){
	
		$idta=$_POST['idtarea'];
		$idu=$_POST['idusu'];
		$datos = array('id_usuario'=>$idu);
		$result = $this->Otrabajos->ModificarUsuarios($idta, $datos);
		print_r($result);
	
	}

	public function ModificarFecha(){
	
		$idta=$_POST['idtarea'];
		$idu=$_POST['idusu'];
		$fe=$_POST['fe'];
		
		$uno=substr($fe, 0, 2); 
        $dos=substr($fe, 3, 2); 
        $tres=substr($fe, 6, 4); 
        $resul = ($tres."/".$dos."/".$uno); 
		$datos = array('fecha'=>$resul);

		
		$result = $this->Otrabajos->ModificarFechas($idta, $datos);
		print_r($result);
	
	}

	public function CambioParcial(){
	
		$idor=$_POST['idfin'];
		$datos = array('estado'=>'TE');
		$result = $this->Otrabajos->CambioParcials($idor, $datos);
		print_r($result);	
	}

	public function FinalizaOt(){
	
		$idequipo=$_POST['idfin'];
		$fecha = date("Y-m-d");
		$result = $this->Otrabajos->update_cambio($idequipo, $fecha);
		print_r($result);	
	}
	
	public function visibBtnEjecutar(){
		
		$id = $this->input->post('ot');
		
		$origenOT = $this->Otrabajos->getDatosOrigenOT($id);
		$tipo = $origenOT[0]['tipo'];	
				
		if($tipo == 2){
			$case_Id = $this->Otrabajos->getCaseIdenSServicios($id);
		}else{
			$case_Id = $this->Otrabajos->getCaseIdOT($id);
		}				
		//dump($case_Id, ' case id en controller: ');
		if ($case_Id != NULL) {
			echo json_encode(true);
		} else {
			echo json_encode(false);
		}	
	}
  
 //Obtener TaskID por OtID (Cuando hay procesos generados, sino los genera)
 public function ObtenerTaskIDxOT(){
		
		$id = (int)$this->input->post('ot');	
		//dump($id, 'id de OT:');
		$case_id = $this->Otrabajos->getCaseIdOT($id);	
		//dump($case_id, 'caseId en 1: ');
		$origenOT = $this->Otrabajos->getDatosOrigenOT($id);		
		
		$tipo = $origenOT[0]['tipo'];	
		$id_solicitud = $origenOT[0]['id_solicitud'];// id de sol reparacion
		//dump($tipo, 'tipo en origen: ');
		//dump($id_solicitud, 'id solicitud guardada en OT: ');
		// si viene de correctivo
		if ($tipo == 2) {		
				$task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Esperando cambio estado "a Ejecutar"');
				//dump($task_id, 'tadk id en *');
				echo $task_id;
				return;
		} 
		//dump($tipo, ' tipo de tarea(back, pred, et): ');
		// si viene de backlog
		if ($tipo == 4) {
				//busco origen del backlog(tiene sore_id o no para diferenciar el origen item menu o SServicio)
				//dump($id_solicitud, 'id solicitud tarea en 4 (back, red, et): ');
				$idSolRep = $this->Otrabajos->getIdSolReparacion($id_solicitud);
				//dump($idSolRep, 'sore_id: ');			
				
				if($idSolRep == NULL){	//viene de item menu 
					// lanzar proceso
					$contract = array(
						"idSolicitudServicio"	=>	0,
						"idOT"  => 	$id
					);
					$responce = $this->bpm->lanzarProceso(BPM_PROCESS_ID, $contract);
					// guardo el caseid en OTrabajo
					if($responce['status']){					
						$case_id = $responce['data']['caseId'];
						$this->Otrabajos->setCaseidenOT($case_id, $id);					
					}	

					$task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Esperando cambio estado "a Ejecutar" 2');			
					//devueve task
					echo $task_id;

					return;		
				}else{	// backlog generado desde una SServicios
					
					// con id solicitud (BACKLOG) busco el case desde solicitud de reparacion
					$case_id = $this->Otrabajos->getCaseIdenSServicios($id);
					//dump($id, 'id sollicitud');
					//$case_id = 14001;
					//dump($case_id, ' id case en controller: ');
					$task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Esperando cambio estado "a Ejecutar" 2');
					// guarda case_id en Otrabajo
					//dump($task_id, 'task en 2: ');	// BIEN!				
					
					///$this->Otrabajos->setCaseidenOT($case_id, $id);	SE GUARDA AL GENERAR LA OTRABAJO 
					//dump($task_id, 'task para cerrar tarea: ');
					echo $task_id;
					return;			
				}
		}
		
		// Para el resto de las Tareas (Predictivo, Preventivo)
		// lanzar proceso
		$contract = array(
			"idSolicitudServicio"	=>	0,
			"idOT"  => 	$id
		);
		$responce = $this->bpm->lanzarProceso(BPM_PROCESS_ID, $contract);
		// guardo el caseid en OTrabajo
		if($responce['status']){					
			$case_id = $responce['case_id'];
			$this->Otrabajos->setCaseidenOT($case_id, $id);					
		}
		// retorna task id 		
		$task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Esperando cambio estado "a Ejecutar" 2');
		echo $task_id;
		return;

	}

	//Ejecuta Orden de Trabajo en BPM
	public function EjecutarOT(){

		//log
		log_message('DEBUG', "#TRAZA | #ASSET | Otrabajo | EjecutarOT()");

		$userdata = $this->session->userdata('user_data');
		$userBpm = $userdata[0]['userBpm']['id']; 

		$task 				= (int)$this->input->post('task');
		$ot 					= (int)$this->input->post('ot');
		$usrId				= (int)$this->input->post('responsable');
		$id_solicitud = (int)$this->input->post('id_solicitud');
		$tipo					= (int)$this->input->post('tipo');
		$estado				= 'AS';		
		$id_tarea 		= (int)$this->input->post('tareastd');
		$tarOpc				=	$this->input->post('tareaOpcional');
		$case_id = $this->Otrabajos->getCaseIdOT($ot);
		

		//Guardo Posicion donde Se Ejecuto OT desde un MOVIL
		$lat = $this->input->post('latitud');
		$lon = $this->input->post('longitud');
		$this->Otrabajos->guardarPosicion($ot , $lat, $lon);

		// si se programa tarea estandar, se guarda tambien en tbl_listarea se inicializa form
		if ($id_tarea != -1) {
			$this->load->model('Tareas');
		  $this->Tareas->instanciarSubtareas($id_tarea, $ot);
		}

		switch ($tipo) {
			case '3':
				$tipo  = 'preventivo';
				break;
			case '4':
				$tipo  = 'backlog';
				// trae id de SServicios para cambiar Estado
				$idSServicios = $this->Otrabajos->getIdSServicioporCaseId($case_id);			
				break;
			case '5':
				$tipo  = 'predictivo';
				break;
			default:
				$tipo  = 'correctivo';
				break;
		}	

		// asigno usuario logueado para finalizar la tarea 'Asignar responsable y recursos'
		$responce = $this->bpm->setUsuario($task,$userBpm);
	
		if(!$responce['status']){echo json_encode($responce);return;}	
		//Cerrar Tarea Ejectuar OT con case que viene de pantalla
		$responce = $this->bpm->cerrarTarea($task);	
		if(!$responce['status']){echo json_encode($responce);return;}

		// buscar task pa asignar la tarea siguiente (ejecutar ot) a un responsable		
		$nextTask = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID,$case_id,'Ejecutar OT');

		if($nextTask == 0){
			echo json_encode(msj(false,'No se pudo Obtener Tarea Siguiente | Ejecutar OT'));return;
		}
		// log
		log_message('DEBUG', 'TRAZA | #ASSET | Otrabajo |  $case_id: '.$case_id);
		log_message('DEBUG', 'TRAZA | #ASSET | Otrabajo | Ejecutar OT-> $task_id: '.$nextTask);

		// sincroniza usuario local con el de BPM, para asignar el usr de BPM
		$rsp = $this->bpm->getInfoSisUserenBPM($usrId);

		if(!$rsp['status']){echo json_encode($rsp);return;}
		
		$usuarioBPM = $rsp['data']['id'];

		// log	
		log_message('DEBUG', 'TRAZA | #ASSET | Otrabajo | Usr asignado (responsable OT) en BPM: '.$usuarioBPM);
		log_message('DEBUG', 'TRAZA | #ASSET | Otrabajo | Usr asignado (responsable OT) en LOCAL: '.$usrId);

		//Asignar Usuario a Tarea para Finanlizar
		$responce = $this->bpm->setUsuario($nextTask,$usuarioBPM);

		if(!$responce['status']){
			echo json_encode($responce);
			return;
		}

		//Cambiar Estado de OT ('ASignada') y en solicitud origen en BD		
		if($this->Otrabajos->cambiarEstado($ot, $estado, 'OT')){
				
				//Cambiar Estado de solicitud origen de tarea(prevent, predic, backl)
				if ($this->Otrabajos->cambiarEstado($id_solicitud, $estado, $tipo)) {			
						
					// SI backlog viene de SServicios, la cambia el estado a Asignado
						if ($idSServicios != NULL) {
							$respuestacambio = $this->Otrabajos->cambiarEstado($idSServicios, $estado, 'correctivo');
						}

						// si viene tarea opcional se actualiza, sino queda la original
						if($tarOpc != null){
							// echo"tarOpc: ";
							// var_dump($tarOpc);
							// echo "entre por 1";
							$datos = array( 'id_tarea' =>$id_tarea,
											'descripcion' =>$tarOpc,
											'id_usuario_a'=>$usrId);
						}else{
							// echo "entre por 2";
							$datos = array( 'id_tarea' =>$id_tarea,
											'id_usuario_a'=>$usrId);
						}

						// actualiza tarea en OT					
						if($this->Otrabajos->updOT($ot, $datos)){
								echo json_encode(['status'=>true, 'msj'=>'OK']);
								return;
						}else {
								echo json_encode(['status'=>false, 'msj'=>'Error Actualizando Tarea en OT']);
								return;
						}	

				} else {
						echo json_encode(['status'=>false, 'msj'=>'Error Cambio Estado en Tarea']);
						return;
				}			
				
		}else{
				echo json_encode(['status'=>false, 'msj'=>'Error Cambio Estado en OT']);
		}		
	}

	//Cambia de estado a "AN"
	public function baja_predictivo(){
	
		$idpre=$_POST['gloid'];
		
		$datos = array('estado'=>"AN");

		//doy de baja
		$result = $this->Otrabajos->update_predictivo($datos, $idpre);
		if ($result) {
			return true;
		}
		else {
			return false;
		}
  }
  
  public function getDisponibilidad()
	{
		$idEquipo = $this->input->post('idEquipo');
		$result   = calcularDisponibilidad($idEquipo);
		echo json_encode($result);
	}
 
 	public function getEquipoDisponibilidad()
 	{
		$result = $this->Otrabajos->getEquipoDisponibilidad();
		echo json_encode($result);
 	}
    /**
	 * Devuelve valores de todos los datos de la OT para mostrar en modal.
	 * @param Integer $idot id de la orden de trabajo
	 * @return Bool true/false segun resultado de la operacion
	*/
	public function getOrigenOt(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | getOrigenOt()");
        $idot     = $_POST['idot'];
        $response = $this->Otrabajos->getOrigenOt($idot);
        echo json_encode($response[0]);
	}

	//devuelve valores de todos los datos de la OT para mostrar en modal.
	public function getViewDataOt()
	{
		$idOt         = $_POST['idOt'];
		$response['otrabajo']     = $this->Otrabajos->getViewDataPreventivo($idOt);
		
		// trae herramientas 
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);

		if($herramientas){
			$response['herramientas']=$herramientas;
		}
		else{ 
			$response['herramientas']=0;
		}
		// trae insumos
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		if($insumos){
				$response['insumos']=$insumos;
		}
		else{ $response['insumos']=0;}

		// trae adjuntos
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);
		
		//dump($adjuntos, 'adjuntos: ');
		if($adjuntos){
				$response['adjunto']=$adjuntos;
		}
		else{ $response['adjunto']=0;}

    echo json_encode($response);

	}

	//devuelve valores de todos los datos de la OT desde Preventivos para mostrar en modal.
	public function getViewDataSolServicio()
	{
		$idOt          = $_POST['idOt'];
		$idSolServicio = $_POST['idSolServicio'];
		$response['solicitud']      = $this->Otrabajos->getViewDataSolServicio($idOt, $idSolServicio);
	
		
		// trae herramientas 
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);

		if($herramientas){
			$response['herramientas']=$herramientas;
		}
		else{ 
			$response['herramientas']=0;
		}
		// trae insumos
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		if($insumos){
				$response['insumos']=$insumos;
		}
		else{ $response['insumos']=0;}

		// trae adjuntos
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);	

		if($adjuntos){
				$response['adjunto']=$adjuntos;
		}
		else{ $response['adjunto']=0;}
	
		//dump($response, 'response');
		echo json_encode($response);
	}

	//devuelve valores de todos los datos de la OT desde Preventivos para mostrar en modal.
	public function getViewDataPreventivo()
	{
		$idOt         = $_POST['idOt'];
		$idPreventivo = $_POST['idPreventivo'];
		$response['preventivo']  = $this->Otrabajos->getViewDataPreventivo($idOt, $idPreventivo);
		

		// trae herramientas 
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);

		if($herramientas){
			$response['herramientas']=$herramientas;
		}
		else{ 
			$response['herramientas']=0;
		}
		// trae insumos
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		if($insumos){
				$response['insumos']=$insumos;
		}
		else{ $response['insumos']=0;}

		// trae adjuntos
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);

	

		if($adjuntos){
				$response['adjunto']=$adjuntos;
		}
		else{ $response['adjunto']=0;}


		//dump($response['adjunto'], 'adjuntos: ');

    echo json_encode($response);
	}

	//devuelve valores de todos los datos de la OT desde Backlog para mostrar en modal.
	public function getViewDataBacklog()
	{
		$idOt      = $_POST['idOt'];
		$idBacklog = $_POST['idBacklog'];
		$response['backlog']  = $this->Otrabajos->getViewDataBacklog($idOt, $idBacklog);

		// trae herramientas 
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);

		if($herramientas){
			$response['herramientas']=$herramientas;
		}
		else{ 
			$response['herramientas']=0;
		}
		// trae insumos
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		if($insumos){
				$response['insumos']=$insumos;
		}
		else{ $response['insumos']=0;}

		// trae adjuntos
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);
		if($adjuntos){
				$response['adjunto']=$adjuntos;
		}
		else{ $response['adjunto']=0;}

    echo json_encode($response);
	}

	//devuelve valores de todos los datos de la OT desde Predictivo para mostrar en modal.
	public function getViewDataPredictivo()
	{
		$idOt         = $_POST['idOt'];
		$idPredictivo = $_POST['idPredictivo'];
	
		$response['predictivo'] = $this->Otrabajos->getViewDataPredictivo($idOt, $idPredictivo);
		
		// trae herramientas 
		$herramientas = $this->Otrabajos->getOTHerramientas($idOt);

		if($herramientas){
			$response['herramientas']=$herramientas;
		}
		else{ 
			$response['herramientas']=0;
		}
		// trae insumos
		$insumos = $this->Otrabajos->getOTInsumos($idOt);
		if($insumos){
				$response['insumos'] = $insumos;
		}
		else{ $response['insumos'] = 0;}

		// trae adjuntos
		$adjuntos = $this->Otrabajos->getOTadjuntos($idOt);
		
		if($adjuntos){
				$response['adjunto']=$adjuntos;
		}
		else{ $response['adjunto']=0;}

		echo json_encode($response);
	}


	public function printOT()
	{
		$datos = $this->input->post('datos');
		$tipo = $this->input->post('tipo');
		switch ($tipo) {
		case '1': //Orden de trabajo
			$this->load->view('otrabajos/printot', $datos);
			break;
		case '2': //Solicitud de servicio
			$this->load->view('otrabajos/printotsolserv', $datos);
			break;
		case '3': //preventivo
			$this->load->view('otrabajos/printotprev', $datos);
			break;
		case '4': //Backlog
			$this->load->view('otrabajos/printotback', $datos);
			break;
		case '5': //predictivo
			$this->load->view('otrabajos/printotpred', $datos);
			break;
		case '6': //correctivo programado
			//break;
		default:
			break;
		}
	}
	/**
	 * Filtrado de listado de Ordenes de Trabajo.
	 *
	 * @param 	filtros 	array[].
	 */
	public function filtrarListado(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | filtrarListado()");
		$data = array();
		if(!empty($this->input->post('fec_desde'))){
            $data['fec_desde'] = $this->input->post('fec_desde');
        }
		if(!empty($this->input->post('fec_hasta'))){
            $data['fec_hasta'] = $this->input->post('fec_hasta');
        }
		if(!empty($this->input->post('estadoFilt'))){
            $data['estadoFilt'] = $this->input->post('estadoFilt');
        }
		if(!empty($this->input->post('equipoFilt'))){
            $data['equipoFilt'] = $this->input->post('equipoFilt');
        }
		if(!empty($this->input->post('permissionFilt'))){
            $permission = $this->input->post('permissionFilt');
        }
		$data['opciones'] = $this->load->view('otrabajos/tabla_opciones',['permission'=>$permission],true);
		$response = $this->Otrabajos->filtrarListado($data,2);
        log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Otrabajo | filtrarListado() $response >> '.json_encode($response));
		if(!empty($data['fec_desde']) || !empty($data['fec_hasta']) || !empty($data['estadoFilt']) || !empty($data['equipoFilt'])){
			echo json_encode($response);
		}else{
			echo json_encode($response=null);
		}
	}
}