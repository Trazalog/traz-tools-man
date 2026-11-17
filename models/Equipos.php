<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Equipos: listado, alta, edición, estados, lecturas, adjuntos, contratistas y metas.
 *
 * MIG-ASSET-1: todos los datos por MANDataService (ADR-014). Se conservan firmas y formato de salida
 * del model de asset (result() → objetos, result_array() → arrays, false sin filas). Toda operación
 * queda limitada a la empresa de la sesión.
 *
 * historial_lecturas es MyISAM: no admite transacciones (tampoco las tenía asset con trans_start), así
 * que los cambios de estado con su lectura se escriben en el mismo orden que asset y la lectura se
 * graba solo si el equipo es de la empresa (equipoDeEmpresa()).
 *
 * No se portan los métodos que ninguna acción usada llama (decisión E9). Lo que asset hacía con el
 * preventivo al registrar una lectura queda para MIG-4 (decisión E16).
 */
class Equipos extends CI_Model{

    /** @var int id del último equipo creado (lo que en asset se leía con insert_id()). */
    public $ultimo_id = 0;

    public function __construct(){
        parent::__construct();
        $this->load->helper('man');
    }

    /**
     * Página de equipos para el listado (DataTables del lado del servidor).
     * @return array numDataTotal (total con el filtro) y datos (filas de la página, como result_array())
     */
    public function equiposPaginados($start, $length, $search){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | equiposPaginados($start, $length, " . json_encode($search) . ")");
        $buscar = rawurlencode((string) $search);
        $cant = man_lista(wso2(REST_MAN . '/equipos/paginado/cantidad/empresa/' . man_empresa() . '?buscar=' . $buscar));
        $filas = man_lista(wso2(REST_MAN . '/equipos/paginado/empresa/' . man_empresa() . '?buscar=' . $buscar
            . '&inicio=' . (int) $start . '&cantidad=' . (int) $length));
        return array(
            'numDataTotal' => $cant ? $cant[0]['cant'] : '0',
            'datos'        => $filas ? $filas : array(),
        );
    }

    /** Baja lógica de un equipo de la empresa. @return bool */
    public function baja_equipos($data, $idequipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | baja_equipos($idequipo)");
        return $this->estado($idequipo, $data['estado']);
    }

    /** Áreas activas de la empresa. @return array|false objetos */
    public function getareas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getareas()");
        return $this->objetos(wso2(REST_MAN . '/areas/empresa/' . man_empresa()));
    }

    /** Criticidades activas de la empresa. @return array|false objetos */
    public function getcriti(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getcriti()");
        return $this->objetos(wso2(REST_MAN . '/equipos/criticidades/empresa/' . man_empresa()));
    }

    /** Grupos activos de la empresa. @return array|false objetos */
    public function getgrupos(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getgrupos()");
        return $this->objetos(wso2(REST_MAN . '/equipos/grupos/empresa/' . man_empresa()));
    }

    /** Procesos activos de la empresa. @return array|false objetos */
    public function getprocesos(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getprocesos()");
        return $this->objetos(wso2(REST_MAN . '/procesos/empresa/' . man_empresa()));
    }

    /** Sectores (etapas) activos de la empresa. @return array|false objetos */
    public function getetapas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getetapas()");
        return $this->objetos(wso2(REST_MAN . '/equipos/sectores/empresa/' . man_empresa()));
    }

    /** Contratistas de la empresa. @return array|false objetos */
    public function getcontra(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getcontra()");
        return $this->objetos(wso2(REST_MAN . '/equipos/contratistas/empresa/' . man_empresa()));
    }

    /** Marcas activas de la empresa. @return array|false objetos */
    public function getmarcas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getmarcas()");
        return $this->objetos(wso2(REST_MAN . '/equipos/marcas/empresa/' . man_empresa()));
    }

    /** Clientes no anulados de la empresa. @return array|false objetos */
    public function getclientes(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getclientes()");
        return $this->objetos(wso2(REST_MAN . '/equipos/clientes/empresa/' . man_empresa()));
    }

    /** Alta de área desde el equipo. @return string|false id */
    public function agregar_area($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_area(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'area', array(
            'descripcion' => (string) $data['descripcion'],
            'id_empresa'  => man_empresa(),
        )));
    }

    /** Alta de proceso desde el equipo. @return string|false id */
    public function agregar_proceso($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_proceso(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'proceso', array(
            'descripcion' => (string) $data['descripcion'],
            'id_empresa'  => man_empresa(),
        )));
    }

    /** Alta de sector (etapa) desde el equipo. @return string|false id */
    public function agregar_etapa($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_etapa(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'equipos/sector', array(
            'descripcion' => (string) $data['descripcion'],
            'id_empresa'  => man_empresa(),
        )));
    }

    /** Alta de criticidad desde el equipo. @return string|false id */
    public function agregar_criti($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_criti(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'equipos/criticidad', array(
            'descripcion' => (string) $data['descripcion'],
            'id_empresa'  => man_empresa(),
        )));
    }

    /** Alta de grupo desde el equipo. @return string|false id */
    public function agregar_grupos($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_grupos(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'equipos/grupo', array(
            'descripcion' => (string) $data['descripcion'],
            'id_empresa'  => man_empresa(),
        )));
    }

    /** Alta de marca desde el equipo. @return string|false id */
    public function agregar_marcas($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_marcas(" . json_encode($data) . ")");
        return $this->id(man_escribir('POST', 'equipos/marca', array(
            'marcadescrip' => $this->texto($data, 'marcadescrip'),
            'estado'       => $this->texto($data, 'estado'),
            'id_empresa'   => man_empresa(),
        )));
    }

    /** Alta de cliente desde el equipo. @return string|false id */
    public function agregar_clientes($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_clientes(" . json_encode($data) . ")");
        $params = array();
        foreach (array('cliName', 'cliLastName', 'cliDni', 'cliAddress', 'cliPhone', 'cliEmail', 'cliRazonSocial', 'estado') as $campo) {
            $params[$campo] = $this->texto($data, $campo);
        }
        $params['id_empresa'] = man_empresa();
        return $this->id(man_escribir('POST', 'equipos/cliente', $params));
    }

    /**
     * ¿Existe ya un equipo con ese código? En asset la unicidad es global (todas las empresas) y se
     * conserva así.
     * @return bool
     */
    public function validaUnicidadCodigo($codigo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | validaUnicidadCodigo(" . json_encode($codigo) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipos/codigo-existe?codigo=' . rawurlencode((string) $codigo)));
        return $filas && (int) $filas[0]['cant'] > 0;
    }

    /** Alta de equipo en la empresa. El id creado queda en $this->ultimo_id. @return bool */
    public function insert_equipo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | insert_equipo(" . json_encode($data) . ")");
        $rsp = man_escribir('POST', 'equipo', array(
            'descripcion'         => $this->texto($data, 'descripcion'),
            'fecha_ingreso'       => man_fecha($data['fecha_ingreso']),
            'fecha_garantia'      => man_fecha($data['fecha_garantia']),
            'marca'               => $this->texto($data, 'marca'),
            'codigo'              => $this->texto($data, 'codigo'),
            'id_empresa'          => man_empresa(),
            'id_sector'           => man_numero($data['id_sector']),
            'id_hubicacion'       => man_numero($data['id_hubicacion']),
            'id_grupo'            => man_numero($data['id_grupo']),
            'id_customer'         => man_numero($data['id_customer']),
            'id_criticidad'       => man_numero($data['id_criticidad']),
            'fecha_ultimalectura' => man_fecha($data['fecha_ultimalectura'], true),
            'ultima_lectura'      => man_numero($data['ultima_lectura']),
            'descrip_tecnica'     => $this->texto($data, 'descrip_tecnica'),
            'id_unidad'           => man_numero($data['id_unidad']),
            'id_area'             => man_numero($data['id_area']),
            'id_proceso'          => man_numero($data['id_proceso']),
            'numero_serie'        => $this->texto($data, 'numero_serie'),
        ));
        $id = $this->id($rsp);
        $this->ultimo_id = $id ? (int) $id : 0;
        return $id !== false;
    }

    /** Inhabilita el equipo y deja la lectura de cambio de estado en el historial. @return bool */
    public function update_cambio($data, $idequipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | update_cambio($idequipo)");
        if (!$this->equipoDeEmpresa($idequipo) || !$this->estado($idequipo, $data['estado'])) {
            return false;
        }
        $this->lectura($idequipo, '0', null, '', '', 'IN');
        return true;
    }

    /** Guarda el equipo editado. @return bool */
    public function update_editar($data, $id){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | update_editar(" . json_encode($data) . ", $id)");
        $rsp = man_escribir('PUT', 'equipo', array(
            'descripcion'         => $this->texto($data, 'descripcion'),
            'fecha_ingreso'       => man_fecha($this->valor($data, 'fecha_ingreso')),
            'fecha_garantia'      => man_fecha($this->valor($data, 'fecha_garantia')),
            'marca'               => $this->texto($data, 'marca'),
            'codigo'              => $this->texto($data, 'codigo'),
            'ubicacion'           => $this->texto($data, 'ubicacion'),
            'id_sector'           => man_numero($this->valor($data, 'id_sector')),
            'id_grupo'            => man_numero($this->valor($data, 'id_grupo')),
            'id_area'             => man_numero($this->valor($data, 'id_area')),
            'id_proceso'          => man_numero($this->valor($data, 'id_proceso')),
            'id_criticidad'       => man_numero($this->valor($data, 'id_criticidad')),
            'id_customer'         => man_numero($this->valor($data, 'id_customer')),
            'numero_serie'        => $this->texto($data, 'numero_serie'),
            'estado'              => $this->texto($data, 'estado'),
            'fecha_ultimalectura' => man_fecha($this->valor($data, 'fecha_ultimalectura'), true),
            'ultima_lectura'      => man_numero($this->valor($data, 'ultima_lectura')),
            'descrip_tecnica'     => $this->texto($data, 'descrip_tecnica'),
            'id_equipo'           => (string) $id,
            'id_empresa'          => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /** Equipo con su marca (modal de contratistas). @return array|false objetos */
    public function getco($data = null){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getco(" . json_encode($data) . ")");
        if ($data == null) {
            return false;
        }
        return $this->objetos(wso2(REST_MAN . '/equipo/' . (int) $data['id_equipo'] . '/marca/empresa/' . man_empresa()));
    }

    /** Asigna un contratista de la empresa a un equipo de la empresa. @return bool */
    public function insert_contratista($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | insert_contratista(" . json_encode($data) . ")");
        $rsp = man_escribir('POST', 'equipo/contratista', array(
            'id_contratista' => (string) $data['id_contratista'],
            'id_equipo'      => (string) $data['id_equipo'],
            'id_empresa'     => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /** Quita un contratista de un equipo de la empresa. @return bool */
    public function delContratista($id_contratista, $id_equipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | delContratista($id_contratista, $id_equipo)");
        $rsp = man_escribir('PUT', 'equipo/contratista/baja', array(
            'id_contratista' => (string) $id_contratista,
            'id_equipo'      => (string) $id_equipo,
            'id_empresa'     => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /**
     * Registra una lectura y deja el equipo en el estado elegido (AC o RE).
     *
     * Asset además buscaba un preventivo del equipo y lo marcaba; esa consulta no filtraba bien por
     * empresa (T1) y la regla del contador se define en MIG-4 (T7, T8). MAN no toca preventivos
     * (decisión E16). Devuelve true si la lectura quedó guardada.
     * @return bool
     */
    public function setLecturas($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | setLecturas(" . json_encode($data) . ")");
        $estado = isset($data['estado']) ? $data['estado'] : '';
        if (($estado !== 'AC' && $estado !== 'RE') || !$this->equipoDeEmpresa($data['id_equipo'])) {
            return false;
        }
        return $this->lectura($data['id_equipo'], man_numero($data['lectura']), $this->valor($data, 'observacion'),
                $this->texto($data, 'operario'), $this->texto($data, 'turno'), $estado)
            && $this->estado($data['id_equipo'], $estado);
    }

    /**
     * Edita valor y observación de una lectura si el valor nuevo queda entre la lectura anterior y la
     * posterior del mismo equipo (cuando existen).
     * @return bool
     */
    public function setLecturaObservacionEdit($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | setLecturaObservacionEdit(" . json_encode($data) . ")");
        $idLectura = $data['id_lectura'];
        $lectura = $data['lectura'];
        $vecinas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $data['id_equipo'] . '/lectura/' . (int) $idLectura
            . '/vecinas/empresa/' . man_empresa()));
        if (!$vecinas) {
            return false;
        }
        $anterior = $vecinas[0]['anterior'];
        $posterior = $vecinas[0]['posterior'];
        if ($anterior !== null && !($lectura > $anterior)) {
            return false;
        }
        if ($posterior !== null && !($lectura < $posterior)) {
            return false;
        }
        $rsp = man_escribir('PUT', 'equipo/lectura', array(
            'lectura'     => man_numero($lectura),
            'observacion' => $this->valor($data, 'observacion'),
            'id_lectura'  => (string) $idLectura,
            'id_empresa'  => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /** Historial de lecturas de un equipo de la empresa. @return array filas (arrays) */
    public function getHistoriaLecturas($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getHistoriaLecturas(" . json_encode($data) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $data['idequipo'] . '/lecturas/empresa/' . man_empresa()));
        return $filas ? $filas : array();
    }

    /** Última lectura de un equipo de la empresa. @return array filas (arrays) */
    public function getEqPorIds($ideq){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getEqPorIds(" . json_encode($ideq) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $ideq . '/ultima-lectura/empresa/' . man_empresa()));
        if (!$filas) {
            return array();
        }
        unset($filas[0]['id_lectura']);
        return $filas;
    }

    /**
     * Equipo a editar con cliente, grupo y adjuntos.
     * @return array una fila (array), o [] si el equipo no es de la empresa o está anulado
     */
    public function getEquipoId($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getEquipoId(" . json_encode($idEquipo) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $idEquipo . '/editar/empresa/' . man_empresa()));
        if (!$filas) {
            return array();
        }
        $adjuntos = $this->getAdjuntos($idEquipo);
        $filas[0]['archivo'] = $adjuntos[0] === null ? null : $adjuntos;
        return $filas;
    }

    /** Contratistas asignados a un equipo de la empresa. @return array filas (arrays) */
    public function getContratistasEquipo($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getContratistasEquipo(" . json_encode($idEquipo) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $idEquipo . '/contratistas/empresa/' . man_empresa()));
        return $filas ? $filas : array();
    }

    /**
     * Habilita el equipo (AC) y deja la lectura de alta en el historial.
     * @return int|false id de la lectura
     */
    public function estado_alta($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | estado_alta($idEquipo)");
        if (!$this->equipoDeEmpresa($idEquipo) || !$this->estado($idEquipo, 'AC')
            || !$this->lectura($idEquipo, '0', 'Lectura alta de equipo', '-', 'Alta', 'AC')) {
            return false;
        }
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $idEquipo . '/ultima-lectura/empresa/' . man_empresa()));
        return $filas ? (int) $filas[0]['id_lectura'] : 0;
    }

    /** Registra un archivo adjunto de un equipo de la empresa. @return bool */
    public function guardaAdjuntoEquipo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | guardaAdjuntoEquipo(" . json_encode($data) . ")");
        $rsp = man_escribir('POST', 'equipo/adjunto', array(
            'adjunto'    => (string) $data['adjunto'],
            'id_equipo'  => (string) $data['id_equipo'],
            'id_empresa' => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /** Marca como eliminado un adjunto (el archivo queda en disco, como en asset). @return bool */
    public function eliminaAdjunto($idAdjunto){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | eliminaAdjunto($idAdjunto)");
        $rsp = man_escribir('PUT', 'equipo/adjunto/baja', array(
            'id_adjunto' => (string) $idAdjunto,
            'id_empresa' => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /** Cambia el archivo de un adjunto. @return array el mismo $archivo, como en asset */
    public function updateAdjunto($archivo, $idAdjunto){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | updateAdjunto(" . json_encode($archivo) . ", $idAdjunto)");
        man_escribir('PUT', 'equipo/adjunto', array(
            'adjunto'    => (string) $archivo['adjunto'],
            'id_adjunto' => (string) $idAdjunto,
            'id_empresa' => man_empresa(),
        ));
        return $archivo;
    }

    /** Meta de disponibilidad de un equipo de la empresa. @return string|null */
    public function getMeta($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getMeta(" . json_encode($data) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $data['eq'] . '/meta/empresa/' . man_empresa()));
        return $filas ? $filas[0]['meta_disponibilidad'] : null;
    }

    /** Asigna la meta de disponibilidad. @return bool */
    public function asignarMeta($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | asignarMeta(" . json_encode($data) . ")");
        $rsp = man_escribir('PUT', 'equipo/meta', array(
            'meta'       => man_numero($data['meta']),
            'id_equipo'  => (string) $data['eq'],
            'id_empresa' => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /**
     * Adjuntos no eliminados de un equipo de la empresa.
     * @return array lista de array(id_adjunto, adjunto), o array(null) si no hay (como en asset)
     */
    public function getAdjuntos($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getAdjuntos(" . json_encode($idEquipo) . ")");
        $filas = man_lista(wso2(REST_MAN . '/equipo/' . (int) $idEquipo . '/adjuntos/empresa/' . man_empresa()));
        return $filas ? $filas : array(null);
    }

    /** ¿El equipo es de la empresa de la sesión? */
    private function equipoDeEmpresa($idEquipo){
        return (bool) man_lista(wso2(REST_MAN . '/equipos/de-empresa/' . man_empresa() . '/equipo/' . (int) $idEquipo));
    }

    /** Cambia el estado de un equipo de la empresa. @return bool */
    private function estado($idEquipo, $estado){
        $rsp = man_escribir('PUT', 'equipo/estado', array(
            'estado'     => (string) $estado,
            'id_equipo'  => (string) $idEquipo,
            'id_empresa' => man_empresa(),
        ));
        return (bool) $rsp['status'];
    }

    /**
     * Agrega una fila a historial_lecturas, con la fecha del servidor web y el usuario de asset. El que
     * llama ya verificó que el equipo es de la empresa. @return bool
     */
    private function lectura($idEquipo, $lectura, $observacion, $operario, $turno, $estado){
        $rsp = man_escribir('POST', 'equipo/lectura', array(
            'lectura'      => $lectura,
            'fecha'        => date('Y-m-d H:i:s'),
            'usrId'        => man_usuario(),
            'observacion'  => $observacion === null ? null : (string) $observacion,
            'operario_nom' => (string) $operario,
            'turno'        => (string) $turno,
            'estado'       => (string) $estado,
            'id_equipo'    => (string) $idEquipo,
        ));
        return (bool) $rsp['status'];
    }

    /** Valor de un campo, o null si no llegó (como $this->input->post() en asset). */
    private function valor($data, $campo){
        return isset($data[$campo]) ? $data[$campo] : null;
    }

    /** Campo de texto: string, o null si no llegó. */
    private function texto($data, $campo){
        return isset($data[$campo]) ? (string) $data[$campo] : null;
    }

    /** Id que devuelve un alta con $GENERATED_KEY (wso2() lo reduce al valor), o false. */
    private function id($rsp){
        return ($rsp['status'] && is_scalar($rsp['data']) && (int) $rsp['data'] > 0) ? (string) $rsp['data'] : false;
    }

    /** Filas como objetos (result()), o false si no hay. */
    private function objetos($rsp){
        $filas = man_lista($rsp);
        return $filas ? json_decode(json_encode($filas)) : false;
    }
}
