<?php defined('BASEPATH') or exit('No direct script access allowed');

class Sservicios extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
    }
    /* FUNCIONES ORIGINALES DE ASSET	*/
	/**
	* Trae solicitudes en estado en Curso	
	* @param integer $empId; @param bool $showConformes 
	* @return array solicitudes de servicios
	*/
	function getServiciosList($showConformes){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getServiciosList($showConformes)");

		$empId = empresa();

		$this->assetDB->select('solicitud_reparacion.*,
											equipos.codigo as equipo,
											sector.descripcion as sector, 
											grupo.descripcion as grupo,
											equipos.ubicacion,
											orden_trabajo.fecha_terminada,
											orden_trabajo.fecha_inicio as "f_inicio",
											orden_trabajo.f_asignacion,
											solicitud_reparacion.f_solicitado,
											orden_trabajo.case_id,
											orden_trabajo.id_usuario_a,
											sisusers.usrName as mantenedor');
		$this->assetDB->from('solicitud_reparacion');
		$this->assetDB->join('equipos', 'solicitud_reparacion.id_equipo = equipos.id_equipo');
		$this->assetDB->join('sector', 'equipos.id_sector = sector.id_sector');
		$this->assetDB->join('grupo', 'equipos.id_grupo = grupo.id_grupo', 'left');
		$this->assetDB->join('orden_trabajo', 'solicitud_reparacion.case_id = orden_trabajo.case_id', 'left');
		$this->assetDB->join('sisusers', 'orden_trabajo.id_usuario_a = sisusers.usrId', 'left');
		$this->assetDB->where('solicitud_reparacion.estado !=', 'AN');
		if ($showConformes == 'false') {
			$this->assetDB->where('solicitud_reparacion.estado !=', 'CN');
		}
		$this->assetDB->where('solicitud_reparacion.id_empresa', $empId);
		$query = $this->assetDB->get();
		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{
			return false;
		}
	}

	function getEquipoSector($idequipo){
		$this->assetDB->select('A.codigo as equipo,A.descripcion as descripcion,B.descripcion as sector, A.id_area as area, A.id_proceso as proceso');
		$this->assetDB->from('equipos as A');
		$this->assetDB->join('sector as B', 'B.id_sector=A.id_sector');
		$this->assetDB->where('A.id_equipo', $idequipo);
		$query = $this->assetDB->get()->result();
		return $query;
	}

	// Elimina solicitud - Listo
	function elimSolicitudes($id){
		$estado = array(
						'estado' => 'AN'		        
				);

		$this->assetDB->where('id_solicitud', $id);
		$result = $this->assetDB->update('solicitud_reparacion', $estado);
	}
	/**
	* Trae equipos segun sector de empresa logueada
	* @param 
	* @return array lista equipos del sector seleccionado
	*/
	function getEquipSectores($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getEquipSectores(".json_encode($data).")");
		$id = $data["id_sector"];
		$this->assetDB->select('equipos.id_equipo, equipos.codigo');
		$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.estado', 'AC');
		$this->assetDB->where('equipos.id_sector', $id);
		$query = $this->assetDB->get();
		if ($query->num_rows()!=0){
			$i = 0;
			foreach ($query->result() as $row)
			{   
				$data2[$i]["descripcion"] = $row->codigo;
				$data2[$i]["id_equipo"]   = $row->id_equipo;
				$i++;
			}		
			return $data2;
		}	    
	}	

	function getSSs($idSS)
	{
		$this->assetDB->select('solicitud_reparacion.*');
		$this->assetDB->from('solicitud_reparacion');
		$this->assetDB->where('solicitud_reparacion.id_solicitud', $idSS);

		$query = $this->assetDB->get()->result();
		return $query;
	}
	/**
	* Trae sectores por empresa logueada
	* @param integer $empId
	* @return array solicitudes de servicios
	*/
	function getSectores(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | getSectores()");
		$empId = empresa();

		$this->assetDB->select('sector.id_Sector, sector.descripcion');
			$this->assetDB->from('sector');    	
			$this->assetDB->where('sector.id_empresa', $empId);
			$this->assetDB->where('sector.estado !=', 'AN');
		$query = $this->assetDB->get();

		$i = 0;
		foreach ($query->result() as $row){
			$sectores[$i]['label'] = $row->descripcion;
				$sectores[$i]['value'] = $row->id_Sector;
				$i++;
		}
		return $sectores;
	}

	//Trae datos para impresion de solicitud - Listo	
	function getsolImps($id){
		
		$this->assetDB->select('solicitud_reparacion.solicitante, 
							solicitud_reparacion.f_solicitado, 
							solicitud_reparacion.f_sugerido, 
							solicitud_reparacion.hora_sug, 
							solicitud_reparacion.causa, 
							equipos.codigo, 
							equipos.ubicacion, 
							equipos.id_sector, 
							equipos.id_grupo, 
							grupo.descripcion AS degr, 
							sector.descripcion AS sec');
			$this->assetDB->from('solicitud_reparacion');
			$this->assetDB->join('equipos', 'equipos.id_equipo = solicitud_reparacion.id_equipo');
			$this->assetDB->join('grupo', 'grupo.id_grupo=equipos.id_grupo');
			$this->assetDB->join('sector', 'sector.id_sector=equipos.id_sector');
			$this->assetDB->where('solicitud_reparacion.id_solicitud', $id);
			$query= $this->assetDB->get();
			
		foreach ($query->result_array() as $row){		
					
					$data['f_solicitado'] = $row['f_solicitado'];
					$data['solicitante'] = $row['solicitante'];
					$data['causa'] = $row['causa'];
					$data['hora_sug'] = $row['hora_sug'];
					$data['codigo'] = $row['codigo'];
					$data['ubicacion'] = $row['ubicacion'];
					$data['degr'] = $row['degr'];
					$data['sec'] = $row['sec'];
				
				return $data; 
		}
	}
	// Trae usuarios para solicitantes de la SServicios
	function getOperarios() // Ok
    {
        $userdata  = $this->session->userdata('user_data');
        $empresaId = $userdata[0]['id_empresa'];
        $this->assetDB->select('sisusers.usrId, sisusers.usrLastName, sisusers.usrname');
        $this->assetDB->join('usuarioasempresa', 'usuarioasempresa.usrId = sisusers.usrId');
        $this->assetDB->from('sisusers');
        $this->assetDB->where('usuarioasempresa.empresaid', $empresaId);
        $this->assetDB->where('usuarioasempresa.estado', 'AC');
        $query = $this->assetDB->get();
        $i     = 0;
        foreach ($query->result() as $row)
        {   
            $equipos[$i]['label'] = $row->usrLastName.", ". $row->usrname ;
            $equipos[$i]['value'] = $row->usrId;
            $i++;
        }
        return $equipos; 
    }
		// Guarda solicitud de Servicio - Listo
		function setservicios($data = null){
			log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Sservicios | setservicios(". json_encode($data).")");
			if($data == null){
				return false;
			}else{
				$equipId  = $this->input->post('equipo');//
				$falla    = $this->input->post('falla');//
				$ci =& get_instance();
				$userdata = $ci->session->userdata();
				$empId = empresa();

				//solicitud de servicio desde mantenimiento autonomo
				if(!$equipId) $equipId = $this->input->post('id_equipo');

				// $userdata = $this->session->userdata('user_data');
				// $usrId    = $userdata[0]['usrId'];     // guarda usuario logueado
				// $usrName  = $userdata[0]['usrName'];
				$ci =& get_instance();
				$usrId = $userdata['id'];
				$usrName = $userdata['first_name'] . " " .  $userdata['last_name'];
				$insert   = array(
					'f_solicitado' => date('Y-m-d H:i:s'), 
					'id_equipo'    => $equipId,
					'estado'       => 'S',	// graba estado Solicitado, cambia a 'C' en Ord Serv
					'usrId'        => $usrId,
					'solicitante'  => $usrName,
					'causa'        => $falla,
					'foto'         => 'assets/files/orders/sinImagen.jpg',
					'id_empresa'   => $empId
				);
				$this->assetDB->insert('solicitud_reparacion', $insert);

				$idSolServicios = $this->assetDB->insert_id();
				return $idSolServicios;

			}
		}	
	/* 	./ FUNCIONES ORIGINALES DE ASSET	*/

			// guarda adjunto en Edicion y en SS nueva
			function setAdjunto($adjunto,$ultimoId){	
				$this->assetDB->where('id_solicitud', $ultimoId);
				$query = $this->assetDB->update("solicitud_reparacion",$adjunto);
				return $query;
			}

			public function eliminar($id)
			{
				$this->assetDB->where('id_solicitud', $id);
				return $this->assetDB->delete('solicitud_reparacion');
			}

/* INTEGRACION CON BPM */
	// trae tareas STD para llenar select por empresa logueada
		function getTareasStandar(){

			$userdata = $this->session->userdata('user_data');
   $empId = $userdata[0]['id_empresa'];
			$this->assetDB->select('tareas.id_tarea,tareas.descripcion');		
			$this->assetDB->from('tareas');			
			$this->assetDB->where('tareas.estado !=', 'AN');
			$this->assetDB->where('tareas.id_empresa', $empId);
			$this->assetDB->where('tareas.visible', 1);
			$query= $this->assetDB->get();

			if ($query->num_rows()!=0){
				return $query->result_array();	
			}else{
				return array();
			}
		}
	// Lanza proceso en BPM
	// 	function lanzarProcesoBPM($param)
	// 	{
	// 		$resource = 'API/bpm/process/';
	// 		$url = BONITA_URL.$resource;
	// 		$com = '/instantiation';			
	// 		$caseId = file_get_contents($url.BPM_PROCESS_ID.$com, false, $param);
	// 		$response['responsecabecera'] = $this->parseHeaders( $http_response_header );
	// 		$response['caseId'] = $caseId;
	// 		return $response;
	// 	}

	// // toma la respuesta del server y devuelve el codigo de respuesta solo
	// 	function parseHeaders( $headers ){
	// 		$head = array();
	// 		foreach( $headers as $k=>$v ){
	// 			$t = explode( ':', $v, 2 );
	// 			if( isset( $t[1] ) )
	// 				$head[ trim($t[0]) ] = trim( $t[1] );
	// 			else{
	// 				$head[] = $v;
	// 				if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
	// 					$head['reponse_code'] = intval($out[1]);
	// 			}
	// 		}
	// 		return $head;
	// 	}

	// guarda CaseId en solic de servicios
		function setCaseId($caseId,$id_solServicio){			
			
			$caseId = array(
				'case_id' => $caseId		        
			);
			$this->assetDB->where('id_solicitud', $id_solServicio);
			return $this->assetDB->update('solicitud_reparacion', $caseId);
		}	

/*	./ INTEGRACION CON BPM */


//////// no vistas

	// Trae solicitudes en estado Terminado
	function get_SolicTerminadas(){		
		
		$this->assetDB->select('solicitud_reparacion.*,
			equipos.descripcion as equipo, 
			sector.descripcion as sector, 
			grupo.descripcion as grupo, 
			equipos.ubicacion');
		$this->assetDB->from('solicitud_reparacion');
		$this->assetDB->join('equipos', 'solicitud_reparacion.id_equipo = equipos.id_equipo');
		$this->assetDB->join('sector', 'equipos.id_sector = sector.id_sector');
		$this->assetDB->join('grupo', 'equipos.id_grupo = grupo.id_grupo');				
		$this->assetDB->where('solicitud_reparacion.estado', 'T');
		$this->assetDB->order_by('solicitud_reparacion.id_solicitud','DESC');
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

	// Activa solicitudes terminadas.
	function activSolicitudes($data){ 

		$this->assetDB->trans_start();   

			$id_solicitud = $data['id_solicitud']; 	

	        $estado['estado'] = 'S';
	        $this->assetDB->where('id_solicitud', $id_solicitud);
	        $this->assetDB->update('solicitud_reparacion', $estado);

        $this->assetDB->trans_complete();

		if ($this->assetDB->trans_status() === FALSE)
		{
		 return false;  
		} else{
		 return true;
		}  
	}

	// Guarda confirmacion de Solicitud de Servicio, por usr que la hizo
	function confSolicitudes($data){
		
		$id = $data['id_sol'];
		$fecha = $data['fecha_conformidad'];
		$obs = $data['observ_conformidad'];		
		
		$datos = array(
		        'fecha_conformidad' => $fecha,
		        'observ_conformidad' => $obs,
		        'estado' => 'T'		        
				);
		
		$this->assetDB->trans_start();
			$this->assetDB->where('id_solicitud', $id);
			$this->assetDB->update('solicitud_reparacion', $datos);
		$this->assetDB->trans_complete();

		if ($this->assetDB->trans_status() === FALSE)
		{
			return false;  
		}
		else{
		    
			return true;
		}
	}	

	function getequipos()
	{
		$userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];     // guarda usuario logueado

		$this->assetDB->select('equipos.id_equipo,
					equipos.codigo,
					equipos.descripcion');
    	$this->assetDB->from('equipos');
    	$this->assetDB->where('equipos.estado !=', 'AN');
    	$this->assetDB->where('equipos.id_empresa', $empId);
    	$this->assetDB->order_by('equipos.id_equipo', 'ASC');
    	$query = $this->assetDB->get();

	    if ($query->num_rows()!=0)
		{
			return $query->result_array();
		}
		else
		{
			return [];
		}
	}
}