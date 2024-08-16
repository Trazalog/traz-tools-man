<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proceso extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Procesos');
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
			$data['list'] = $this->Procesos->Listado_procesos();
			$data['permission'] = $permission;
			$this->load->view('proceso/view_', $data);
		}
	}
    /**
	* Trae los procesos para el ID de procesos seleccionado
	* @param array $id_proceso
	* @return array lista de procesos para ID seleccionado
	*/
	public function Obtener_proceso(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Proceso | Obtener_proceso()");
		$id = $_POST['id_proceso'];
		$result = $this->Procesos->Obtener_procesos($id);
		echo json_encode($result);

	}

	public function Guardar_proceso(){
	  	
	    $descripcion=$this->input->post('descripcion');
	    $id_empresa=$this->input->post('id_empresa');
	    $data = array(
						    'descripcion' => $descripcion,
							'id_empresa' => $id_empresa,
							'estado' => "AC"
	    );
	    $sql = $this->Procesos->Guardar_procesos($data);
	    echo json_encode($sql);
	   
	}
	  
	public function Modificar_proceso(){
  		$id=$this->input->post('id_proceso');
	    $descripcion=$this->input->post('descripcion');
	    $id_empresa=$this->input->post('id_empresa');
	    $data = array(
	    	    		   'id_proceso' => $id,
						    'descripcion' => $descripcion,
					   );
	    $sql = $this->Procesos->Modificar_procesos($data);
	    echo json_encode($sql);

	}
	  
	public function Eliminar_proceso(){
	
		$id=$_POST['id_proceso'];	
		$result = $this->Procesos->Eliminar_procesos($id);
		echo json_encode($result);
		
	}
}	

?>