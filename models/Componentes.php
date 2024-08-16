<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Componentes extends CI_Model{
	function __construct(){
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
	
	function listadoABM()
	{
			$userdata = $this->session->userdata('user_data');
			$empId = $userdata[0]['id_empresa'];

			$this->assetDB->select('componentes.id_componente, componentes.descripcion, componentes.informacion, componentes.marcaid, componentes.pdf,
					marcasequipos.marcaid, marcasequipos.marcadescrip');
			$this->assetDB->from('componentes');
			$this->assetDB->join('marcasequipos', 'componentes.marcaid = marcasequipos.marcaid');
			$this->assetDB->where('componentes.id_empresa', $empId);
			$this->assetDB->where('componentes.estado', 'AC');
			$this->assetDB->where('marcasequipos.estado', 'AC');	
			$query= $this->assetDB->get();

			if ($query->num_rows()!=0)
			{
					return $query->result_array();
			}
			else
			{
					return false;
			}
	}
    /**
	* Trae listado de componentes por empresa logueada
	* @param 
	* @return array listado de componentes
	*/
	function componentes_List(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | componentes_List()");
        log_message('DEBUG', "HARCODE EMPR_ID 6");
		// $userdata = $this->session->userdata('user_data');
        $empId = 6;

	 	$this->assetDB->select('equipos.id_equipo, 
												equipos.codigo, 
												equipos.descripcion, 
												componentes.id_componente, 
                        componentes.descripcion AS descomp,
                        componentes.pdf,  
                        sistema.descripcion AS sistema,
                        componenteequipo.idcomponenteequipo,
                        componenteequipo.estado,
                        componenteequipo.codigo AS codcomponente');
    	$this->assetDB->from('equipos');
    	$this->assetDB->join('componenteequipo', 'componenteequipo.id_equipo = equipos.id_equipo');
    	$this->assetDB->join('componentes', 'componentes.id_componente=componenteequipo.id_componente');
        $this->assetDB->join('sistema', 'componenteequipo.sistemaid = sistema.sistemaid');
    	$this->assetDB->where('componentes.id_empresa', $empId);
    	$query= $this->assetDB->get();   
		
		if ($query->num_rows() != 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	/**
	* Trae equipos segun empresa logueada
	* @param 
	* @return array lista de equipos segun empresa logueada
	*/
	function traerequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | traerequipo()");
        $empId = empresa();    

	 	$this->assetDB->select('equipos.*');
	 	$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.estado !=', 'AN');
		$this->assetDB->where('equipos.id_empresa', $empId);
		$this->assetDB->order_by('equipos.id_equipo', 'ASC');

	 	$query= $this->assetDB->get();   
		
		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{
			return false;
		}
	}

  // Devuelve descripcion de equipo segun id 
	function getequipo($id)
    {
        $query= $this->assetDB->get_where('equipos',$id);
	    foreach ($query->result() as $row){	
	       $data['descripcion'] = $row['descripcion']; 
	       return $data; 
		}		
	}
    /**
	* Trae marcas para modal agregar componente
	* @param 
	* @return array listado de marcas por empresa
	*/
	function getmarca(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getmarca()");
		$empId = empresa();

		$this->assetDB->select('marcasequipos.*');
		$this->assetDB->from('marcasequipos');
		$this->assetDB->where('marcasequipos.id_empresa', $empId);
		$this->assetDB->where('marcasequipos.estado !=', 'AN');
		$this->assetDB->order_by('marcasequipos.marcadescrip');
		$query= $this->assetDB->get();
		if($query->num_rows()>0){
			return $query->result();
		}
		else{
			return false;
		}	
	}	

    /**
	* Trae componentes segun empresa (no equipos)
	* @param 
	* @return array lista de componentes
	*/
	function getcomponente(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getcomponente()");
        log_message('DEBUG', "HARCODE EMPR_ID 6");
		// $empId    = empresa();
		$empId    = 6;

		$this->assetDB->select('CONCAT(descripcion,\' - \',marcadescrip,\' - \', informacion) AS label, 
											id_componente AS value', FALSE);    	
		$this->assetDB->from('componentes');
		$this->assetDB->join('marcasequipos', 'componentes.marcaid=marcasequipos.marcaid');
		$this->assetDB->where('componentes.id_empresa', $empId);
		$this->assetDB->where('componentes.estado !=', 'AN');
		$this->assetDB->order_by('label', 'ASC');
		$query = $this->assetDB->get();
		
		if($query->num_rows()>0){
			return $query->result_array(); 
		}else{
			return array();
		} 
		
	}
	/**
	* Trae listado de sistemas por empresa
	* @param 
	* @return array lista de sistemas
	*/
	function getsistema(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componente | getsistema()");
		$empId    = empresa();

		$this->assetDB->select('sistema.*');     
		$this->assetDB->from('sistema');
		$this->assetDB->where('sistema.id_empresa', $empId);
		$this->assetDB->where('sistema.estado', 'AC');
		$this->assetDB->order_by('descripcion');
		$query = $this->assetDB->get();
		if($query->num_rows() > 0){
			return $query->result();
		}
		else{
			return false;
		}   
	}
	
	// Agrega componente nuevo - Listo
	function agregar_componente($insert){
		
		$userdata             = $this->session->userdata('user_data');
		$insert['id_empresa'] = $userdata[0]['id_empresa'];                 
		$query = $this->assetDB->insert("componentes", $insert);
		return $query;    
	}

  // Asocia equipo/componente - Listo
	function insert_componente($data2)
	{
			$userdata            = $this->session->userdata('user_data');
			$data2['id_empresa'] = $userdata[0]['id_empresa'];  
			$query = $this->assetDB->insert("componenteequipo", $data2);
			return $query;
	}

	// Devuelve componentes asociados a un equipo
	function getcompo($id){

			$sql= "SELECT equipos.id_equipo, equipos.descripcion, marcasequipos.marcadescrip, 
			componentes.descripcion AS dee11, componentes.informacion, componenteequipo.id_componente
			FROM equipos			
			LEFT JOIN componenteequipo ON componenteequipo.id_equipo = equipos.id_equipo 
			AND componenteequipo.estado = 'AC'			
			LEFT JOIN componentes ON componentes.id_componente=componenteequipo.id_componente
			LEFT JOIN marcasequipos ON componentes.marcaid=marcasequipos.marcaid
			WHERE equipos.id_equipo = $id
			ORDER BY dee11";

			$query = $this->assetDB->query($sql);   

			if($query->num_rows()>0){
					return $query->result();
			}
			else{
					return false;
			}		
	}
  
	// guarda path de pdf subido
	function updatecomp($ultimoId, $update)
	{
			$this->assetDB->where('id_componente', $ultimoId);
			$query = $this->assetDB->update("componentes", $update);
			return $query;
	}
	/**
	* Camba estado de la asociacion equipo/componente
	* @param string $idequipo; @param array $datos
	* @return bool true/false segun corresponda
	*/
	function delete_asociacion($idequip,$idcomp){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | delete_asociacion(ID: $idequip | Data: ". json_encode($idcomp)."");  
		$update = array('estado' => 'AN' );
		$this->assetDB->where('id_componente', $idcomp);
		$this->assetDB->where('id_equipo', $idequip);
		$query = $this->assetDB->update("componenteequipo", $update);
		return $query;		
	}	

    /**
	* Trae la data para llenar el modal de editar
	* @param 
	* @return array data del componente
	*/
	function getEditar($idCompEq){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getEditar($idCompEq)");
        // $empId    = empresa();
        $empId = 6;

        $this->assetDB->select('componenteequipo.idcomponenteequipo, componenteequipo.id_equipo, componenteequipo.id_componente, componenteequipo.codigo, 
                equipos.codigo as codigoEq, equipos.descripcion');
        $this->assetDB->from('componenteequipo');
        $this->assetDB->join('equipos', 'componenteequipo.id_equipo = equipos.id_equipo');
        $this->assetDB->where('componenteequipo.estado', 'AC');
        $this->assetDB->where('componenteequipo.id_empresa', $empId);
        $this->assetDB->where('componenteequipo.idcomponenteequipo', $idCompEq);
        $query = $this->assetDB->get();
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }       
	}
	/**
	* Obtiene la data desde el formulario para editar asociacion
	* @param 
	* @return bool true/false segun corresponda
	*/
	function updateEditar($data, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | updateEditar(Data: ".json_encode($data)." | ID: $id)");
		$this->assetDB->where('idcomponenteequipo', $id);
		$query = $this->assetDB->update("componenteequipo",$data);
		return $query;
	}   

	//
	function bajaComponente($idcomp) // Ok
	{
			$update = array('estado' => 'AN' );
			$this->assetDB->where('id_componente', $idcomp);
			$query = $this->assetDB->update("componentes", $update);
			return $query;
	}

	//
	function editarComponente($datos,$idComponente) // Ok
	{
			//dump($datos);
			//dump_exit($idComponente);
			$this->assetDB->where('id_componente', $idComponente);
			$query = $this->assetDB->update('componentes', $datos);
			
			return $query;
	}
}	
