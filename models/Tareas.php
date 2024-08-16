<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tareas extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(FRM . 'Forms');
        $this->assetDB = $this->load->database('asset_db', TRUE);
    }
/* TAREAS ASSET ORIGINALES (TAREAS ESTANDAR)*/
    public function Listado_Tareas()
    {
        $userdata = $this->session->userdata('user_data');
        $empresaId = $userdata[0]['id_empresa'];
        $this->assetDB->where('estado', 'AC');
        $this->assetDB->where('tareas.id_empresa', $empresaId);
        $query = $this->assetDB->get('tareas');
        if ($query->num_rows() != 0) {
            return $query->result_array();
        }
    }

    public function Obtener_Tareas($id)
    {
        $this->assetDB->where('id_tarea', $id);
        $query = $this->assetDB->get('tareas');
        if ($query->num_rows() != 0) {
            return $query->result_array();
        }
    }

    public function Guardar_Tareas($data)
    {
        $userdata = $this->session->userdata('user_data');
        $data['id_empresa'] = $userdata[0]['id_empresa'];
        $query = $this->assetDB->insert("tareas", $data);
        return $query;
    }

    public function Guardar_SubTareas($data)
    {
        //$userdata           = $this->session->userdata('user_data');
        //$data['id_empresa'] = $userdata[0]['id_empresa'];
        $query = $this->assetDB->insert_batch('tbl_listarea', $data);
        return $query;
    }

    public function Modificar_Tareas($data)
    {
        $userdata = $this->session->userdata('user_data');
        $data['id_empresa'] = $userdata[0]['id_empresa'];
        $query = $this->assetDB->update('tareas', $data, array('id_tarea' => $data['id_tarea']));
        return $query;
    }

    public function Eliminar_Tareas($id)
    {
        $this->assetDB->set('estado', 'AN');
        $this->assetDB->where('id_tarea', $id);
        $query = $this->assetDB->update('tareas');
        return $query;
    }

/* ./ TAREAS ASSET ORIGINALES (TAREAS ESTANDAR)*/

/* INTEGRACION CON BPM */

/*********** funciones nuevas de asset */
    //devuelve array con subtareas
    public function getSubtareaseEstandar($nomTarea)
    {

        $idTarea = $this->getIdTareaSTD($nomTarea);
        $subtareas = array();

        if ($idTarea != 0) {
            $subtareas = $this->getSubtareas($idTarea);
            return $subtareas;
        } else {
            return $subtareas;
        }
    }
    // devuelve id de tarea estandar por nombre
    public function getIdTareaSTD($id_OT)
    {

        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->select('tareas.id_tarea,
											tareas.descripcion,
											tareas.id_empresa');
        $this->assetDB->from('tareas');
        $this->assetDB->join('orden_trabajo', 'tareas.id_tarea = orden_trabajo.id_tarea');
        $this->assetDB->where('orden_trabajo.id_orden', $id_OT);
        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }
    // devuelve subtareas por
    public function getSubtareas($ot)
    {
        $this->assetDB->select('tbl_listarea.*,
												asp_subtareas.tareadescrip AS subtareadescrip,
												asp_subtareas.id_subtarea,
												asp_subtareas.duracion_prog,
												asp_subtareas.form_asoc');
        $this->assetDB->from('tbl_listarea');
        $this->assetDB->join('asp_subtareas', 'asp_subtareas.id_subtarea = tbl_listarea.id_subtarea', 'left');
        $this->assetDB->where('tbl_listarea.id_orden', $ot);
        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function ObtenerSubtareas($tarea_std)
    {
        $this->assetDB->where('id_tarea', $tarea_std);
        return $this->assetDB->get('asp_subtareas')->result_array();
    }
    // Cambia estado a Terminado o Curso de subtareas
    public function cambiarEstadoSubtask($idlistarea, $estado)
    {
        $dato = array('estado' => $estado);
        $this->assetDB->where('tbl_listarea.id_listarea', $idlistarea);
        $response = $this->assetDB->update('tbl_listarea', $dato);
        return $response;
    }
    // Cierra Tareas estandar en BPM
    public function terminarTareaStandarenBPM($idTarBonita, $param)
    {

        $method = '/execution';
        $resource = 'API/bpm/userTask/';
        $url = BONITA_URL . $resource . $idTarBonita . $method;
        //$url = BONITA_URL.$resource.$usrId.$method;
        file_get_contents($url, false, $param);
        $response = $this->parseHeaders($http_response_header);
        return $response;
    }

    // genera una entrada en el historial de lectura con la ultima lectura generada en el informe de servicio
    public function setUltimaLecturaIS($data)
    {
        $id_equipo = $data["id_equipo"];
        $lectura = $data["lectura"];
        $fecha = $data["fecha"];
        $usrid = $data["usrId"];
        $observacion = $data["observacion"];
        $operario_nom = $data["operario_nom"];
        $turno = $data["turno"];
        $estado = $data["estado"];

        //log_message('DEBUG','#Main/setUltimaLecturaIS |  estado: '.$estado);


        $sql = "INSERT INTO historial_lecturas(id_equipo,lectura,fecha,usrId,observacion,operario_nom,turno,estado)
            VALUES('" . $id_equipo . "','" . $lectura . "','" . $fecha . "','" . $usrid . "','" . $observacion . "','" . $operario_nom . "','" . $turno . "','" . $estado . "')
            ";

        $query = $this->assetDB->query($sql);

        return $query;
    }

    // trae tareas por ID de usuario
    public function getTareas($param)
    {

        $userdata = $this->session->userdata('user_data');
        $userBpm = $userdata[0]['userBpm']; // guarda usuario logueado en BPM

        //$tareas = file_get_contents(BONITA_URL.'API/bpm/humanTask?p=0&c=10&f=user_id%3D5', false, $param);
        $resource = 'API/bpm/humanTask?p=0&c=1000&f=user_id%3D';
        $url = BONITA_URL . $resource . $userBpm;
        $tareas = file_get_contents($url, false, $param);

        $tar = json_decode($tareas, true);

        return $tar;
    }
    // agrega datos de pedido de trabajo desde la base local
    public function AgregarDatos($tareas)
    {

        $tar = json_decode($tareas, true);
        foreach ($tar as $key => $value) {

            $caseId = $tar[$key]["caseId"];
            $datos = $this->getDatPedidoTrabajo($caseId);
            $tar[$key]['petr_id'] = $datos[0]['petr_id'];
            $tar[$key]['cod_interno'] = $datos[0]['cod_interno'];
        }
        return $tar;
    }
    //devuelve datos de tarea por nombre
    public function getDatosTarea($nombre)
    {
        $this->assetDB->where('descripcion', $nombre);
        return $this->assetDB->get('tareas')->result_array()[0];
    }
    // Devuelve el id de tareas de trazaj correspond al id_tarea bonita para detatareas
    // function getIdTareaTraJobs($idBonita,$param){

    //     $urlResource = 'API/bpm/activityVariable/';
    //     $idListEnBPM = '/trazajobsTaskId';

    //     try {
    //         $idTJ = @file_get_contents(BONITA_URL.$urlResource.$idBonita.$idListEnBPM , false, $param);
    //         $idTJobs = json_decode($idTJ,true); //sin true no se puede acceder
    //         $id_listarea = $idTJobs["value"];
    //     } catch (Exception $e) {
    //         echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    //         $id_listarea = 0;
    //     }

    //     return $id_listarea;
    // }
    // Trae los datos de la tarea desde BPM
    public function getDatosBPM($idTarBonita, $param)
    {

        $urlResource = BONITA_URL . 'API/bpm/humanTask/';
        $data = file_get_contents($urlResource . $idTarBonita, false, $param);
        return $data;
    }
    // Devuelve id de OT por id de BPM
    public function getIdOtPorIdBPM($idTarBonita)
    {

        $this->assetDB->select('orden_trabajo.id_orden');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.bpm_task_id_plan', $idTarBonita);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('id_orden');
        } else {
            return 0;
        }
    }
    //devuelve el id de tarea estandar asociada a listarea
    public function getTarea_idListarea($id_listarea)
    {

        $this->assetDB->select('tbl_listarea.id_tarea');
        $this->assetDB->from('tbl_listarea');
        $this->assetDB->where('tbl_listarea.id_listarea', $id_listarea);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('id_tarea');
        } else {
            return false;
        }
    }
    // devuelve id de equipo por id Sol Servicios
    public function getIdEquipoPorIdSolServ($id_SS)
    {

        $this->assetDB->select('solicitud_reparacion.id_equipo');
        $this->assetDB->from('solicitud_reparacion');
        $this->assetDB->where('solicitud_reparacion.id_solicitud', $id_SS);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('id_equipo');
        } else {
            return false;
        }
    }
    // devuelve id de equipo por id OT
    public function getIdEquipoPorIdOT($id_OT)
    {

        $this->assetDB->select('orden_trabajo.id_equipo');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_orden', $id_OT);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('id_equipo');
        } else {
            return false;
        }
    }
    // Verifica si la tarea fue guardada la fecha de inicio
    public function confInicioTareas($id_OT)
    {
        $this->assetDB->select('orden_trabajo.fecha_inicio');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_orden', $id_OT);
        $query = $this->assetDB->get();
        $row = $query->row('fecha_inicio');
        return $row;
    }
    // marca inicio de Tarea en OT
    public function inicioTareas($id_OT)
    {
        $fechaInicio = date("Y-m-d H:i:s");
        $estado = 'C';
        $this->assetDB->where('id_orden', $id_OT);
        $result = $this->assetDB->update('orden_trabajo', array('fecha_inicio' => $fechaInicio));
        return $result;
    }

    //actualiza latitud y longitud de la OT
    public function actualizarLatLng($lati, $long, $id_OT)
    {
        $this->assetDB->where('id_orden', $id_OT);
        $result = $this->assetDB->update('orden_trabajo', array('latitud' => $lati, 'longitud' => $long));
        return $result;
    }

    // marca fin de Tarea en OT
    public function finTareas($id_OT)
    {
        $fechaFin = date("Y-m-d H:i:s");
        $estado = 'T';
        $this->assetDB->where('id_orden', $id_OT);
        $result = $this->assetDB->update('orden_trabajo', array('fecha_terminada' => $fechaFin, 'estado' => $estado));
        return $result;
    }

    // cambia de estado la Tareas(SServ, Prevent, Predic, Back y OT), VerificaInforme sirve para no modificar la fecha inicio en OT en el paso verificaInforme
    public function cambiarEstado($id_solicitud, $estado, $tipo, $VerificaInforme = null)
    {
        $f_inicio =  date("Y-m-d H:i:s"); 

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
            $this->assetDB->set('estado', $estado);
            if(!$VerificaInforme) $this->assetDB->set('fecha_inicio', $f_inicio);
            $this->assetDB->where('id_orden', $id_solicitud);
            return $this->assetDB->update('orden_trabajo');
        }

        if ($tipo == 'informe servicios') {
            $this->assetDB->set('estado', $estado);
            $this->assetDB->where('id_ot', $id_solicitud);
            return $this->assetDB->update('orden_servicio');
        }

        return $response;
    }

    //devuelve valores de todos los datos de la OT para mostrar en modal.
    public function getOrigenOt($idot)
    {
        $this->assetDB->select('orden_trabajo.tipo, orden_trabajo.id_solicitud');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_orden', $idot);

        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }
    // devueve sore_id por id de backlog
    public function getSoreIdporBackId($id_solicitud)
    {
        $this->assetDB->select('tbl_back.sore_id');
        $this->assetDB->from('tbl_back');
        $this->assetDB->where('tbl_back.backId', $id_solicitud);
        $query = $this->assetDB->get();
        return $query->row('sore_id');
    }

    /*FORMULARIOS */
    // Devuelve form asociado a una tarea std
    public function getIdFormPorIdTareaSTD($idTareaStd)
    {

        $this->assetDB->select('tareas.form_asoc');
        $this->assetDB->from('tareas');
        $this->assetDB->where('tareas.id_tarea', $idTareaStd);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('form_asoc');
        } else {
            return 0;
        }
    }
    // Comprueba si hay form guardado asoc a id de orden y de tarea
    public function getEstadoForm($bpm_task_id, $idForm)
    {

        $this->assetDB->select('frm_formularios_completados.LITA_ID');
        $this->assetDB->from('frm_formularios_completados');
        $this->assetDB->where('frm_formularios_completados.LITA_ID', $bpm_task_id);
        $this->assetDB->where('frm_formularios_completados.FORM_ID', $idForm);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Guarda la configuracion inicial del formulario
    public function setFormInicial($bpm_task_id, $idFormAsoc, $ot_id)
    { //$id_listarea){

        $userdata = $this->session->userdata('user_data');
        $usrId = $userdata[0]['usrId']; // guarda usuario logueado
        $empId = $userdata[0]['id_empresa'];
        $i = 1;
        $dat = array();
        //    instancia form y devuelve id  para relacionar form con OT
        //    en form_completados(desp se toca frm_instanciasform)
        $idInstanciaForm = $this->instanciarForm($ot_id);

        // Trae la info del form sin valores validos desp se actualiza al guardar
        $form = $this->getFormInicial($idFormAsoc);

        // Agrego id de usuario y demas datos al array para insertar
        foreach ($form as $key) {
            $key['USUARIO'] = $usrId;
            $key['LITA_ID'] = $bpm_task_id; //$id_listarea;
            $key['INFO_ID'] = $idInstanciaForm; //
            $key['ORDEN'] = $i;
            $key['ID_EMPRESA'] = $empId; // guarda id de empresa logueada
            $i++;
            $dat[$i] = $key;
        }

        $response = $this->assetDB->insert_batch('frm_formularios_completados', $dat);

        return $idInstanciaForm;
    }

    // Trae configuracion de form inicial para guardar en frm_frm_completados
    public function getFormInicial($idFormAsoc)
    {

        ////  ID DE EMPRESA PARA CLOUD
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $sql = "SELECT
					form.form_id AS FORM_ID,
					form.nombre AS FORM_NOMBRE,
					form.fec_creacion AS FEC_CREACION,
					cate.NOMBRE AS CATE_NOMBRE,
					grup.NOMBRE AS GRUP_NOMBRE,
					tida.NOMBRE AS TIDA_NOMBRE,
					valo.NOMBRE AS VALO_NOMBRE,
					valo.ORDEN
					FROM
					frm_formularios form,
					frm_categorias cate,
					frm_grupos grup ,
					frm_tipos_dato tida,
					frm_valores valo
					where FORM.FORM_ID = CATE.FORM_ID
					AND CATE.CATE_ID = GRUP.CATE_ID
					AND GRUP.GRUP_ID = VALO.GRUP_ID
					AND TIDA.TIDA_ID = VALO.TIDA_ID
					AND FORM.FORM_ID = $idFormAsoc
					ORDER BY cate.ORDEN,grup.ORDEN,valo.ORDEN
					AND FORM.ID_EMPRESA = $empId";

        $query = $this->assetDB->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    // instancia el form inicial relacionandolo con la OT
    public function instanciarForm($ot_id)
    {

        $data['ortra_id'] = $ot_id;
        $this->assetDB->insert('frm_instancias_formulario', $data);
        return $idInstanciaForm = $this->assetDB->insert_id();
    }

    // trae instancias de form guardado por id de OT
    public function getInstaciasForm($id_OT)
    {

        $this->assetDB->select('frm_instancias_formulario.*');
        $this->assetDB->from('frm_instancias_formulario');
        $this->assetDB->where('ortra_id', $id_OT);
        $query = $this->assetDB->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $inst[$i] = $row->info_id;
            $i++;
        }
        return $inst;
    }
    // Trae form para dibujar pantalla (agregar where de id de form)
    public function get_form($infoId)
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];
        $sql = "SELECT foco.FOCO_ID AS idValor,
						foco.FORM_ID,
						foco.FORM_NOMBRE,
						'' AS habilitado,
						foco.FEC_CREACION,
						foco.CATE_NOMBRE AS nomCategoria,
						'' AS idCategoria,
						foco.GRUP_NOMBRE AS nomGrupo,
						foco.TIDA_NOMBRE AS nomTipoDatos,
						'' AS idGrupo,
						foco.VALO_NOMBRE AS nomValor,
						foco.TIDA_ID,
						foco.VALOR AS valDefecto,
						'' AS LONGITUD,
						'' AS PISTA,
						foco.VALO_ID,
						foco.OBLIGATORIO as obligatorio,
						foco.ORDEN
						FROM
						frm_formularios_completados foco
						WHERE foco.INFO_ID = $infoId
						AND foco.ID_EMPRESA = $empId
						ORDER BY foco.ORDEN";

        $query = $this->assetDB->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function validarCamposObligatorios($idForm, $idOT)
    {

        $this->assetDB->select('count(*) > 0 AS result');
        $this->assetDB->from('frm_formularios_completados');
        $this->assetDB->join('frm_instancias_formulario', 'frm_instancias_formulario.info_id = 																				frm_formularios_completados.INFO_ID');
        $this->assetDB->where('frm_instancias_formulario.ortra_id', $idOT);
        $this->assetDB->where('FORM_ID', $idForm);
        $this->assetDB->where('frm_formularios_completados.OBLIGATORIO', true);
        $this->assetDB->where('frm_formularios_completados.VALIDADO', false);
        $query = $this->assetDB->get();
        return $query->row('result');
    }

    // Arma array p/ insertar en frm_formularios_completados por focoId
    public function getDatos($focoId)
    {
        //dump($focoId, 'en getdatos: ');
        $this->assetDB->select('frm_formularios_completados.*');
        $this->assetDB->from('frm_formularios_completados');
        $this->assetDB->where('frm_formularios_completados.FOCO_ID', $focoId);
        $query = $this->assetDB->get();

        foreach ($query->result_array() as $row) {
            $response['FORM_ID'] = $row['FORM_ID'];
            $response['FORM_NOMBRE'] = $row['FORM_NOMBRE'];
            $response['CATE_NOMBRE'] = $row['CATE_NOMBRE'];
            $response['GRUP_NOMBRE'] = $row['GRUP_NOMBRE'];
            $response['VALO_NOMBRE'] = $row['VALO_NOMBRE'];
            $response['TIDA_NOMBRE'] = $row['TIDA_NOMBRE'];
            $response['VALOR'] = $row['VALOR'];
        }

        return $response;
    }
    // Inserta datos de Form en frm_formularios_completados
    public function UpdateForm($datos, $key)
    {
        //dump($datos, 'datos de form: ');
        $this->assetDB->where('FOCO_ID', $key);
        $response = $this->assetDB->update('frm_formularios_completados', $datos);
        return $response;
    }
    /*    ./ FORMULARIOS */

    // devuelve detalle de tareas para notificacion standart a partir de id_listarea
    public function detaTareas($id_listarea)
    {

        $this->assetDB->select('tbl_listarea.id_listarea,
							tbl_listarea.id_orden,
							tareas.duracion_std,
							tbl_listarea.tareadescrip,
							tbl_listarea.id_tarea,
							tbl_listarea.id_usuario,
							tbl_listarea.estado,
							tbl_listarea.fecha');
        $this->assetDB->from('tbl_listarea');
        $this->assetDB->join('tareas', 'tareas.id_tarea = tbl_listarea.id_tarea');
        $this->assetDB->where('tbl_listarea.id_listarea', $id_listarea);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    // devuelve id tarea y form asociado por nomb de tarea BPM
    public function getidFormTareaBPM($nomtarea)
    {

        $this->assetDB->select('tareas.id_tarea,
							tareas.form_asoc');
        $this->assetDB->from('tareas');
        $this->assetDB->where('tareas.descripcion', $nomtarea);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }
    //obtener Comentarios
    public function ObtenerComentariosBPM($caseId, $param)
    {
        $processInstance = 'processInstanceId%3D' . $caseId;
        $comentarios = file_get_contents(BONITA_URL . 'API/bpm/comment?f=' . $processInstance . '&o=postDate%20DESC&p=0&c=200&d=userId', false, $param);
        return json_decode($comentarios, true);
    }

    /* TAREAS BPM */
    // Tomar Tareas
    public function tomarTarea($idTarBonita, $param)
    {

        try {
            $resource = 'API/bpm/humanTask/';
            $url = BONITA_URL . $resource . $idTarBonita;

            file_get_contents($url, false, $param);
            $response = $this->parseHeaders($http_response_header);

            return $response;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
    // Soltar Tareas
    public function soltarTarea($idTarBonita, $param)
    {

        $resource = 'API/bpm/humanTask/';
        $url = BONITA_URL . $resource . $idTarBonita;
        file_get_contents($url, false, $param);
        $response = $this->parseHeaders($http_response_header);
        return $response;
    }

    public function getIdOtPorid_SS($id_SS)
    {
        $this->assetDB->select('orden_trabajo.id_orden');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_solicitud', $id_SS);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->row('id_orden');
        } else {
            return 0;
        }

    }

    public function getJustifiacionOT($caseId)
    {
        $this->assetDB->select('orden_trabajo.justificacion');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.case_id', $caseId);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->row('justificacion');
        } else {
            return 0;
        }
    }

    public function setJustificacionOT($idOrden, $justificacion)
    {

        $this->assetDB->set('justificacion', $justificacion);
        $this->assetDB->where('id_orden', $idOrden);
        $query = $this->assetDB->update('orden_trabajo');
        return $query;
    }

    public function getIdOTPorIdCaseEnBD($caseId)
    {
        $this->assetDB->select('orden_trabajo.id_orden');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.case_id', $caseId);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->row('id_orden');
        } else {
            return 0;
        }
    }

    // trae id de OT por caseid
    public function getIdOTPorIdCase($caseId, $param)
    {
        // [URL_BONITA]/API/bpm/caseVariable/:caseId/gIdOT
        $urlResource = BONITA_URL . 'API/bpm/caseVariable/';
        $var = '/gIdOT';
        $data = json_decode(file_get_contents($urlResource . $caseId . $var, false, $param), true);

        return $data;
    }

    // devuelve id de backlog
    public function getIdBackporid_OT($id_OT, $tipo)
    {

        $this->assetDB->select('orden_trabajo.id_solicitud AS idBacklog');
        $this->assetDB->from('orden_trabajo');
        $this->assetDB->where('orden_trabajo.id_orden', $id_OT);
        $this->assetDB->where('orden_trabajo.tipo', $tipo);
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->row('idBacklog');
        } else {
            return 0;
        }
    }
    // Trae datos de backlog para editar
    public function geteditar($id_SS)
    {

        $this->assetDB->select('tbl_back.*,
												equipos.codigo,
												equipos.descripcion,
												equipos.id_equipo,
												equipos.fecha_ingreso,
												equipos.ubicacion,
												marcasequipos.marcadescrip AS marca');
        $this->assetDB->from('tbl_back');
        $this->assetDB->join('equipos', 'tbl_back.id_equipo = equipos.id_equipo');
        $this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
        $this->assetDB->where('tbl_back.sore_id', $id_SS);
        $query = $this->assetDB->get();
        //dump_exit($this->assetDB->last_query());
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }
    // terminar tarea analisis de Solicitud de Servicios
    public function cerrarTarea($idTarBonita, $param)
    {
        ///API/bpm/userTask/:userTaskId/execution
        $method = '/execution';
        $resource = 'API/bpm/userTask/';
        $url = BONITA_URL . $resource . $idTarBonita . $method;
        file_get_contents($url, false, $param);
        $response = $this->parseHeaders($http_response_header);
        return $response;
    }
    public function procedimientos($ot){

        $this->assetDB->select('B.prev_adjunto');
        $this->assetDB->from('orden_trabajo as A');
        $this->assetDB->join('preventivo as B', 'A.id_solicitud = B.prevId', 'left');
        $this->assetDB->where('A.id_orden', $ot);

        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return false;
        }  
    }

    public function actualizarIdOTenBPM($caseId, $param)
    {

        // /API/bpm/caseVariable/:caseId/:variableName
        $variableName = '/execution';
        $resource = 'API/bpm/caseVariable/';
        $variableName = 'gIdOT';
        $url = BONITA_URL . $resource . $caseId . "/" . $variableName;
        file_get_contents($url, false, $param);
        $response = $this->parseHeaders($http_response_header);
    }
    // toma la respuesta del server y devuelve el codigo de respuesta solo
    public function parseHeaders($headers)
    {
        $head = array();
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['reponse_code'] = intval($out[1]);
                }

            }
        }
        return $head;
    }
    // Comentarios
    public function GuardarComentarioBPM($param)
    {
        $respuesta = file_get_contents(BONITA_URL . 'API/bpm/comment', false, $param);
        return $respuesta;
    }
    // Agrega datos desde BPM y BD local
    public function CompletarToDoList($data , $search = '')
    {
        $filtrado = [];
        foreach ($data as $key => $value) {

            if ($value['processId'] == BPM_PROCESS_ID_PEDIDOS_NORMALES) {
                $res = $this->assetDB->get_where('alm_pedidos_materiales', ['case_id' => $value['caseId']])->row();
                $data[$key]['pema_id'] = $res->pema_id;
                $data[$key]['ot'] = $res->ortr_id;

                $this->assetDB->select('B.id_solicitud as "ss", A.tipo as "tip_ta", id_orden as "ot", A.descripcion as "desc", causa, X.codigo as "desceq", P.cliRazonSocial as "nomCli", Z.descripcion as "descsec"');
                $this->assetDB->from('orden_trabajo  as A');
                $this->assetDB->join('solicitud_reparacion as B', 'A.case_id = B.case_id', 'left');
                $this->assetDB->join('equipos as X', 'X.id_equipo = A.id_equipo', 'left');
                $this->assetDB->join('admcustomers as P', 'P.cliId = X.id_customer');
                $this->assetDB->join('sector as Z', 'Z.id_sector = X.id_sector', 'left');
                $this->assetDB->where('A.id_orden', $res->ortr_id);
                $res = $this->assetDB->get()->first_row();

                $data[$key]['displayDescription'] = $res->desc;
                $data[$key]['equipoDesc'] = $res->desceq;
                $data[$key]['sectorDesc'] = $res->descsec;
																$data[$key]['nomCli'] = $res->nomCli;
				
																$data = $this->infoUser($data, $key);
                if($search){
                    if((strpos(strtoupper($data[$key]['displayDescription']), strtoupper($search)) !== false) 
                    || (strpos(strtoupper($data[$key]['equipoDesc']), strtoupper($search)) !== false) 
                    || (strpos($data[$key]['ss'], $search) !== false) 
                    || (strpos($data[$key]['ot'], $search) !== false) 
                    || (strpos($data[$key]['pema_id'], $search) !== false) )
                    {
                        array_push($filtrado,$data[$key]);
                    }
                }
                continue;
                //log_message('DEBUG','#TRAZA | #TAREA >> CompletarToDoList >> BPM_PROCESS_ID_PEDIDOS_NORMALES');
            }

            
            if ($value['processId'] == BPM_PROCESS_ID_PEDIDOS_EXTRAORDINARIOS) {
                $res = $this->assetDB->get_where('alm_pedidos_extraordinario', ['case_id' => $value['caseId']])->row();
                $data[$key]['pema_id'] = $res->peex_id;
                $data[$key]['ot'] = $res->ortr_id;
                if($search){

                if((strpos($data[$key]['ot'], $search) !== false) 
                || (strpos($data[$key]['pema_id'], $search) !== false) )
                    {
                        array_push($filtrado,$data[$key]);
                    }
                }
                continue;
                //log_message('DEBUG','#TRAZA | #TAREA >> CompletarToDoList >> BPM_PROCESS_ID_PEDIDOS_EXTRAORDINARIOS');

            }

            $this->assetDB->select('A.id_solicitud as \'ss\', B.tipo as  \'tip_ta\', id_orden as \'ot\', B.descripcion as \'desc\', causa, X.codigo as \'desceq\', P.cliRazonSocial as \'nomCli\', Z.descripcion as \'descsec\'');

            $this->assetDB->from('solicitud_reparacion as A');
            $this->assetDB->join('orden_trabajo as B', 'A.case_id = B.case_id', 'left');
            $this->assetDB->join('equipos as X', 'X.id_equipo = A.id_equipo', 'left');
            $this->assetDB->join('admcustomers as P', 'P.cliId = X.id_customer');
            $this->assetDB->join('sector as Z', 'Z.id_sector = X.id_sector', 'left');
            $this->assetDB->where('A.case_id', $value['caseId']);
            $res = $this->assetDB->get()->first_row();
            
            //log_message('DEBUG','#TRAZA | #TAREA >> CompletarToDoList  $res>> '.json_encode($res));

            if (!$res) {

                $this->assetDB->select('A.id_solicitud as \'ss\', B.tipo as  \'tip_ta\', id_orden as \'ot\', B.descripcion as \'desc\', causa, X.codigo as \'desceq\', P.cliRazonSocial as \'nomCli\', Z.descripcion as \'descsec\'');
                $this->assetDB->from('solicitud_reparacion as A');
                $this->assetDB->from('orden_trabajo as B');
                $this->assetDB->from('tbl_back as C');
                $this->assetDB->join('equipos as X', 'X.id_equipo = A.id_equipo', 'left');
                $this->assetDB->join('admcustomers as P', 'P.cliId = X.id_customer');
                $this->assetDB->join('sector as Z', 'Z.id_sector = X.id_sector', 'left');
                $this->assetDB->where('A.case_id', $value['caseId']);
                $this->assetDB->where('C.backId', 'B.id_solicitud', 'left');
                $this->assetDB->where('C.sore_id', 'A.id_solicitud', 'left');

                $res = $this->assetDB->get()->first_row();
                
                if (!$res) {

                    $this->assetDB->select('id_orden as \'ot\', B.tipo as  \'tip_ta\', B.descripcion as \'desc\', causa, X.codigo as \'desceq\', P.cliRazonSocial as \'nomCli\', Z.descripcion as \'descsec\'');
                    $this->assetDB->where('A.case_id', $value['caseId']);
                    $this->assetDB->from('solicitud_reparacion as A');
                    $this->assetDB->join('orden_trabajo as B', 'B.id_solicitud = A.id_solicitud', 'left');
                    $this->assetDB->join('equipos as X', 'X.id_equipo = A.id_equipo', 'left');
                    $this->assetDB->join('admcustomers as P', 'P.cliId = X.id_customer');
                    $this->assetDB->join('sector as Z', 'Z.id_sector = X.id_sector', 'left');
                    $res = $this->assetDB->get()->first_row();

                    if (!$res) {

                        $this->assetDB->select('id_orden as \'ot\', A.tipo as  \'tip_ta\', A.descripcion as \'desc\', X.codigo as \'desceq\', P.cliRazonSocial as \'nomCli\', Z.descripcion as \'descsec\'');
                        $this->assetDB->from('orden_trabajo as A');
                        $this->assetDB->join('equipos as X', 'X.id_equipo = A.id_equipo', 'left');
                        $this->assetDB->join('admcustomers as P', 'P.cliId = X.id_customer');
                        $this->assetDB->join('sector as Z', 'Z.id_sector = X.id_sector', 'left');
                        $this->assetDB->where('A.case_id', $value['caseId']);
                        $res = $this->assetDB->get()->first_row();

                        $data[$key]['ss'] = '';
                        $data[$key]['ot'] = $res->ot;
                        $data[$key]['tip_ta'] = $res->tip_ta;
                        $data[$key]['displayDescription'] = $res->desc;
                        $data[$key]['equipoDesc'] = $res->desceq;
                        $data[$key]['sectorDesc'] = $res->descsec;
                        $data[$key]['nomCli'] = $res->nomCli;
                    } else {
                        $data[$key]['ss'] = $res->ss;
                        $data[$key]['ot'] = $res->ot;
                        $data[$key]['tip_ta'] = $res->tip_ta;
                        $data[$key]['equipoDesc'] = $res->desceq;
                        $data[$key]['sectorDesc'] = $res->descsec;
                        $data[$key]['nomCli'] = $res->nomCli;
                        if ($res->desc != null) {
                            $data[$key]['displayDescription'] = $res->desc;
                        } else {
                            $data[$key]['displayDescription'] = $res->causa;
                        }
                    }
                } else {
                    $data[$key]['ss'] = $res->ss;
                    $data[$key]['ot'] = $res->ot;
                    $data[$key]['tip_ta'] = $res->tip_ta;
                    $data[$key]['equipoDesc'] = $res->desceq;
                    $data[$key]['sectorDesc'] = $res->descsec;
                    $data[$key]['nomCli'] = $res->nomCli;
                    if ($res->desc != null) {
                        $data[$key]['displayDescription'] = $res->desc;
                    } else {
                        $data[$key]['displayDescription'] = $res->causa;
                    }
                }
            } else {
                $data[$key]['ss'] = $res->ss;
                $data[$key]['ot'] = $res->ot;
                $data[$key]['tip_ta'] = $res->tip_ta;
                $data[$key]['equipoDesc'] = $res->desceq;
                $data[$key]['sectorDesc'] = $res->descsec;
                $data[$key]['nomCli'] = $res->nomCli;
                if ($res->desc != null) {
                    $data[$key]['displayDescription'] = $res->desc;
                } else {
                    $data[$key]['displayDescription'] = $res->causa;
                }
            }
            //log_message('DEBUG','#Main/mostratemaster |  ');

            // si existe OT
            $data = $this->infoUser($data, $key);
            
            //BUSQUEDA
            //filtro por descripcion, equipo, id SS, id OT, id pp 
            if($search){
                if((strpos(strtoupper($data[$key]['displayDescription']), strtoupper($search)) !== false) 
                || (strpos(strtoupper($data[$key]['equipoDesc']), strtoupper($search)) !== false) 
                || (strpos($data[$key]['ss'], $search) !== false) 
                || (strpos($data[$key]['ot'], $search) !== false) 
                || (strpos($data[$key]['pema_id'], $search) !== false) )
                {
                    array_push($filtrado,$data[$key]);
                }
            }
        }
								//log_message('DEBUG','#Main/BUSCADOR |  '.json_encode($filtrado));

        if($search){
            return $filtrado;
        }
        else{
            return $data;
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

/* ./ INTEGRACION CON BPM */

    public function instanciarSubtareas($idTarea, $ot)
    {
        $this->assetDB->select('tareadescrip, id_subtarea, form_asoc as idForm');
        $this->assetDB->where('id_tarea', $idTarea);
        $res = $this->assetDB->get('asp_subtareas')->result();

        if (!$res) {
            return false;
        }

        foreach ($res as $key => $o) {

            $infoId = $this->Forms->guardar($o->idForm);
            if (!$infoId) {
                continue;
            }

            $o->info_id = $infoId;
            $o->id_orden = $ot;
            $o->fecha = date('Y-m-d');
            $o->estado = 'AC';

            unset($o->idForm);
            $this->assetDB->insert('tbl_listarea', $o);
        }

        return true;
    }
    public function CambiarEstadoPedidoMat($id, $tipo)
    {
				if ($tipo == "parcial") {
					$estado = 'Ent. Parcial';
				} else {
					$estado = 'Entregado';
				}
				
        $this->assetDB->set('estado', $estado);
        $this->assetDB->where('case_id', $id);
        $query = $this->assetDB->update('alm_pedidos_materiales');
        return $query;
    }

    /**
    * Devuelve correspondencua entre Case_id con Empresa
    * @param
    * @return bool true o false
    */
	function bandejaEmpresa($case_id, $empr_id)
	{
		$ci =& get_instance();
		$aux = $ci->rest->callAPI("GET",REST_CORE."/assetbandeja/linea/validar/case_id/".$case_id."/empr_id/".$empr_id);
		$aux =json_decode($aux["data"]);
		
		if ($aux->respuesta->case_id) {
			return  true;
		} else {
			return  false;
		}
	}

    /**
	*Genera lista pedido de trabajo paginados
	* @param integer;integer;string start donde comienza el listado; length cantidad de registros; search cadena a buscar
	* @return array listado de tareas paginada, filtrada por empresa y la cantidad
	**/
    function tareaspaginadas($start, $length, $search, $ordering){

        //recupero tareas guardadas en session
        $tareas = $_SESSION['listadoTareas'];
        if($search){

            //completo todas las tareas primero para poder buscar en todas las paginas
            $filtrado = $this->CompletarToDoList($tareas, $search);
            //Ordeno las tareas segun el criterio seleccionado
            if($ordering[0]['column'] != "" && $ordering[0]['dir'] != ""){
                $dataOrdenada = $this->sortTareasBy($filtrado, $ordering);
            }
            $query_total = count($dataOrdenada);

            $data = array_slice($dataOrdenada, $start, $length);

            // $data = $tareasPaginadas; 
        }else{
            
            $query_total = count($tareas);
            //completo los datos de las tareas por pagina
            $data = $this->CompletarToDoList($tareas);
            //Ordeno las tareas segun el criterio seleccionado
            if($ordering[0]['column'] != "" && $ordering[0]['dir'] != ""){
                $dataOrdenada = $this->sortTareasBy($data, $ordering);
            }

            //armo las paginas con las tareas
            $data = array_slice($dataOrdenada, $start, $length);
        }
        
        $result = array(
            'numDataTotal' => $query_total,
            'datos' => $data
        );
        //log_message('DEBUG','#TRAZA | #TAREA >> tareaspaginadas >> $result: '.json_encode($result));
           
        return $result;

    }
    /**
	*Metodo de ordenamiento para el pagina de la tabla de tareas
	* @param integer;integer;string start donde comienza el listado; length cantidad de registros; search cadena a buscar
	* @return array listado de tareas paginada, filtrada por empresa y la cantidad
	**/
    function sortTareasBy($data,$ordering){
        $column = $ordering[0]['column'];
        $direction = $ordering[0]['dir'];

        switch ($column) {
            case '2':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['usr_asignado'] == $b['usr_asignado']) return 0;
                    return ($direction == 'asc') ? ($a['usr_asignado'] < $b['usr_asignado'] ? -1 : 1) : ($a['usr_asignado'] > $b['usr_asignado'] ? -1 : 1);
                });
                break;
            case '3':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['assigned_date'] == $b['assigned_date']) return 0;
                    return ($direction == 'asc') ? ($a['assigned_date'] < $b['assigned_date'] ? -1 : 1) : ($a['assigned_date'] > $b['assigned_date'] ? -1 : 1);
                });
                break;
            case '4':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['equipoDesc'] == $b['equipoDesc']) return 0;
                    return ($direction == 'asc') ? ($a['equipoDesc'] < $b['equipoDesc'] ? -1 : 1) : ($a['equipoDesc'] > $b['equipoDesc'] ? -1 : 1);
                });
                break;
            case '5':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['sectorDesc'] == $b['sectorDesc']) return 0;
                    return ($direction == 'asc') ? ($a['sectorDesc'] < $b['sectorDesc'] ? -1 : 1) : ($a['sectorDesc'] > $b['sectorDesc'] ? -1 : 1);
                });
                break;
            case '6':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['nomCli'] == $b['nomCli']) return 0;
                    return ($direction == 'asc') ? ($a['nomCli'] < $b['nomCli'] ? -1 : 1) : ($a['nomCli'] > $b['nomCli'] ? -1 : 1);
                });
                break;
            case '7':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['displayName'] == $b['displayName']) return 0;
                    return ($direction == 'asc') ? ($a['displayName'] < $b['displayName'] ? -1 : 1) : ($a['displayName'] > $b['displayName'] ? -1 : 1);
                });
                break;
            case '8':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['displayDescription'] == $b['displayDescription']) return 0;
                    return ($direction == 'asc') ? ($a['displayDescription'] < $b['displayDescription'] ? -1 : 1) : ($a['displayDescription'] > $b['displayDescription'] ? -1 : 1);
                });
                break;
            case '9':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['ss'] == $b['ss']) return 0;
                    return ($direction == 'asc') ? ($a['ss'] < $b['ss'] ? -1 : 1) : ($a['ss'] > $b['ss'] ? -1 : 1);
                });
                break;
            case '10':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['ot'] == $b['ot']) return 0;
                    return ($direction == 'asc') ? ($a['ot'] < $b['ot'] ? -1 : 1) : ($a['ot'] > $b['ot'] ? -1 : 1);
                });
                break;
            case '11':
                usort($data, function($a, $b) use ($direction) {
                    if ($a['pema_id'] == $b['pema_id']) return 0;
                    return ($direction == 'asc') ? ($a['pema_id'] < $b['pema_id'] ? -1 : 1) : ($a['pema_id'] > $b['pema_id'] ? -1 : 1);
                });
                break;
            default:
                
                break;
        }
        return $data;
    }
    
}
