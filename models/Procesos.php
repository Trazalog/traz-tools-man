<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Procesos extends CI_Model
{
	function __construct()
	{
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}

	function Listado_procesos(){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa'];// guarda usuario logueado
	
		$this->assetDB->where('estado', 'AC');
		$this->assetDB->where('proceso.id_empresa', $empId);

 		$query= $this->assetDB->get('proceso');

		if ($query->num_rows()!=0)
		{
		return $query->result_array();	
		}

 	}
	/**
	* Trae los procesos para el ID de procesos seleccionado
	* @param array $id_proceso
	* @return array lista de procesos para ID seleccionado
	*/
	function Obtener_procesos($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Procesos | Obtener_procesos(".json_encode($id).")");
		$this->assetDB->where('id_proceso', $id);
		$query = $this->assetDB->get('proceso');
   
		if ($query->num_rows()!=0){   
			return $query->result_array();
		}
	}

	function Guardar_procesos($data){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 
		$data['id_empresa'] = $empId;

		$query = $this->assetDB->insert("proceso",$data);
        return $query;

	}

	function Modificar_procesos($data){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 
		$data['id_empresa'] = $empId;

		$query =$this->assetDB->update('proceso', $data, array('id_proceso' => $data['id_proceso']));
		return $query;
	}

	function Eliminar_procesos($data){
    	
        $this->assetDB->set('estado', 'AN');
		$this->assetDB->where('id_proceso', $data);
		$query=$this->assetDB->update('proceso');
		return $query;

    }
}	

?>