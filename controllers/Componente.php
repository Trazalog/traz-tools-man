<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Componente extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Componentes');
	}

	public function index($permission)
	{	
		$data = $this->session->userdata();
		log_message('DEBUG','#Main/index | Componente >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));

		if(empty($data['user_data'][0]['usrName'])){
			log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
			$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
			$this->session->set_userdata($var);
			$this->session->unset_userdata(null);
			$this->session->sess_destroy();

			echo ("<script>location.href='login'</script>");

		}else{
			$data['list']       = $this->Componentes->listadoABM();
			//dump_exit($data['list']);
			$data['permission'] = $permission;
			$this->load->view('componente/listabm', $data);
		}
	}

    /**
	* Carga vista principal de asociacion de componentes a equipos
	* @param 
	* @return view asociar componentes a equipos
	*/
	public function asigna($permission = "Add-Edit-Del-"){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | asigna()");
		
        $data['list']       = $this->Componentes->componentes_List();
        $data['permission'] = $permission;
        $this->load->view('componente/list', $data);

	}
	/**
	* Carga vista agregar relacion comp/equipo
	* @param 
	* @return view agregar asociacion de componentes a equipos
	*/
	public function cargarcomp($permission){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | cargarcomp($permission)");
		$data['permission'] = $permission;
		$this->load->view('componente/view_', $data);
	}
	/**
	* Trae equipos segun empresa logueada
	* @param 
	* @return array lista de equipos segun empresa logueada
	*/
	public function traerequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | traerequipo()");
		$equipo = $this->Componentes->traerequipo();
		if($equipo){	
			$arre = array();
	        foreach ($equipo as $row ){   
	           $arre[] = $row;
	        }
			echo json_encode($arre);			
		}
		else echo "nada";
	}
	/**
	* Elimina la Asociacion compon/equipo
	* @param string $idequipo; @param array $datos
	* @return bool asociar componentes a equipos
	*/
	public function baja_comp(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | baja_comp()");    
		$idequip = $_POST['idequipo'];
		$idcomp  = $_POST['datos'];		
		$result  = $this->Componentes->delete_asociacion($idequip, $idcomp);
		print_r($result);
	}

  // Devuelve descripcion de equipo segun id - Listo
	public function getequipo()
	{	
		$id     = $_POST['idequipo'];
		$equipo = $this->Componentes->getequipo($id);
		if($equipo)
		{
			$arre = array();
	        foreach ($equipo as $row )
	        {
	            $arre['datos'] = $row;
	        }
			print_r(json_encode($arre)) ;
		}
		else echo "nada";
	}
    /**
	* Trae marcas para modal agregar componente
	* @param 
	* @return array listado de marcas por empresa
	*/
	public function getmarca(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getmarca()");
		$marca = $this->Componentes->getmarca();
		if($marca)
		{	
			$arre = array();
	        foreach ($marca as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

	// Agrega componente nuevo - Listo
	public function agregar_componente()
	{
		$datos = $_POST['parametros'];
		//print_r($datos);
        if ($datos >0) {
			$descripcion = $datos['descripcion'];
			//$equipId     = $datos['id_equipo'];
			$fechahora   = date("Y-m-d H:i:s");
			$informacion = $datos['informacion'];
			$marca       = $datos['marcaid'];
			$pdf         = $datos['pdf'];
			$insert = array(
				'id_equipo'   => -1 ,
				'descripcion' => $descripcion,
				'informacion' => $informacion,
				'fechahora'   => $fechahora,
				'marcaid'     => $marca
			);
	     	$result = $this->Componentes->agregar_componente($insert);
	     	print_r(json_encode($result));
	     	if ($result){
	     		$ultimoId = $this->db->insert_id(); 
				//print_r($ultimoId);
	     		$path = "assets/files/equipos/".$ultimoId.".pdf"; 
	     		file_put_contents($path,base64_decode($pdf));
				//actualizar path en base de datos
	     		$update = array(
	     			'pdf' => $path
	     		);
	     		$comp = $this->Componentes->updatecomp($ultimoId,$update);
				//print_r($comp);
	     		return $comp;
	     	}
	    }
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


  	// Asocia equipo/componente - Listo
	public function guardar_componente()
	{	
		$idequipo = $_POST['idequipo'];
		$compo    = $_POST['comp'];
		$codigo   = $_POST['codigo'];
		$sistema  = $_POST['sistemaid'];
		$ba       = $_POST['x'];
		$ede      = $_POST['ge'];
		$j        = 1;
		
		dump($sistema, 'sist: ');
		
	  for ($i=0; $i < $ba ; $i++)
	  {     
	 	    if($compo[$j])
	 	    {
	        	$datos2 = array(
													'id_equipo'     => $idequipo, 
													'id_componente' => $compo[$j],
													'codigo'        => $codigo[$j],
													'estado'        => 'AC',
													'sistemaid'     => $sistema[$j]
														);	
	        	//print_r($datos2);
						$res = $this->Componentes->insert_componente($datos2);
						
						
	        }
	        $j++;
	    }
		return $res;    
	}

	// Trae componentes segun id de equipo - Listo
  public function getcompo(){
		$id    = $_POST['idequipo']; 
		$compo = $this->Componentes->getcompo($id);
		if($compo!=0)
		{	
			$arre = array();
	        foreach ($compo as $row ) 
	        {   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		return $compo;
	}

    /**
	* Trae la data para llenar el modal de editar
	* @param 
	* @return array data del componente
	*/
	public function getEditar(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getEditar()");
		$idComponenteEquipo = $this->input->post('idCompEq');
		$dataCompEq = $this->Componentes->getEditar($idComponenteEquipo);
		$data = array();
        foreach ($dataCompEq[0] as $clave=>$valor){
           $data[$clave] = $valor;
        }
		echo json_encode($data);
	}
    /**
	* Trae la lista de componentes por empresa
	* @param 
	* @return array lista de componentes
	*/
	public function getcomponente(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getcomponente()");
		$compo = $this->Componentes->getcomponente();
		echo json_encode($compo);
	}
	/**
	* Trae listado de sistemas por empresa
	* @param 
	* @return array lista de sistemas
	*/
	public function getsistema(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getsistema()");
		$compo = $this->Componentes->getsistema();	
		if($compo){	
			$arre = array();
			$arre['status'] = true;
	        foreach ($compo as $row ){   
	           $arre['sistemas'][] = $row;
	        }
			echo json_encode($arre);
		}
		else echo json_encode(array('status' => false));
	}
	/**
	* Obtiene la data desde el formulario para editar asociacion
	* @param 
	* @return array lista de componentes
	*/
	public function editarCompEq(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | editarCompEq()");
		$id     = $this->input->post('idCompEq');
		$datos  = $this->input->post('data');
		$result = $this->Componentes->updateEditar($datos,$id);
		echo json_encode($result);	
	}

	// ESTE METODO AGREGA COMPNENTE NUEVO DESDE ABM COMPONENTE
	public function agregarComponente() // 
	{
		$descripcion   = $this->input->post("descrip1");
		$informacion   = $this->input->post("info");
		$marcaid       = $this->input->post("ma");
		$fechahora     = date("Y-m-d H:i:s");

		// arma array con datos
		$datos = array(
			"descripcion" => $descripcion,
			'id_equipo'   => -1 ,
			'fechahora'   => $fechahora,
			"informacion" => $informacion,
			"marcaid"     => $marcaid,           
			"estado"      => "AC"
		);
		// inserta array
		$response=$this->Componentes->agregar_componente($datos);	

		if($response){

				$ultimoId = $this->db->insert_id();
			
			
				$nomcodif = $this->codifNombre($ultimoId,$empId); // codificacion de nomb  		
			
				$config = [
					"upload_path" => "./assets/files/equipos",
					'allowed_types' => "png|jpg|pdf|xlsx",
					'file_name'=> $nomcodif
				];
			
				$this->load->library("upload",$config);
				
				if ($this->upload->do_upload('inputPDF')) {	
									
					$data = array("upload_data" => $this->upload->data());
				
					$extens = $data['upload_data']['file_ext'];//guardo extesnsion de archivo
					$nomcodif = $nomcodif.$extens;
					$adjunto = array('pdf' => $nomcodif);
					
					$response = $this->Componentes->updatecomp($ultimoId,$adjunto);
				}else{
					$response = false;
				}		

		}
		
		echo json_encode($response);
		
		
	}

	//
	public function bajaComponente() // Ok
	{
		$id     = $this->input->post('idComponente');
		$result = $this->Componentes->BajaComponente($id);
		echo json_encode($result);	
	}

	//
	public function editarComponente() // Ok
	{
		//dump( $this->input->post() );
		//dump( $_FILES );
		$id_componente = $this->input->post("idComponenteE");
		$descripcion   = $this->input->post("descripcionE");
		$informacion   = $this->input->post("informacionE");
		$marcaid       = $this->input->post("marcaE");
		
		// si trae archivo 
		if(isset($_FILES) && $_FILES['pdfE']['size'] > 0){
			dump("trae file");
			$config = [
				"overwrite"     => true,
				"upload_path"   => "./assets/files/equipos",
				'allowed_types' => "pdf",
				'file_name'     => "comp".$id_componente
			];
			$this->load->library("upload",$config);
		
			if ($this->upload->do_upload('pdfE')) {
				$datos = array(
					"descripcion" => $descripcion,
					"informacion" => $informacion,
					"marcaid"     => $marcaid,           
					"pdf"         => "comp".$id_componente.$this->upload->data('file_ext')
				);
				if($this->Componentes->editarComponente($datos,$id_componente) == true)
				{
					echo json_encode(true);
				}
				else
				{
					echo json_encode(false);
				}
			}
			else
			{
				echo json_encode(false);
			}
   
		}
		else // update sin pdf
		{
			$datos = array(
				"descripcion" => $descripcion,
				"informacion" => $informacion,
				"marcaid"     => $marcaid
			);
			if($this->Componentes->editarComponente($datos,$id_componente) == true)
			{
				echo json_encode(true);
			}
			else
			{
				echo json_encode(false);
			}
		}
	}
}
