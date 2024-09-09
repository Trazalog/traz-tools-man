<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backlogs extends CI_Model
{
	function __construct()
	{
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
	/**
    * Trae listado de backlogs
    * @param 
    * @return array listado de backlogs
    */
	function backlog_List(){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | backlog_List()");
        $empId = empresa();

	    $this->assetDB->select('tbl_back.*,
												equipos.descripcion AS des, 
												equipos.marca, 
												equipos.codigo, 
												tareas.descripcion as de1,
												componentes.descripcion AS componente,
												sistema.descripcion as sistema');	 
	    $this->assetDB->from('tbl_back'); 
	    $this->assetDB->join('equipos', 'equipos.id_equipo = tbl_back.id_equipo');
	    $this->assetDB->join('tareas', 'tareas.id_tarea = tbl_back.id_tarea','left');
			$this->assetDB->join('componenteequipo', 'componenteequipo.idcomponenteequipo = tbl_back.idcomponenteequipo', 'left');
			$this->assetDB->join('componentes', 'componentes.id_componente = componenteequipo.id_componente', 'left');
			$this->assetDB->join('sistema', 'sistema.sistemaid = componenteequipo.sistemaid',  'left');
			$this->assetDB->where('tbl_back.id_empresa', $empId);
			$this->assetDB->where('tbl_back.estado !=', 'B');
	    $query= $this->assetDB->get();
	    if( $query->num_rows() > 0){
			$data['data'] = $query->result_array();	
			return  $data;
	    } else $data['data'] = array();
		return  $data;
	}
	// Trae equipos para llenar select vista por empresa logueada - Listo
	function getequipo(){
			
		$userdata = $this->session->userdata('user_data');
			$empId = $userdata[0]['id_empresa']; 
			
		$this->assetDB->select('equipos.*');
		$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.estado !=', 'IN');
		$this->assetDB->where('equipos.id_empresa', $empId);    	
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
    * Obtiene listado de compronentes por ID de equipo 
    * @param integer $idEquipo id del equipo
    * @return array listado de componentes
    */
	public function getComponentes(){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getComponentes()");
		$empresaId = empresa();
		$this->assetDB->select('componenteequipo.idcomponenteequipo AS idce, 
											componenteequipo.codigo, 
											componentes.descripcion, 
											sistema.descripcion AS sistema');
		$this->assetDB->from('componenteequipo');
		$this->assetDB->join('componentes', 'componentes.id_componente = componenteequipo.id_componente');
		$this->assetDB->join('sistema', 'sistema.sistemaid = componenteequipo.sistemaid');
		if($idEquipo!="") {
			$this->assetDB->where('componenteequipo.id_equipo', $idEquipo);	
		}
		$this->assetDB->where('componenteequipo.id_empresa', $empresaId);
		$this->assetDB->where('componenteequipo.estado !=', 'AN');
		$this->assetDB->order_by('componenteequipo.codigo');
		$query = $this->assetDB->get();
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return false;
		}
	}
	/**
    * Trae info de equipo por id
    * @param integer $id_equipo id del equipo seleccionado
    * @return array data del equipo
    */
	function getInfoEquipos($data = null){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | getComponentes()");
		if($data == null){
			return false;
		}else{			
			$id_equipo = $data['id_equipo'];
			//Datos del 
			$this->assetDB->select('E.*, M.*');
			$this->assetDB->from('equipos E');
			$this->assetDB->join('marcasequipos M', 'E.marca = M.marcaid', 'left');
			$this->assetDB->where('id_equipo',$id_equipo);
			$query = $this->assetDB->get();
			if($query->num_rows()>0){
				return $query->result()[0];
			}else{
				return false;
			}			
		}
	}
	// Trae tareas por empresa logueada - Listo
	function gettareas(){

		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa']; 

		$query= $this->assetDB->get_where('tareas', array('id_empresa' => $empId));
		
		if($query->num_rows()>0){
				return $query->result();
		}
		else{
				return false;
		}			
	}
	// Anula backlog por ID - Listo
	function update_back($data,$id){
		$this->assetDB->where('backId', $id);
		$query = $this->assetDB->update("tbl_back",$data);
		return $query;
	}
	/**
    * Inserta Backlog nuevo
    * @param array $data array con datos de backlog
    * @return array resultado de la operacion e id de insercion
    */
	function insert_backlog($data){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | insert_backlog()");
		$query['status'] = $this->assetDB->insert("tbl_back",$data);
		$query['id'] = $this->assetDB->insert_id();
		return $query;
	}  	
	// Guarda el nombre de adjunto
	function updateAdjunto($adjunto,$ultimoId){
		$this->assetDB->where('backId', $ultimoId);
		$query = $this->assetDB->update("tbl_back",$adjunto);
		return $adjunto;
	}
	// Trae datos de backlog para editar
	function geteditar($id){

		$this->assetDB->select('tbl_back.*, tareas.descripcion AS tareadescrip');	
		$this->assetDB->from('tbl_back');
		$this->assetDB->join('tareas', 'tareas.id_tarea = tbl_back.id_tarea', 'left');
		$this->assetDB->where('tbl_back.backId',$id);	    
		$query= $this->assetDB->get();
		
		if( $query->num_rows() > 0)
		{
			return $query->result_array();	
		} 
		else {
			return 0;
		}
	}
	// trae info de equipos para edicion
	function traerequiposBack($ide,$id){
		
		$this->assetDB->select('equipos.descripcion AS des,
											equipos.codigo, 
											equipos.ubicacion, 
											equipos.fecha_ingreso,
											marcasequipos.marcadescrip AS marca,
											componenteequipo.codigo as codcompeq,
											componentes.descripcion as componente,
											sistema.descripcion as sistema');
		$this->assetDB->from('tbl_back');
		$this->assetDB->join('equipos', 'equipos.id_equipo = tbl_back.id_equipo');	
		$this->assetDB->join('marcasequipos', 'equipos.marca = marcasequipos.marcaid');
		$this->assetDB->join('componenteequipo', 'componenteequipo.idcomponenteequipo = tbl_back.idcomponenteequipo','left');
		$this->assetDB->join('componentes', 'componentes.id_componente = componenteequipo.id_componente','left');
		$this->assetDB->join('sistema', 'sistema.sistemaid = componenteequipo.sistemaid','left');
		$this->assetDB->where('tbl_back.backId', $id);
		$this->assetDB->where('tbl_back.id_equipo', $ide);
		$query = $this->assetDB->get();
		
		if( $query->num_rows() > 0)
		{
			return $query->result_array();	
		} 
		else {
			return 0;
		}
	}
	// Trae herramientas ppor id de preventivo para Editar
	function getBacklogHerramientas($id){
        
		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 

		$this->assetDB->select('tbl_backlogherramientas.cantidad,
												herramientas.herrcodigo,
												herramientas.herrmarca,
												herramientas.herrdescrip,
												herramientas.herrId');
		$this->assetDB->from('tbl_backlogherramientas');
		$this->assetDB->join('herramientas', 'herramientas.herrId = tbl_backlogherramientas.herrId');   
		$this->assetDB->where('tbl_backlogherramientas.backId', $id);        
		$this->assetDB->where('tbl_backlogherramientas.id_empresa', $empId);
		$query= $this->assetDB->get();

		if( $query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
	// Trae insumos por id de preventivo para Editar
	function getBacklogInsumos($id){
			
		$userdata = $this->session->userdata('user_data');
		$empId = $userdata[0]['id_empresa']; 

		$this->assetDB->select('tbl_backloginsumos.id,
												tbl_backloginsumos.cantidad,
												articles.artBarCode,
												articles.artId,
												articles.artDescription,
												articles.id_empresa');                            
		$this->assetDB->from('tbl_backloginsumos');
		$this->assetDB->join('articles', 'articles.artId = tbl_backloginsumos.artId');   
		$this->assetDB->where('tbl_backloginsumos.backId', $id);        
		$this->assetDB->where('articles.id_empresa', $empId);
		$query= $this->assetDB->get(); 

		if( $query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
  // Actualiza edicion de Backlog
 	function editar_backlogs($datos,$id_back){
 		$this->assetDB->where('backId', $id_back);
		$query = $this->assetDB->update("tbl_back",$datos);
		return $query;
	 }

	// borra herramientas en edicion 
	function deleteHerramBack($id){
		$this->assetDB->where('backId', $id);
		$query = $this->assetDB->delete('tbl_backlogherramientas');
		return $query;
	}
	/**
    * Guarda las herramientas
    * @param array $herram data de herramientas cargadas en formulario
    * @return integer/bool cantidad de inserciones del batch / false si no se inserto
    */
	function insertBackHerram($herram){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | insertBackHerram(".json_encode($herram).")");
		$query = $this->assetDB->insert_batch("tbl_backlogherramientas",$herram);
		return $query;
	}
	// borra insumos en edicion
	function deleteInsumBack($id){
		$this->assetDB->where('backId', $id);
		$query = $this->assetDB->delete('tbl_backloginsumos');
		return $query;
	}
	/**
    * Guarda los insumos
    * @param array $insumo data de insumos cargadas en formulario
    * @return integer/bool cantidad de inserciones del batch / false si no se inserto
    */
	function insertBackInsum($insumo){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Backlogs | insert_bainsertBackInsumcklog(".json_encode($insumo).")");
		$query = $this->assetDB->insert_batch("tbl_backloginsumos",$insumo);
		return $query;
	}
}	
