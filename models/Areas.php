<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Areas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}

	function Listado_areas()
	{
		$userdata = $this->session->userdata('user_data');
		$empId    = $userdata[0]['id_empresa'];// guarda usuario logueado
		$this->assetDB->where('estado', 'AC');
		$this->assetDB->where('area.id_empresa', $empId);
		$query = $this->assetDB->get('area');

		if ($query->num_rows()!=0)
		{
			return $query->result_array();	
		}
	}
	/**
	* Devuelve listado de areas por ID de area
	* @param integer $id_equipo
	* @return array lista de areas
	*/
	function Obtener_areas($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Areas | Obtener_areas(".json_encode($id).")");
		$this->assetDB->where('id_area', $id);
		$query = $this->assetDB->get('area');
   
    	if ($query->num_rows()!=0){   
            return $query->result_array();  
        }
	}

	function Guardar_areas($data){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 
		$data['id_empresa'] = $empId;

		$query = $this->assetDB->insert("area",$data);
		return $query;

	}

	function Modificar_areas($data){

		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 
		$data['id_empresa'] = $empId;

		$query =$this->assetDB->update('area', $data, array('id_area' => $data['id_area']));
		return $query;
	}

	function Eliminar_areas($data){

		$this->assetDB->set('estado', 'AN');
		$this->assetDB->where('id_area', $data);
		$query=$this->assetDB->update('area');
		return $query;
    	
    }
}	

?>