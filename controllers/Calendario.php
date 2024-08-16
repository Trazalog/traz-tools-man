<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calendario extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Calendarios');
        $this->load->model('Tareas');
        $this->load->model('Otrabajos');

    }

    public function indexot($permission) // Ok
    {
        $data = $this->session->userdata();
        log_message('DEBUG','#Main/index | Calendario >> data '.json_encode($data)." ||| ". $data['user_data'][0]['usrName'] ." ||| ".empty($data['user_data'][0]['usrName']));
    
        if(empty($data['user_data'][0]['usrName'])){
            log_message('DEBUG','#Main/index | Cerrar Sesion >> '.base_url());
            $var = array('user_data' => null,'username' => null,'email' => null, 'logged_in' => false);
            $this->session->set_userdata($var);
            $this->session->unset_userdata(null);
            $this->session->sess_destroy();
    
            echo ("<script>location.href='login'</script>");
    
        }else{
            $data['permission'] = $permission . "Correctivo-Preventivos-Backlog-Predictivo-";

            $this->load->view('calendar/calendar1', $data);
        }
    }

    public function getcalendarot() // Ok
    {
        $data = $this->Calendarios->getot($this->input->post());
        if ($data == false) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }

    public function getTablas() // Ok
    {
        
						$mes = $this->input->post('mes');
						$year = $this->input->post('year');
						$permission = $this->input->post('permission');
						$data['mes'] = $mes;
						$data['year'] = $year;

						
						// var_dump($data_extend);

						$data['list0'] = $this->Calendarios->getPreventivosHoras($mes, $year);
						$data['list1'] = $this->Calendarios->getpredlist($mes, $year); // listo
						$data['list2'] = $this->Calendarios->getbacklog($mes, $year); // listo
						$data['list3'] = $this->Calendarios->getPreventivos($mes, $year); // listo
						//$data['list4'] = $this->Calendarios->getsservicio($mes, $year); // listo
						//Buscar Bonita IdTarea para pode
						//Completar Tareas con ID Solicitud y ID OT
						//Nueva consulta Para traer los mismos datos de la bandeja de entrada
						//Obtener Bandeja de Usuario desde Bonita

      $response = $this->bpm->getToDoList();
						if($response['status']){
            // Si trae datos une las tareas con las solicitudes
            $data_extend = $this->Calendarios->getServicioTareas($response['data'],$mes,$year);
            $data['list4'] = $data_extend; // listo
      }
        
      $data['permission'] = $permission;

        
        
						log_message('DEBUG','#CALENDARIO >> getTablas() response >> '.json_encode($response));
						log_message('DEBUG','#CALENDARIO >> getTablas() data_extend >> '.json_encode($data_extend));
						log_message('DEBUG', '#CALENDARIO >> getTablas() $datos >> ' . $preventivosHoras);
						log_message('DEBUG', '#CALENDARIO >> getTablas() $data[list0] >> ' . json_encode($data['list0']));
						log_message('DEBUG', '#CALENDARIO >> getTablas() $data[list1] >> ' . json_encode($data['list1']));
						log_message('DEBUG', '#CALENDARIO >> getTablas() $data[list2] >> ' . json_encode($data['list2']));
						log_message('DEBUG', '#CALENDARIO >> getTablas() $data[list3] >> ' . json_encode($data['list3']));
						log_message('DEBUG', '#CALENDARIO >> getTablas() $data[list4] >> ' . json_encode($data['list4']));
        
						//para cada preventivo
						if ($preventivosHoras) {
										$j = 0;
										for ($i = 0; $i < sizeof($preventivosHoras); $i++) {
														$estaAlertado = false;
														//sacar tipo alerta
														//proximo servicio = lectura base + frecuencia
														$proximoServicio = $preventivosHoras[$i]['lectura_base'] + $preventivosHoras[$i]['cantidad'];
														$proximaAlerta = $preventivosHoras[$i]['lectura_base'] + $preventivosHoras[$i]['critico1'];
														$lecturaAutonomo = $preventivosHoras[$i]['ultima_lectura'];
														//si alerta amarilla pone en array y agrega dato amarillo
														if ($lecturaAutonomo >= $proximaAlerta) {
																		$tipoAlerta = 'A';
																		$estaAlertado = true;
														}
														//si alerta es roja pone en array y agrega rojo
														if ($lecturaAutonomo >= $proximoServicio) {
																		$tipoAlerta = 'R';
																		$estaAlertado = true;
														}
														//si esta alertado guardo
														if ($estaAlertado) {
																		$preventivosHorasVisible[$j] = $preventivosHoras[$i];
																		//agrego tipo alerta, proximo servicio y ultima lectura
																		$preventivosHorasVisible[$j]['tipoAlerta'] = $tipoAlerta;
																		$preventivosHorasVisible[$j]['proximoServicio'] = $proximoServicio;
																		$preventivosHorasVisible[$j]['ultimaLectura'] = $preventivosHoras[$i]['ultima_lectura'];
																		$j++;
														} else {
																		$preventivosHorasVisible = false;
														}
										}
						} else {
										$preventivosHorasVisible = false;
						}

						$data['list'] = $preventivosHorasVisible;

						$response['html'] = $this->load->view('calendar/tablas', $data);
						echo json_encode($response);
    }

    // Devuelve info de Preventivo por Id para llenar en OT
    public function getPrevPorId() //

    {
        $id = $this->input->post('id');
        $data = $this->Calendarios->getPrevPorIds($id);
        echo json_encode($data);
    }

    // Guarda la OT simple o redirije para guardar varias
    public function guardar_agregar()
    {
        $userdata = $this->session->userdata('user_data');
        $userBpm = $userdata[0]['userBpm'];

        // log
        //log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar');
        $data = $this->input->post();
        log_message('DEBUG', 'TRAZA | Data: '.json_encode($data));

        $userdata = $this->session->userdata('user_data');//linea repetida
        $usrId = $userdata[0]['usrId'];
        $empId = $userdata[0]['id_empresa'];

        # Bandera que si esta en TRUE Aborta la creacion de la OTget
        $error = false;

        if ($_POST) {

            if (isset($_POST['event_tipo'])) {
                $event_tipo = $_POST['event_tipo']; //evento unico '1' evnto repetitivo '2'
            }
            $id_solicitud = $_POST['id_sol']; // id predic - correct - back
            $id_tarea = $_POST['id_tarea'];
            $hora_progr = $_POST['hora_progr'];
            $fecha_progr = $_POST['fecha_progr'];
            $fecha_progr = explode('-', $fecha_progr);
            $fec_programacion = $fecha_progr[2] . '-' . $fecha_progr[1] . '-' . $fecha_progr[0] . ' ' . $hora_progr . ':00';
            $fecha_inicio = '0000-00-00 00:00:00';
            $descripcion = $_POST['descripcion']; //descripcion del predictivo/correc/backlog/etc
            $tipo = $_POST['tipo']; //numero de tipo segun tbl orden_trabajo
            $equipo = $_POST['ide'];
            if (isset($_POST['cant_meses'])) {
                $cant_meses = $_POST['cant_meses']; //cantidad de meses a programar las OT
            }
            if (isset($_POST['lectura_programada'])) {
                $lectura_programada = $_POST['lectura_programada'];
            } else {
                $lectura_programada = '0000-00-00 00:00:00';
            }
            if (isset($_POST['lectura_ejecutada'])) {
                $lectura_ejecutada = $_POST['lectura_ejecutada'];
            } else {
                $lectura_ejecutada = '0000-00-00 00:00:00';
            }

            // si no es correctivo busca duracion sino pone 60' por defecto
            if ($tipo != '2') {
                $duracion = $this->getDurTarea($tipo, $id_solicitud);
            } else {
                $duracion = 60;
            }

            $datos2 = array(
                'id_tarea' => $id_tarea, // id de tarea a realizar(tabla tareas)
                'nro' => 1, //por defecto( no se usa)
                'fecha' => date('Y-m-d'),
                'fecha_program' => $fec_programacion,
                'descripcion' => $descripcion,
                'cliId' => 1, //por defecto( no se usa)
                'estado' => 'PL', // estado Planificado
                'id_usuario' => $usrId,
                'id_usuario_e' => 1,
                'id_sucursal' => 1,
                'id_solicitud' => $id_solicitud, // id prev-correct-back-predict
                'tipo' => $tipo, // tipo solicitud (prev-correct-back-predict )
                'id_equipo' => $equipo,
                'duracion' => $duracion,
                'id_tareapadre' => $id_solicitud, //solic que genera la 1º OT y las repetitivas
                'id_empresa' => $empId,
                'lectura_programada' => $lectura_programada,
                'lectura_ejecutada' => $lectura_ejecutada,
            );

            log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | datos2'.json_encode($data));


            // si el evento es unico lo guarda
            if ($event_tipo == '1') {

                //log
                log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Evento tipo: ' . $event_tipo);

                /// Interaccion con BPM ///
                $estado = 'PL';
                // $tipo == '2' -> S.Servicios
                if ($tipo == '2') {
                    // si es correctivo u S.Servicio
                    $tipo = 'correctivo';
                    $infoTarea = $this->getInfoTareaporIdSolicitud($id_solicitud, $tipo);
                    log_message('DEBUG','#Calendario/guardar_agregar | infoTarea: '.json_encode($infoTarea));

                    $respCerrar = $this->cerrarTarea($infoTarea['taskId']);
                    //log
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Tipo solicitud en 2: ' . $tipo);
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | $taskId: ' . $infoTarea['taskId']);
                    if ($respCerrar['status']) {
                        $resActualizar = $this->actualizarIdOTenBPM($infoTarea['caseId'], $idOTnueva);
                        // cambio de estado a PL de SServicio
                        $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                    } else {
                        log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Cerrar SS: ' . json_encode($respCerrar));
                        $error = true;
                        $estado = 'S';
                        return msj(false, ASP_104);
                    }
                    // guardo el case_id en Otrabajo
                    $this->Calendarios->setCaseidenOT($infoTarea['caseId'], $idOTnueva);

                    ////////////////////////////////////////////////////////////////////////

                    //// buscar task para asignar la tarea 'Planificar Solicitud' para caso de
                    // SServicio Urgente planificada sin tomar Tarea  'Planificar Solicitud'
                    // Usuario logueado en BPM

                    // log

                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Evento Tipo: ' . $event_tipo);
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Usr en BPM: ' . $userBpm);
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | caseId: ' . $infoTarea['caseId']);
                    // busca taskId de     'Planificar Solicitud'
                    $prevTask = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $infoTarea['caseId'], 'Planificar Solicitud');
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Taskid Planificar Solicitud: ' . $prevTask);

                    if ($prevTask != 0) {
                        // Asigno ususario logueado
                        $responce = $this->bpm->setUsuario($prevTask, $userBpm);
                        if (!$responce['status']) {echo json_encode($responce);return;}
                        // Cierro tarea 'Planificar Solicitud'
                        $responce = $this->bpm->cerrarTarea($prevTask);
                        if (!$responce['status']) {echo json_encode($responce);return;}
                    }

                    ///////////////////////////////////////////////////////////////////////

                }
                // $tipo == '3' -> Preventivo
                if ($tipo == '3') {
                    //log
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar |  Tipo solicitud en 3: ' . $tipo.' Datos2'.json_encode($datos2));
                    $tipo = 'preventivo';
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                }
                // $tipo == '4' -> Backlog
                if ($tipo == '4') {
                    //log
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar |  Tipo solicitud en 4: ' . $tipo);
                    // actualizo estado del backlog
                    $tipo = 'backlog';

                    //////////////////////////////////////////////////////////
                    //// buscar task para asignar la tarea 'Planificar Solicitud' para caso de
                    // SServicio NO Urgente planificada sin tomar Tarea  'Planificar Solicitud'
                    // Usuario logueado en BPM
                    // busca taskId de     'Planificar Solicitud'
                    //$infoTarea = $this->getInfoTareaporIdSolicitud($id_solicitud, $tipo);
                    $infoTarea['caseId'] = $this->getInfoTareEnBack($id_solicitud);
                    //log
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar |  ID Solicitud: ' . $id_solicitud);
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar |  Case_id desde infotarea/idsolicitud: ' . $infoTarea['caseId']);

                    // si backlog es generado en SServicios tiene case id de SolServicio
                    if ($infoTarea['caseId'] != 0) {
                        $prevTask = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $infoTarea['caseId'], 'Planificar Backlog');
                        log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | Taskid en Planificar Backlog: ' . $prevTask);

                        if ($prevTask != 0) {
                            // Asigno ususario logueado
                            $responce = $this->bpm->setUsuario($prevTask, $userBpm);
                            if (!$responce['status']) {echo json_encode($responce);return;}
                            // Cierro tarea 'Planificar Solicitud'
                            $responce = $this->bpm->cerrarTarea($prevTask);
                            if (!$responce['status']) {echo json_encode($responce);return;}
                        }
                    }

                    //////////////////////////////////////////////////////////

                    //Actualizar Tablas >> Backlog ||Solicitud
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                    $infoTarea = $this->getInfoTareaporIdSolicitud($id_solicitud, $tipo);

                    //? Si trae task se orgino de una SS
                    if ($infoTarea) {
                        $respCerrar = $this->cerrarTarea($infoTarea['taskId']);
                        $resActualizar = $this->actualizarIdOTenBPM($infoTarea['caseId'], $idOTnueva);
                        // averiguo case para saber si es autogenerado por BPM o no
                        $caseDeBacklog = $infoTarea['caseId'];
                        $idSServicio = $this->Calendarios->getIdSServicioporCaseId($caseDeBacklog);
                        // Si el backlog viene de una SServicios la actualializa a Planificada
                        if ($idSServicio != null) {
                            $this->Calendarios->cambiarEstado($idSServicio, 'PL', 'correctivo');
                        }
                    }

                }
                // $tipo == '5' -> Predictivo
                if ($tipo == '5') {
                    $tipo = 'predictivo';
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                }
                if ($tipo == '') {
                    $tipo = 'predictivo';
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                }

                if ($error) {
                    return false;
                }

                // genera la Otrabajo devuelve el id de OT
                $idOT = $this->Calendarios->guardar_agregar($datos2);
                // guarda herramientas, insumos y rrhh de las tareas en OT
                $this->setHerramInsPorTarea($idOT, $tipo, $id_solicitud);
                // si es Preventivo o Predictivo lanza proceso nuevo
                if (($tipo == 'preventivo') || ($tipo == 'predictivo') || (($caseDeBacklog == 0) && ($tipo != 'correctivo'))) {

                    $contract = array(
                        "idSolicitudServicio" => 0,
                        "idOT" => $idOT,
                    );
                    $result = $this->bpm->lanzarProceso(BPM_PROCESS_ID, $contract);

                    if (!$result['status']) {
                        $this->Otrabajos->eliminar($idOT);
                        return false;
                    }
                    // guarda case id generado el lanzar proceso
                    $respcaseOT = $this->Calendarios->setCaseidenOT($result['data']['caseId'], $idOT);
                } else {

                    // guarda caseid ya generado anteriormente
                    $respcaseOT = $this->Calendarios->setCaseidenOT($infoTarea['caseId'], $idOT);
                }

            } else { // evento repetitivo solo PREVENTIVO o PREDICTIVO
                // Sumo a la fecha de program la cant de meses p/ sacar fecha limite
                $fecha_limite = strtotime('+' . $cant_meses . ' month', strtotime($fec_programacion));
                $fecha_limite = date('Y-m-d H:i:s', $fecha_limite); /// "2018-06-16 00:00:00"
                //busco la frecuencia de la tarea
                $diasFrecuencia = $this->getPeriodTarea($tipo, $id_solicitud);
                log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | diasfrecuencia: '.$diasFrecuencia);

                // si es preventivo ACTUALIZA NUEVAMENTE LA FECHA BASE_ OK!
                $estado = 'PL';

                if ($tipo == '3') {
                    //pongo nueva fecha base en preventivos
                    log_message('DEBUG', 'TRAZA | Calendario/guardar_agregar | tipo: '.$tipo.' fechalimte: '.$fecha_limite.' idsolicitud: '.$id_solicitud);
                    $this->Calendarios->actualizarFechaBasePreventivos($fecha_limite, $id_solicitud);
                    // cambia estado al preventivo
                    $tipo = 'preventivo';                    
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                }
                // cambia estado al predictivo
                if ($tipo == '5') {
                    $tipo = 'predictivo';
                    $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
                }

                // guarda las OT de acuerdo a la cant que entren en $cantidad_meses e insumos, herramientas y adjuntos
                $this->setOTenSerie($fecha_limite, $fec_programacion, $diasFrecuencia, $datos2, $tipo, $id_solicitud);
            }

        }
        return true;

    }

    public function getInfoTareEnBack($id_solicitud)
    {

        $this->db->select('sore_id');
        $this->db->from('tbl_back');
        $this->db->where('backId', $id_solicitud);
        $query = $this->db->get();
        $row = $query->row();
        $sore_id = $row->sore_id;

        if ($sore_id != null) {
            $this->db->select('case_id');
            $this->db->from('solicitud_reparacion');
            $this->db->where('id_solicitud', $sore_id);
            $resp = $this->db->get();
            $row2 = $resp->row();
            $case_id = $row2->case_id;
            return $case_id;
        } else {
            return 0;
        }
    }

    public function getDurTarea($tipo, $id_solicitud)
    {
        $duracion = 0;
        // devuelve la duracion de la taresa segun prde prev o backlog
        switch ($tipo) {
            case '1': // O Trabajo
                $this->db->select('orden_trabajo.duracion');
                $this->db->from('orden_trabajo');
                $this->db->where('orden_trabajo.id_orden', $id_solicitud);
                $query = $this->db->get();
                $duracion = $query->row('duracion');
                break;
            case '2': // Sol de Servicio
                break;
            case '3': // Preventivo
                $this->db->select('preventivo.prev_duracion');
                $this->db->from('preventivo');
                $this->db->where('preventivo.prevId', $id_solicitud);
                $query = $this->db->get();
                $duracion = $query->row('prev_duracion');
                break;
            case '4': // Backlog
                $this->db->select('tbl_back.back_duracion');
                $this->db->from('tbl_back');
                $this->db->where('tbl_back.backId', $id_solicitud);
                $query = $this->db->get();
                $duracion = $query->row('back_duracion');
                break;
            case '5': // Predictivo
                $this->db->select('predictivo.pred_duracion');
                $this->db->from('predictivo');
                $this->db->where('predictivo.predId', $id_solicitud);
                $query = $this->db->get();
                $duracion = $query->row('pred_duracion');
                break;
        }
        return $duracion;
    }

    // Devuelve periodo de la tarea original (backlog, predictivo, preventivo)
    public function getPeriodTarea($tipo, $id_solicitud)
    {
        // TODO: SELECCIONAR LOS TIPOS DE FRECUENCIA QUE SEAN TIEMPO  DE ACUERDO AL ID DE PERIODO Y TRADUCIR A DIAS PARA DEVOLVER
        $duracion = 0;
        // devuelve la duracion de la tarea segun prde prev o backlog
        switch ($tipo) {
            case '3': // Preventivo
                $this->db->select('preventivo.cantidad, periodo.descripcion');
                $this->db->from('preventivo');
				$this->db->join('periodo', 'periodo.idperiodo = preventivo.perido');
                $this->db->where('preventivo.prevId', $id_solicitud);

                $query = $this->db->get();
				$str = $this->db->last_query();
                $info = $query->result_array();
                log_message('DEBUG', 'TRAZA | Calendario/getPeriodTarea | info: '.json_encode($info));
                break;
            case '5': // Predictivo
                $this->db->select('predictivo.cantidad, periodo.descripcion');
                $this->db->from('predictivo');
                $this->db->where('predictivo.predId', $id_solicitud);
                $this->db->join('periodo', 'periodo.idperiodo = predictivo.periodo');
                $query = $this->db->get();
                $info = $query->result_array();
                break;
        }

        $duracion = $this->getDiasDuracion($info);
        log_message('DEBUG', 'TRAZA | Calendario/getPeriodTarea | info: '.json_encode($duracion));
        return $duracion;
    }

    public function getDiasDuracion($info)
    { // bien no tocar!
        //dump($info, 'info en duracion nenenenne: ');
        $cantidad = $info[0]["cantidad"];
        $especie = $info[0]["descripcion"];
        $dias = 0;
        switch ($especie) {
            case 'Mensual':
                $dias = 30 * $cantidad;
                break;
            case 'Semanal':
                $dias = 7 * $cantidad;
                break;
            case 'Semestral':
                $dias = 180 * $cantidad;
                break;
            case 'Anual':
                $dias = 365 * $cantidad;
                break;
            default:
                $dias = $cantidad;
                break;
        }
        return $dias;
    }
    // guarda las OT que correspondan de acuerdo a la fecuencia y $cantidad_meses
    public function setOTenSerie($fecha_limite, $fec_programacion, $diasFrecuencia, $datos2, $tipo, $id_solicitud)
    {

        //cargo libreria BPM
        $estado = 'PL';
        
        while ($fecha_limite >= $fec_programacion) {

            $idOT = $this->Calendarios->guardar_agregar($datos2);

            $this->setHerramInsPorTarea($idOT, $tipo, $id_solicitud);

            // setea estado 'PL' a las OT
            $this->Calendarios->cambiarEstado($id_solicitud, $estado, $tipo);
            // lanza proceso
            $contract = array(
                "idSolicitudServicio" => 0,
                "idOT" => $idOT,
            );
            $result = $this->bpm->lanzarProceso(BPM_PROCESS_ID, $contract);
            // guarda case id generado el lanzar proceso
            $respcaseOT = $this->Calendarios->setCaseidenOT($result['data']['caseId'], $idOT);
            // a la fecha de programacion le sumo la frecuencia en dias
            $nuev_fecha = strtotime('+' . $diasFrecuencia . 'day', strtotime($fec_programacion));
            $nuev_fecha = date('Y-m-d H:i:s', $nuev_fecha);
            // guardo la fecha nueva en el array para nuevva OT
            $datos2['fecha_program'] = $nuev_fecha;
            // actualizo la fecha de programacion
            $fec_programacion = $nuev_fecha;
        }

        return;
    }

    // Guarda herramientas e insumos que vienen de Backlog, Prevent y Predictivo
    public function setHerramInsPorTarea($idOT, $tipo, $id_solicitud)
    {

        switch ($tipo) {
            case 'predictivo': // Predictivo
                $herra = $this->Calendarios->getPredictivoHerramientas($id_solicitud);
                $insumos = $this->Calendarios->getPredictivoInsumos($id_solicitud);
                $adjunto = $this->Calendarios->getAdjunto($id_solicitud, $tipo);
                // Guarda el bacht de datos de herramientas
                if (!empty($herra)) {
                    $result['respHerram'] = $this->Calendarios->insertOTHerram($idOT, $herra);
                }
                // Guarda el bacht de datos de insumos
                if (!empty($insumos)) {
                    $result['respInsumo'] = $this->Calendarios->insertOTInsum($idOT, $insumos);
                }
                // guarda el adjunto en la taba Orden trabajos (url)
                if (!empty($adjunto)) {
                    $url = 'assets/filespredictivos/';
                    $file = $url . $adjunto;
                    $result['respAdjunto'] = $this->Calendarios->insertAdjunto($idOT, $file);

                }
                break;
            case 'backlog': //Backlog
                $herra = $this->Calendarios->getBacklogHerramientas($id_solicitud);
                $insumos = $this->Calendarios->getBacklogInsumos($id_solicitud);
                $adjunto = $this->Calendarios->getAdjunto($id_solicitud, $tipo);

                if (!empty($herra)) {
                    $result['respHerram'] = $this->Calendarios->insertOTHerram($idOT, $herra);
                }
                if (!empty($insumos)) {
                    $result['respInsumo'] = $this->Calendarios->insertOTInsum($idOT, $insumos);
                }
                if (!empty($adjunto)) {
                    $url = 'assets/filesbacklog/';
                    $file = $url . $adjunto;
                    $result['respAdjunto'] = $this->Calendarios->insertAdjunto($idOT, $file);
                }
                break;
            case 'preventivo': // Preventivos (tipo 3)
                $herra = $this->Calendarios->getPreventivoHerramientas($id_solicitud);
                $insumos = $this->Calendarios->getPreventivoInsumos($id_solicitud);
                $adjunto = $this->Calendarios->getAdjunto($id_solicitud, $tipo);
                if (!empty($herra)) {
                    $result['respHerram'] = $this->Calendarios->insertOTHerram($idOT, $herra);
                }
                if (!empty($insumos)) {
                    $result['respInsumo'] = $this->Calendarios->insertOTInsum($idOT, $insumos);
                }
                if (!empty($adjunto)) {
                    $url = 'assets/filespreventivos/';
                    $file = $url . $adjunto;
                    $result['respAdjunto'] = $this->Calendarios->insertAdjunto($idOT, $file);
                }
                break;

            default:

                break;
        }
    }
    // devuelve operarios para asignar OT
    public function getOperario()
    {
        $response = $this->Calendarios->getOperarios();
        echo json_encode($response);
    }
    // carga modal ver OT y ejecutar OT
    public function verEjecutarOT($idOt)
    {

        $this->load->model('traz-comp/Componentes');
        $this->load->model(ALM . 'new/Pedidos_Materiales');

        #COMPONENTE ARTICULOS
        $data['items'] = $this->Componentes->listaArticulos();
        $data['lang'] = lang_get('spanish', 'Ejecutar OT');

        #PEDIDO MATERIALES
        $info = new StdClass();
        $info->ortr_id = $idOt;

        $info->pema_id = $this->Pedidos_Materiales->getPedidoMaterialesOT($idOt)->pema_id;

        if (!$info->pema_id) {
            #NO EXISTE PEDIDO DE MATERIALES CREARA UNO CON INSUMOS OT
            $info->pema_id = $this->Pedidos_Materiales->crearPedidoOT($idOt);
        }

        $data['info'] = $info;

        // ifno de la OTrabajo
        $data['idOt'] = $idOt;
        $data['detaOT'] = $this->Calendarios->getDataOt($idOt);
        // Tarea estandar
        $data['tareas'] = $this->Calendarios->gettareas();
        // Datos de la Solicitud que le da origen a la OT
        $origen = $this->Calendarios->getOrigenOt($idOt);

        // si no hay solicitud de origen
        if ($origen[0]['id_solicitud'] == 0) {
            $numtipo = 0;
            $id_solicitud = $idOt;
        } else {
            $numtipo = $origen[0]['tipo'];
            $id_solicitud = $origen[0]['id_solicitud'];
												$data['componente'] = $this->Calendarios->getCompEquipoOT($numtipo,$id_solicitud,$idOt);
        }

        $data['infoSolicOrigen'] = $this->Calendarios->getInfoTareaProgram($numtipo, $id_solicitud);

        $task = $this->ObtenerTaskIDxOT($idOt);

        if ($task) {
            $data['btnVisibilidad'] = true;
        } else {
            $data['btnVisibilidad'] = false;
        }
        $data['task'] = $task;

        $this->load->view('calendar/view_OtEjecutar_modal', $data);
    }

    //Obtener TaskID por OtID (Cuando hay procesos generados, sino los genera)
    public function ObtenerTaskIDxOT($id)
    {

        $case_id = $this->Otrabajos->getCaseIdOT($id);
        $origenOT = $this->Otrabajos->getDatosOrigenOT($id);
        $tipo = $origenOT[0]['tipo'];
        $id_solicitud = $origenOT[0]['id_solicitud']; // id de sol reparacion

        // si viene de correctivo
        if ($tipo == 2) {
            $task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $case_id, 'Asignar Recursos y Tareas Urgente');
            return $task_id;
        }
        // si viene de backlog
        if ($tipo == 4) {

            //busco origen del backlog(tiene sore_id o no para diferenciar el origen item menu o SServicio)
            $idSolRep = $this->Otrabajos->getIdSolReparacion($id_solicitud);

            if ($idSolRep == null) { //viene de item menu

                $task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $case_id, 'Asignar Recursos y Tareas');
                return $task_id;

            } else { // backlog generado desde una SServicios
                // con id solicitud (BACKLOG) busco el case desde solicitud de reparacion
                $case_id = $this->Otrabajos->getCaseIdenSServicios($id);
                $task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $case_id, 'Asignar Recursos y Tareas');
                return $task_id;
            }
        }
        // Para el resto de las Tareas (Predictivo, Preventivo) devuelve task
        $task_id = $this->bpm->ObtenerTaskidXNombre(BPM_PROCESS_ID, $case_id, 'Asignar Recursos y Tareas');
        return $task_id;
    }
    /**
	* Trae listado de periodos cargados
	* @param 
	* @return array periodos cargados
	*/
    public function getperiodo(){
        log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Calendario | getperiodo()');

        $periodo = $this->Calendarios->getperiodo($this->input->post());
        if ($periodo) {
            $arre = array();
            foreach ($periodo as $row) {
                $arre[] = $row;
            }
            log_message('DEBUG','#TRAZA | TRAZ-TOOLS-MAN | Calendario | Periodos >> data '.json_encode($arre)); 
            echo json_encode($arre);
        } else {
            echo "nada";
        }

    }

    public function indexpred($permission)
    {

        $this->load->view('calendar/calendar2');
    }

    public function getPreventivo()
    {
        $data = $this->Calendarios->getPreventivos($this->input->post());
        if ($data == false) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }

    public function getbacklog()
    {
        $data = $this->Calendarios->getbacklog($this->input->post());
        if ($data == false) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }

    public function getcalendarpred()
    {
        $data = $this->Calendarios->getpred($this->input->post());
        if ($data == false) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }

    public function getCorrectPorId()
    {

        $id = $_POST['id'];
        $data = $this->Calendarios->getCorrectPorIds($id);

        echo json_encode($data);
    }

    public function getBackPorId()
    {

        $id = $_POST['id'];
        $data = $this->Calendarios->getBackPorIds($id);
        //dump($data, 'backlog info: ');
        echo json_encode($data);
    }

    public function getPredictPorId()
    {

        $id = $_POST['id'];
        $data = $this->Calendarios->getPredictPorIds($id);

        echo json_encode($data);
    }

    // Cambio de dia nuevo de programacion
    public function updateDiaProg()
    {

        $id = $this->input->post('id');
        $diaNuevo = $this->input->post('prog');
        $response = $this->Calendarios->updateDiaProgramacion($id, $diaNuevo);

        echo json_encode($response);
    }

    // Devuelve duracion de tarea de acuerdo a un id de OT
    public function getDuracionOTrabajo($id)
    {

        $this->db->select('orden_trabajo.duracion');
        $this->db->from('orden_trabajo');
        $this->db->where('orden_trabajo.id_orden', $id);
        $query = $this->db->get();
        $duracion = $query->row('duracion');
        return $duracion;
    }

    public function updateDuracion()
    {

        $id = $this->input->post('id');
        $duracion = $this->input->post('duracion'); // duracion adicional

        $nueva = $this->getDuracionOTrabajo($id, $duracion);
        $nueva = $nueva + $duracion;

        $response = $this->Calendarios->updateDuraciones($id, $nueva);
        echo json_encode($response);
    }

    /* INTEGRACION CON BPM */
    public function getInfoTareaporIdSolicitud($id_solicitud, $tipo)
    {

        if ($tipo == 'correctivo') {
            //$id_solicitud    -> id sol de servicios
            $caseId = $this->Calendarios->getCaseIdporIdSolServicios($id_solicitud);
        }
        if ($tipo == 'backlog') {
            //$id_solicitud    -> id de backlog
            $caseId = $this->Calendarios->getCaseIdporIdBacklog($id_solicitud);
        }

        // traer de bpm el id de tarea (id)
        $actividades = $this->bpm->ObtenerActividades(BPM_PROCESS_ID, $caseId);
        $infoTarea['taskId'] = json_decode($this->getIdTask($actividades, $tipo), true);
        $infoTarea['caseId'] = $caseId;
        return $infoTarea;

    }
    // devuelve task_id coincidente con la actividad 'Analisis de Solicitud de Servicio'
    public function getIdTask($actividades, $tipo)
    {

        if ($tipo == 'correctivo') {
            $actividad = 'Planificar Solicitud';
        }
        if ($tipo == 'backlog') {
            $actividad = 'Planificar Backlog';
        }

        for ($i = 0; $i < count($actividades); $i++) {
            if ($actividades[$i]["displayName"] == $actividad) {
                $id = $actividades[$i]["id"];
            }
        }
        return $id;
    }

    public function cerrarTarea($idTask)
    {

        return $this->bpm->cerrarTarea($idTask);

    }

    public function actualizarIdOTenBPM($caseId, $idOTnueva)
    {
        $this->bpm->actualizarIdOT($caseId, $idOTnueva);
    }

    public function panelFiltro()
    {
        $data['filtro'] = $this->Calendarios->opcionesFiltro();
        $this->load->view('calendar/filtro', $data);
    }

    /*    ./ INTEGRACION CON BPM */
}
