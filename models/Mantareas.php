<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Modelo del proceso "Proceso de Mantenimiento AssetPlanner" para la bandeja de tools (traz-comp-bpm) — MIG-ASSET-7.
 *
 * La bandeja de tools atiende cada proceso con un modelo registrado en BPM_PROCESS (constants.php) que implementa cuatro ganchos:
 *   map              las pastillas de colores de la tarjeta y su descripción
 *   desplegarCabecera la franja de datos de arriba del panel
 *   desplegarVista    la pantalla de trabajo, según el nombre de la tarea (queda bloqueada hasta tomar la tarea)
 *   getContrato       los datos con que se cierra la tarea en Bonita
 * Acá el cierre no pasa por getContrato: el botón "Hecho" del panel llama a cerrarTarea(), que define cada pantalla, y esa función manda a
 * los recursos de toolsMANAPI (Bonita y base juntos, con deshacer). Ver doc/migracion/PROPUESTA-BANDEJA.md.
 */
class Mantareas extends CI_Model
{
    // Tipo de orden: texto y color (los mismos del Calendario)
    private $tipos = array(
        '1' => array('Orden de trabajo', '#3c8dbc'), '2' => array('Urgente', '#f56954'), '3' => array('Preventivo', '#39CCCC'),
        '4' => array('Backlog', '#ff851b'), '5' => array('Predictivo', '#00a65a'), '6' => array('Correctivo programado', '#D81B60'),
    );
    // Estado de la orden o de la solicitud: texto y clase de la pastilla (amarillo mientras está pendiente, verde si terminó)
    private $estados = array(
        'S' => array('Solicitada', 'warning'), 'PL' => array('Planificada', 'warning'), 'AS' => array('Asignada', 'warning'), 'C' => array('En curso', 'warning'),
        'T' => array('Terminada', 'success'), 'TE' => array('Terminada parcial', 'success'), 'E' => array('Entregada', 'success'), 'CE' => array('Cerrada', 'success'),
        'AN' => array('Anulada', 'default'), 'CA' => array('Cancelada', 'default'), 'EL' => array('Eliminada', 'default'), 'B' => array('Borrador', 'default'),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(MAN . 'man');
        $this->load->helper(MAN . 'man_tarea');
    }

    /** Datos de asset del caso de la tarea (solicitud, orden, equipo, sector, cliente y responsable), o array vacío si no es de la empresa */
    private function info($caseId)
    {
        $filas = man_lista(wso2(REST_MAN . '/bandeja/tarea-info/caso/' . (int) $caseId . '/empresa/' . man_empresa()));
        return $filas ? $filas[0] : array();
    }

    private function pastilla($texto, $clase = 'default', $estilo = '')
    {
        $p = new StdClass();
        $p->color = $clase;
        $p->texto = $texto;
        if ($estilo !== '') {
            $p->estilo = $estilo;
        }
        return $p;
    }

    /** Pastillas de la tarjeta de la bandeja */
    public function map($tarea)
    {
        $r = $this->info($tarea->caseId);
        if (empty($r)) {
            return array('descripcion' => 'Sin datos de Mantenimiento para este caso');
        }
        $lleno = function ($v) {
            return isset($v) && $v !== '' && $v !== null;
        };
        $info = array();
        if ($lleno($r['tip_ta']) && isset($this->tipos[$r['tip_ta']])) {
            $t = $this->tipos[$r['tip_ta']];
            $info[] = $this->pastilla($t[0], 'default', 'background-color:' . $t[1] . ' !important;color:#fff !important');
        }
        if ($lleno($r['ss'])) {
            $info[] = $this->pastilla('SS N° ' . $r['ss'], 'info');
        }
        if ($lleno($r['ot'])) {
            $info[] = $this->pastilla('OT N° ' . $r['ot'], 'primary');
        }
        if ($lleno($r['equipo'])) {
            $info[] = $this->pastilla('Equipo: ' . $r['equipo']);
        }
        if ($lleno($r['sector'])) {
            $info[] = $this->pastilla('Sector: ' . $r['sector']);
        }
        if ($lleno($r['cliente'])) {
            $info[] = $this->pastilla('Cliente: ' . $r['cliente']);
        }
        if ($lleno($r['ot'])) {
            $info[] = $lleno($r['responsable']) ? $this->pastilla($r['responsable'], 'success') : $this->pastilla('Sin asignar');
        }
        $estado = $lleno($r['ot_estado']) ? $r['ot_estado'] : ($lleno($r['ss_estado']) ? $r['ss_estado'] : '');
        if ($estado !== '' && isset($this->estados[$estado])) {
            $info[] = $this->pastilla($this->estados[$estado][0], $this->estados[$estado][1]);
        }
        return array('info' => $info, 'descripcion' => $lleno($r['descr']) ? $r['descr'] : 'Sin descripción', 'tagCase' => '');
    }

    /** Franja de datos de arriba del panel: equipo, solicitud y orden */
    public function desplegarCabecera($tarea)
    {
        $r = $this->info($tarea->caseId);
        if (empty($r)) {
            return '';
        }
        $this->load->model(MAN . 'Tareas', 'ManTareas');
        $ot = (isset($r['ot']) && $r['ot'] !== '') ? $this->ManTareas->cabeceraOt($r['ot']) : null;
        $ss = (isset($r['ss']) && $r['ss'] !== '') ? $this->ManTareas->cabeceraSolicitud($r['ss']) : null;
        $eq = (isset($r['id_equipo']) && $r['id_equipo'] !== '') ? $this->ManTareas->cabeceraEquipo($r['id_equipo']) : null;
        return man_cabecera($eq, $ss, $ot);
    }

    /** Pantalla de trabajo de la tarea: la de la bandeja de MAN, adaptada al panel (sin cabecera, comentarios ni línea de tiempo) */
    public function desplegarVista($tarea)
    {
        $this->load->model(MAN . 'Tareas', 'ManTareas');
        $permiso = $this->session->userdata('user_data');
        $permiso = (is_array($permiso) && !empty($permiso['permission'])) ? $permiso['permission'] : 'Add-Edit-Del';
        $r = $this->ManTareas->datosVista($tarea->taskId, $permiso);
        if (isset($r['error'])) {
            return '<div class="alert alert-danger">' . htmlspecialchars($r['error'], ENT_QUOTES, 'UTF-8') . '</div>';
        }
        $r['data']['cabecera'] = '';   // la pone la plantilla de la bandeja
        return $this->load->view(MAN . 'tareas/panel', array('vista' => $r['vista'], 'data' => $r['data'], 'nombre' => $r['tarea']['displayName']), true);
    }

    /** El cierre no pasa por acá: lo hace cerrarTarea() de la pantalla, con los recursos de toolsMANAPI */
    public function getContrato($tarea, $form)
    {
        return array();
    }
}
