<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Equipo extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('Equipos');
        $this->load->model('Sservicios');
	}
	/**
	* Vista principal para la gestion de Equipos
	* @param 
	* @return view
	*/
	public function index($permission = "Add-Edit-Del-"){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | index()");
        // $data = $this->session->userdata();
        /*log_message('DEBUG','#Main/index | Equipo >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));*/

        // if(empty($data['user_data'][0]['usrName'])){
        //     log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
        //     $var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
        //     $this->session->unset_userdata(null);
        //     $this->session->sess_destroy();

        //     echo ("<script>location.href='login'</script>");

        // }else{

            // $userdata = $this->session->userdata('user_data');
            // $data['empresa']    = $userdata[0]['id_empresa'];
            $data['empresa'] = empresa();
            //$data['list']       = $this->Equipos->equipos_List();
            $data['permission'] = $permission;
            //dump( $data['list'] );
            /*log_message('DEBUG','#Main/index | Equipo List >> data '.json_encode($data['list']));*/
            $this->load->view('Equipo/list', $data);
        // }
	}

	/**
	 * @param int inicio
	 * @param int tamaño
	 * @param string busqueda
	 * @return Array equipos
	 */
	public function paginado(){//server side processing
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | paginado()");
		
		$start = $this->input->post('start');
		//$start = 40;
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value'];

		$r = $this->Equipos->equiposPaginados($start,$length,$search);
		
		$resultado =$r['datos'];
		$totalDatos = $r['numDataTotal'];

		$datos = $resultado->result_array();
		$datosPagina = $resultado->num_rows();

		$json_data = array(
					"draw" 				=> intval($this->input->post('draw')),
					"recordsTotal"  	=> intval($datosPagina),
					"recordsFiltered"	=> intval($totalDatos),
					"data" 				=> $datos
		);
		$result = json_encode($json_data);
		echo $result;
	}
	/**
	* Da de baja equipos (AN)
	* @param integer $idEquipo
	* @return view
	*/
	public function baja_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | baja_equipo()");
		$idequipo = $_POST['idEquipo'];
		$datos    = array('estado'=>"AN");
		$result   = $this->Equipos->baja_equipos($datos, $idequipo);
		print_r(json_encode($result));
	}
	/**
	 * Equipo::getEditar()
	 * Trae info de equipo a editar
	 *
	 * @return 	String 	Json con información del equipo.
	 */
	public function getEditar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getEditar()");
		$idEquipo = $this->input->post('idEquipo');
		$info = $this->Equipos->getEquipoId($idEquipo); 
		
		if($info)
			echo json_encode($info);
		else 
			echo "nada";
	}
	/**
	* Trae listado de areas cargadas
	* @param 
	* @return array listado de areas 
	*/
    public function getarea(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getarea()");
		$area = $this->Equipos->getareas();
		if($area){
			$arre = array();
	        foreach ($area as $row){
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de procesos cargadas
	* @param 
	* @return array listado de procesos 
	*/
	public function getproceso(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getproceso()");
		$proceso = $this->Equipos->getprocesos();
		if($proceso)
		{	
			$arre = array();
	        foreach ($proceso as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de criticidad cargadas
	* @param 
	* @return array listado de criticidad 
	*/
	public function getcriti(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcriti()");
		$criti = $this->Equipos->getcriti();
		if($criti)
		{	
			$arre = array();
	        foreach ($criti as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de sector/etapa cargadas
	* @param 
	* @return array listado de sector/etapa 
	*/
	public function getetapa(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getetapa()");
		$etapa = $this->Equipos->getetapas();
		if($etapa)
		{	
			$arre = array();
	        foreach ($etapa as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de grupos cargadas
	* @param 
	* @return array listado de grupos 
	*/
	public function getgrupo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getgrupo()");
		$grupo = $this->Equipos->getgrupos();
		if($grupo)
		{	
			$arre = array();
	        foreach ($grupo as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de marcas cargadas
	* @param 
	* @return array listado de marcas 
	*/
	public function getmarca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getmarca()");
		$marca = $this->Equipos->getmarcas();
		if($marca){
			$arre = array();
	        foreach ($marca as $row ){   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Trae listado de clientes cargadas
	* @param 
	* @return array listado de clientes 
	*/
	public function getcliente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcliente()");
		$cliente = $this->Equipos->getclientes();
		if($cliente)
		{	
			$arre = array();
	        foreach ($cliente as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Edita los datos de un equipo
	* @param array $data enviado a traves de formulario en pantalla
	* @return array respuesta de la operacion 
	*/
	public function editar_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | editar_equipo()");
		$datos = $_POST['data'];
		$id = $_POST['idEquipo'];	
		$datos['id_empresa'] = empresa();
		$result = $this->Equipos->update_editar($datos,$id);
		echo json_encode($result);
	}
	/**
	* trae asiganciones de contratistas
	* @param integer $id_equipo ID del equipo
	* @return array lista de contratiastas coincidentes
	*/
	public function getContratistasEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getContratistasEquipo()");
		$id_equipo = $this->input->post('id_equipo');
		$info     = $this->Equipos->getContratistasEquipo($id_equipo); 
		echo json_encode($info);
	}
	/**
	* Trae listado de contratistas
	* @param 
	* @return array lista de contratiastas cargados por empresa
	*/
	public function getcontra(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getcontra()");
		$empresa = $this->Equipos->getcontra();
		if($empresa){	
			$arre = array();
	        foreach ($empresa as $row ) 
	        {   
	           $arre['contra'][] = $row;
	        }
			$arre['status'] = true;
			echo json_encode($arre);
		}
		else echo json_encode(array('status'=>false));
	}
    
    // Trae empresa logueada (Listo)
	public function getempresa(){
		$empresa = $this->Equipos->getempresa();
		echo json_encode($empresa);
	}
	/**
	* Carga la vista para agregar Equipos
	* @param 
	* @return view
	*/
    public function cargarequipo($permission){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | cargarequipo($permission)");
    	// $userdata = $this->session->userdata('user_data');

		$data['empresa'] = empresa();//unidad insdustrial es una rreglo(hay que ligar unidad productiva con usuario)
        $data['permission'] = $permission;    // envia permisos 
        //$data['id_equipo'] = $idglob;
        $this->load->view('Equipo/view_',$data); //equipo controlador 
    }

    public function getequipoficha(){
		$id=$_POST['idglob'];
		$equipo = $this->Equipos->getequipofichas($id);
		//echo json_encode($Customers);

		if($equipo!=false)
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


	public function getunidad(){
		
		$unidad = $this->Equipos->getunidads();
		
		if($unidad)
		{	
			$arre=array();
	        foreach ($unidad as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}


	
	


	public function getcodigo(){
		$this->load->model('Equipos');
		$codigo = $this->Equipos->getcodigo();

		if($codigo)
		{	
			$arre=array();
	        foreach ($codigo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	public function getasegurado(){
		$this->load->model('Equipos');
		$codigo = $this->Equipos->getasegurados();

		if($codigo)
		{	
			$arre=array();
	        foreach ($codigo as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	public function agregar_seguro(){

		if($_POST)
		{
			$datos=$_POST['parametros'];

			$result = $this->Equipos->agregar_seguros($datos);
				//print_r($this->db->insert_id());
				if($result)
					echo $this->db->insert_id();
				else echo 0;
		}
	}

	// Agrega unidad industrial y devuelve id de insercion - Listo
	public function agregar_unidad(){

		if($_POST)
		{
			$datos=$_POST['datos'];

			$result = $this->Equipos->agregar_unidades($datos);
				//print_r($this->db->insert_id());
				if($result > 0)
					echo json_encode($result); // devuelve id de insercion en BD
				else echo 0;
		}
	}
	/**
	* Da de alta un area
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_area(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_area()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_area($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta una criticidad
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_criti(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_criti()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_criti($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta un proceso
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_proceso(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_proceso()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_proceso($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta una etapa
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_etapa(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_etapa()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_etapa($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta un grupo
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_grupo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_etapa()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_grupos($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta una marca
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_marca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_marca()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_marcas($datos);

			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Da de alta un cliente
	* @param 
	* @return integer ID de insercion
	*/
	public function agregar_cliente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | agregar_cliente()");
		if($_POST){
			$datos=$_POST['parametros'];
			$result = $this->Equipos->agregar_clientes($datos);
			if($result) echo $result;
			else echo 0;
		}
	}
	/**
	* Agrega equipo nuevo
	* @param 
	* @return array response de la operacion
	*/
	public function guardar_equipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | guardar_equipo()");
		$codigo = $this->input->post("codigo");
		$exisCodEquipo = $this->Equipos->validaUnicidadCodigo($codigo);

		if ($exisCodEquipo) {
			echo json_encode(['status'=>'error','code'=>'201','msj'=>'El código ingresado se encuentra en uso']);
			return;
		}
		$files = $_FILES['inputPDF'];
		$names = $_FILES['inputPDF']['name'];

		$descripcion         = $this->input->post("descripcion");
		$fecha_ingreso       = $this->input->post("fecha_ingreso");
		//fecha_baja
		$fecha_garantia      = $this->input->post("fecha_garantia");
		$marca               = $this->input->post("marca");

		$id_hubicacion       = $this->input->post("ubicacion");
		$id_empresa          = empresa();
		$id_sector           = $this->input->post("etapa");
		//id_hubicacion
		$id_grupo            = $this->input->post("grupo");
		$id_customer         = $this->input->post("cliente");
		$id_criticidad       = $this->input->post("criticidad");
		$estado              = "AL";
		$fecha_ultimalectura = $this->input->post("fecha_ultima");
		$ultima_lectura      = $this->input->post("ultima_lectura");
		//tipo_horas
		//id-centrodecosto
		//valor_reposicion
		//fecha_reposicion
		//id_proveedor
		//valor
		//comprobante
		$descrip_tecnica     = $this->input->post("destec");
		$id_unidad           = $this->input->post("unin");
		$id_area             = $this->input->post("area");
		$id_proceso          = $this->input->post("proceso");
		$numero_serie        = $this->input->post("numse");

		$data = array(
			'descripcion'         => $descripcion,
			'fecha_ingreso'       => $fecha_ingreso,
			'fecha_garantia'      => $fecha_garantia,
			'marca'               => $marca,
			'codigo'              => $codigo,
			'id_hubicacion'       => $id_hubicacion,
			'id_empresa'          => $id_empresa,
			'id_sector'           => $id_sector,
			'id_grupo'            => $id_grupo,
			'id_customer'         => $id_customer,
			'id_criticidad'       => $id_criticidad,
			'estado'              => $estado,
			'fecha_ultimalectura' => $fecha_ultimalectura,
			'ultima_lectura'      => $ultima_lectura,
			'descrip_tecnica'     => $descrip_tecnica,
			'id_unidad'           => $id_unidad,
			'id_area'             => $id_area,
			'id_proceso'          => $id_proceso,
			'numero_serie'        => $numero_serie
		);
		$result = $this->Equipos->insert_equipo($data);

		if($result['result']){
			$ultimoId = $result['id_equipo'];

			if(!empty($files['name'])){
				$filesCount = count($files['name']);
				for($i = 0; $i < $filesCount; $i++){
					
					$nombreArchivo = $names[$i];
					$extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);//Extension del archivo
					$nomcodif = $this->codificaNombreV2($nombreArchivo, $extension); // codificacion de nombre

					$config   = [
						"upload_path"   => "./assets/filesequipos",
						'allowed_types' => "*",
						'file_name' => $nomcodif
					];
					$_FILES['inputPDF']['name'] =  $nomcodif;
					$_FILES['inputPDF']['type'] = $files['type'][$i];
					$_FILES['inputPDF']['tmp_name'] = $files['tmp_name'][$i];
					$_FILES['inputPDF']['error'] = $files['error'][$i];
					$_FILES['inputPDF']['size'] = $files['size'][$i];
			
					$config = [
						"upload_path"   => "./assets/filesequipos",
						'allowed_types' => "*",
						'file_name'     => $nomcodif			];
		
					$this->load->library("upload", $config);

					if ($this->upload->do_upload('inputPDF')) {
						 
						$data     = array("upload_data" => $this->upload->data());
						$datos =array(
							'id_equipo' => $ultimoId,
							'adjunto' => $nomcodif
						);
						$respuesta = $this->Equipos->guardaAdjuntoEquipo($datos);

						// if ($respuesta != null) {
						// 	$resp = array('status'=>'ok','code'=>'200');
						// } else {
						// 	$resp = array('status'=>'ok','code'=>'500','msj'=>'Error al guardar referencia en Tabla adjunto');
						// }

					} else{
						// $resp = array('status'=>'ok','code'=>'500','msj'=>'Error al subir el adjunto');

					} 
				}
			}

			echo json_encode(['status'=>'ok','code'=>'200','msj'=>'Guardado exitoso de nuevo equipo']);

		} else {

			echo json_encode(['status'=>'ok','code'=>'500','msj'=>'Error al guardar equipo']);
		}

	}
	// Codifica nombre de imagen para no repetir en servidor
	// saco los espacios del nombre, elimino la extension para obtener solo el nombre y despues le agrego valores random mas la extension del archivo
	function codificaNombreV2($nombre, $extension){
		$nombreSinEspacios = strtr($nombre, " ", "_");
		$long = strlen($extension) + 1; 
		$newname = substr($nombreSinEspacios, 0 , -$long).rand(1,3000).".".$extension;
		return $newname;
	}

	// Codifica nombre de imagen para no repetir en servidor
	// formato "12_6_2018-05-21-15-26-24" idpreventivo_idempresa_fecha(año-mes-dia-hora-min-seg)
	function codifNombre($ultimoId,$empId)
	{
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

	public function mostrar_ventana(){		  
	
		echo $this->load->view('equipo/ventana','' , true);	
	}
	
	public function mostrar_asignar()
	{
		$data['permission'] = $permission;     //envia permisos       
        $this->load->view('equipo/asigna',$data);
	
		// echo $this->load->view('equipo/asigna','' , true);
	
		
	
	}

	
	public function cambio_equipo()
	{
		$idequipo = $_POST['idequipo'];
		$datos    = array('estado'=>"IN");
		$result   = $this->Equipos->update_cambio($datos, $idequipo);
		print_r($result);
	}

	public function cambio_estado()
	{
		$idequipo = $_POST['idequipo'];
		$result   = $this->Equipos->update_estado($idequipo);
		print_r($result);
	}

	// Trae datos de equipo segun id
	public function getpencil(){
		
		$id = $this->input->post('id_equipo');		
		$result = $this->Equipos->getpencil($id);
		print_r(json_encode($result));
	}
	
	public function getdatosficha(){

		$id=$_POST['cod'];
		//print_r($id);
		$result = $this->Equipos->getdatosfichas($id);
		print_r(json_encode($result));

	}

	public function contratista(){

		$ide=$_GET['id_equipo'];
		//print_r($id);
		$result = $this->Equipos->contratista($ide);
		
	}

	public function autocompleteempresa(){
	 
	 if (isset($_GET['term'])){
	  $q = strtolower($_GET['term']);
	  $valores = $this->autocomplete->getAutocompleteempresa($q);
	  echo json_encode($valores);
	 } 
	}


	/**
	* 
	* @param 
	* @return array 
	*/
	public function getco(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getco()");
		$codi = $this->Equipos->getco($this->input->post());
		if($codi){	
			$arre=array();
        	foreach ($codi as $row ){   
           		$arre[]=$row;
        	}
			echo json_encode($arre);
		}
		else echo "nada";
	}

	public function getinfo(){
		
		$this->load->model('Equipos');
		$info = $this->Equipos->getco($this->input->post());
		if($info)
		{	
			$arre=array();
	        foreach ($info as $row ) 
	        {   
	           $arre[]=$row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}
	/**
	* Guarda contratista asignado a equipo
	* @param $id_equipo, $id_contratista
	* @return bool respuesta de operacion
	*/
	public function guardarcontra(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | guardarcontra()");
		$id_equipo      = $_POST['id_equipo'];//idequipo
		$id_contratista = $_POST['id_contratista'];//contratista		
		$datos          = array(
			'id_equipo'      => $id_equipo, 
			'id_contratista' => $id_contratista
		);	
		$response = $this->Equipos->insert_contratista($datos);
		echo json_encode($response);
	}
	/**
	* Elimina relacion contratista asignado a equipo
	* @param $id_equipo, $id_contratista
	* @return bool respuesta de operacion
	*/
	public function delContratista(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | delContratista()");
		$id_contratista = $_POST['id_contratista'];
		$id_equipo = $_POST['id_equipo'];
		$response = $this->Equipos->delContratista($id_contratista,$id_equipo );
		echo json_encode($response);
	}

	public function agregar_componente(){

	    if($_POST){
	    	$datos=$_POST['datos'];

	     	$result = $this->Equipos->agregar_componente($datos);	    
	      	
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}

  	public function guardar_ficha(){

	    if($_POST)
	    {
	    	$datos=$_POST['data'];

	     	$result = $this->Equipos->agregar_ficha($datos);
	      	//print_r($this->db->insert_id());
	      	
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}

  	public function editar_ficha(){

	    if($_POST)
	    {
	    	$datos=$_POST['data'];
	    	$id=$_POST['idglob'];

	     	$result = $this->Equipos->update_ficha($datos, $id);
	      	//print_r($this->db->insert_id());
	      	
	      	if($result)
	      		echo $this->db->insert_id();
	      	else echo 0;
	    }
  	}

  	public function getsolImp(){  

      $id=$_POST['idequip'];
      $result = $this->Equipos->getsolImps($id);

      if($result){ 
        
        $arre['datos']=$result;
       $equipos = $this->Equipos->getequiposseguro($id);
        
        if($equipos)
        {
          $arre['equipos']=$equipos;
        }
        else $arre['equipos']=0;

        $orden = $this->Equipos->getequiposorden($id);
        
        if($orden)
        {
          $arre['orden']=$orden;
        }
        else $arre['orden']=0;


        echo json_encode($arre);
      }
      else echo "nada";


  	}

  	/// Guarda lectura Hugo
  	public function setLectura(){

		//Larga solicitud de servicio desde Mantenimiento autonomo
		if($this->input->post('falla'))
		{
			$id_solServicio = $this->Sservicios->setservicios($this->input->post());
			if ($id_solServicio) {
				//Contrato para lanzar Solcitud de Servicio
				$contract = array(
					"idSolicitudServicio"	=>	$id_solServicio,
					"idOT"  => 	0
				);			

				//Lanzar Proceso
				$responce  = $this->bpm->lanzarProceso(BPM_PROCESS_ID, $contract);
				
				if(!$responce['status']){
					$this->Sservicios->eliminar($id_solServicio);
					echo json_encode($responce); 
					return;
				}
				
				//update de solic de servicio concaseid
				if($this->Sservicios->setCaseId($responce['data']['caseId'],$id_solServicio)){

					echo json_encode(['status'=> true, 'msj'=>'OK']);

				}else{

					echo json_encode(['status'=> false, 'msj'=>ASP_0200]);return;

				}	
			}
		}
  		$result = $this->Equipos->setLecturas($this->input->post());
  		echo json_encode($result);
		}
		
	public function setLecturaObservacionEdit(){
		$result = $this->Equipos->setLecturaObservacionEdit($this->input->post());
  		echo json_encode($result);
		}

  	public function getHistoriaLect(){
  		$result = $this->Equipos->getHistoriaLecturas($this->input->post());
  	  	echo json_encode($result);	
  	}

  	public function getEqPorId(){
		$idEquipo = $this->input->post()['idEquipo'];
  		$result = $this->Equipos->getEqPorIds($idEquipo);
		/*log_message('DEBUG','#Main/index | getEqPorId >> data '.json_encode($result));*/
  	  	echo json_encode($result);
  	}

	/**
	* Habilita el equipo seleccionado
	* @param integer $idequipo
	* @return integer id de la insercion
	*/
  	public function estado_alta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | estado_alta()");
  		$datos  = $this->input->post();
  		$result = $this->Equipos->estado_alta( $datos["idequipo"] );
  	  	echo json_encode($result);
  	}

  	public function alta_historial_lectura()
  	{
  		$datos  = $this->input->post();
  		$result = $this->Equipos->alta_historial_lectura( $datos['parametros'] );
  	  	echo json_encode($result);
  	}

  	/**
	 * Equipo:eliminarAdjunto();
	 *
	 * @return Bool 	True si se eliminó el archivo o false si hubo error
	 */
	public function eliminarAdjunto()
	{
		$idEquipo = $this->input->post('idEquipo');
		$response = $this->Equipos->eliminarAdjunto($idEquipo);
		echo json_encode($response);
	}

	/**
	 * Equipo:eliminaAdjunto();
	 *
	 * @return Bool 	True si se eliminó el archivo o false si hubo error
	 */
	public function eliminaAdjunto()
	{
		$idEquipo = $this->input->post('idEquipo');
		$idAdjunto = $this->input->post('idAdjunto');
		$response = $this->Equipos->eliminaAdjunto($idAdjunto);
		if($response)
		{
			$responseArchivos =  $this->Equipos->getAdjuntos($idEquipo); //listado adjuntos asosiados al equipo 
			echo json_encode($responseArchivos);
		}		
		else{
			echo json_encode(['msj'=>'Error al eliminar archivo']);
		}
		
	}


	
	/**
	 * Equipo:agregarAdjunto();
	 *
	 * @param 
	 * @return String nomre de archivo adjunto
	 */
	public function agregarAdjunto()
	{
		$files = $_FILES['inputPDF'];
		$userdata     = $this->session->userdata('user_data');
		$empId        = $userdata[0]['id_empresa'];
		$idEquipo = $this->input->post('idAgregaAdjunto');
        $names=$_FILES['inputPDF']['name'];
		
		if(!empty($files['name'])){
			$filesCount = count($files['name']);
			for($i = 0; $i < $filesCount; $i++){
				
				$nombreArchivo = $names[$i];
				$extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);//Extension del archivo
				$nomcodif = $this->codificaNombreV2($nombreArchivo, $extension); // codificacion de nombre
				
				$config   = [
					"upload_path"   => "./assets/filesequipos",
					'allowed_types' => "*",
					'file_name' => $nomcodif
				];
				$_FILES['inputPDF']['name'] =  $nomcodif;
				$_FILES['inputPDF']['type'] = $files['type'][$i];
				$_FILES['inputPDF']['tmp_name'] = $files['tmp_name'][$i];
				$_FILES['inputPDF']['error'] = $files['error'][$i];
				$_FILES['inputPDF']['size'] = $files['size'][$i];
				$this->load->library("upload",$config);
				
				if ($this->upload->do_upload('inputPDF'))
				{	
					$data     = array("upload_data" => $this->upload->data());
					$datos    = array(
										'id_equipo' => $idEquipo,
										'adjunto' => $nomcodif
									);
					$resp = $this->Equipos->guardaAdjuntoEquipo($datos); //Guarda adjunto tabla  
				}else{
					$resp = false;
				}
			}
			$responseArchivos =  $this->Equipos->getAdjuntos($idEquipo);
			if($responseArchivos){
				echo json_encode($responseArchivos);
			} else{
				$responseArchivos=false;
				echo json_encode($responseArchivos);
			}
			
		}
	}

	/**
	 * Equipo:EditarAdjunto();
	 *
	 * @return array listado de adjuntos 
	 */
	public function EditarAdjunto(){
		$idEquipo = $this->input->post('idEquipo');
		$idAdjunto = $this->input->post('id_adjunto');
		$files = $_FILES['inputEditarPDF'];

		$nombreArchivo = $files['name'];
		$extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);//Extension del archivo
		$nomcodif = $this->codificaNombreV2($nombreArchivo, $extension); // codificacion de nombre
		$config   = [
			"upload_path"   => "./assets/filesequipos",
			'allowed_types' => "*",
			'file_name'     => $nomcodif
		];
		$this->load->library("upload",$config);
		if ($this->upload->do_upload('inputEditarPDF')) 
		{	
			$data     = array("upload_data" => $this->upload->data());
			$adjunto  = array('adjunto' => $nomcodif);
			$response = $this->Equipos->updateAdjunto($adjunto , $idAdjunto);
			$responseArchivos =  $this->Equipos->getAdjuntos($idEquipo); //listado adjuntos asosiados al equipo 
		}
		echo json_encode($responseArchivos);
	}
	/**
	* Obtiene la meta del equipo
	* @param integer $eq ID del equipo
	* @return array respuesta de operacion
	*/
	public function getMeta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | getMeta()");
		$data = $this->input->post();
		$response = $this->Equipos->getMeta($data);
		if(!$response){
			echo json_encode(['msj'=>'Error al extraer Meta']);
		}else{		
			echo json_encode($response);
		}
	}
	/**
	* Asigna una meta al equipo
	* @param array $eq ID del equipo; $meta valor de la meta
	* @return array respuesta de operacion
	*/
	function asignarMeta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | asignarMeta()");
		$data = $this->input->post();
		
		if(!$this->Equipos->asignarMeta($data)){
			echo json_encode(['msj'=>'Error al asinar Meta']);
		}else{
			echo json_encode(['msj'=>'OK']);
		}
	}

	function getFormxIdGrupo()
	{
		log_message('DEBUG','#TRAZA | #TRAZ-COMP-FORM | #Equipo | getFormxIdGrupo()');
		$id_grupo = $this->input->post('id_grupo');
		$res = $this->Equipos->getFormxIdGrupo($id_grupo);
		echo json_encode($res);	
	}

	function guardaInfo_idLectura(){
		log_message('DEBUG','#TRAZA | #TRAZ-COMP-FORM | #Equipo | guardaInfo_idLectura()');
		$res = $this->Equipos->guardaInfo_idLectura($this->input->post());
		echo json_encode($res);
	}


}
