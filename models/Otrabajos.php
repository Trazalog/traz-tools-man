<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Otrabajos extends CI_Model {

	function __construct()
	{
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}
	
	/**
	 * Trae las ordenes de de trabajo de la empresa logueada.
	 *
	 * @return  Array   Arreglo con Ordenes de Trabajo.
	 */
	function otrabajos_List( $ot=NULL, $tipo ) // Ok
	{
	
		$userdata = $this->session->userdata('user_data');
		$empId    = $userdata[0]['id_empresa'];
	
		$this->assetDB->select('orden_trabajo.*, tbl_tipoordentrabajo.descripcion AS tipoDescrip, 
												user1.usrName AS nombre, user1.usrLastName,
												sisusers.usrName, 
												sisusers.usrLastName, equipos.codigo, 
												0 as grpId,
												equipos.id_equipo,
												admcustomers.cliRazonSocial AS nomCli,
												orden_servicio.id_orden AS ordenservicioId');
		$this->assetDB->from('orden_trabajo');
		$this->assetDB->join('tbl_tipoordentrabajo', 'tbl_tipoordentrabajo.tipo_orden = orden_trabajo.tipo');
		$this->assetDB->join('sisusers', 'sisusers.usrId = orden_trabajo.id_usuario');
		$this->assetDB->join('sisusers AS user1', 'orden_trabajo.id_usuario_a = user1.usrId', 'left');//usuario asignado?
		$this->assetDB->join('equipos','equipos.id_equipo = orden_trabajo.id_equipo');
		$this->assetDB->join('admcustomers','admcustomers.cliId = equipos.id_customer');
	
		//LEFT JOIN orden_servicio ON orden_trabajo.id_orden = orden_servicio.id_ot

		$this->assetDB->join('orden_servicio', 'orden_trabajo.id_orden = orden_servicio.id_ot', 'left');
	
		$this->assetDB->where('equipos.estado !=','AN');

		if($tipo == 1){
			$this->assetDB->where('orden_trabajo.tipo', 1);
			
		}

		$this->assetDB->where('orden_trabajo.id_empresa', $empId);
		$query = $this->assetDB->get();




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
	 * Trae los proveedores de la empresa logueada.
	 *
	 * @return  Array   Arreglo con Proveedores.
	 */
	function getproveedor() // Ok
	{
			$userdata = $this->session->userdata('user_data');
			$empId    = $userdata[0]['id_empresa'];
			$this->assetDB->select('abmproveedores.provnombre, abmproveedores.provid');
			$this->assetDB->from('abmproveedores');
			$this->assetDB->where('abmproveedores.estado', 'AC');
			$this->assetDB->where('abmproveedores.id_empresa', $empId);
			$query = $this->assetDB->get();

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
	 * Trae las ordenes de de trabajo de ua empresa logueada.
	 *
	 * @return  Array   Arreglo con Ordenes de Trabajo.
	 */
	function traer_sucursal() // Ok
	{
			$userdata = $this->session->userdata('user_data');
			$empId    = $userdata[0]['id_empresa'];
			$query    = $this->assetDB->get_where('sucursal', array('id_empresa' => $empId));
			if($query->num_rows()>0)
			{
					return $query->result();
			}
			else
			{
					return false;
			}       
	}
	/**
	 * Trae las ordenes de de trabajo de ua empresa logueada.
	 *
	 * @return  Array   Arreglo con Ordenes de Trabajo.
	 */
	function getequipo() // Ok
	{
			$userdata = $this->session->userdata('user_data');
			$empId    = $userdata[0]['id_empresa'];
			$this->assetDB->select('*');
			$this->assetDB->from('equipos');
			$this->assetDB->where('id_empresa', $empId);
			$this->assetDB->where('estado !=', 'AN');
			//$this->assetDB->where( array('estado'=>'AC', 'id_empresa'=>$empId) );
			//$this->assetDB->or_where('estado', 'RE');
			$query = $this->assetDB->get();
			if($query->num_rows()>0)
			{
					return $query->result();
			}
			else
			{
					return false;
			}   
	}

	
	// Trae equipos por empresa logueada - Listo
	function getEquiposNuevaOT(){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | listOrden()");
		$empId = empresa();
			
		$this->assetDB->select('equipos.id_equipo,equipos.codigo');
		$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.estado!=', 'AN');
		$this->assetDB->where('equipos.id_empresa', $empId);
		$query = $this->assetDB->get();
		
	 	if ($query->num_rows()!=0){
	 		return $query->result_array();
	 	}else{	
	 		return false;
	 	}	
	}
	/**
    * Trae info de equipos por ID y por empresa logueada
    * @param Integer $id_equipo id del equipo
    * @return Array data del equipo
    */
	function getInfoEquiposNuevaOT($id){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getInfoEquipoNuevaOT($id)");
    	$empId = empresa();
		
		$this->assetDB->select('equipos.*, marcasequipos.marcadescrip, admcustomers.cliRazonSocial AS nomCli');
		$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
		$this->assetDB->join('admcustomers', 'admcustomers.cliId = equipos.id_customer' );
		$this->assetDB->from('equipos');
		$this->assetDB->where('equipos.id_empresa', $empId);
		$this->assetDB->where('equipos.id_equipo', $id); 	
		$query= $this->assetDB->get();
	
		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	/**
	 * Devuelve la descripcion para una tarea estandar.por ID
	 * @param Integer $id_tar id de tarea
	 * @return String descripcion de la tarea.
	*/
	function getDescTareaSTD($id_tar){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getDescTareaSTD(ID tarea: $id_tar)");
		$this->assetDB->select('tareas.descripcion');
		$this->assetDB->from('tareas');
		$this->assetDB->where('tareas.id_tarea', $id_tar);
		$query = $this->assetDB->get();
		$row = $query->row();	
		
		return $row->descripcion; 
	}
	/**
	 * Guarda Orden de Trabajo
	 * @param Array $data array con los datos de la OT.
	 * @return Int Id de la OT guardada.
	 */
	function guardar_agregar($data){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | guardar_agregar(".json_encode($data).")");
		$this->assetDB->insert("orden_trabajo", $data);
		$id_insert = $this->assetDB->insert_id(); 
		return $id_insert;
	}

	/**
	 * guarda responsable en modalejecutar ot al ser 
	 * seleccionado en tabla Orden_trabajo
	 * 
	 */
	function updateResponsables($id_orden, $id_responsable){
		$this->assetDB->set('orden_trabajo.id_usuario_a', $id_responsable);
		$this->assetDB->where('orden_trabajo.id_orden', $id_orden);
		$response = $this->assetDB->update('orden_trabajo');
		return $response;
	}
	/**
	 * guarda responsable en modalejecutar ot al ser 
	 * seleccionado en tabla Orden_trabajo
	 * 
	 */
	function updateTarea($id_orden, $idTarea, $tarea){
		
	
		$this->assetDB->where('orden_trabajo.id_orden', $id_orden);
		$response = $this->assetDB->update('orden_trabajo', array('id_tarea'=>$idTarea,
		'descripcion'=>$tarea));
		return $response;
	}


	/**
	 * Guarda el Case id generado por bonita en la OT
	 * @param Integer $case_id id de case; @param Integer $id id de OT
	 * @return Bool true si se actualizo, false si no.
	 */
	function setCaseidenOTNueva($case_id, $id){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | setCaseidenOTNueva(case_id: $case_id |id_ot : $id");
		$this->assetDB->where('orden_trabajo.id_orden', $id);
		return $this->assetDB->update('orden_trabajo', array('case_id'=>$case_id));			
	}
	//////////////		EDICION 	//////////////////
		/**
		 * Devuelve valores de la OT con id_orden = $id.
		 * @param Integer Id de Orden de Trabajo.
		 * @return Array datos de la OT
		 */
		function getpencil($id) {
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getpencil(id OT: $id");
			$this->assetDB->select('orden_trabajo.id_orden,
												orden_trabajo.id_tarea,												
												orden_trabajo.nro,
												orden_trabajo.fecha_program,
												orden_trabajo.fecha_inicio,
												orden_trabajo.fecha_terminada,
												orden_trabajo.descripcion AS tareadescrip,
												orden_trabajo.estado,
												orden_trabajo.id_usuario,
												orden_trabajo.id_usuario_a,
												orden_trabajo.id_usuario,
												orden_trabajo.id_sucursal,
												admcustomers.cliRazonSocial AS nomCli,
												sucursal.descripc,											
												abmproveedores.provnombre,
												abmproveedores.provid,
												equipos.id_equipo,
												equipos.fecha_ingreso,
												
												equipos.ubicacion,
												equipos.descripcion AS equipodescrip,
												equipos.codigo,
												marcasequipos.marcadescrip AS marca');
			$this->assetDB->from('orden_trabajo');		
			$this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
			$this->assetDB->join('sucursal', 'sucursal.id_sucursal = orden_trabajo.id_sucursal', 'left');
			$this->assetDB->join('marcasequipos', 'equipos.marca = marcasequipos.marcaid');
			//$this->assetDB->join('sisusers', 'sisusers.usrId=orden_trabajo.id_usuario');
			$this->assetDB->join('abmproveedores', 'abmproveedores.provid=orden_trabajo.id_proveedor', 'left');
			$this->assetDB->join('admcustomers','admcustomers.cliId = equipos.id_customer');
			$this->assetDB->where('orden_trabajo.id_orden', $id);
			$query = $this->assetDB->get();
			
			if( $query->num_rows() > 0)
			{
				return $query->result_array();
			}
			else {
				return 0;
			}
		}
		/**
		 * Trae herramientas por id de preventivo para Editar
		 *  @param Integer $idp id de la orden de trabajo
		 *  @return Array herramientas de la OT
		*/
		function getOTHerramientas($id){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTHerramientas(id OT: $id");	
			$empId = empresa(); 

			$this->assetDB->select('tbl_otherramientas.cantidad,
													herramientas.herrcodigo,
													herramientas.herrmarca,
													herramientas.herrdescrip,
													herramientas.herrId');
			$this->assetDB->from('tbl_otherramientas');
			$this->assetDB->join('herramientas', 'herramientas.herrId = tbl_otherramientas.herrId');   
			$this->assetDB->where('tbl_otherramientas.otId', $id);        
			$this->assetDB->where('tbl_otherramientas.id_empresa', $empId);
			$query= $this->assetDB->get();

			if( $query->num_rows() > 0){
				return $query->result_array();
			}else{
				return 0;
			}
		}
		/**
		 * Trae insumos por id de preventivo para Editar
		 *  @param Integer $idp id de la orden de trabajo
		 *  @return Array insumos de la OT
		*/
		function getOTInsumos($id){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTHerramientas(id OT: $id");	
			$empId = empresa(); 

			$this->assetDB->select('tbl_otinsumos.id,
													tbl_otinsumos.cantidad,
													articles.artBarCode,
													articles.artId,
													articles.artDescription,
													articles.id_empresa');                            
			$this->assetDB->from('tbl_otinsumos');
			$this->assetDB->join('articles', 'articles.artId = tbl_otinsumos.artId');   
			$this->assetDB->where('tbl_otinsumos.otId', $id);        
			$this->assetDB->where('articles.id_empresa', $empId);
			$query= $this->assetDB->get(); 

			if( $query->num_rows() > 0){
				return $query->result_array();
			}else {
				return 0;
			}
		}
		/**
		 * 	Trae adjuntos de OT por id
		 *  @param Integer $idp id de la orden de trabajo
		 *  @return Array insumos de la OT
		*/
		function getOTadjuntos($id){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOTadjuntos(id OT: $id");	
			$this->assetDB->select('tbl_otadjuntos.*');
			$this->assetDB->from('tbl_otadjuntos');
			$this->assetDB->where('tbl_otadjuntos.otId', $id);
			$query = $this->assetDB->get();

			if( $query->num_rows() > 0)
			{
				return $query->result_array();
			}
			else {
				return 0;
			}
		}
		/**
		 * Guarda le edicion de una OT (actualiza OT).
		 *
		 * @param   Int     $idequipo   Id de equipo.
		 * @param   Array   $data       Arreglo con los datos a editar del equipo $idequipo.
		 */
		function update_edita($id,$data) // Ok
		{
				$this->assetDB->where('id_orden', $id);
				$query = $this->assetDB->update("orden_trabajo",$data);
				return $query;
		}
		/**
		 * guarda adjunto en Edicion y en OT nueva
		 * @param String $adjunto nombre codificado del adjunto.
		 * @return Bool  true/false segun resultado de la operacion.
		 */
		function setAdjunto($adjunto){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | setAdjunto(nombre: $adjunto");
			$query = $this->assetDB->insert("tbl_otadjuntos", $adjunto);
			return $query;
		}
		// Elimina adjunto por id 
		function eliminarAdjunto($id){
			$this->assetDB->where('id', $id);
			$query = $this->assetDB->delete('tbl_otadjuntos');
			return $query;
		}

		// Delete herramientas 
		function deleteHerramOT($id){        
			$this->assetDB->where('otId', $id);
			$query = $this->assetDB->delete('tbl_otherramientas');
			return $query;
		}
		/**
		*Guarda el batch de datos de herramientas cargados en la OT
		* @param Array $herram datos de las herramientas
		* @return Int/Bool id de la cantidad de herramientas guardadas o false si no se guardo.
		*/
		function insertOTHerram($herram){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | insertOTHerram(". json_encode($herram).")");
			$query = $this->assetDB->insert_batch("tbl_otherramientas",$herram);
			return $query;
		}
		// Delete insumos
		function deleteInsumOT($id){
			$this->assetDB->where('otId', $id);
			$query = $this->assetDB->delete('tbl_otinsumos');
			return $query;
		}
		/**
		*Guarda el batch de datos de insumos cargados en la OT
		* @param Array $insumo datos de los insumos
		* @return Int/Bool id de la cantidad de insumos guardados o false si no se guardo.
		*/
		// Guarda el bacht de insumos 
		function insertOTInsum($insumo){
			log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | insertOTInsum(". json_encode($insumo).")");
			$query = $this->assetDB->insert_batch("tbl_otinsumos",$insumo);
			return $query;
		}

	//////////////		FIN EDICION 	//////////////////


	/**
	 * Devuelve el listado de tareas asociadas a una OT.
	 *
	 * @param   Int     $idglob     Id de Orden de Trabajo.
	 * @return  Array               Listado de Tareas.
	 */
	function cargartareas($idglob) // Ok
	{
			//$userdata = $this->session->userdata('user_data');
			//$empId    = $userdata[0]['id_empresa'];
			$this->assetDB->select('*');
			$this->assetDB->from('tbl_listarea');
			$this->assetDB->join('sisusers', 'sisusers.usrId = tbl_listarea.id_usuario', 'left outer');
			$this->assetDB->where('tbl_listarea.id_orden',$idglob);
			//$this->assetDB->where('tbl_listarea.id_empresa',$empId);
			$this->assetDB->group_by('tbl_listarea.id_listarea');
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
	 * Trae datos de orden de trabajo por id y empresa logueada para asignar.
	 *
	 * @param   Int     $id     Id de orden de trabajo.
	 *
	 * @return  Array|false     Arreglo con los datos de la orden de trabajo
	 */
	function getasigna($id) // Ok
	{
			$userdata = $this->session->userdata('user_data');
			$empId = $userdata[0]['id_empresa'];

			$this->assetDB->select('orden_trabajo.id_orden, orden_trabajo.nro, orden_trabajo.id_usuario,
					orden_trabajo.fecha_inicio,
					orden_trabajo.fecha_entrega,
					orden_trabajo.descripcion,
					equipos.id_equipo,
					equipos.codigo,
					equipos.descripcion as equipoDescrip');
			$this->assetDB->from('orden_trabajo');
			$this->assetDB->join('equipos','equipos.id_equipo=orden_trabajo.id_equipo');
			$this->assetDB->where('orden_trabajo.id_empresa', $empId);//no hace falta. Es redundante
			$this->assetDB->where('orden_trabajo.id_orden', $id);
			$query = $this->assetDB->get();

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
	 * Trae usuarios por id de empresa logueada
	 *
	 * @return  Array|false     Arreglo con usuarios de la empresa.
	 */
	function getusuario() // Ok
	{
			$userdata = $this->session->userdata('user_data');
			$empId    = $userdata[0]['id_empresa'];
			$this->assetDB->select('*');
			$this->assetDB->from('sisusers');
			$this->assetDB->join('usuarioasempresa', 'usuarioasempresa.usrId = sisusers.usrId');
			$this->assetDB->where('usuarioasempresa.empresaid', $empId);
			$query = $this->assetDB->get();
			if ($query->num_rows()!=0)
			{
					$i = 0;
					foreach ($query->result() as $row)
					{
							$data[$i]["usrId"]       = $row->usrId;
							$data[$i]["usrName"]     = $row->usrName;
							$data[$i]["usrLastName"] = $row->usrLastName;
							$i++;
					}
					return $data;
			}
	}
	/**
	 * trae detalle de nota de pedido (hg)
	 *
	 * @return  Array|false     Arreglo con detallle de la nota de pedido por id de OT
	 */
	function getdatos($id_OT){ //id_trabajo
		
		$this->assetDB->select('tbl_detanotapedido.id_detaNota,
											tbl_detanotapedido.id_notaPedido,
											tbl_detanotapedido.artId,
											tbl_detanotapedido.cantidad,
											tbl_detanotapedido.provid,
											tbl_detanotapedido.fechaEntrega,
											tbl_detanotapedido.fechaEntregado,
											tbl_detanotapedido.remito,
											tbl_detanotapedido.estado,
											articles.artDescription,
											articles.artBarCode,
											abmproveedores.provnombre,
											tbl_notapedido.id_ordTrabajo,
											tbl_notapedido.fecha');
		$this->assetDB->from('tbl_detanotapedido');
		$this->assetDB->join('tbl_notapedido', 'tbl_detanotapedido.id_notaPedido = tbl_notapedido.id_notaPedido');
		$this->assetDB->join('articles', 'articles.artId = tbl_detanotapedido.artId');
		$this->assetDB->join('abmproveedores', 'abmproveedores.provid = tbl_detanotapedido.provid');
		$this->assetDB->where('tbl_notapedido.id_ordTrabajo', $id_OT);
		$query = $this->assetDB->get();
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return false;
		}		
	}
    function traer_cli()
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->select('admcustomers.*');
        $this->assetDB->from('admcustomers');
        $this->assetDB->where('admcustomers.id_empresa', $empId);
        $this->assetDB->where('admcustomers.estado', 'C');
        $query = $this->assetDB->get();   
        if( $query->num_rows() > 0)
        {
          return $query->result_array();    
        } 
        else {
          return false;
        }
    }

	function getprint($id){ //JOIN grupo ON grupo.id_grupo=equipos.id_grupo
	    $sql="SELECT orden_trabajo.id_orden, orden_trabajo.id_tarea, orden_trabajo.nro, orden_trabajo.fecha, orden_trabajo.fecha_program, orden_trabajo.fecha_inicio, orden_trabajo.fecha_entrega, orden_trabajo.fecha_terminada, orden_trabajo.fecha_aviso, orden_trabajo.fecha_entregada, orden_trabajo.descripcion, orden_trabajo.estado, orden_trabajo.id_usuario, orden_trabajo.id_usuario_a, orden_trabajo.id_solicitud, orden_trabajo.tipo, orden_trabajo.id_equipo, orden_trabajo.duracion, orden_trabajo.id_tareapadre, tareas.descripcion AS detarea, orden_trabajo.id_equipo, equipos.codigo, equipos.descripcion AS deequipos, user1.usrName AS nombre,user1.usrLastName, sisgroups.grpId AS grp, sisusers.usrName, sisusers.usrLastName, sisgroups.grpId, tbl_tipoordentrabajo.id
	    	FROM orden_trabajo
	    	JOIN tareas ON tareas.id_tarea=orden_trabajo.id_tarea
	    	JOIN tbl_tipoordentrabajo ON tbl_tipoordentrabajo.id=orden_trabajo.tipo
	    	JOIN equipos ON equipos.id_equipo=orden_trabajo.id_equipo
	    	JOIN sisusers ON sisusers.usrId = orden_trabajo.id_usuario
			join sisusers AS user1 ON user1.usrId = orden_trabajo.id_usuario_a
			JOIN usuarioasempresa ON usuarioasempresa.usrId = user1.usrId
			join sisgroups ON sisgroups.grpId = usuarioasempresa.grpId
	    	  WHERE equipos.estado !='AN' AND orden_trabajo.id_orden=$id AND usuarioasempresa.tipo = 1";

	    $query= $this->assetDB->query($sql);

	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();
	    }
	    else {
	      return 0;
	    }
	}
	
	function getotrabajos($data = null){

		if($data == null)
		{
			return false;
		}
		else
		{
			$action = $data['act'];
			$otid = $data['id'];
			$data = array();
			$query= $this->assetDB->get_where('orden_trabajo',array('id_orden'=>$otid));

			if ($query->num_rows() != 0)
			{
				
				$f = $query->result_array();
				$data['ot'] = $f[0];
			} else {
				$ot = array();
				$ot['nro'] = '';
				$ot['fecha_inicio'] = '';
				$ot['fecha_fecha_entrega'] = '';
				$ot['descripcion'] = '';
				$ot['cliId'] = '';
				$ot['estado'] = '';
				$ot['id_usuario'] = '';
				$ot['id_sucursal'] = '';
				$data['ot'] = $ot;
			}

			//Readonly
			$readonly = false;
			if($action == 'Del' || $action == 'View'){
				$readonly = true;
			}
			$data['read'] = $readonly;
				$query= $this->assetDB->get('sucursal');
			if ($query->num_rows() != 0)
			{
				$data['sucursal'] = $query->result_array();	
			}

				$query= $this->assetDB->get('admcustomers');
			if ($query->num_rows() != 0)
			{
				$data['clientes'] = $query->result_array();	
			}
			

			return $data;
		}
	}
	
	function setotrabajos($data = null){

		if($data == null)
		{
			return false;
		}
		else
		{
			$id = $data['id'];
			$nro = $data['nro'];
			$fech = $data['fech'];
			$deta = $data['deta'];
			$sucid = $data['sucid'];
			$act = $data['act'];
			$cli=$data['cli'];
			$usu=1;
			$estado='C';

			$data = array(
					   'nro' => $nro,
					    'fecha_inicio' => $fech,
					    'descripcion' => $deta,
					    'id_sucursal' => $sucid,
					     'cliId' => $cli,
					     'id_usuario' => $usu,
					     'id_usuario_a' => $usu,
					      'id_usuario_e' => $usu,
					      'estado' => $estado
					   
					);

			switch($act)
			{
				case 'Add':
					//Agregar familia
					if($this->assetDB->insert('orden_trabajo', $data) == false) {
						return false;
					}
					break;

				case 'Edit':
					//Actualizar nombre
					if($this->assetDB->update('orden_trabajo', $data, array('id_orden'=>$id)) == false) {
						return false;
					}
					break;

				case 'Del':
					//Eliminar familia
					if($this->assetDB->delete('orden_trabajo', $data, array('id_orden'=>$id)) == false) {
						return false;
					}
					
					break;
			}

			return true;

		}
	}

	//pediso a entregar x fecha
	function getpedidos($id){

	    $sql="SELECT * 
	    	  FROM orden_trabajo
	    	  JOIN admcustomers ON admcustomers.cliId= orden_trabajo.cliId
	    	  WHERE id_orden=$id
	    	  ";
	    
	    $query= $this->assetDB->query($sql);  
	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();	
	    } 
	    else {
	      return 0;
	    }
	}

	function getcliente($data = null){

		if($data == null)
		{
			return false;
		}
		else{
			
			$idcli = $data['idcliente'];
			//Datos del usuario
			$query= $this->assetDB->get_where('admcustomers',array('cliId'=>$idcli));
			if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
			
		}
	}

	function getnums($id){

		$query= "SELECT id_orden
	        FROM orden_pedido
	        WHERE nro_trabajo=$id";

		$query= $this->assetDB->query($sql);

		if($query!=""){

			foreach ($query->result_array() as $row){		
			$data = $row['id_orden'];
			}
		return $data;
		}
		else
			{
			return 0;
			}
	}


	function agregar_usuario($data){
                   
        $query = $this->assetDB->insert("sisusers",$data);
    	return $query;
    }


    function agregar_pedidos($data){          
        $query = $this->assetDB->insert("orden_pedido",$data);
    	return $query;
    }
        

    function update_guardar($id, $datos){
        $this->assetDB->where('id_orden', $id);
        $query = $this->assetDB->update("orden_trabajo",$datos);
        return $query;
    }

    
    function update_ordtrab($id, $datos){
        $this->assetDB->where('id_orden', $id);
        $query = $this->assetDB->update("orden_trabajo",$datos);
        return $query;
    }
		
	// seleccionar el grupo
	function getgrupo(){
       $query= $this->assetDB->get_where('sisgroups');
		if($query->num_rows()>0){
			 return $query->result();
        }
        else{
            return false;
        }
	}

	//insertar pedido 
	function insert_pedido($data)
    {
        $query = $this->assetDB->insert("orden_pedido",$data);
        return $query;
    }

    function agregar_tareas($data) {
        $query = $this->assetDB->insert("tbl_listarea",$data);
        return $query;
    }

	function get_pedido($id){
		$query= "SELECT *
				 FROM orden_pedido 
				 WHERE id_orden=$id";

        $result = $this->assetDB->query($query);
		if($result->num_rows()>0){
            return $result->result_array();
        }
        else{
            return false;
        }
	}
	
    //agrega proveedor
	function agregar_proveedor($data){
        $query = $this->assetDB->insert("proveedores", $data);
    	return $query;
    }

	
	  
	function eliminacion($data){
       	$this->assetDB->where('id_orden', $data);
		$query =$this->assetDB->delete('orden_trabajo');
        return $query;
    }

    function update_cambio($ido,$fecha){
    	$consulta= "UPDATE orden_trabajo SET estado='T',
    										fecha_terminada='$fecha'
                               
				WHERE id_orden=$ido" ;

		$query= $this->assetDB->query($consulta);
        
		return $query;
    }

	function getArticulos(){
		$sql= "SELECT *
			FROM sisusers
			
			"; //
			$query= $this->assetDB->query($sql);

        //$query = $this->assetDB->query("SELECT `artId`, `artBarCode` FROM `articles`");
        $i=0;
        foreach ($query->result() as $row)
        {	
        	$insumos[$i]['label'] = $row->usrName;
            $insumos[$i]['value'] = $row->usrId;
            
            
            $i++;
        }
        return $insumos;
    }

    function EliminarTareas($idor,$data){
        $this->assetDB->where('id_listarea', $idor);
        $query = $this->assetDB->update("tbl_listarea",$data);
        return $query;
    }

    function ModificarUsuarios($idta,$datos){
        $this->assetDB->where('id_listarea', $idta);
        $query = $this->assetDB->update("tbl_listarea",$datos);
        return $query;
    }

    function TareaRealizadas($id, $datos){
        $this->assetDB->where('id_listarea', $id);
        $query = $this->assetDB->update("tbl_listarea",$datos);
        return $query;
    }

    function ModificarFechas($idta,$datos){
        $this->assetDB->where('id_listarea', $idta);
        $query = $this->assetDB->update("tbl_listarea",$datos);
        return $query;
    }
    
    function CambioParcials($idor,$datos){
        $this->assetDB->where('id_orden', $idor);
        $query = $this->assetDB->update("orden_trabajo",$datos);
        return $query;
    }

    function agregar_pedidos_fecha($fecha,$id){
        $this->assetDB->where('id_orden', $id);
        $query = $this->assetDB->update("orden_pedido",$fecha);
        return $query;
    }

    function update_predictivo($data, $id){
	    $this->assetDB->where('id_orden', $id);
	    $query = $this->assetDB->update("orden_trabajo",$data);
	    return $query;
	}
	
    /**
     * Cuenta la cantidad de ordenes de trabajo agrupadas por tipo.
     *
     * @return Void|Array     Cantidad de ordenes de trabajo.
     */
    function kpiCantTipoOrdenTrabajo()
    {
		$empresaId = empresa();

        $sql = "SELECT tt.descripcion as descripcion, count(*) as cantidad 
		        FROM orden_trabajo ot
                JOIN tbl_tipoordentrabajo tt  on ot.tipo = tt.tipo_orden
                WHERE ot.id_empresa =".$empresaId."
                AND (ot.estado  = 'CE' or estado = 'CN')
                GROUP BY ot.tipo
                ORDER BY ot.tipo";

                $query = $this->assetDB->query($sql);
                $res = $query->result();

		//log_message('DEBUG','#Main/index | kpiCantTipoOrdenTrabajo >> data '.json_encode($query));

        if($query->num_rows()!=0)
        {
            return $res;
        }
        else
        {
            return false;
        }
    }

    function getEquipoDisponibilidad() // Ok
    {
    	$userdata = $this->session->userdata('user_data');
    	$empId    = $userdata[0]['id_empresa'];

    	$this->assetDB->select('equipos.id_equipo AS value, equipos.codigo AS label');
    	$this->assetDB->from('equipos');
    	$this->assetDB->where('id_empresa', $empId);
    	$this->assetDB->where('estado !=', 'AN');
    	$query = $this->assetDB->get();

    	if($query->num_rows()>0)
    	{
    		return $query->result();
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * Devuelve valores de todos los datos de la OT para mostrar en modal.
	 * @param Integer $idot id de la orden de trabajo
	 * @return array datos de la OT
	*/
    function getOrigenOt($idot){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getOrigenOt($idot)");
    	$this->assetDB->select('orden_trabajo.tipo, orden_trabajo.id_solicitud');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_orden', $idot);

        $query = $this->assetDB->get();
        if($query->num_rows()!=0){
            return $query->result();
        }else{
            return false;
        }
    }



    //devuelve valores de todos los datos de la OT para mostrar en modal.
    function getViewDataOt($idOt)
    {
    	$this->assetDB->select('orden_trabajo.id_orden, orden_trabajo.nro, orden_trabajo.descripcion AS descripcionFalla, orden_trabajo.fecha_inicio, orden_trabajo.fecha_entrega, 
    		orden_trabajo.fecha_program, tbl_estado.descripcion AS estado, sisusers.usrName, sisusers.usrLastName, 
    		orden_trabajo.tipo, orden_trabajo.id_solicitud,
    		sucursal.id_sucursal, sucursal.descripc,
    		abmproveedores.provid, abmproveedores.provnombre,
    		equipos.codigo, equipos.fecha_ingreso, equipos.marca, equipos.ubicacion, equipos.descripcion AS descripcionEquipo');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->join('sisusers', 'sisusers.usrId = orden_trabajo.id_usuario_a');
        $this->assetDB->join('sucursal', 'sucursal.id_sucursal = orden_trabajo.id_sucursal','left');
        $this->assetDB->join('abmproveedores', 'abmproveedores.provid = orden_trabajo.id_proveedor','left');
        $this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
        $this->assetDB->join('tbl_estado', 'tbl_estado.estado = orden_trabajo.estado');
        $this->assetDB->where('orden_trabajo.id_orden', $idOt);

        $query = $this->assetDB->get();
        if($query->num_rows()!=0)
        {
            $datos = $query->result_array();
        }
        else
        {
            return false;
        }
    }

    //devuelve valores de todos los datos de la OT para mostrar en modal.
    function getViewDataSolServicio($idOt, $idSolicitud=null)
    {
			$this->assetDB->select('orden_trabajo.id_orden, 
				orden_trabajo.nro, orden_trabajo.descripcion AS descripcionFalla, 
				orden_trabajo.fecha_inicio,orden_trabajo.fecha_program, orden_trabajo.fecha_terminada, orden_trabajo.estado, 
				sisusers.usrName, sisusers.usrLastName, 
				orden_trabajo.tipo, orden_trabajo.id_solicitud,				
				sucursal.id_sucursal, sucursal.descripc,
				admcustomers.cliRazonSocial AS nomCli,
    			abmproveedores.provid, abmproveedores.provnombre,
				equipos.codigo, equipos.fecha_ingreso, equipos.ubicacion, equipos.descripcion AS descripcionEquipo,
				marcasequipos.marcadescrip AS marca,
				grupo.descripcion AS grupodescrip, grupo.id_grupo');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->join('sisusers', 'orden_trabajo.id_usuario_a = sisusers.usrId', 'left');
        $this->assetDB->join('sucursal', ' orden_trabajo.id_sucursal = sucursal.id_sucursal ', 'left');
        $this->assetDB->join('abmproveedores', 'orden_trabajo.id_proveedor = abmproveedores.provid', 'left');
		$this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
		$this->assetDB->join('admcustomers','admcustomers.cliId = equipos.id_customer');
		$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
		$this->assetDB->join('grupo', 'grupo.id_grupo = equipos.id_grupo');
		$this->assetDB->where('orden_trabajo.id_orden', $idOt);

				$query = $this->assetDB->get();

        if($query->num_rows()!=0)
        {
            $datos = $query->result_array();
            //dump_exit($datos);
            $datos[0]['solServicio'] = $this->getViewDataInfoSolServicio( $datos[0]['id_solicitud']);
            return $datos;
        }
        else
        {
            return false;
        }
    }

    	//
    	function getViewDataInfoSolServicio($id_solicitud)
	    {
	    
				$this->assetDB->select('sector.descripcion AS sector, 			
													solicitud_reparacion.solicitante, 
													solicitud_reparacion.f_sugerido AS fechaSugerida, 
													solicitud_reparacion.hora_sug AS horarioSugerido, 										
													solicitud_reparacion.causa AS falla');
				$this->assetDB->from('solicitud_reparacion');
				$this->assetDB->join('equipos', 'solicitud_reparacion.id_equipo = equipos.id_equipo');
				$this->assetDB->join('sector', 'equipos.id_sector = sector.id_sector');
				$this->assetDB->where('solicitud_reparacion.id_solicitud', $id_solicitud);

				$query = $this->assetDB->get();
				if($query->num_rows()!=0)
				{
						$solServicio = $query->result_array();
						return $solServicio[0];
				}
				else
				{
						return null;
				}
	    }



    //devuelve valores de todos los datos de la OT para mostrar en modal.
    function getViewDataPreventivo($idOt, $idSolicitud=null)
    {
			$this->assetDB->select('orden_trabajo.id_orden, 
												orden_trabajo.nro, 
												orden_trabajo.descripcion AS descripcionFalla, 
												orden_trabajo.fecha_inicio, 
												orden_trabajo.fecha_terminada, 
												orden_trabajo.fecha_program, 
												orden_trabajo.estado, 
												sisusers.usrName, sisusers.usrLastName, 
												orden_trabajo.tipo, 
												orden_trabajo.id_solicitud,
												admcustomers.cliRazonSocial AS nomCli,
												sucursal.id_sucursal, 
												sucursal.descripc,
												equipos.codigo, 
												equipos.fecha_ingreso, 												 
												equipos.ubicacion, 
												equipos.descripcion AS descripcionEquipo,
												marcasequipos.marcadescrip AS marca');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->join('sisusers', 'orden_trabajo.id_usuario_a  = sisusers.usrId', 'left');
        $this->assetDB->join('sucursal', 'orden_trabajo.id_sucursal = sucursal.id_sucursal', 'left');
        $this->assetDB->join('abmproveedores', 'orden_trabajo.id_proveedor = abmproveedores.provid', 'left');
        $this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
		$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
		$this->assetDB->join('admcustomers','admcustomers.cliId = equipos.id_customer');
        $this->assetDB->where('orden_trabajo.id_orden', $idOt);
				$query = $this->assetDB->get();
        if($query->num_rows()!=0)
        {
            $datos = $query->result_array();
            //dump_exit($datos);
            $datos[0]['tarea'] = $this->getViewDataTareaPreventivo( $datos[0]['id_solicitud']);
            return $datos;
        }
        else
        {
            return false;
        }
    }
    
	    function getViewDataTareaPreventivo($id_solicitud)
	    {
	    	$this->assetDB->select('preventivo.prevId, preventivo.ultimo, preventivo.cantidad AS frecuencia, preventivo.lectura_base, preventivo.critico1 AS alerta, preventivo.prev_duracion, preventivo.prev_canth, preventivo.prev_adjunto,
	    		tareas.descripcion AS tareadescrip,
	    		unidad_tiempo.unidaddescrip AS perido,
	    		componentes.descripcion AS descripComponente');
	        $this->assetDB->from('preventivo');
	        $this->assetDB->where('preventivo.prevId', $id_solicitud);
	        $this->assetDB->join('tareas', 'tareas.id_tarea = preventivo.id_tarea');
	        $this->assetDB->join('unidad_tiempo', 'unidad_tiempo.id_unidad = preventivo.id_unidad');
	        $this->assetDB->join('componentes', 'componentes.id_componente = preventivo.id_componente');
	        $query = $this->assetDB->get();
	        if($query->num_rows()!=0)
	        {
	            $preventivos = $query->result_array();
	            for ($i=0; $i < sizeof($preventivos) ; $i++) { 
		            $herramientas = null;
		            $insumos      = null;

		            $herramientas[$i] = $this->getPreventivoHerramientas( $preventivos[$i]['prevId'] );
		            $insumos[$i]      = $this->getPreventivoInsumos( $preventivos[$i]['prevId'] );
	            }
	            $preventivos[0]['herramientas'] = $herramientas;
	            $preventivos[0]['insumos']  = $insumos;

	            return $preventivos[0];
	        }
	        else
	        {
	            return null;
	        }
	    }

		    // Trae herramientas ppor id de preventivo para Editar
		    function getPreventivoHerramientas($id)
		    {
		        $this->assetDB->select('tbl_preventivoherramientas.cantidad,
		                            herramientas.herrcodigo,
		                            herramientas.herrmarca,
		                            herramientas.herrdescrip,
		                            herramientas.herrId');
		        $this->assetDB->from('tbl_preventivoherramientas');
		        $this->assetDB->join('herramientas', 'herramientas.herrId = tbl_preventivoherramientas.herrId');   
		        $this->assetDB->where('tbl_preventivoherramientas.prevId', $id);   
		             
		        $query = $this->assetDB->get();
		        if( $query->num_rows() > 0)
		        {
		        	$herramientas = $query->result_array();
		          	return $herramientas;
		        }
		        else {
		          return 0;
		        }
		    }

		    // Trae insumos por id de preventivo para Editar
		    function getPreventivoInsumos($id)
		    {    
		        $this->assetDB->select('tbl_preventivoinsumos.id,
		                            tbl_preventivoinsumos.cantidad,
		                            articles.artBarCode,
		                            articles.artId,
		                            articles.artDescription,
		                            articles.id_empresa');                            
		        $this->assetDB->from('tbl_preventivoinsumos');
		        $this->assetDB->join('articles', 'articles.artId = tbl_preventivoinsumos.artId');   
		        $this->assetDB->where('tbl_preventivoinsumos.prevId', $id);        
		        $query= $this->assetDB->get(); 

		        if( $query->num_rows() > 0)
		        {
		          	$insumos = $query->result_array();
		          	return $insumos;
		        }
		        else {
		          return 0;
		        }
		    }



	//devuelve valores de todos los datos de la OT para mostrar en modal.
    function getViewDataBacklog($idOt, $idSolicitud)
    {
			$this->assetDB->select('orden_trabajo.id_orden, orden_trabajo.nro,
												orden_trabajo.estado,
												orden_trabajo.descripcion AS descripcionFalla, 
												orden_trabajo.fecha_inicio, 
												orden_trabajo.fecha_program,
												orden_trabajo.fecha_terminada, 
												sisusers.usrName, sisusers.usrLastName, 
												orden_trabajo.tipo, orden_trabajo.id_solicitud,
												sucursal.id_sucursal, sucursal.descripc,
												abmproveedores.provid, abmproveedores.provnombre,
												equipos.codigo, equipos.fecha_ingreso, marcasequipos.marcadescrip AS marca,
												equipos.ubicacion, equipos.descripcion AS descripcionEquipo');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->join('sisusers', 'orden_trabajo.id_usuario_a = sisusers.usrId', 'left');
        $this->assetDB->join('sucursal', 'orden_trabajo.id_sucursal =  sucursal.id_sucursal', 'left');
        $this->assetDB->join('abmproveedores', 'orden_trabajo.id_proveedor = abmproveedores.provid', 'left');
        $this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
      	$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
        $this->assetDB->where('orden_trabajo.id_orden', $idOt);

        $query = $this->assetDB->get();
				
				if($query->num_rows()!=0)
        {
            $datos = $query->result_array();
            $datos[0]['tarea'] = $this->getViewDataTareaBacklog( $datos[0]['id_solicitud']);
            //dump_exit($datos);
            return $datos;
        }
        else
        {
            return false;
        }
    }

		function getViewDataTareaBacklog($idBacklog){
			$this->assetDB->select('tbl_back.fecha,
												tbl_back.id_tarea,	 
												tbl_back.back_duracion, 
												tbl_back.tarea_opcional,
												tbl_back.idcomponenteequipo,
												tareas.descripcion AS tareadescrip');
			$this->assetDB->from('tbl_back');
			$this->assetDB->join('tareas', 'tbl_back.id_tarea = tareas.id_tarea', 'left');
			$this->assetDB->where('tbl_back.backId', $idBacklog);
			$query = $this->assetDB->get();
			//$dato = $this->assetDB->last_query();
			//dump($dato, 'backlog info: ');
				if($query->num_rows()!=0)
				{
						$tarea = $query->result_array();
						$tarea[0]['compEquipo'] = $this->getViewDataComponenteEquipoBacklog( $tarea[0]['idcomponenteequipo']);
						return $tarea[0];
				}
				else
				{
						return null;
				}
	  }
	/**
	* Devuelve los datos de la OT para mostrar en modal.
	* @param 
	* @return array datos de la OT
	*/
	function getLecturasOrden($id_ot){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | getLecturasOrden(id_ot : $id_ot");
		$this->assetDB->select('orden_trabajo.fecha_inicio,
							orden_trabajo.fecha_terminada');
		$this->assetDB->from('orden_trabajo');
		$this->assetDB->where('orden_trabajo.id_orden', $id_ot);
		$query = $this->assetDB->get();

		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{   
			return false;
		}   
	}

		function getViewDataComponenteEquipoBacklog($idComponenteEquipo)
		{
			$this->assetDB->select('componenteequipo.codigo AS codigoComponente, 
				componentes.descripcion AS descripComponente,
				sistema.descripcion AS descripSistema');
				$this->assetDB->from('componenteequipo');
				$this->assetDB->join('componentes', 'componentes.id_componente = componenteequipo.id_componente');
				$this->assetDB->join('sistema', 'sistema.sistemaid = componenteequipo.sistemaid');
				$this->assetDB->where('componenteequipo.idcomponenteequipo', $idComponenteEquipo);

				$query = $this->assetDB->get();
				if($query->num_rows()!=0)
				{
						$compEquipo = $query->result_array();
						return $compEquipo[0];
				}
				else
				{
						return null;
				}
		}

		function getViewDataPredictivo($idOt, $idSolicitud)
		{
			$this->assetDB->select('orden_trabajo.id_orden, orden_trabajo.descripcion AS descripcionFalla, 
												orden_trabajo.fecha_inicio, orden_trabajo.fecha_terminada, 
												orden_trabajo.fecha_program, 
												orden_trabajo.tipo, orden_trabajo.id_solicitud, 
												orden_trabajo.estado,
												sisusers.usrName, 
												sisusers.usrLastName, 
												equipos.codigo, equipos.fecha_ingreso, equipos.ubicacion, 
												equipos.descripcion AS descripcionEquipo,	
												marcasequipos.marcadescrip AS marca,		
												sucursal.id_sucursal, sucursal.descripc, 
												abmproveedores.provid, abmproveedores.provnombre ');													
			$this->assetDB->from('orden_trabajo');			
			$this->assetDB->join('sucursal', 'orden_trabajo.id_sucursal = sucursal.id_sucursal','left');
			$this->assetDB->join('sisusers', 'orden_trabajo.id_usuario_a = sisusers.usrId', 'left');
			$this->assetDB->join('abmproveedores', 'orden_trabajo.id_proveedor = abmproveedores.provid', 'left');				
			$this->assetDB->join('equipos', 'equipos.id_equipo = orden_trabajo.id_equipo');
			$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');				
			$this->assetDB->where('orden_trabajo.id_orden', $idOt);
			$query = $this->assetDB->get();	
		
			if($query->num_rows()!=0)
			{
					$datos = $query->result_array();
					//dump_exit($datos);
					$datos[0]['tarea'] = $this->getViewDataTareaPredictivo( $datos[0]['id_solicitud']);
					return $datos;
			}
			else
			{
					return false;
			}
		}	
    //
    function getViewDataTareaPredictivo($id_solicitud)
    {
      $this->assetDB->select('tareas.descripcion AS tareadescrip,
        predictivo.fecha, predictivo.periodo, 
        predictivo.cantidad AS frecuencia, predictivo.pred_duracion AS duracion,
        unidad_tiempo.unidaddescrip,
        predictivo.pred_canth AS cantOperarios, predictivo.horash');
        $this->assetDB->from('predictivo');
        $this->assetDB->where('predictivo.predId', $id_solicitud);
        $this->assetDB->join('tareas', 'tareas.id_tarea = predictivo.tarea_descrip');
        $this->assetDB->join('unidad_tiempo', 'unidad_tiempo.id_unidad = predictivo.id_unidad');
        $query = $this->assetDB->get();
        if($query->num_rows()!=0)
        {
            $predictivos = $query->result_array();
            return $predictivos[0];
        }
        else
        {
            return null;
        }
		}
		   
		//Define si la OT tiene un proceso lanzado
		function getCaseIdOT($id){	
			$this->assetDB->select('orden_trabajo.case_id');
			$this->assetDB->from('orden_trabajo');			
			$this->assetDB->where('orden_trabajo.id_orden',$id);
			$query = $this->assetDB->get();
      $row = $query->row('case_id');
      return $row;  
		}

		// develve tipo de solicitud e id q dieron origen a OT
		function getDatosOrigenOT($id){
			$this->assetDB->select('orden_trabajo.id_solicitud, orden_trabajo.tipo');
			$this->assetDB->from('orden_trabajo');
			$this->assetDB->where('id_orden',$id);
			$query = $this->assetDB->get();
			return $query->result_array();
		}
		// trae id de SServicio desde backlog
		function getIdSolReparacion($id_solicitud){
			
			//$id_solicitud = 7;
			$this->assetDB->select('tbl_back.sore_id');
			$this->assetDB->from('tbl_back');
			$this->assetDB->where('backId',$id_solicitud);
			return $this->assetDB->get()->first_row()->sore_id;
			
			// $query = $this->assetDB->get();
			// $row = $query->row('sore_id');
			// return $row;;			
		}
		// trae case_id desde SServicios
		function getCaseIdenSServicios($id_solicitud){
			$this->assetDB->select('orden_trabajo.case_id');
			$this->assetDB->from('orden_trabajo');
			$this->assetDB->where('id_orden',$id_solicitud);
			$query = $this->assetDB->get();
			$row = $query->row('case_id');
			return $row;	
			// $this->assetDB->select('solicitud_reparacion.case_id');
			// $this->assetDB->from('solicitud_reparacion');
			// $this->assetDB->where('id_solicitud',$id_solicitud);
			// $query = $this->assetDB->get();
			// $row = $query->row('case_id');
			// return $row;		
		}
		//Valida si hay o no un proceso lanzado en BPM
		function validarProcesoEnOT($id){
			$this->assetDB->select('orden_trabajo.case_id');
			$this->assetDB->from('orden_trabajo');
			$this->assetDB->where('id_orden',$id);
			$result = $query->row();			
			return $result->case_id;
		}
		// guarda case_id en Otrabajo
		function setCaseidenOT($case_id, $id){
			$this->assetDB->where('orden_trabajo.id_orden', $id);
			return $this->assetDB->update('orden_trabajo', array('case_id'=>$case_id));			
		}

		// devuelve id de SServicio por Case_id
		function getIdSServicioporCaseId($caseDeBacklog){
			$this->assetDB->select('solicitud_reparacion.id_solicitud');
			$this->assetDB->from('solicitud_reparacion');
			$this->assetDB->where('solicitud_reparacion.case_id', $caseDeBacklog);			
			$query = $this->assetDB->get();
			$row = $query->row('id_solicitud');
      return $row;
		}

		// cambbia de estado la Tareas(SServ, Prevent, Predic, Back y OT)
		function cambiarEstado($id_solicitud, $estado, $tipo){		
			$f_asignacion =  date("Y-m-d H:i:s"); 				
			
			if ($tipo == 'correctivo') {
				$this->assetDB->set('estado', $estado);
				$this->assetDB->where('id_solicitud', $id_solicitud);
				$response = $this->assetDB->update('solicitud_reparacion');
			}

			if ($tipo == 'preventivo') {
				$this->assetDB->set('estadoprev', $estado);
				$this->assetDB->where('prevId', $id_solicitud);
				$response = $this->assetDB->update('preventivo');
			}

			if ($tipo == 'backlog') {
				$this->assetDB->set('estado', $estado);
				$this->assetDB->where('backId', $id_solicitud);
				$response = $this->assetDB->update('tbl_back');
			}			

			if ($tipo == 'predictivo') {
				$this->assetDB->set('estado', $estado);
				$this->assetDB->where('predId', $id_solicitud);
				$response = $this->assetDB->update('predictivo');
			}	
			
			if ($tipo == 'OT') {
				$this->assetDB->set('estado',$estado);
				$this->assetDB->set('f_asignacion', $f_asignacion);
				$this->assetDB->where('id_orden',$id_solicitud);
        		return $this->assetDB->update('orden_trabajo');
			}

			return $response;
		}

		// Actualiza tareas en OT
		function updOT($ot, $datos){
			$this->assetDB->where('orden_trabajo.id_orden', $ot);
			return $this->assetDB->update('orden_trabajo', $datos);	
		} 

		function obtenerOT($ot){
			$this->assetDB->where('orden_trabajo.id_orden', $ot);
			return $this->assetDB->get('orden_trabajo')->first_row();	
		} 
		// Obtener OT dado un CaseID
		function ObtenerOTporCaseId($case_id)
		{
			$this->assetDB->where('orden_trabajo.case_id', $case_id);
			$query = $this->assetDB->get('orden_trabajo');
			return $query->row('id_orden');
		}

		function guardarPosicion($ot, $lat, $lon){
			$this->assetDB->where('id_orden', $ot);
			$this->assetDB->set('latitud', $lat);
			$this->assetDB->set('longitud', $lon);
			return $this->assetDB->update('orden_trabajo');
		}
	/**
	 * Elimina una OT por su ID
	 * @param Integer $id id de la orden de trabajo
	 * @return Bool true/false segun resultado de la operacion
	*/
	public function eliminar($id){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | eliminar(ID ot: $id)");
		$this->assetDB->where('id_orden', $id);
		return $this->assetDB->delete('orden_trabajo');
	}

	public function filtrarListado($data, $tipo){
		log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Otrabajos | filtrarListado(Data:".json_encode($data)." Tipo: $tipo)");
		$empId    = empresa();
		$this->assetDB->select('orden_trabajo.*, tareas.descripcion as tareaSTD,
								tbl_tipoordentrabajo.descripcion AS tipoDescrip,
								user1.usrName AS nombre, user1.usrLastName,
								sisusers.usrName, 
								sisusers.usrLastName, equipos.codigo, 
								0 as grpId,
								equipos.id_equipo,
								admcustomers.cliRazonSocial AS nomCli,
								orden_servicio.id_orden AS ordenservicioId');
		$this->assetDB->from('orden_trabajo');
		$this->assetDB->join('tbl_tipoordentrabajo', 'tbl_tipoordentrabajo.tipo_orden = orden_trabajo.tipo');
		$this->assetDB->join('sisusers', 'sisusers.usrId = orden_trabajo.id_usuario');
		$this->assetDB->join('sisusers AS user1', 'orden_trabajo.id_usuario_a = user1.usrId', 'left');//usuario asignado?
		$this->assetDB->join('equipos','equipos.id_equipo = orden_trabajo.id_equipo');
		$this->assetDB->join('admcustomers','admcustomers.cliId = equipos.id_customer');
		$this->assetDB->join('tareas', 'tareas.id_tarea = orden_trabajo.id_tarea', 'left');
		//LEFT JOIN orden_servicio ON orden_trabajo.id_orden = orden_servicio.id_ot
		$this->assetDB->join('orden_servicio', 'orden_trabajo.id_orden = orden_servicio.id_ot', 'left');
		$this->assetDB->where('equipos.estado !=','AN');

		if($tipo == 1){
			$this->assetDB->where('orden_trabajo.tipo', 1);
		}
		//FILTRADO
		//Entre Fechas
		if(!empty($data['fec_hasta']) && !empty($data['fec_desde'])){
			$this->assetDB->where('DATE(orden_trabajo.fecha_program) >=', $data['fec_desde']);
			$this->assetDB->where('DATE(orden_trabajo.fecha_program) <=', $data['fec_hasta']);
		}elseif (!empty($data['fec_desde'])) {
			$this->assetDB->where('DATE(orden_trabajo.fecha_program)',$data['fec_desde']);
		}
		//Estado
        if(!empty($data['estadoFilt'])){
            $this->assetDB->where('orden_trabajo.estado',$data['estadoFilt']);
        }
		//Equipo
        if(!empty($data['equipoFilt'])){
            $this->assetDB->where('equipos.id_equipo',$data['equipoFilt']);
        }
		$this->assetDB->where('orden_trabajo.id_empresa', $empId);
		$query = $this->assetDB->get();

		if ($query->num_rows()!=0){
			return $query->result_array();
		}else{
			return false;
		}
	}
}	