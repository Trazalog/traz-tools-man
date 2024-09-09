<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendarios extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
    }   

    /**
     * MANDA ORDENES DE TRABAJO AL CALENDARIO (programadas).
     *
     * @param   Array   $data   Arreglo con el mes y el año a mostrar.
     * @return  Array           Ordenes de trabajo de la fecha especificada.
     */
    function getot($data = null) // Ok
    {
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        //dump_exit($data);
        $month = $data['month'] + 1 ; 
        $year  = $data['year'] ;
 
        $this->assetDB->select('OT.id_orden,OT.nro,OT.fecha_program,OT.tipo,OT.descripcion,OT.duracion, OT.id_usuario_a');
        $this->assetDB->select('EQ.codigo');
        $this->assetDB->select('A.descripcion as area');
        $this->assetDB->select('G.descripcion as grupo');
        $this->assetDB->select('S.descripcion as sector');
        $this->assetDB->select('TO.descripcion as origen');
        $this->assetDB->from('orden_trabajo OT');
        $this->assetDB->join('equipos EQ', 'EQ.id_equipo = OT.id_equipo', 'left');
        $this->assetDB->join('area A', 'A.id_area = EQ.id_area', 'left');
        $this->assetDB->join('grupo G', 'G.id_grupo = EQ.id_grupo', 'left');
        $this->assetDB->join('sector S', 'S.id_sector = EQ.id_sector', 'left');
        $this->assetDB->join('tbl_tipoordentrabajo TO', 'TO.tipo_orden = OT.tipo', 'left');
        $this->assetDB->where('OT.id_empresa', $empId);
        $this->assetDB->where('OT.estado!=','T');
        $this->assetDB->where('OT.estado!=','AN');
        $this->assetDB->where("month(OT.fecha_program) = $month AND year(OT.fecha_program) = $year");
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

    public function opcionesFiltro()
    {   
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];

        $res =  new StdClass();

        $this->assetDB->select('EQ.codigo as descripcion');
        $this->assetDB->where('id_empresa', $empId);
        $res->equipos = $this->assetDB->get('equipos EQ')->result();
        
        $this->assetDB->select('A.descripcion');
        $this->assetDB->where('id_empresa', $empId);
        $res->areas = $this->assetDB->get('area A')->result();

        $this->assetDB->select('G.descripcion');
        $this->assetDB->where('id_empresa', $empId);
         $res->grupos = $this->assetDB->get('grupo G')->result();

        $this->assetDB->select('S.descripcion');
        $this->assetDB->where('id_empresa', $empId);
        $res->sectores = $this->assetDB->get('sector S')->result();
        
        $this->assetDB->select('TO.descripcion');
        $res->origenes = $this->assetDB->get('tbl_tipoordentrabajo TO')->result();

        return $res;
    }

    // Preventivos por Hora para la Tabla
    function getPreventivosHoras($mes, $year)
    {
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        //$month = $data;//['month'] + 1 ;    
        //$month = $data['month'] + 1 ; 
        //$year  = $data['year'] ;
        $sql = "SELECT preventivo.prevId, 
            preventivo.id_tarea, 
            preventivo.perido, 
            preventivo.cantidad, 
            preventivo.ultimo,
            preventivo.id_equipo,
            preventivo.critico1,
            preventivo.lectura_base,
            equipos.codigo, 
            equipos.id_equipo,
            equipos.ultima_lectura, 
            tareas.descripcion,
            periodo.descripcion as desc_periodo,
            DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY) AS prox 
            from preventivo 
            join equipos ON preventivo.id_equipo = equipos.id_equipo 
            join tareas ON preventivo.id_tarea = tareas.id_tarea 
            join periodo ON preventivo.perido = periodo.idperiodo
            WHERE preventivo.id_empresa = $empId AND (preventivo.estadoprev = 'C' OR preventivo.estadoprev = 'M') AND ((periodo.descripcion = 'Ciclos') OR (periodo.descripcion = 'Horas') OR (periodo.descripcion = 'Kilómetros')) AND (equipos.ultima_lectura >= (preventivo.lectura_base + preventivo.critico1))";//horas o ciclos
            //AND month(DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY)) = $mes 
            //AND year(orden_trabajo.fecha_program) = $year
            //";
            log_message('debug', $sql);
            $query = $this->assetDB->query($sql);

                if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }       
    }

    // Predictivos para la Tabla por id de empresa logueada
    function getpredlist($month, $year) // Ok
    {
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        $sql      = "SELECT predictivo.predId, 
            predictivo.tarea_descrip,  
            predictivo.periodo, 
            predictivo.cantidad, 
            predictivo.fecha,
            predictivo.horash, 
            predictivo.id_equipo,               
            equipos.id_equipo,
            equipos.codigo,     
            tareas.descripcion,
            sector.descripcion AS sector 
            FROM predictivo 
                JOIN equipos ON predictivo.id_equipo = equipos.id_equipo 
                JOIN tareas ON predictivo.tarea_descrip = tareas.id_tarea
                JOIN sector ON sector.id_sector = equipos.id_sector 
            WHERE predictivo.id_empresa = $empId
                AND month(predictivo.fecha) = $month 
                AND year(predictivo.fecha) = $year 
                AND predictivo.estado = 'C'
            ";
        $query = $this->assetDB->query($sql);
        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }
    }

    // Backlog para la Tabla por id de empresa logueada
    function getbacklog($month, $year) // Ok
    {
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];    
        $sql      = "SELECT
                    tbl_back.backId,
                    tbl_back.sore_id,
                    tbl_back.estado,
                    tbl_back.fecha,
                    tbl_back.id_tarea,
                    tbl_back.id_equipo,
                    tbl_back.back_duracion,
                    tbl_back.tarea_opcional,
                    equipos.descripcion,
                    equipos.codigo,
                    tareas.descripcion AS tarea
                    FROM tbl_back
                    INNER JOIN equipos ON equipos.id_equipo = tbl_back.id_equipo
                    LEFT JOIN tareas ON tareas.id_tarea = tbl_back.id_tarea
                    WHERE tbl_back.id_empresa = $empId
                    AND year(tbl_back.fecha) = $year 
                    AND month(tbl_back.fecha) = $month 
                    AND (tbl_back.estado = 'C' OR tbl_back.estado = 'S')
										 ";
										// AND tbl_back.estado != 'OT'
										// AND tbl_back.estado != 'PL'";
        $query= $this->assetDB->query($sql);
        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }
    }
    
      function getPreventivos($month, $year) // Ok
    {   
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        $sql  = "select preventivo.prevId, preventivo.id_tarea, preventivo.perido, preventivo.cantidad, preventivo.ultimo, preventivo.id_equipo, equipos.codigo, equipos.id_equipo, tareas.descripcion, 
            DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY) AS prox ,
            periodo.descripcion as desc_periodo
            from preventivo join equipos ON preventivo.id_equipo = equipos.id_equipo 
            join tareas ON preventivo.id_tarea = tareas.id_tarea 
            join periodo ON preventivo.perido = periodo.idperiodo
            where (preventivo.estadoprev = 'C') AND ((periodo.descripcion != 'Ciclos') AND (periodo.descripcion != 'Horas') AND (periodo.descripcion != 'Kilómetros')) AND 
            (month(DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY)) = $month or month(preventivo.ultimo) = $month)
            and preventivo.id_empresa = $empId";  
        // $sql = "SELECT preventivo.prevId, 
        //     preventivo.id_tarea, 
        //     preventivo.perido, 
        //     preventivo.cantidad, 
        //     preventivo.ultimo,
        //     preventivo.id_equipo,
        //     equipos.codigo, 
        //     equipos.id_equipo, 
        //     tareas.descripcion,
        //     DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY) AS prox 
        //     FROM preventivo 
        //         JOIN equipos ON preventivo.id_equipo = equipos.id_equipo 
        //         JOIN tareas ON preventivo.id_tarea = tareas.id_tarea 
        //     WHERE preventivo.id_empresa = $empId
        //         AND preventivo.estadoprev = 'C'
        //         AND year(DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY)) = $year 
        //         AND month(DATE_ADD(preventivo.ultimo, INTERVAL preventivo.cantidad DAY)) = $month 
        //     ";    
        $query = $this->assetDB->query($sql);
        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }       
    }
  
    function getServicioTareas($data,$month,$year){

        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];

        foreach ($data as $key => $value) {

            $case_id = $value['caseId'];


            $this->assetDB->select('solicitud_reparacion.id_solicitud,solicitud_reparacion.numero,solicitud_reparacion.id_tipo,solicitud_reparacion.nivel,
                               solicitud_reparacion.solicitante,solicitud_reparacion.f_solicitado,solicitud_reparacion.f_sugerido,solicitud_reparacion.hora_sug,
                               solicitud_reparacion.estado,solicitud_reparacion.correctivo,solicitud_reparacion.causa,
                               equipos.descripcion,equipos.codigo,equipos.id_equipo,            
                               sector.descripcion AS sector');
            $this->assetDB->from('solicitud_reparacion');
            $this->assetDB->join('equipos', 'equipos.id_equipo = solicitud_reparacion.id_equipo');
            $this->assetDB->join('sector', 'sector.id_sector = equipos.id_sector');
            $this->assetDB->where('solicitud_reparacion.id_empresa',$empId);
            $this->assetDB->where('solicitud_reparacion.estado', 'S');
            $this->assetDB->where('solicitud_reparacion.urgente !=0');
            $this->assetDB->where('year(solicitud_reparacion.f_solicitado)',$year);
            $this->assetDB->where('month(solicitud_reparacion.f_solicitado)',$month);
            $this->assetDB->where('solicitud_reparacion.case_id', $case_id);                
            $res = $this->assetDB->get()->first_row();

            //log_message('debug', json_encode($res));

            $data[$key]['id_solicitud'] = $res->id_solicitud;
            $data[$key]['numero'] = $res->numero;
            $data[$key]['id_tipo'] = $res->id_tipo;
            $data[$key]['nivel'] = $res->nivel;
            $data[$key]['solicitante'] = $res->solicitante;
            $data[$key]['f_solicitado'] = $res->f_solicitado;
            $data[$key]['f_sugerido'] = $res->f_sugerido;
            $data[$key]['hora_sug'] = $res->hora_sug;
            $data[$key]['hora_sug'] = $res->hora_sug;
            $data[$key]['estado'] = $res->estado;
            $data[$key]['descripcion'] = $res->descripcion;
            $data[$key]['codigo'] = $res->codigo;
            $data[$key]['id_equipo'] = $res->id_equipo;
            $data[$key]['correctivo'] = $res->correctivo;
            $data[$key]['causa'] = $res->causa;
                
            $data = $this->infoUser($data, $key);
        
        }

        return $data;
    }

    


    // Correctivos para la Tabla por id de empresa logueada
    function getsservicio($month, $year) // Ok
    {        
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        $sql      = " SELECT solicitud_reparacion.id_solicitud,
											solicitud_reparacion.numero,
											solicitud_reparacion.id_tipo,
											solicitud_reparacion.nivel,
											solicitud_reparacion.solicitante,
											solicitud_reparacion.f_solicitado,
											solicitud_reparacion.f_sugerido,
											solicitud_reparacion.hora_sug,
											solicitud_reparacion.estado,
											equipos.descripcion,
											equipos.codigo,
											equipos.id_equipo,
											solicitud_reparacion.correctivo,
											solicitud_reparacion.causa,
											sector.descripcion AS sector
											FROM solicitud_reparacion
											INNER JOIN equipos ON equipos.id_equipo = solicitud_reparacion.id_equipo
											INNER JOIN sector ON sector.id_sector = equipos.id_sector
											WHERE solicitud_reparacion.id_empresa = $empId											
											AND solicitud_reparacion.estado = 'S'
											AND solicitud_reparacion.urgente != 0
											AND year(solicitud_reparacion.f_solicitado) = $year
											AND month(solicitud_reparacion.f_solicitado) = $month";
        $query = $this->assetDB->query($sql);
        log_message('debug', $sql);
        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }       
    }



    // Devuelve info de Preventivo por Id para llenar en OT
    function getPrevPorIds($data)
    {
        $id = $data;
        $this->assetDB->select('preventivo.id_tarea, 
            preventivo.ultimo, 
            preventivo.prevId,
            preventivo.id_equipo,
            tareas.descripcion
            ');
        $this->assetDB->from('preventivo');
        $this->assetDB->join('tareas', 'tareas.id_tarea = preventivo.id_tarea');
        $this->assetDB->where('preventivo.prevId', $id);
        $query = $this->assetDB->get();  
       
        return $query->result_array();                
    }

    function actualizarFechaBasePreventivos($fecha_limite, $id_orden)
    {
        $this->assetDB->set('ultimo', $fecha_limite);
        $this->assetDB->where('prevId', $id_orden);
        $resposnse = $this->assetDB->update('preventivo');
        return $resposnse;
    }
    /**
	* Devuelve listado de periodos cargados
	* @param 
	* @return array listado de periodos
	*/
    function getperiodo($data = null){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getperiodo(".json_encode($data).")"); 
        $empresaId = empresa();

        $this->assetDB->select('periodo.idperiodo, periodo.descripcion');
        $this->assetDB->from('periodo');
        if($data == null) {
            $id = $data['periodoId'];
            $this->assetDB->where('periodo.idperiodo', $id);
        }
        $this->assetDB->where('periodo.id_empresa', $empresaId);
        $this->assetDB->where('periodo.estado', 'AC');
        $this->assetDB->order_by('periodo.descripcion');
        $query = $this->assetDB->get();
        if($query->num_rows()>0){
            log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getperiodos >> true ');
            return $query->result();
        }else{
            log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Calendarios | getperiodos >> false');
            return false;
        }
    }
    /*     ./ TAREAS BPM */

    public function infoUser($data, $key)
    {
        if (isset($data[$key]["ot"])) {
            // si hay un usr asignado en bpm
            if (isset($data[$key]['assigned_id'])) {

                $sql = 'select (concat(usrName,", ", usrLastName) ) as usr_asig_nomb
                    from sisusers SU
                    join orden_trabajo OT on OT.id_usuario_a = SU.usrId
                    where OT.id_orden = ' . $data[$key]["ot"];

                $query = $this->assetDB->query($sql);
                $row = $query->row();

                $data[$key]['usr_asignado'] = $row->usr_asig_nomb;
            } else {
                $data[$key]['usr_asignado'] = " S/A ";
            }
        } else {
            $data[$key]['usr_asignado'] = " S/A ";
        }
        
        return $data;
    }

    function Equipos_List()
    {
        $this->assetDB->order_by('codigo','asc');
        $this->assetDB->order_by('descripcion','asc');
        $query = $this->assetDB->get('equipos');
        
        if ($query->num_rows()!=0)
        {
            return $query->result_array();  
        }
        else
        {
            return false;
        }
    }
    
    function setVisit($data = null)
    {
        if($data == null)
        {
            return false;
        }
        else
        {
            $equipId = $data['equip'];
            $dia = $data['fecha'];
            $hora = $data['hora'];
            $min = $data['min'];
            $note = $data['falla'];
            $dia = explode('-', $dia);
            $insert = array(
                   'fecha_inicio' => $dia[2].'-'.$dia[1].'-'.$dia[0],
                   'id_equipo' => $equipId,
                   'estado' => 'C',
                   'tipo' => '2',
                   'id_sucursal' => '1',
                   'id_tarea' => '1',
                   'id_usuario' => '1',
                   'id_usuario_a' => '1',
                    'id_usuario_e' => '1',
                   'descripcion' => $note
                );


            if($this->assetDB->insert('orden_trabajo', $insert) == false) {
                return false;
            }else{
                return "Se programo la Orden de Trabajo para el día <br>".$data['dia']." a las ".$data['hora'].":".$data['min'];
            }
        }
    }       

    function getpred($data = null){
        
						if($data == null)
						{
										return false;
						}
						else
						{
								$month = $data['month'] + 1 ;

								$sql= "select predictivo.predId,
										predictivo.tarea_descrip,
										predictivo.periodo,
										predictivo.cantidad,
										predictivo.fecha,
										equipos.id_equipo,
										predictivo.estado,
										predictivo.id_equipo,
										tareas.descripcion,
										DATE_ADD(predictivo.fecha, INTERVAL predictivo.cantidad DAY) AS prox
										from predictivo
										join equipos ON predictivo.id_equipo = equipos.id_equipo
										join tareas ON predictivo.tarea_descrip = tareas.id_tarea
										where predictivo.estado = 'C'
										AND month(DATE_ADD(predictivo.fecha, INTERVAL predictivo.cantidad DAY)) = $month ";
			
								$query= $this->assetDB->query($sql);

								if ($query->num_rows()!=0)
								{
									return $query->result_array();
								}
								else
								{
									return false;
								}
						}
				}
		
	// FUNCIONES DE OT	
		//Guarda orden de trabajo a partir de Pred/Correc/Backlog/Prevent
		function guardar_agregar($data){
	
				$query = $this->assetDB->insert("orden_trabajo",$data);
				$idOT = $this->assetDB->insert_id();
				return $idOT;
		}

		// guarda case_id en Otrabajo
		function setCaseidenOT($case_id, $id){
			$this->assetDB->where('orden_trabajo.id_orden', $id);
			return $this->assetDB->update('orden_trabajo', array('case_id'=>$case_id));			
		}

		function cambiarEstado($id_solicitud, $estado, $tipo){
			
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

			return $response;
		}

		// Trae adjunto de Tarea original segun tipo (Backlog, Prevent y predict)
		function getAdjunto($id_solicitud,$tipo){
			
			switch ($tipo) {
				case 'predictivo':		// Predictivo
								$this->assetDB->select('predictivo.pred_adjunto');
								$this->assetDB->from('predictivo');
								$this->assetDB->where('predictivo.predId',$id_solicitud);
								$query = $this->assetDB->get();
								$row = $query->row();
								$result =  $row->pred_adjunto; 
								break;
				case 'backlog':		//Backlog
								$this->assetDB->select('tbl_back.back_adjunto');
								$this->assetDB->from('tbl_back');
								$this->assetDB->where('tbl_back.backId',$id_solicitud);
								$query = $this->assetDB->get();
								$row = $query->row();
								$result =  $row->back_adjunto; 
								break;		
				default:		//Preventivo
								$this->assetDB->select('preventivo.prev_adjunto');
								$this->assetDB->from('preventivo');
								$this->assetDB->where('preventivo.prevId',$id_solicitud);
								$query = $this->assetDB->get();
								$row = $query->row();
								$result =  $row->prev_adjunto; 								
			}
			return $result;
		}

		// guarda el adjunto que viene de la Tarea Original(Backlog, prevent y predict)
		function insertAdjunto($idOT,$adjunto){
						$data = array('otId'=>$idOT,
																																		'ot_adjunto'=>$adjunto);
						$query = $this->assetDB->insert("tbl_otadjuntos",$data);
						return $query;
		}

		// TODO: ENTENDER SI YA NO SE USA CON LA NUEVA MODIFICACION DE HERRAM E INSUMOS
		// Guarda batch de OT
		function setOTbatch($data)
		{
			$this->assetDB->insert_batch('orden_trabajo', $data);
		}

		//devuelve valores de todos los datos de la OT para mostrar en modal.

		function getDataOt($idOt) {

				$sql = "SELECT
							ot.id_orden,
							ot.id_tarea,
							ot.descripcion,
							ot.tipo,
							ot.id_solicitud,
							ot.fecha_program,
							e.codigo,
							e.descripcion AS descripcionEquipo,
							CASE
											WHEN ot.id_tarea = 0 THEN 'sin tarea estandar'
											ELSE tstd.descripcion
							END AS descTareaStandar,
							CASE
											WHEN ot.id_usuario_a IS NULL THEN ''
											ELSE su.usrId
							END AS usrId,
							CASE
											WHEN ot.id_usuario_a IS NULL THEN ''
											ELSE su.usrLastName
							END AS usrLastName,
							CASE
											WHEN ot.id_usuario_a IS NULL THEN ''
											ELSE su.usrName
							END AS usrName
				FROM orden_trabajo ot
				LEFT JOIN tareas tstd ON ot.id_tarea = tstd.id_tarea
				JOIN equipos e ON ot.id_equipo = e.id_equipo
				LEFT JOIN sisusers su ON ot.id_usuario_a = su.usrId
				JOIN tbl_tipoordentrabajo tipoOT ON ot.tipo = tipoOT.id
				WHERE ot.id_orden = ".$idOt."";

			 $query = $this->assetDB->query($sql);

				if($query->num_rows()!=0)
				{

						return $query->result_array();
				}
				else
				{
						return array();
				}
		}

		/**
		* Develvecomonentes concatenados de OT.
		* @param string $idOT
		* @return
		*/
		function getCompEquipoOT($numtipo,$id_solicitud,$idOt)
		{

				switch ($numtipo) {

					case 3:
						// $sql = "select c.descripcion as descCompo, ce.codigo as codCompo
						// from orden_trabajo ot, tbl_back bck, componenteequipo ce, componentes c
						// where bck.idcomponenteequipo = ce.idcomponenteequipo
						// and ce.idcomponenteequipo = c.id_componente
						// and bck.backId = ".$id_solicitud."
						// and ot.id_orden = ".$idOt."";

						$sql = "select c.id_componente
												,c.descripcion as descCompo
											from orden_trabajo ot
												, preventivo pr
												, componentes c
											where pr.id_componente = c.id_componente
											and pr.prevId = ".$id_solicitud."
											and ot.id_orden = ".$idOt."";


						return $this->assetDB->query($sql)->result_array()[0];
						break;

					case 4:
						$sql = "select c.descripcion as descCompo, ce.codigo as codCompo
						from orden_trabajo ot, tbl_back bck, componenteequipo ce, componentes c
						where bck.idcomponenteequipo = ce.idcomponenteequipo
						and ce.idcomponenteequipo = c.id_componente
						and bck.backId = ".$id_solicitud."
						and ot.id_orden = ".$idOt."";
						return $this->assetDB->query($sql)->result_array()[0];
						break;

					default:
					return array();
						break;
				}







		}





		function getInfoTareaProgram($numtipo, $id_solicitud){
			
			switch ($numtipo) {	
				// OT generada desde item menu
				case '0':
					// OT generada desde item menu
					// traer info de OT solamente
					$this->assetDB->select('orden_trabajo.*');
					$this->assetDB->from('orden_trabajo');
					$this->assetDB->where('orden_trabajo.id_orden', $id_solicitud);
					$query = $this->assetDB->get();
					return $query->result_array();					
					break;
				// preventivo
				case '3':
					$this->assetDB->select('unidad_tiempo.unidaddescrip,
														preventivo.prev_duracion AS duracionTarea,
														preventivo.cantidad AS frecuencia,
														periodo.descripcion AS especieFrecuencia');
					$this->assetDB->from('unidad_tiempo');
					$this->assetDB->join('preventivo', 'unidad_tiempo.id_unidad = preventivo.id_unidad');
					$this->assetDB->join('periodo', 'preventivo.perido = periodo.idperiodo');
					$this->assetDB->where('preventivo.prevId', $id_solicitud);
					$query = $this->assetDB->get();
					return $query->result_array();
					break;
				// backlog
				case '4':
					$this->assetDB->select('tbl_back.back_duracion AS duracionTarea,                                    
                                    unidad_tiempo.unidaddescrip');
					$this->assetDB->from('unidad_tiempo');									
					$this->assetDB->join('tbl_back', 'unidad_tiempo.id_unidad = tbl_back.id_unidad');
					$this->assetDB->where('tbl_back.backId', $id_solicitud);
					$query = $this->assetDB->get();			
					return $query->result_array();				
					break;
				// predictivo
				default:			
					$this->assetDB->select('predictivo.pred_duracion AS duracionTarea,
														predictivo.id_unidad,
														predictivo.cantidad AS frecuencia,
														unidad_tiempo.unidaddescrip');
					$this->assetDB->from('unidad_tiempo');
					$this->assetDB->join('predictivo', 'predictivo.id_unidad = predictivo.id_unidad');
					$this->assetDB->where('predictivo.predId', $id_solicitud);	
					$query = $this->assetDB->get();
					return $query->result_array();
					break;
			}
		}

		//devuelve valores de todos los datos de la OT para mostrar en modal.
		function getOrigenOt($idot)
		{
			$this->assetDB->select('orden_trabajo.tipo, orden_trabajo.id_solicitud');
				$this->assetDB->from('orden_trabajo');
				$this->assetDB->where('orden_trabajo.id_orden', $idot);

				$query = $this->assetDB->get();
				if($query->num_rows()!=0)
				{
						return $query->result_array();
				}
				else
				{
						return false;
				}
		}

		// devuelve usuarios a asignar OT
		function getOperarios(){
			$userdata  = $this->session->userdata('user_data');
			$empresaId = $userdata[0]['id_empresa'];
			dump($empresaId, 'id de empresa: ');
			// $this->assetDB->select('sisusers.usrId, sisusers.usrLastName, sisusers.usrname');
			// $this->assetDB->join('usuarioasempresa', 'usuarioasempresa.usrId = sisusers.usrId');
			// $this->assetDB->from('sisusers');
			// $this->assetDB->where('usuarioasempresa.empresaid', $empresaId);
			// $this->assetDB->where('usuarioasempresa.estado', 'AC');
			// $query = $this->assetDB->get();
			// $i     = 0;
			// foreach ($query->result() as $row)
			// {   
			// 		$operarios[$i]['label'] = $row->usrLastName.", ". $row->usrname ;
			// 		$operarios[$i]['value'] = $row->usrId;
			// 		$i++;
			// }
			// return $operarios; 
		} 

		// Trae tareas por empresa logueada - Listo
		function gettareas(){

			$userdata = $this->session->userdata('user_data');
			$empId = $userdata[0]['id_empresa']; 

			$this->assetDB->select('tareas.id_tarea, tareas.descripcion');			
			$this->assetDB->from('tareas');
			$this->assetDB->where('tareas.id_empresa',$empId);
			$query= $this->assetDB->get();
			//$query->result_array();
			
			if($query->num_rows()>0){
					return $query->result_array();
			}
			else{
					return array();
			}			
		}

	//////// FUNCIONES CALENDARIO	
		// Actualiza dia nueva fecha de programacion en OT
		function updateDiaProgramacion($id, $diaNuevo){

						$this->assetDB->set('fecha_program', $diaNuevo);
										$this->assetDB->where('id_orden', $id);
										$resposnse = $this->assetDB->update('orden_trabajo');
										return $resposnse;
		}
		// Actualiza la nueva duracion de la OT
			function updateDuraciones($id, $nueva){

							$this->assetDB->set('duracion', $nueva);
							$this->assetDB->where('id_orden', $id);
							$resposnse = $this->assetDB->update('orden_trabajo');
							return $resposnse;
			}

		////// CORRECTIVOS
					function getCorrectPorIds($data){

									$id = $data;
									
																	$this->assetDB->select('solicitud_reparacion.id_solicitud,
																																	solicitud_reparacion.f_solicitado, 
																																	solicitud_reparacion.causa,
																									solicitud_reparacion.id_equipo                      
																									');
																	$this->assetDB->from('solicitud_reparacion');        
																	$this->assetDB->where('solicitud_reparacion.id_solicitud', $id);
																	$query = $this->assetDB->get();      
																	
																	return $query->result_array();  
					}

		/////	BACKLOG
		function getBackPorIds($data){
			$id = $data;

			$this->assetDB->select('tbl_back.*,
			tareas.descripcion as tareadesc');
			$this->assetDB->from('tbl_back');
			$this->assetDB->join('tareas', 'tbl_back.id_tarea = tareas.id_tarea', 'left');
			$this->assetDB->where('tbl_back.backId', $id);
			$query = $this->assetDB->get();      
			
			return $query->result_array(); 
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



        
    ///// PREDICTIVOS   
        function getPredictPorIds($data){
            $id = $data;
            
            $this->assetDB->select('predictivo.tarea_descrip,
                              tareas.descripcion,
                              predictivo.predId,
                              predictivo.id_equipo,
                              predictivo.fecha');
            $this->assetDB->from('predictivo');
            $this->assetDB->join('tareas', 'tareas.id_tarea = predictivo.tarea_descrip');
            $this->assetDB->where('predictivo.predId', $id);
            $query = $this->assetDB->get();
            return $query->result_array(); 
        }
        // Trae herramientas por id de predictivo para guardar en OT
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
        // Guarda el bacht de datos de herramientas 
        function insertOTHerram($idOT,$herra){

            $userdata  = $this->session->userdata('user_data');
            $empId    = $userdata[0]['id_empresa']; 
            $i = 0;
            foreach ($herra as $h) {
                $herramientas[$i]['otId'] = $idOT;
                $herramientas[$i]['herrId'] = $h["herrId"];
                $herramientas[$i]['cantidad'] = $h["cantidad"];
                $herramientas[$i]['id_empresa']= $empId;
                $i++;                                
            }           
            $query = $this->assetDB->insert_batch("tbl_otherramientas",$herramientas);
            return $query;
        }
        // Guarda el bacht de insumos 
        function insertOTInsum($idOT,$ins){
            //dump($ins, 'insumos en entrada de funcion insert: ');
            $userdata  = $this->session->userdata('user_data');
            $empId    = $userdata[0]['id_empresa']; 
            $j = 0;
            foreach ($ins as $in) {
                $insumo[$j]['otId'] = $idOT;
                $insumo[$j]['artId'] = $in['artId'];
                $insumo[$j]['cantidad'] = $in['cantidad'];
                $insumo[$j]['id_empresa'] = $empId;
                $j++;                                
            }           
            $query = $this->assetDB->insert_batch("tbl_otinsumos",$insumo);
            return $query;
        }
     






    ///// PREVENTIVOS   
                
        // Trae herramientas ppor id de preventivo para Editar
        function getPreventivoHerramientas($id){
                
            $userdata = $this->session->userdata('user_data');
            $empId = $userdata[0]['id_empresa']; 

            $this->assetDB->select('tbl_preventivoherramientas.cantidad,
                                                    herramientas.herrcodigo,
                                                    herramientas.herrmarca,
                                                    herramientas.herrdescrip,
                                                    herramientas.herrId');
            $this->assetDB->from('tbl_preventivoherramientas');
            $this->assetDB->join('herramientas', 'herramientas.herrId = tbl_preventivoherramientas.herrId');   
            $this->assetDB->where('tbl_preventivoherramientas.prevId', $id);        
            $this->assetDB->where('tbl_preventivoherramientas.id_empresa', $empId);
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
        function getPreventivoInsumos($id){
                
            $userdata = $this->session->userdata('user_data');
            $empId = $userdata[0]['id_empresa']; 

            $this->assetDB->select('tbl_preventivoinsumos.id,
                                                    tbl_preventivoinsumos.cantidad,
                                                    articles.artBarCode,
                                                    articles.artId,
                                                    articles.artDescription,
                                                    articles.id_empresa');                            
            $this->assetDB->from('tbl_preventivoinsumos');
            $this->assetDB->join('articles', 'articles.artId = tbl_preventivoinsumos.artId');   
            $this->assetDB->where('tbl_preventivoinsumos.prevId', $id);        
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
    

        /**
     * Trae listado de equipos que tengan mantenimiento preventivo por horas
     *
     * @return  Array   Vuleca la variable o no devuelve nada
     */
    function getPreventivosPorHora()
    {
        $this->assetDB->select('equipos.codigo, equipos.descripcion, equipos.id_equipo, equipos.ultima_lectura, sector.descripcion as descripSector, preventivo.estadoprev, preventivo.prevId, preventivo.cantidad, preventivo.critico1');
        $this->assetDB->from('preventivo');
        $this->assetDB->join('equipos', 'equipos.id_equipo = preventivo.id_equipo', 'inner');
        $this->assetDB->join('sector', 'sector.id_sector = equipos.id_sector', 'inner');
        $this->assetDB->where('preventivo.perido', '5');//horas
        $this->assetDB->where('equipos.estado', 'AC');

        $query= $this->assetDB->get();

        if ($query->num_rows() > 0)
        {
            $preventivos  = $query->result_array();
            $data['data'] = $this->revisaEstadoPreventivosPorHoras( $preventivos );
            //$data['data'] = $query->result_array();
            return  $data;
        }
        else
        {
            return false;
        }
    }

    
    // bucle que recorra preventivos
            //      con id_equipo traigo historial_lecturas ->ultima lectura
            //      hago cuenta
            //      si es necesario llamo funcion que cambia estado de preventivo
            //      cambio $preventivos[estadoprev]
            // cierro bucle
            // devuelvo $preventivos
    function revisaEstadoPreventivosPorHoras($preventivos)
    {
        $cantPreventivos = sizeof( $preventivos );
        for ($i=0; $i<$cantPreventivos; $i++)
        {
            $lecturaActual = $this->getLecturaActual( $preventivos[$i]['id_equipo'] );
            //dump( $lecturaActual, 'Lectura Actual' );
            //dump( $preventivos[$i]['ultima_lectura'], 'Ultima lectura' );
            //dump( $preventivos[$i]['cantidad'], 'cantidad' );
            //dump( $preventivos[$i]['critico1'], 'critico' );

            //1er caso: lecturaactual - ultimalectura >= cantidad  => estado vencido
            if (($lecturaActual - $preventivos[$i]['ultima_lectura']) >= $preventivos[$i]['cantidad'])
            {
                if ($preventivos[$i]['estadoprev'] != 'VE')
                {
                    $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'VE' );
                    $preventivos[$i]['estadoprev'] = 'VE';
                }
            }

            //2do caso: lecturaactual - ultimalectura < cantidad  => estado en curso
            if (($lecturaActual - $preventivos[$i]['ultima_lectura']) < $preventivos[$i]['cantidad'])
            {
                //3er caso: > cantidad => estado critico
                if (($lecturaActual - $preventivos[$i]['ultima_lectura']) > $preventivos[$i]['cantidad'])
                {
                    if ($preventivos[$i]['estadoprev'] != 'CR')
                    {
                        $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'CR' );
                        $preventivos[$i]['estadoprev'] = 'CR';
                    }
                }
                else // si no es critico => esta en curso
                {
                    if ($preventivos[$i]['estadoprev'] != 'C')
                    {
                        $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'C' );
                        $preventivos[$i]['estadoprev'] = 'C';
                    }
                }
            }
        }
        return $preventivos;
    }

    /**
     * Devuelve la ultima lectura de un equipo determinado
     *
     * @param   String  $id_equipo  Equipo que se quiere saber la ultima lectura
     * @return  Int     Última lectura
     */
    function getLecturaActual($id_equipo)
    {
        $this->assetDB->select('lectura');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->where('id_equipo', $id_equipo);
        $this->assetDB->order_by('id_lectura', 'desc');
        $this->assetDB->limit(1);

        $query= $this->assetDB->get();

        if ($query->num_rows() > 0)
        {
            $data  = $query->result_array();
            return  (int)$data[0]['lectura'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Cambia el campo Estado de la tabla preventivo
     *
     * @param   String  $idPreventivo   Id del preventivo a modificar
     * @param   String  $estado         Valor del nuevo estado
     * @return  bool                    Cambio correcto o incorrecto
     */
    function cambiaEstadoPreventivo($idPreventivo, $estado)
    {
        $data = array(
           'estadoprev' => $estado
        );
        $this->assetDB->where('prevId', $idPreventivo);
        $this->assetDB->update('preventivo', $data);

        if ($this->assetDB->trans_status() === FALSE)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
   


	/* funciones para BPM */
    function getCaseIdporIdBacklog($id_solicitud){
			// $this->assetDB->select('solicitud_reparacion.case_id');
			// $this->assetDB->from('tbl_back');
			// $this->assetDB->join('solicitud_reparacion', 'tbl_back.sore_id = solicitud_reparacion.id_solicitud');
			// $this->assetDB->where('tbl_back.sore_id', $id_solicitud);
			// $query = $this->assetDB->get();
			// if ($query->num_rows() > 0){
			// 	return $query->row('case_id');				
			// }
			// else{
			// 	return 0;
			// }
				
			$this->assetDB->select('solicitud_reparacion.case_id');
			$this->assetDB->from('solicitud_reparacion');
			$this->assetDB->join('tbl_back', 'tbl_back.sore_id = solicitud_reparacion.id_solicitud');
			$this->assetDB->where('tbl_back.backId', $id_solicitud);
			$query = $this->assetDB->get();
			if ($query->num_rows() > 0){
				return $query->row('case_id');				
			}
			else{
				return 0;
			}


		}
						
		function getCaseIdporIdSolServicios($id_solicitud){
			$this->assetDB->select('solicitud_reparacion.case_id');
			$this->assetDB->from('solicitud_reparacion');			
			$this->assetDB->where('solicitud_reparacion.id_solicitud', $id_solicitud);
			$query = $this->assetDB->get();
			if ($query->num_rows() > 0){
				return $query->row('case_id');				
			}
			else{
				return 0;
			}
		}
	}		