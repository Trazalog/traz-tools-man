<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Tareas de la bandeja de Bonita de AssetPlanner.
 *
 * MIG-ASSET-3: portado desde el asset actual (reemplaza al model del fork de 2024). Las tareas del usuario salen de Bonita y los datos
 * de asset de cada una de MANDataService, en una sola llamada por página a toolsMANAPI (A5, ADR-014): asset hacía hasta cuatro
 * consultas por tarea y leía el usuario de Bonita de su propia sesión. Se conservan el armado de las filas y el cache por sesión de
 * asset (la bandeja va pidiendo páginas a Bonita hasta juntar las filas de la empresa que necesita la tabla).
 */
class Tareas extends CI_Model
{
    // Tareas de Bonita que pide cada vuelta al API (en asset, 50)
    const POR_PAGINA = 50;
    // Tope de vueltas por pedido de la tabla, para que no se cuelgue (en asset, 10)
    const MAX_VUELTAS = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('man');
    }

    /**
     * Pide al API una página de tareas del usuario con los datos de asset de la empresa.
     * @return array|false tareas (Bonita), casos (asset) y cantidad de tareas que trajo Bonita, o false
     */
    private function pagina($p, $c)
    {
        $rsp = $this->rest->callAPI('POST', HOST . '/tools/man/bandeja/pagina', array(
            'usrNick'    => (string) userNick(),
            'id_empresa' => man_empresa(),
            'p'          => (string) $p,
            'c'          => (string) $c,
            'session'    => $this->session->has_userdata('bpm_token') ? $this->session->userdata('bpm_token') : '',
        ));
        $aux = json_decode($rsp['data'], true);
        if (!$rsp['status'] || empty($aux['status'])) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | Tareas | pagina() >> ' . json_encode($rsp['data']));
            return false;
        }
        if (!empty($aux['session'])) {
            $this->session->set_userdata('bpm_token', $aux['session']);
        }
        return $aux;
    }

    /**
     * Filas de datos de asset de la página, por caso y origen (SS, OT, PN, PE).
     * @return array case_id => origen => fila
     */
    private function casosPorCaso($casos)
    {
        $out = array();
        $filas = isset($casos['caso']) ? $casos['caso'] : array();
        // Una sola fila puede llegar como objeto y no como lista
        if (!empty($filas) && array_keys($filas) !== range(0, count($filas) - 1)) {
            $filas = array($filas);
        }
        foreach ($filas as $f) {
            if (!isset($out[$f['case_id']][$f['origen']])) {
                $out[$f['case_id']][$f['origen']] = $f;
            }
        }
        return $out;
    }

    /**
     * Completa cada tarea con los datos de asset y saca las que no son de la empresa (CompletarToDoList, infoUser y el filtro
     * por case_empresa de asset). Si no encuentra los datos de la tarea la deja igual, con las celdas vacías, como asset. Si hay búsqueda,
     * deja solo las que coinciden con la descripción, el equipo, la SS, la OT o el pedido.
     * @return array tareas completas
     */
    private function completar($tareas, $casos, $search)
    {
        $porCaso = $this->casosPorCaso($casos);
        $out = array();
        foreach ($tareas as $t) {
            // Solo los casos de la empresa (case_empresa)
            if (!isset($porCaso[$t['caseId']]['CE'])) {
                continue;
            }
            $caso = $porCaso[$t['caseId']];
            if ($t['processId'] == BPM_PROCESS_ID_PEDIDOS_NORMALES) {
                $r = isset($caso['PN']) ? $caso['PN'] : null;
                $t['pema_id'] = $r ? $r['pema_id'] : null;
                $t['ot'] = $r ? $r['ot'] : null;
                $t['displayDescription'] = $r ? $r['descr'] : null;
                $t['equipoDesc'] = $r ? $r['equipo'] : null;
                $t['sectorDesc'] = $r ? $r['sector'] : null;
                $t['nomCli'] = $r ? $r['cliente'] : null;
                $t = $this->infoUser($t, $r);
            } elseif ($t['processId'] == BPM_PROCESS_ID_PEDIDOS_EXTRAORDINARIOS) {
                $r = isset($caso['PE']) ? $caso['PE'] : null;
                $t['pema_id'] = $r ? $r['pema_id'] : null;
                $t['ot'] = $r ? $r['ot'] : null;
            } else {
                // La solicitud del caso, y si no hay, la OT del caso
                $r = isset($caso['SS']) ? $caso['SS'] : (isset($caso['OT']) ? $caso['OT'] : null);
                $t['ss'] = $r ? $r['ss'] : '';
                $t['ot'] = $r ? $r['ot'] : null;
                $t['tip_ta'] = $r ? $r['tip_ta'] : null;
                $t['equipoDesc'] = $r ? $r['equipo'] : null;
                $t['sectorDesc'] = $r ? $r['sector'] : null;
                $t['nomCli'] = $r ? $r['cliente'] : null;
                $t['displayDescription'] = $r ? $r['descr'] : null;
                $t = $this->infoUser($t, $r);
            }
            if ($search && !$this->coincide($t, $search)) {
                continue;
            }
            $out[] = $t;
        }
        return $out;
    }

    /** Nombre del responsable de la OT, o " S/A " si la tarea no tiene OT (infoUser de asset). */
    private function infoUser($t, $r)
    {
        if (isset($t['ot']) && $t['ot'] !== null) {
            $t['usr_asignado'] = ($r && isset($r['usr_nombre'])) ? $r['usr_nombre'] : null;
        } else {
            $t['usr_asignado'] = ' S/A ';
        }
        return $t;
    }

    /** Búsqueda de la bandeja: descripción y equipo sin distinguir mayúsculas, SS, OT y pedido como texto. */
    private function coincide($t, $search)
    {
        $campo = function ($k) use ($t) {
            return isset($t[$k]) ? (string) $t[$k] : '';
        };
        return strpos(strtoupper($campo('displayDescription')), strtoupper($search)) !== false
            || strpos(strtoupper($campo('equipoDesc')), strtoupper($search)) !== false
            || strpos($campo('ss'), $search) !== false
            || strpos($campo('ot'), $search) !== false
            || strpos($campo('pema_id'), $search) !== false;
    }

    /**
     * Lista de tareas paginada para la tabla. Pide páginas a Bonita hasta tener las filas que hacen falta, y las guarda en la sesión
     * hasta que la tabla se actualiza o cambia la búsqueda.
     * @return array numDataTotal y datos
     */
    public function tareaspaginadas($start, $length, $search, $ordering)
    {
        $searchQuery = isset($search) ? $search : '';
        $sessionSearch = isset($_SESSION['man_band_search']) ? $_SESSION['man_band_search'] : null;

        // Con start 0, otra búsqueda o sin cache se empieza de nuevo
        if ($start == 0 || $searchQuery !== $sessionSearch || !isset($_SESSION['man_band_cache'])) {
            $_SESSION['man_band_cache'] = array();
            $_SESSION['man_band_page'] = 0;
            $_SESSION['man_band_exhausted'] = false;
            $_SESSION['man_band_search'] = $searchQuery;
        }

        $limit_needed = $start + $length;
        $iters = 0;
        while (count($_SESSION['man_band_cache']) < $limit_needed && !$_SESSION['man_band_exhausted'] && $iters < self::MAX_VUELTAS) {
            $iters++;
            $rsp = $this->pagina($_SESSION['man_band_page'], self::POR_PAGINA);
            if (!$rsp) {
                $_SESSION['man_band_exhausted'] = true;
                break;
            }
            $tareas = isset($rsp['tareas']) && is_array($rsp['tareas']) ? $rsp['tareas'] : array();
            if (empty($tareas)) {
                $_SESSION['man_band_exhausted'] = true;
                break;
            }
            if (count($tareas) < self::POR_PAGINA) {
                $_SESSION['man_band_exhausted'] = true;
            }
            $completas = $this->completar($tareas, isset($rsp['casos']) ? $rsp['casos'] : array(), $searchQuery);
            if (!empty($completas)) {
                $_SESSION['man_band_cache'] = array_merge($_SESSION['man_band_cache'], $completas);
            }
            $_SESSION['man_band_page']++;
        }

        $todas = $_SESSION['man_band_cache'];

        // Se ordena solo lo que ya está en el cache
        if (isset($ordering[0]['column']) && $ordering[0]['column'] != '' && $ordering[0]['dir'] != '') {
            $todas = $this->sortTareasBy($todas, $ordering);
        }

        $total = count($todas);
        if (!$_SESSION['man_band_exhausted']) {
            $total += 1; // Indica a DataTables que todavía hay más páginas
        }
        return array('numDataTotal' => $total, 'datos' => array_slice($todas, $start, $length));
    }

    /** Vacía el cache de la bandeja para que la tabla vuelva a pedir todo a Bonita. */
    public function reiniciarCache()
    {
        $_SESSION['man_band_cache'] = null;
        $_SESSION['man_band_page'] = 0;
        $_SESSION['man_band_exhausted'] = false;
        $_SESSION['man_band_search'] = '';
    }

    /**
     * Acción sobre una tarea de Bonita por toolsMANAPI (ADR-014, regla 3: Bonita y base en una sola operación). El API comprueba que el
     * caso de la tarea sea de la empresa y saca de ahí la OT y su origen, así que acá solo viajan la tarea y la decisión del usuario.
     * @param string $ruta recurso de /tools/man/bandeja
     * @param array $datos datos propios de la acción
     * @return array status (true|false) y msj si falla
     */
    private function accion($ruta, $datos)
    {
        $aux = $this->llamar($ruta, $datos);
        return $aux['status'] ? array('status' => true, 'msj' => 'OK') : array('status' => false, 'msj' => $aux['msj']);
    }

    /**
     * Llama a un recurso de /tools/man/bandeja con el usuario, la empresa y la sesión de Bonita; guarda la sesión que devuelve.
     * @return array la respuesta del API con status (true|false) y msj si falla
     */
    private function llamar($ruta, $datos)
    {
        $rsp = $this->rest->callAPI('POST', HOST . '/tools/man/bandeja/' . $ruta, array_merge(array(
            'usrNick'    => (string) userNick(),
            'id_empresa' => man_empresa(),
            'session'    => $this->session->has_userdata('bpm_token') ? $this->session->userdata('bpm_token') : '',
        ), $datos));
        $aux = json_decode($rsp['data'], true);
        if (!$rsp['status'] || empty($aux['status'])) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | Tareas | llamar(' . $ruta . ') >> ' . json_encode($rsp['data']));
            return array('status' => false, 'msj' => (is_array($aux) && !empty($aux['msj'])) ? $aux['msj'] : 'Error en la operación');
        }
        if (!empty($aux['session'])) {
            $this->session->set_userdata('bpm_token', $aux['session']);
        }
        return $aux;
    }

    /** El usuario toma la tarea (queda asignada a él en Bonita). */
    public function tomarTarea($idTarea)
    {
        return $this->accion('tarea/asignar', array('id_tarea' => (string) (int) $idTarea, 'accion' => 'tomar'));
    }

    /** El usuario suelta la tarea (queda sin asignar en Bonita). */
    public function soltarTarea($idTarea)
    {
        return $this->accion('tarea/asignar', array('id_tarea' => (string) (int) $idTarea, 'accion' => 'soltar'));
    }

    /** Termina el análisis de la solicitud de servicio con la decisión de urgencia ('true' o 'false'). */
    public function decidirUrgencia($idTarea, $opcion)
    {
        return $this->accion('tarea/urgencia', array('id_tarea' => (string) (int) $idTarea, 'opcion' => $this->logico($opcion)));
    }

    /** Termina "Ejecutar OT": la OT queda terminada y el informe de servicio, a nombre de quien la ejecutó. */
    public function ejecutarOT($idTarea)
    {
        return $this->accion('tarea/ejecutar', array(
            'id_tarea' => (string) (int) $idTarea,
            'fecha'    => date('Y-m-d H:i:s'),
            'usr_id'   => man_usuario(),
        ));
    }

    /** Verifica el informe de servicio: conforme ('true') cierra todo; no conforme lo devuelve a quien lo hizo, con la justificación. */
    public function verificarInforme($idTarea, $opcion, $justificacion)
    {
        return $this->accion('tarea/verificar', array(
            'id_tarea'      => (string) (int) $idTarea,
            'opcion'        => $this->logico($opcion),
            'justificacion' => (string) $justificacion,
        ));
    }

    /** El solicitante presta (o no) conformidad con el trabajo. */
    public function prestarConformidad($idTarea, $opcion)
    {
        return $this->accion('tarea/conformidad', array('id_tarea' => (string) (int) $idTarea, 'opcion' => $this->logico($opcion)));
    }

    /** Marca el inicio de la OT: fecha, posición y estado En curso (de la OT y de su origen). */
    public function inicioTarea($idOt, $lat, $lon)
    {
        $rsp = $this->rest->callAPI('POST', HOST . '/tools/man/bandeja/ot/inicio', array(
            'id_empresa' => man_empresa(),
            'id_ot'      => (string) (int) $idOt,
            'lat'        => (string) $lat,
            'lon'        => (string) $lon,
            'fecha'      => date('Y-m-d H:i:s'),
        ));
        $aux = json_decode($rsp['data'], true);
        if (!$rsp['status'] || empty($aux['status'])) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | Tareas | inicioTarea() >> ' . json_encode($rsp['data']));
            return false;
        }
        return true;
    }

    /** ¿La OT ya tiene fecha de inicio? (asset la guarda con ceros cuando no la tiene) */
    public function inicioConfirmado($idOt)
    {
        $filas = man_lista(wso2(REST_MAN . '/ot/' . (int) $idOt . '/inicio/empresa/' . man_empresa()));
        $fecha = $filas ? $filas[0]['fecha_inicio'] : '';
        return !empty($fecha) && strpos($fecha, '0000-00-00') !== 0;
    }

    /**
     * Tarea de Bonita y datos de su caso en asset (OT, origen y solicitud), solo si el caso es de la empresa.
     * @return array|null tarea (nombre, estado, asignada a, fechas), caso, id_ot, tipo, id_solicitud, id_ss y sore_id; null si no se pudo
     */
    public function detalle($idTarea)
    {
        $r = $this->llamar('tarea/detalle', array('id_tarea' => (string) (int) $idTarea));
        return $r['status'] ? $r : null;
    }

    /** Línea de tiempo del caso: actividades pendientes (listAct) y terminadas (listArch), de la más nueva a la más vieja. */
    public function lineaTiempo($idTarea)
    {
        $r = $this->llamar('tarea/linea', array('id_tarea' => (string) (int) $idTarea));
        if (!$r['status']) {
            return array('listAct' => array(), 'listArch' => array());
        }
        return array('listAct' => $this->actividades($r['activas'], 'last_update_date'),
            'listArch' => $this->actividades($r['archivadas'], 'last_update_date'));
    }

    /** Saca las actividades de varias instancias y ordena de la más nueva a la más vieja por el campo dado. */
    private function actividades($filas, $campo)
    {
        $out = array();
        foreach (is_array($filas) ? $filas : array() as $f) {
            if (isset($f['type']) && $f['type'] == 'MULTI_INSTANCE_ACTIVITY') {
                continue;
            }
            $out[] = $f;
        }
        usort($out, function ($a, $b) use ($campo) {
            return strtotime(isset($b[$campo]) ? $b[$campo] : '') - strtotime(isset($a[$campo]) ? $a[$campo] : '');
        });
        return $out;
    }

    /** Comentarios del caso de la tarea, del más nuevo al más viejo. @return array filas de Bonita (con el usuario) */
    public function comentarios($idTarea)
    {
        $r = $this->llamar('tarea/comentarios', array('id_tarea' => (string) (int) $idTarea));
        return ($r['status'] && is_array($r['comentarios'])) ? $r['comentarios'] : array();
    }

    /** Agrega un comentario al caso de la tarea, a nombre del usuario. @return array status y msj */
    public function comentar($idTarea, $contenido)
    {
        return $this->accion('tarea/comentar', array('id_tarea' => (string) (int) $idTarea, 'contenido' => (string) $contenido));
    }

    /** Datos del equipo para la cabecera de la tarea. @return array|null */
    public function cabeceraEquipo($idEquipo)
    {
        $f = man_lista(wso2(REST_MAN . '/tarea/cabecera/equipo/' . (int) $idEquipo . '/empresa/' . man_empresa()));
        return $f ? $f[0] : null;
    }

    /** Datos de la solicitud de servicio para la cabecera de la tarea, con sus adjuntos. @return array|null */
    public function cabeceraSolicitud($idSolicitud)
    {
        $f = man_lista(wso2(REST_MAN . '/tarea/cabecera/solicitud/' . (int) $idSolicitud . '/empresa/' . man_empresa()));
        if (!$f) {
            return null;
        }
        $this->load->model('Sservicios');
        $adj = $this->Sservicios->getAdjuntosSolServicio($idSolicitud);
        $f[0]['adjuntos'] = $adj ? $adj : array();
        return $f[0];
    }

    /** Datos de la OT para la cabecera de la tarea, con el motivo del último rechazo y si ya tiene informe. @return array|null */
    public function cabeceraOt($idOt)
    {
        $f = man_lista(wso2(REST_MAN . '/tarea/cabecera/ot/' . (int) $idOt . '/empresa/' . man_empresa()));
        return $f ? $f[0] : null;
    }

    /** Subtareas (tareas estándar) de la OT con su formulario asociado. @return array filas */
    public function getSubtareas($idOt)
    {
        $f = man_lista(wso2(REST_MAN . '/tarea/subtareas/ot/' . (int) $idOt . '/empresa/' . man_empresa()));
        return $f ? $f : array();
    }

    /**
     * Cambia el estado de una subtarea de la OT de la tarea. Asset cambiaba cualquier subtarea por su número; acá tiene que ser de la
     * OT del caso de la tarea, que a su vez tiene que ser de la empresa.
     * @return bool
     */
    public function cambiarEstadoSubtask($idTarea, $idListarea, $estado)
    {
        $det = $this->detalle($idTarea);
        if ($det === null || !ctype_digit((string) $det['id_ot']) || !in_array($estado, array('T', 'C'), true)) {
            return false;
        }
        $propias = array();
        foreach ($this->getSubtareas($det['id_ot']) as $s) {
            $propias[] = (string) $s['id_listarea'];
        }
        if (!in_array((string) (int) $idListarea, $propias, true)) {
            return false;
        }
        $this->load->model('Otrabajos');
        return $this->Otrabajos->cambiarTarea((int) $idListarea, 'estado', $estado);
    }

    /** Adjunto del preventivo del que viene la OT (procedimiento de la tarea). @return array filas con prev_adjunto */
    public function procedimiento($idOt)
    {
        $f = man_lista(wso2(REST_MAN . '/ot/' . (int) $idOt . '/procedimiento/empresa/' . man_empresa()));
        return $f ? $f : array();
    }

    /**
     * Guarda el informe de servicio de la OT de la tarea y cierra la tarea "Confecciona informe servicio" (toolsMANAPI, ADR-014 regla 3).
     * La OT, su equipo y la solicitud salen del caso de la tarea; el navegador solo aporta lo que el usuario cargó. Si ya había un
     * informe (la verificación lo devolvió) se reemplaza completo. Sin herramientas ni insumos (pañol y almacenes no se migran).
     * @param array $d fecha_inicio, fecha_fin, horometro_inicio, horometro_fin
     * @param array $tareas tareas realizadas (texto), $operarios ids de usuario, $evidencias filas con valor_base64 y nombre
     * @return array status y msj
     */
    public function guardarInforme($idTarea, $d, $tareas, $operarios, $evidencias)
    {
        $det = $this->detalle($idTarea);
        if ($det === null) {
            return array('status' => false, 'msj' => 'La tarea no es de la empresa');
        }
        $inicio = strtotime(isset($d['fecha_inicio']) ? $d['fecha_inicio'] : '');
        $fin = strtotime(isset($d['fecha_fin']) ? $d['fecha_fin'] : '');
        if (!$inicio || !$fin) {
            return array('status' => false, 'msj' => 'Faltan las fechas de inicio y fin');
        }
        $numero = function ($v) {
            return is_numeric($v) ? (string) (float) $v : '0';
        };
        $ss = ctype_digit((string) $det['id_ss']) ? $det['id_ss'] : (ctype_digit((string) $det['sore_id']) ? $det['sore_id'] : '0');
        $lista = function ($filas, $campo) {
            $out = array();
            foreach (is_array($filas) ? $filas : array() as $f) {
                $v = is_array($f) ? reset($f) : $f;
                if ($v !== null && $v !== '') {
                    $out[] = array($campo => (string) $v);
                }
            }
            return $out;
        };
        $ops = array();
        foreach (is_array($operarios) ? $operarios : array() as $o) {
            $id = is_array($o) ? end($o) : $o;
            if (ctype_digit((string) $id)) {
                $ops[] = array('usrId' => (string) $id);
            }
        }
        $evs = array();
        foreach (is_array($evidencias) ? $evidencias : array() as $e) {
            if (!empty($e['valor_base64'])) {
                $evs[] = array('valor_base64' => (string) $e['valor_base64'], 'nombre' => isset($e['nombre']) ? (string) $e['nombre'] : '');
            }
        }
        return $this->accion('tarea/informe', array(
            'id_tarea'               => (string) (int) $idTarea,
            'id_solicitudreparacion' => (string) $ss,
            'fecha_inicio'           => date('Y-m-d H:i:s', $inicio),
            'fecha_fin'              => date('Y-m-d H:i:s', $fin),
            'horometro_inicio'       => $numero(isset($d['horometro_inicio']) ? $d['horometro_inicio'] : ''),
            'horometro_fin'          => $numero(isset($d['horometro_fin']) ? $d['horometro_fin'] : ''),
            'tareas'                 => $lista($tareas, 'tarea'),
            'operarios'              => $ops,
            'evidencias'             => $evs,
        ));
    }

    /** 'true' o 'false' como texto, como lo reciben las variables de Bonita (en asset llegaba tal cual el texto del navegador). */
    private function logico($valor)
    {
        return ($valor === true || $valor === 'true' || $valor === '1' || $valor === 1) ? 'true' : 'false';
    }

    /** Ordena por la columna de la tabla (las columnas 0, 1 y mayores a 11 no ordenan). */
    private function sortTareasBy($data, $ordering)
    {
        $campos = array(
            '2' => 'usr_asignado', '3' => 'assigned_date', '4' => 'equipoDesc', '5' => 'sectorDesc', '6' => 'nomCli',
            '7' => 'displayName', '8' => 'displayDescription', '9' => 'ss', '10' => 'ot', '11' => 'pema_id',
        );
        $columna = $ordering[0]['column'];
        if (!isset($campos[$columna])) {
            return $data;
        }
        $campo = $campos[$columna];
        $asc = $ordering[0]['dir'] == 'asc';
        usort($data, function ($a, $b) use ($campo, $asc) {
            $x = isset($a[$campo]) ? $a[$campo] : null;
            $y = isset($b[$campo]) ? $b[$campo] : null;
            if ($x == $y) {
                return 0;
            }
            return $asc ? ($x < $y ? -1 : 1) : ($x > $y ? -1 : 1);
        });
        return $data;
    }
}
