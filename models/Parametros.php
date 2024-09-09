<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parametros extends CI_Model{

	function __construct(){
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
    /**
	* Trae equipos por empresa logueada
	* @param 
	* @return array listado de equipos
	*/
	function getequipo(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | getequipo()");
		$empresaId = empresa();
		
		$this->assetDB->select('equipos.id_equipo,equipos.codigo');
		$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.estado', 'AC');
		$this->assetDB->where('equipos.id_empresa', $empresaId);
		$query = $this->assetDB->get();

		if($query->num_rows()>0){
			return $query->result();
		}
		else{
			return false;
		}
	}
    /**
	* Trae parametros asociados al id de equipo
	* @param 
	* @return array listado de parametros
	*/
	function getparametros($id){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | getparametros()");
		$sql  = "SELECT setupparam.id_equipo,
						setupparam.id_parametro,
						setupparam.maximo,
						setupparam.minimo,						
						parametros.paramdescrip,
						equipos.descripcion
			FROM setupparam 
			JOIN parametros ON parametros.paramId = setupparam.id_parametro
			JOIN equipos ON equipos.id_equipo = setupparam.id_equipo 
			WHERE equipos.id_equipo = $id 
            AND equipos.estado = 'AC'
            ";
        $query = $this->assetDB->query($sql);
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
	}
	// trae info para editar parametros
	function editar($id_equipo,$id_param){
		$sql   = "SELECT setupparam.id_equipo, setupparam.id_parametro, setupparam.maximo, 						setupparam.minimo,equipos.codigo,parametros.paramdescrip
					FROM setupparam
					JOIN equipos ON equipos.id_equipo = setupparam.id_equipo
					JOIN parametros ON parametros.paramId = setupparam.id_parametro
					WHERE setupparam.id_equipo = $id_equipo 
					AND setupparam.id_parametro = $id_param
					";
		$query = $this->assetDB->query($sql);
		if( $query->num_rows() > 0)
		{
			return $query->result_array();	
		} 
		else {
			return 0;
		}
	}
	// trae info de parametro a editar
	function geteditar($id, $idp){
			$sql="SELECT setupparam.id_equipo, setupparam.id_parametro, setupparam.maximo, setupparam.minimo, 
						equipos.codigo, 
						parametros.paramdescrip
						FROM setupparam
						JOIN equipos ON equipos.id_equipo = setupparam.id_equipo
						JOIN parametros ON parametros.paramId = setupparam.id_parametro
						WHERE setupparam.id_equipo = $id 
						AND setupparam.id_parametro = $idp
						";
			$query = $this->assetDB->query($sql);
			if( $query->num_rows() > 0)
			{
				return $query->result_array();	
			} 
			else {
				return 0;
			}
	}	
	//  actualiza el parametro editado
	function update_editar($m, $n, $pa, $equ){
		$sql   = "UPDATE setupparam SET maximo = $m, minimo = $n
							WHERE id_equipo = $equ 
							AND id_parametro = $pa";
		$query = $this->assetDB->query($sql);
		return $query;			
	}
	// llimina asociacion de parametros
	function elimina_param($ide,$idp){

		$this->assetDB->where('id_equipo',$ide);
		$this->assetDB->where('id_parametro',$idp);
		$query =$this->assetDB->delete('setupparam');
		return $query;
	}
    /**
	* guarda asociacion nueva de parametros
	* @param array $data datos a guardar
	* @return bool true/false segun resultado 
	*/
	function guardar_todo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | guardar_todo(".json_encode($data).")");
        $data['id_empresa'] = empresa(); 
		$query = $this->assetDB->insert("setupparam", $data);
		return $query;    
	}
    /**
	* guarda parametro nuevo
	* @param string $data nombre del parametro 
	* @return integer id insercion
	*/
	function guardar($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | guardar(".json_encode($data).")");
		$data['id_empresa'] = empresa();
		$data['estado'] = 'AC';
		$query = $this->assetDB->insert("parametros", $data);
		$id_insercion = $this->assetDB->insert_id();	// devuelve el ultimo id
		
		return $id_insercion;
	}
	/**
	* trae parametros para asociar
	* @param 
	* @return array listado de parametros
	*/
	public function traerparametro(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | traerparametro()");
        $empresaId = empresa();
        $this->assetDB->select('parametros.*');
        $this->assetDB->from('parametros');
        $this->assetDB->where('parametros.id_empresa', $empresaId);
        $this->assetDB->where('parametros.estado !=', 'AN');
        $query = $this->assetDB->get();

        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }	
	}
}	
