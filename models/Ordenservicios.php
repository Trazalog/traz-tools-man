<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ordenservicios extends CI_Model {
	
    function __construct(){
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
    /**
    * Devuelve listado de ordenes de servicio por ID de empresa
    * @param 
    * @return array listado de ordenes de servicio
    */
    function getOrdServiciosList(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOrdServiciosList()");
        log_message('DEBUG',"#HARCODE EMPR_ID 6");
        // $empresaId = empresa();
        $empresaId = 6;
        $this->assetDB->select('
            orden_servicio.id_orden, 
            orden_servicio.estado,
            orden_servicio.comprobante,
            orden_servicio.fecha, 
            orden_servicio.id_ot,
            orden_trabajo.descripcion AS descripcion_ot,
            equipos.codigo AS equipo,
            equipos.id_equipo
        ');
        $this->assetDB->from('orden_servicio');
        $this->assetDB->join('orden_trabajo', 'orden_servicio.id_ot = orden_trabajo.id_orden');
        $this->assetDB->join('equipos', 'orden_trabajo.id_equipo = equipos.id_equipo');
        $this->assetDB->where('orden_servicio.id_empresa', $empresaId);
        $query = $this->assetDB->get();

        if ($query->num_rows()!=0){
            return $query->result_array();  
        }else{   
            return array();
        }
    }
    /**
    * Devuelve listado de equipos
    * @param 
    * @return array listado de equipos
    */
	function getEquipos($data){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getEquipos(data : ".json_encode($data).")");    

        $id = $data['id_equipo'];
        $this->assetDB->select('
            equipos.codigo AS nomb_equipo,                
            equipos.descripcion AS desc_equip,
            equipos.fecha_ingreso,
            equipos.fecha_baja,
            equipos.fecha_garantia,
            equipos.estado,
            equipos.marca,
            grupo.descripcion AS grupo_desc,
            sector.descripcion As sector_desc,
            equipos.ubicacion
            ');           
        $this->assetDB->from('equipos');        
        $this->assetDB->join('grupo', 'equipos.id_grupo = grupo.id_grupo', 'left');
        $this->assetDB->join('sector', 'equipos.id_sector = sector.id_sector');      
		$this->assetDB->group_by('equipos.id_equipo');
        $this->assetDB->where('equipos.id_equipo', $id);
        $query = $this->assetDB->get();      			
				
        foreach ($query->result_array() as $row){
            $datos['nomb_equipo']    = $row['nomb_equipo'];
            $datos['desc_equipo']    = $row['desc_equip'];
            $datos['fecha_ingreso']  = $row['fecha_ingreso'];
            $datos['fecha_baja']     = $row['fecha_baja'];
            $datos['fecha_garantia'] = $row['fecha_garantia'];
            $datos['estado']         = $row['estado'];
            $datos['marca']          = $row['marca'];
            $datos['grupo_desc']     = $row['grupo_desc'];
            $datos['sector']         = $row['sector_desc'];
            $datos['ubicacion']      = $row['ubicacion'];
        }
        return $datos;
    }

    

    function getDatosOrdenServicios($data = null) // Ok ¿al pedo?
    {
        $id = $data['id_ordenservicio'];       
        $this->assetDB->select('
            orden_servicio.lectura,
            orden_servicio.comprobante,
            contratistas.id_contratista,
            contratistas.nombre');
        $this->assetDB->from('orden_servicio');
        $this->assetDB->join('contratistas', 'contratistas.id_contratista = orden_servicio.id_contratista');
        $this->assetDB->where('orden_servicio.id_solicitudreparacion', $id);
        $query = $this->assetDB->get();
        foreach ($query->result_array() as $row)
        { 
                $data['comprobante']    = $row['comprobante'];
                $data['lectura']        = $row['lectura'];
                $data['contratista']    = $row['nombre'];
                $data['id_contratista'] = $row['id_contratista'];
                return $data;
        }
    }

    function getHerramientas() // Ok
    {
        $userdata  = $this->session->userdata('user_data');
        $empresaId = $userdata[0]['id_empresa'];
        $this->assetDB->select('herrdescrip, herrmarca, herrcodigo, herrId');
        $this->assetDB->from('herramientas');
        $this->assetDB->where('id_empresa', $empresaId);
        $this->assetDB->where('equip_estad !=', 'AN');
        $query = $this->assetDB->get();
        $i     = 0;
        foreach ($query->result() as $row)
        {
            $herramientas[$i]['label'] = $row->herrdescrip;
            $herramientas[$i]['value'] = $row->herrmarca;
            $herramientas[$i]['codherram'] = $row->herrcodigo;
            $herramientas[$i]['herrId'] = $row->herrId;
            $i++;
        }
        return $herramientas;
    }
    /**
	*  Trae operarios por empresa logeada
	* @param integer $empId
	* @return array lista de operarios
	*/
    function getOperarios(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOperarios()");
        log_message('DEBUG', "HARCODE EMPR_ID 6");

        // $userdata  = $this->session->userdata('user_data');
        // $empresaId = $userdata[0]['id_empresa'];
        $empresaId = 6;
        $this->assetDB->select('sisusers.usrId, sisusers.usrLastName, sisusers.usrname');
        $this->assetDB->join('usuarioasempresa', 'usuarioasempresa.usrId = sisusers.usrId');
        $this->assetDB->from('sisusers');
        $this->assetDB->where('usuarioasempresa.empresaid', $empresaId);
        $this->assetDB->where('usuarioasempresa.estado', 'AC');
        $query = $this->assetDB->get();
        $i     = 0;
        foreach ($query->result() as $row)
        {
            // $equipos[$i]['label'] = $row->usrLastName.", ". $row->usrname ;
            $equipos[$i]['label'] = $row->usrname.", ". $row->usrLastName;
            $equipos[$i]['value'] = $row->usrId;
            $i++;
        }
        return $equipos;
    }

		function getRRHHOrdenTrabajo($idOT){
			//dump($idOT, 'id de OT en modell');	
			$this->assetDB->select('tbl_listarea.id_usuario,
												sisusers.usrName,
												sisusers.usrLastName');			
			$this->assetDB->from('tbl_listarea');
			$this->assetDB->join('sisusers', 'sisusers.usrId = tbl_listarea.id_usuario');
			$this->assetDB->where('tbl_listarea.id_orden', $idOT);
			$query = $this->assetDB->get();
			$i     = 0;
			foreach ($query->result() as $row)
			{   
					$equipos[$i]['label'] = $row->usrLastName.", ". $row->usrName ;
					$equipos[$i]['value'] = $row->id_usuario;
					$i++;
			}
			return $equipos; 
		}
		function getResponsableOT($idOT){
			$this->assetDB->select('sisusers.usrId,
												sisusers.usrName,
												sisusers.usrLastName');			
			$this->assetDB->from('orden_trabajo');
			$this->assetDB->join('sisusers', 'sisusers.usrId = orden_trabajo.id_usuario_a');
			$this->assetDB->where('orden_trabajo.id_orden', $idOT);
			$query = $this->assetDB->get();
			$i     = 0;
						
			foreach ($query->result() as $row)
			{   
					$equipos['label'] = $row->usrLastName.", ". $row->usrName ;
					$equipos['value'] = $row->usrId;
					$i++;
			}
			return $equipos;
		}


		// Devuelve id de Orden Servicios por id de OTrabajo
		function getOServicioPorIdOT($id_ot){

			$this->assetDB->select('orden_servicio.id_orden');
			$this->assetDB->from('orden_servicio');
			$this->assetDB->where('orden_servicio.id_ot', $id_ot);
			$query = $this->assetDB->get();
			$result = $query->row();      
      return $result->id_orden;		
		}

		// Guarda Informe de Servicios nuevo
    function setOrdenServicios($data){

			$userdata      = $this->session->userdata('user_data');
			$usrId         = $userdata[0]['usrId'];     // guarda usuario logueado
			$empresaId     = $userdata[0]['id_empresa'];
			////////// para guardar herramientas 
			if ( !empty($data['herramienta']) ){
				$date          = $data['fecha'];
				$valeSalHerram = array(
						'fecha'      => $date,
						'usrid'      => $usrId,
						'id_empresa' => $empresaId
				);
				if ( ! $this->assetDB->insert('tbl_valesalida', $valeSalHerram) ){
						return $this->assetDB->error(); // Has keys 'code' and 'message'
				}
				$idInsertVale = $this->assetDB->insert_id();

				// detalle herramientas
				for ($i=0; $i < count($data['herramienta']) ; $i++){ 
						$detavalHerram["valesid"]    = $idInsertVale;
						$detavalHerram["herrId"]     = $data["herramienta"][$i][3];
						$detavalHerram["id_empresa"] = $empresaId;
						if ( ! $this->assetDB->insert('tbl_detavalesalida', $detavalHerram) ){
								return $this->assetDB->error(); // Has keys 'code' and 'message'
						}
				}
			}else{

				$idInsertVale = 0;    // esta ba en 1 hardcode (no puede ser 0 por la clave foranea)
			}						
			////// guarda orden servicio
			$id_equipo              = $data['id_equipo'];
			$id_solicitudreparacion = $data['id_solicitudreparacion'];
			$id_ot                  = $data['id_ot'];
			$horometroinicio        = $data['horometro_inicio'];
			$horometrofin           = $data['horometro_fin'];
			$fechahorainicio        = $data['fecha_inicio'];
			$fechahorafin           = $data['fecha_fin'];
			$ord_serv               = array(
																	'fecha'                  => date('Y-m-d H:i:s'), 
																	'id_equipo'              => $id_equipo,
																	'id_solicitudreparacion' => $id_solicitudreparacion,
																	'valesid'                => $idInsertVale,	
																	'id_ot'                  => $id_ot,
																	'estado'                 => 'C',
																	'id_empresa'             => $empresaId,
																	'fechahorainicio'        => $fechahorainicio,
																	'fechahorafin'           => $fechahorafin,
																	'horometroinicio'        => $horometroinicio,
																	'horometrofin'           => $horometrofin,
															);				

			if ( ! $this->assetDB->insert('orden_servicio', $ord_serv) ){
					return $this->assetDB->error(); // Has keys 'code' and 'message'
			}
			$idInsertOrden = $this->assetDB->insert_id();

			// deta orden servicio
			for ($i=0; $i < count($data['tarea']) ; $i++)
			{
					$tarea_id        = $data['tarea'][$i][0];
					$tarea           = array(
							'id_ordenservicio' => $idInsertOrden,
							'id_tarea'         => $tarea_id
					);
					if ( ! $this->assetDB->insert('deta_ordenservicio', $tarea) )
					{
							return $this->assetDB->error(); // Has keys 'code' and 'message'
					}
			}

			// ////// guarda Operarios
			if (!empty($data['operario'])) 
			{
					$fechaSist = date('Y-m-d H:i:s');
					for ($i=0; $i < count($data['operario']) ; $i++) 
					{
							$asigUsr["usrId"]     = $data['operario'][$i][1];
							$asigUsr["id_orden"]  = $idInsertOrden;      // id orden servicio
							$asigUsr["fechahora"] = $fechaSist;
							if ( ! $this->assetDB->insert('asignausuario', $asigUsr) )
							{
									return $this->assetDB->error(); // Has keys 'code' and 'message'
							}
					}
			}

			return true;
        
		}
		// borra herramientas de Informe Servicios
		function borrarHerramOrden($id_ot){

			$this->assetDB->select('orden_servicio.valesid');
			$this->assetDB->from('orden_servicio');
			$this->assetDB->where('orden_servicio.id_ot', $id_ot);
			$resp = $this->assetDB->get();
			$valeSalId = $resp->row('valesid');
			// si hay Vale salida
			if ($valeSalId) {
				// borra el detalle de vale salida				
				$this->assetDB->where('valesid', $valeSalId);							
				$response = $this->assetDB->delete('tbl_detavalesalida');
				// borra vale salida
				$this->assetDB->where('valesid', $valeSalId);							
				$response = $this->assetDB->delete('tbl_valesalida');				
				return $response;	
			}else{
				return TRUE;
			}			
		}

		// borra RRHH de Informe de Servicios
		function borrarRecursosOrden($id_ot){
			
			$this->assetDB->select('orden_servicio.id_orden');
			$this->assetDB->from('orden_servicio');
			$this->assetDB->where('orden_servicio.id_ot', $id_ot);
			$resp = $this->assetDB->get();
			$idOrdServ = $resp->row('id_orden');
		
			// si hay orden servicios
			if ($idOrdServ) {
				// borra el detalle de vale salida				
				$this->assetDB->where('id_orden', $idOrdServ);							
				$response = $this->assetDB->delete('asignausuario');
				return $response;
			}else{
				return TRUE;
			}	

		}

		// borra Informe Servicios completo
		function borrarOrden($id_ot){

			$this->assetDB->select('orden_servicio.id_orden');
			$this->assetDB->from('orden_servicio');
			$this->assetDB->where('orden_servicio.id_ot', $id_ot);
			$resp = $this->assetDB->get();
			$idOrdServ = $resp->row('id_orden');

			$this->assetDB->where('id_ordenservicio', $idOrdServ);							
			$response = $this->assetDB->delete('deta_ordenservicio');
			
			if($response){
				$this->assetDB->where('id_ot', $id_ot);							
				$response = $this->assetDB->delete('orden_servicio');
				return $response;
			}else{
				return $response;
			}		

		}
    /**
    * Devuelve listado de lecturas
    * @param integer $id_ot id orden de trabajo
    * @return array listado de lecturas
    */
    function getLecturasOrden($id_ot){		
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getLecturasOrden(id_ot : $id_ot)");

        $this->assetDB->select('orden_servicio.horometroinicio,
                                        orden_servicio.horometrofin,
                                        orden_servicio.fechahorainicio,
                                        orden_servicio.fechahorafin
                                        ');
        $this->assetDB->from('orden_servicio');
        $this->assetDB->where('orden_servicio.id_ot', $id_ot);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0){
            return $query->result_array();
        }else{   
            return false;
        }
    }
    /**
    * Devuelve listado de tareas para el modal de informe de servicios
    * @param integer $id_ot id orden de trabajo
    * @return array listado de tareas
    */
    function getTareasOrden($id_ot){  
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getTareasOrden(id_ot : $id_ot)");      
        $this->assetDB->select('
            deta_ordenservicio.id_tarea
            ');
        $this->assetDB->from('orden_servicio');
        $this->assetDB->join('deta_ordenservicio', 'deta_ordenservicio.id_ordenservicio = orden_servicio.id_orden');
        $this->assetDB->where('orden_servicio.id_ot', $id_ot);
        $query = $this->assetDB->get();
        if ($query->num_rows()!=0){
            return $query->result_array();
        }else{   
            return false;
        }
    }
    /**
    * Devuelve listado de herramientas para el modal de informe de servicios
    * @param integer $id_ot id orden de trabajo
    * @return array listado de herramientas
    */
    function getHerramOrdenes($id_ot){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getHerramOrdenes(id_ot : $id_ot)"); 
        $this->assetDB->select('herramientas.herrcodigo,
                                                herramientas.herrmarca,
                                                herramientas.herrdescrip
                                        ');
        $this->assetDB->from('orden_servicio');        
        $this->assetDB->join('tbl_valesalida', 'orden_servicio.valesid = tbl_valesalida.valesid');        
        $this->assetDB->join('tbl_detavalesalida', 'tbl_detavalesalida.valesid = tbl_valesalida.valesid');
        $this->assetDB->join('herramientas', 'tbl_detavalesalida.herrId = herramientas.herrId');        
        $this->assetDB->where('orden_servicio.id_ot', $id_ot);
        $query = $this->assetDB->get();
        if ($query->num_rows()!=0){
            return $query->result_array();
        }else{   
            return array();
        }
    }
    /**
    * Devuelve listado de operarios para el modal de informe de servicios
    * @param integer $id_orden id orden de trabajo
    * @return array listado de operarios
    */
    function getOperariosOrden($id_orden){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getOperariosOrden(id_orden : $id_orden)");
        $this->assetDB->select('sisusers.usrName,
                                            sisusers.usrLastName');
        $this->assetDB->from('asignausuario');        
        $this->assetDB->join('sisusers', 'asignausuario.usrId = sisusers.usrId');        
        $this->assetDB->join('orden_servicio', 'orden_servicio.id_orden = asignausuario.id_orden'); 
        $this->assetDB->where('orden_servicio.id_ot', $id_orden);
        $query = $this->assetDB->get();

        if ($query->num_rows()!=0){
                return $query->result_array();  
        }else{   
                return array();
        }                
    }
    /**
    * Devuelve listado de insumos pedidos por id de OT
    * @param integer $id_ot id orden de trabajo
    * @return array insumos pedidos por id de OT
    */
    function getInsumosPorOT($id_ot){     
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicios | getInsumosPorOT(id_ot : $id_ot)");
        log_message('DEBUG',"#HARCODE EMPR_ID 6");
        // $empresaId     = empresa();
        $empresaId = 6;
        $this->assetDB->select('alm_pedidos_materiales.pema_id, 
                                            alm_pedidos_materiales.ortr_id , 
                                            alm_articulos.barcode, alm_articulos.descripcion, 
                                            alm_pedidos_materiales.fecha,
                                            alm_pedidos_materiales.estado,
                                            alm_deta_pedidos_materiales.cantidad');
        $this->assetDB->from('alm_pedidos_materiales');
        $this->assetDB->join('alm_deta_pedidos_materiales', 'alm_pedidos_materiales.pema_id = alm_deta_pedidos_materiales.pema_id');
        $this->assetDB->join('alm_articulos', 'alm_deta_pedidos_materiales.arti_id = alm_articulos.arti_id');
        $this->assetDB->where('alm_pedidos_materiales.ortr_id', $id_ot);
        $this->assetDB->where('alm_pedidos_materiales.empr_id', $empresaId);
        $query = $this->assetDB->get();
    
        if ($query->num_rows()!=0){
            return $query->result_array();
        }else{   
            return array();
        }
    }
    /**
    * Devuelve Orden de trabajo por id
    * @param integer $id id orden de trabajo
    * @return array detalle de orden de trabajo
    */
    function getorden($id){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Ordenservicio | getorden($id)");  
        $this->assetDB->select('orden_trabajo.*, 
                                            tareas.id_tarea, 
                                            tareas.descripcion AS tareadescrip, 
                                            sisusers.usrId, 
                                            CONCAT(sisusers.usrLastName,", ",sisusers.usrName) AS responsable');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->join('tareas','tareas.id_tarea  = orden_trabajo.id_tarea', 'left');
        $this->assetDB->join('sisusers','sisusers.usrId  = orden_trabajo.id_usuario_a');
        $this->assetDB->where('id_orden',$id);
        $query= $this->assetDB->get();		

        if( $query->num_rows() > 0){
            return $query->result_array();	
        }else{
            return 0;
        }
    }











    function getsolicitudes($data=null){

        $this->assetDB->select('solicitud_reparacion.id_solicitud, 
                            solicitud_reparacion.causa, 
                            solicitud_reparacion.id_equipo');
        $this->assetDB->from('solicitud_reparacion');
        $this->assetDB->where('estado', 'S');
        $query = $this->assetDB->get();

        if ($query->num_rows()!=0)
        {               
            return $query->result_array();
        }
        else
        {
            return array();
        }       
    }

    function getOrdenInactivas(){

        $this->assetDB->select(
                    'orden_servicio.id_orden, 
                    orden_servicio.estado,
                    equipos.id_equipo,
                    orden_servicio.comprobante,
                    orden_servicio.fecha, 
                    equipos.codigo,
                    solicitud_reparacion.id_solicitud,
                    solicitud_reparacion.solicitante, 
                    solicitud_reparacion.f_solicitado,                     
                    solicitud_reparacion.causa');
        $this->assetDB->from('orden_servicio');
        $this->assetDB->join('solicitud_reparacion', 'orden_servicio.id_solicitudreparacion = solicitud_reparacion.id_solicitud');
        $this->assetDB->join('equipos', 'solicitud_reparacion.id_equipo = equipos.id_equipo');
        $this->assetDB->where('orden_servicio.estado','T');
        $query = $this->assetDB->get();

        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {   
            return array();
        }  
    }

    function getComponentes($data = null){
        
        $id = $data['id_equipo'];
    
        $this->assetDB->select('componentes.id_componente, componentes.descripcion');
        $this->assetDB->from('componentes');
        $this->assetDB->join('componenteequipo', 'componentes.id_componente = componenteequipo.id_componente');
        $this->assetDB->join('equipos', 'componenteequipo.id_equipo = equipos.id_equipo'); 
        $this->assetDB->where('equipos.id_equipo', $id);
        $query = $this->assetDB->get();      
        
        return $query->result_array();                
    }

    function getContratistas()
    {
        $this->assetDB->select('id_contratista, nombre');
        $this->assetDB->from('contratistas');
        $query = $this->assetDB->get();
        return $query->result_array();
    }

    function getArticulos(){
        $query = $this->assetDB->query("SELECT articles.artId, articles.artBarCode,articles.artDescription FROM articles");
        $i=0;
        foreach ($query->result() as $row){

        	$insumos[$i]['value'] = $row->artId;
            $insumos[$i]['label'] = $row->artBarCode;
            $insumos[$i]['descripcion'] = $row->artDescription;
            $i++;
        }
        return $insumos;
    }

    // function getInsumOrdenes($data){        

    //     $id_orden = $data['id_orden'];

    //     $this->assetDB->select(
    //                 'deta_ordeninsumos.cantidad,
    //                 articles.artDescription AS descripcion,
    //                 abmdeposito.depositodescrip AS deposito');        
    //     $this->assetDB->from('orden_insumos');
    //     $this->assetDB->join('orden_servicio', 'orden_servicio.id_orden_insumo = orden_insumos.id_orden');        
    //     $this->assetDB->join('deta_ordeninsumos', 'deta_ordeninsumos.id_ordeninsumo = orden_insumos.id_orden');        
    //     $this->assetDB->join('tbl_lote', 'deta_ordeninsumos.loteid = tbl_lote.loteid');        
    //     $this->assetDB->join('articles','articles.artId = tbl_lote.artId');
    //     $this->assetDB->join('abmdeposito','abmdeposito.depositoId = tbl_lote.depositoid');
    //     $this->assetDB->where('orden_servicio.id_orden', $id_orden);

    //     $query = $this->assetDB->get();

    //     if ($query->num_rows()!=0)
    //     {
    //         return $query->result_array();  
    //     }
    //     else
    //     {   
    //         return array();
    //     }   
    // }

    function getDepositos(){

        $query = $this->assetDB->query("SELECT `depositoId`, `depositodescrip` FROM `abmdeposito`");
        $depositos = $query->result_array();

        return $depositos;
    }



    function getTareas(){      

        $query = $this->assetDB->query("SELECT `id_tarea`, `descripcion` FROM `tareas`");
        $tareas = $query->result_array();
        
        return $tareas;
    }



    function validaOperarios($data){
        
			$query = $this->assetDB->query("SELECT CONCAT(`usrLastName`,', ',`usrname`)  as `operario` FROM `sisusers`");
			$recurso = (string)$data['operario'];
			
			foreach($query->result_array() as $row){                
						
					$usuario = (string)$row['operario'];
					
					if (strcasecmp ($usuario , $recurso) == 0) { 
							$resp['resp'] = true;                
							return $resp;  
					}  
					
			}
			$resp['resp'] = false;
			return $resp;
    }



    function getSolEquipCausas($data){

        $id_solicitud = $data['id_solic'];
        $this->assetDB->select('solicitud_reparacion.id_solicitud, solicitud_reparacion.causa, solicitud_reparacion.id_equipo ');
        $this->assetDB->from('solicitud_reparacion');       
        $this->assetDB->where('solicitud_reparacion.id_solicitud', $id_solicitud);
        $query = $this->assetDB->get();

        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {   
            return array();
        }  
    }

    function getLotesActivos($depos){  // devuelve id lote y cant s/dep, estado e id de articulo
        
        $depo = $depos['depoid'];
        $insum = $depos['id_insum'];
        
        $this->assetDB->select('loteid, cantidad');
        $this->assetDB->from('tbl_lote');
        $this->assetDB->where('lotestado', 'AC');
        $this->assetDB->where('depositoid', $depo);
        $this->assetDB->where('artId', $insum);
                                
        $query = $this->assetDB->get();               
        
        foreach ($query->result() as $row){ 
          
            $datos_lote['id_lote'] = $row->loteid;
            $datos_lote['cantidad'] = $row->cantidad;           
        } 

        return $datos_lote;                       
    }
    ////// Cierra Informe de Servicios
    function setEstados($data){
        
        $id_ordenservicio = $data['id_orden'];       
        $estado['estado'] = 'T';
        
        $this->assetDB->where('id_orden', $id_ordenservicio);
        $this->assetDB->update('orden_servicio', $estado); 

        // cierra Solicitud de Servicio
        // $this->assetDB->select('orden_servicio.id_solicitudreparacion');
        // $this->assetDB->from('orden_servicio');
        // $this->assetDB->where('orden_servicio.id_orden', $id_ordenservicio);
        // $query= $this->assetDB->get();

        // foreach ($query->result() as $row){                            
        //     $id_solicitud =  $row->id_solicitudreparacion;       
        // }

        // $this->assetDB->where('id_solicitud', $id_solicitud);
        // $this->assetDB->update('solicitud_reparacion', $estado);        
    }


}

