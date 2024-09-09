<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Predictivos extends CI_Model
{
	function __construct()
	{
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
	/**
    * Lista Predictivos con estado 'C' por Empresa Logueada
    * @param 
    * @return array listado de Predictivos
    */
	function predictivo_List(){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Predictivos | predictivo_List()");
        $empId = empresa(); 
        
		$this->assetDB->select('predictivo.predId, 
								predictivo.id_equipo,
								predictivo.tarea_descrip, 
								predictivo.fecha,  												 
								predictivo.cantidad,
								predictivo.estado, 
								predictivo.horash,
								predictivo.pred_adjunto, 
								equipos.descripcion AS des, 
								equipos.marca, 
								equipos.codigo, 
								equipos.ubicacion, 
								equipos.fecha_ingreso, 
								tareas.descripcion as de1,
								periodo.descripcion AS periodo');
    	$this->assetDB->from('predictivo');
    	$this->assetDB->join('equipos','equipos.id_equipo = predictivo.id_equipo');
		$this->assetDB->join('tareas', 'tareas.id_tarea = predictivo.tarea_descrip');
		$this->assetDB->join('periodo', 'periodo.idperiodo = predictivo.periodo');    	
    	$this->assetDB->where('predictivo.estado !=', 'AN');
    	$this->assetDB->where('predictivo.id_empresa', $empId);    	    	
    	$query= $this->assetDB->get(); 		
	    
	    if( $query->num_rows() > 0){
	      $data['data'] = $query->result_array();	
	      return  $data;
	    } else $data['data'] = array();
		return  $data; 
	}

	// Trae equipos por empresa logueada - Listo
	function getEquipos(){
		$userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 
        
    	$this->assetDB->select('equipos.id_equipo,equipos.codigo');
    	$this->assetDB->from('equipos');
    	$this->assetDB->where('equipos.estado!=', 'AN');
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
    * Trae info de equipos por ID y por empresa logueada
    * @param integer $id_equipo id del equipo
    * @return array data del equipo
    */
	function getInfoEquipos($id){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getInfoEquipos($id)");
        $empId = empresa(); 
        
    	$this->assetDB->select('equipos.*,marcasequipos.marcadescrip,marcasequipos.marcaid');
		$this->assetDB->from('equipos');
		$this->assetDB->join('marcasequipos', 'equipos.marca = marcasequipos.marcaid');
    //	$this->assetDB->where('equipos.estado', 'AC');
    	$this->assetDB->where('equipos.id_empresa', $empId);
    	$this->assetDB->where('equipos.id_equipo', $id);      	
    	$query= $this->assetDB->get();   

		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{
			return false;
		}
	}

	// Trae tareas por empresa logueada - Listo
	function gettarea(){

		$userdata = $this->session->userdata('user_data');
    $empId = $userdata[0]['id_empresa']; 
		$this->assetDB->select('tareas.id_tarea AS value, tareas.descripcion AS label');
		$this->assetDB->from('tareas');    	
		$this->assetDB->where('tareas.id_empresa', $empId);
		$this->assetDB->where('estado', 'AC');
		$this->assetDB->order_by('label', 'ASC');
		$query= $this->assetDB->get();

		if($query->num_rows()>0){
			return $query->result_array();
		}
		else{
			return false;
		}
	}

	// Trae unidades de tiempo  - Listo
	function getUnidTiempos(){

		$this->assetDB->select('unidad_tiempo.*');
    	$this->assetDB->from('unidad_tiempo');    	
    	$query= $this->assetDB->get();

		if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
	}

	//Insertar  predictivo  - Listo
	function insert_predictivo($data){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Predictivos | insert_predictivo()");
		$query['status'] = $this->assetDB->insert("predictivo",$data);
		$query['id'] = $this->assetDB->insert_id();
		return $query;
	}

	// Guarda el bacht de datos de herramientas de Preventivo - Listo
	function insertPredHerram($herramPred){

		$query = $this->assetDB->insert_batch("tbl_predictivoherramientas",$herramPred);
		return $query;
	}

	// Guarda insumos del Preventivo - Listo 
	function insertPredInsum($insumoPred){

		$query = $this->assetDB->insert_batch("tbl_predictivoinsumos",$insumoPred);
		return $query;
	}
/**
 * Guarda el nombre de adjunto
 * @param String $adjunto nombre codificado del adjunto.
 * @return Bool true/false segun resultado de la operacion.
*/
function updateAdjunto($adjunto,$ultimoId){
	log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Predictivos | updateAdjunto(Adjunto: $adjunto | ID: $ultimoId)");
	$this->assetDB->where('predId', $ultimoId);
	$query = $this->assetDB->update("predictivo",$adjunto);
	return $adjunto;
}

	// Trae info para edicion de preventivo por id - Listo
    function getInfopred($ide,$id){
		
	    $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 
        
    	$this->assetDB->select('predictivo.id_equipo,
												predictivo.predId, 												
												predictivo.fecha, 
												predictivo.estado as es, 
												predictivo.cantidad, 
												predictivo.periodo,
												predictivo.pred_duracion as duracion,
												predictivo.id_unidad as unidtiempo, 
												predictivo.pred_canth as operarios,
												predictivo.horash as hh,
												predictivo.pred_adjunto,
												equipos.codigo, 
												equipos.ubicacion, 
												equipos.marca, 
												equipos.fecha_ingreso, 
												equipos.descripcion,
												tareas.id_tarea, 
                        tareas.descripcion AS tarea_descrip');    	
    	$this->assetDB->from('predictivo'); 
			$this->assetDB->join('equipos', 'equipos.id_equipo=predictivo.id_equipo');   
			$this->assetDB->join('tareas', 'tareas.id_tarea=predictivo.tarea_descrip');	
    	$this->assetDB->where('predictivo.predId', $id);
    	$this->assetDB->where('predictivo.id_equipo', $ide);
    	$this->assetDB->where('predictivo.id_empresa', $empId);
    	$query= $this->assetDB->get();

			if($query->num_rows()>0){
							return $query->result();
					}
					else{
							return false;
					}	
		}	

	// Trae herramientas ppor id de preventivo para Editar
    function getPredictivoHerramientas($id){
        
			$userdata = $this->session->userdata('user_data');
			$empId = $userdata[0]['id_empresa']; 

			$this->assetDB->select('tbl_predictivoherramientas.cantidad,
													herramientas.herrcodigo,
													herramientas.herrmarca,
													herramientas.herrdescrip,
													herramientas.herrId');
			$this->assetDB->from('tbl_predictivoherramientas');
			$this->assetDB->join('herramientas', 'herramientas.herrId = tbl_predictivoherramientas.herrId');   
			$this->assetDB->where('tbl_predictivoherramientas.predId', $id);        
			$this->assetDB->where('tbl_predictivoherramientas.id_empresa', $empId);
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
    function getPredictivoInsumos($id){
        
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 

        $this->assetDB->select('tbl_predictivoinsumos.id,
                            tbl_predictivoinsumos.cantidad,
                            articles.artBarCode,
                            articles.artId,
                            articles.artDescription,
                            articles.id_empresa');                            
        $this->assetDB->from('tbl_predictivoinsumos');
        $this->assetDB->join('articles', 'articles.artId = tbl_predictivoinsumos.artId');   
        $this->assetDB->where('tbl_predictivoinsumos.predId', $id);        
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

	
	// Devuelve info de Predictivo por ID
	function getInfoPredId($id){	 

	    $this->assetDB->select('predictivo.*');
    	$this->assetDB->from('predictivo');    	
    	$this->assetDB->where('predictivo.predId', $id);
    	$query= $this->assetDB->get();
	    
	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();	
	    } 
	    else {
	      return 0;
	    }
	}
	
	// Guarda Preventivo editado
	function updatePredictivos($datos,$id){
	    $this->assetDB->where('predId', $id);
	    $query = $this->assetDB->update("predictivo",$datos);
	    return $query;
	}

	// Update herramientas preventivo
	function deleteHerramPred($id_predictivo){        
		$this->assetDB->where('predId', $id_predictivo);
		$query = $this->assetDB->delete('tbl_predictivoherramientas');
		return $query;
}

function deleteInsumPred($id_predictivo){
		$this->assetDB->where('predId', $id_predictivo);
		$query = $this->assetDB->delete('tbl_predictivoinsumos');
		return $query;
}

	// Cambia predictivo a aestado AN
	function baja_predictivos($datos,$id){
	    $this->assetDB->where('predId', $id);
	    $query = $this->assetDB->update("predictivo",$datos);
	    return $query;
	}
}	

?>