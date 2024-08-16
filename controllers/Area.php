<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Areas');
	}

	public function index($permission)					
	{
		$data = $this->session->userdata();
		log_message('DEBUG','#Main/index | Trazacomp >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
	
		if(empty($data['user_data'][0]['usrName'])){
			log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
			$var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
			$this->session->set_userdata($var);
			$this->session->unset_userdata(null);
			$this->session->sess_destroy();
	
			echo ("<script>location.href='login'</script>");
	
		}else{
			$data['list'] = $this->Areas->Listado_areas();
			$data['permission'] = $permission;
			$this->load->view('area/view_', $data);
		}
	}
    /**
	* Trae el listado de areas por ID de area
	* @param integer $id_area
	* @return array lista de areas para ID seleccionado
	*/
	public function Obtener_area(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Area | Obtener_area()");
		$id = $_POST['id_area'];
		$result = $this->Areas->Obtener_areas($id);
		echo json_encode($result);
	}

	public function Guardar_area(){

	    $descripcion=$this->input->post('descripcion');
	    $id_empresa=$this->input->post('id_empresa');
	    $data = array(
						    'descripcion' => $descripcion,
							'id_empresa' => $id_empresa,
							'estado' => "AC"
	    );
	    $sql = $this->Areas->Guardar_areas($data);
	    echo json_encode($sql);
	   
  	}
	  	public function Modificar_area(){

  		$id=$this->input->post('id_area');
	    $descripcion=$this->input->post('descripcion');
	    $id_empresa=$this->input->post('id_empresa');
	    $data = array(
	    	    		   	'id_area' => $id,
						    'descripcion' => $descripcion,
					   );
	    $sql = $this->Areas->Modificar_areas($data);
	    echo json_encode($sql);

	  }
	  
	public function Eliminar_area(){
	
		$id=$_POST['id_area'];	
		$result = $this->Areas->Eliminar_areas($id);
		echo json_encode($result);
		
	}
}