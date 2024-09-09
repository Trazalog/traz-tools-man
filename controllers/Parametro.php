<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Parametro extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('Parametros');
	}
    /**
    * Carga vista principal de parametrizar predictivo
    * @param 
    * @return view listado parametrizar predictivo
    */
	public function index($permission = "Add-Edit-Del-"){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Parametro | index()");
        $data['permission'] = $permission;
        $this->load->view('parametro/list', $data);
	}
    /**
	* Trae equipos por empresa logueada
	* @param 
	* @return array listado de equipos
	*/
	public function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | getequipo()");
		$equipo = $this->Parametros->getequipo();
		if($equipo){	
			$arre = array();
	        foreach ($equipo as $row ) {   
	           $arre[] = $row;
            }
			echo json_encode($arre);
		}
		else echo "nada";
	}
    /**
	* Trae parametros asociados al id de equipo
	* @param 
	* @return array listado de parametros
	*/
	public function getparametros(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | getparametros()");
		$id         = $_POST['id_equipo'];
		$parametros = $this->Parametros->getparametros($id);
		if($parametros){	
			$arre = array();
	        foreach ($parametros as $row ) {   
	           $arre[] = $row;
	        }
			echo json_encode($arre );
		}
		else echo "nada";
	}

	public function baja_parametro()
	{
		$t  = $_POST['tr'];
		$co = $_POST['comp'];
		$eq = $_POST['e'];
		//$p=$_POST['parent'];
		//$idp=$_POST['idparam'];
		//$i=0;
		/*if(count($p) > 0 ){ 
	       	if($co[$p] ){
	       		$datos2 = $co[$p];
				$result = $this->Parametros->delete_parametro($idq,$datos2);
				print_r($result);
			}
		}*/
		if($co[$t]){  
       		$datos2 = $co[$t];
       		//$i++;
			$result = $this->Parametros->delete_parametro($eq,$datos2);
		}
		return $result;
	}

	public function bajaparametro()
	{
		$idq    = $_POST['id_equipo'];
		$idp    = $_POST['id_parametro'];
		if(count($idq) > 0 ){
			if($idp){
				$result = $this->Parametros->delete_p($idq,$idp);
				print_r($result);
			}
		}
	}

	public function geteditar()
	{
		$id     = $_GET['equipoglob'];
		$idp    = $_GET['id_parametro'];
		$result = $this->Parametros->geteditar($id,$idp);
		if($result)
		{	
			$arre['datos'] = $result;
			echo json_encode($arre); //echo json_encode($arre)
		}
		else echo "nada";
	}

	public function editar()
	{
		//$pa=$_GET['idparam'];
		//$i=0;
		$id  = $_GET['e'];//id_equipo
		$pp  = $_GET['parent'];//numer de fila
		$cop = $_GET['comp']; //id de param
		if (count($pp>0)) {
			if ($cop[$pp]) {
				$idp2   = $cop[$pp];
				$result = $this->Parametros->editar($id,$idp2);
			}
		}
		/*if ($pa[$i]) {
			$idp2=$pa[$i];
			$i++;
			$result = $this->Parametros->editar($id,$idp2);
		}*/
		if($result)
		{	
			$arre['datos'] = $result;
			echo json_encode($arre);
		}
		else echo "nada";
	}

	public function agregar_componente()
	{
		//$ide=$_POST['e'];
		$datos = $_POST['datos'];
		if ($datos) {
			$pa     = $datos['id_parametro'];
			$m      = $datos['maximo'];
			$n      = $datos['minimo'];
			$equ    = $datos['id_equipo'];
			//doy de baja
			$result = $this->Parametros->update_editar($m,$n,$pa,$equ);
			print_r(json_encode($result));
		}
  	}

  public function agregarcomponente()
	{
		$ide   = $_POST['equipoglob'];
		$datos = $_POST['datos'];
		print_r($ide);
		print_r($datos);
		if ($datos) {
			$pa     = $datos['id_parametro'];
			$maxi   = $datos['maximo'];
			$mini   = $datos['minimo'];
			//doy de baja
			$result = $this->Parametros->update_editar($maxi,$mini, $ide,$pa);
			print_r(json_encode($result));
		}
  }
	//guarda la asociacio de parametros editada
	public function guardarmodif(){
		
		$datos = $_POST['datos'];
		
		if ($datos) {
			$pa     = $datos['id_parametro'];
			$m      = $datos['maximo'];
			$n      = $datos['minimo'];
			$equ    = $datos['id_equipo'];
			//doy de baja
			$result = $this->Parametros->update_editar($m,$n,$pa,$equ);
			if($result){
				$parametros = $this->Parametros->getparametros($equ);
			}else{
				$parametros = false;
			}
			
		echo json_encode($parametros);
		}
	}
	// elimina asociacion
	public function eliminar(){

		$id_equipoElim = $_POST['id_equipoElim'];
		
		$id_parametroElim = $_POST['id_parametroElim'];

		// dump($id_equipoElim, 'id de equipo');
		// dump($id_parametroElim,'ide parametro');
		$response = $this->Parametros->elimina_param($id_equipoElim,$id_parametroElim);
		if($response){
			$parametros = $this->Parametros->getparametros($id_equipoElim);
		}else{
			$parametros = false;
		}
		
		echo json_encode($parametros);
	}
    /**
	* guarda asociacion nueva de parametros
	* @param 
	* @return array 
	*/
	public function guardar_todo(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | guardar_todo()");
		$datos = $_POST['data'];
		$id_equipo = $datos['id_equipo'];
		$response = $this->Parametros->guardar_todo($datos);
		if($response){
			$parametros = $this->Parametros->getparametros($id_equipo);
		}else{
			$parametros = false;
		}		
		echo json_encode($parametros);
	}
    /**
	* guarda parametro nuevo
	* @param string $data nombre del parametro 
	* @return integer id insercion
	*/
	public function guardar(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | guardar()");
		$datos  = $_POST['data'];
		$id_insercion = $this->Parametros->guardar($datos);
		echo json_encode($id_insercion);  
	}
    /**
	* trae parametros para asociar
	* @param 
	* @return array listado de parametros
	*/
	public function traerparametro(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | traerparametro()");
		$equipo = $this->Parametros->traerparametro();
		if($equipo){	
			$arre = array();
	        foreach ($equipo as $row ){   
	           $arre[] = $row;
	        }
			echo json_encode($arre);
		}
		else echo "nada";
	}

}	
