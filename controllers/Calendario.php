<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Plan de Mantenimiento (Calendario) — MIG-ASSET-4.
 *
 * Reescrito, no portado: el calendario de asset usaba FullCalendar 2.2.5 sobre jQuery 2. Acá usa el FullCalendar 5 del layout de tools.
 * El controller es una capa delgada: arma lo que pide la pantalla y delega todo en el model y en los recursos de toolsMANAPI (programar
 * órdenes, asignar responsable). Especificación: doc/migracion/MIG-4-CALENDARIO.md del repositorio tools.
 */
class Calendario extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('man');
        $this->load->model('Calendarios');
    }

    /**
     * Pantalla del Plan de Mantenimiento
     * @param string $permission acciones habilitadas; asset agrega siempre las cuatro tablas laterales
     */
    public function indexot($permission = '')
    {
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendario | indexot($permission)");
        if (man_empresa() === null) {
            echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
            return;
        }
        $data['permission'] = $permission . 'Correctivo-Preventivos-Backlog-Predictivo-';
        $this->output->set_header('Content-Type: text/html; charset=utf-8');
        $this->load->view('calendar/calendar1', $data);
    }

    /** Órdenes del rango visible del calendario (POST desde y hasta, aaaa-mm-dd) */
    public function getcalendarot()
    {
        $desde = (string) $this->input->post('desde');
        $hasta = (string) $this->input->post('hasta');
        if (!$this->esFecha($desde) || !$this->esFecha($hasta)) {
            echo json_encode(false);
            return;
        }
        echo json_encode($this->Calendarios->getot($desde, $hasta));
    }

    /** Panel con las opciones del filtro */
    public function panelFiltro()
    {
        $this->output->set_header('Content-Type: text/html; charset=utf-8');
        $data['filtro'] = $this->Calendarios->opcionesFiltro();
        $this->load->view('calendar/filtro', $data);
    }

    /** Tablas de lo que falta programar en el mes (POST mes, year, permission) */
    public function getTablas()
    {
        $mes = (int) $this->input->post('mes');
        $year = (int) $this->input->post('year');
        $permission = (string) $this->input->post('permission');
        if ($mes < 1 || $mes > 12 || $year < 2000 || $year > 2100) {
            $this->output->set_status_header(400);
            echo 'Mes o año inválido';
            return;
        }
        $data['mes'] = $mes;
        $data['year'] = $year;
        $data['permission'] = $permission;
        $data['list0'] = (strpos($permission, 'Preventivos') !== false) ? $this->Calendarios->getPreventivosHoras() : false;
        $data['list1'] = (strpos($permission, 'Predictivo') !== false) ? $this->Calendarios->getpredlist($mes, $year) : false;
        $data['list2'] = (strpos($permission, 'Backlog') !== false) ? $this->Calendarios->getbacklog($mes, $year) : false;
        $data['list3'] = (strpos($permission, 'Preventivos') !== false) ? $this->Calendarios->getPreventivos($mes, $year) : false;
        $data['list4'] = (strpos($permission, 'Correctivo') !== false) ? $this->Calendarios->getServicioTareas($mes, $year) : false;
        $this->output->set_header('Content-Type: text/html; charset=utf-8');
        $this->load->view('calendar/tablas', $data);
    }

    /** Períodos de la empresa (selector de Preventivo, Predictivo, OT y Solicitudes); "nada" si no hay, como asset */
    public function getperiodo()
    {
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Calendario | getperiodo()");
        $periodo = $this->Calendarios->getperiodo($this->input->post());
        if ($periodo) {
            echo json_encode($periodo);
        } else {
            echo 'nada';
        }
    }

    /** Cambia la fecha y hora de una orden (POST id, prog = aaaa-mm-dd hh:mm:ss) */
    public function updateDiaProg()
    {
        $prog = (string) $this->input->post('prog');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}(:\d{2})?$/', $prog)) {
            echo json_encode(false);
            return;
        }
        $prog = str_replace('T', ' ', $prog) . (strlen($prog) == 16 ? ':00' : '');
        echo json_encode($this->Calendarios->updateDiaProgramacion($this->input->post('id'), $prog));
    }

    /** Suma (o resta) minutos a la duración de una orden (POST id, duracion) */
    public function updateDuracion()
    {
        $minutos = $this->input->post('duracion');
        if (!is_numeric($minutos)) {
            echo json_encode(false);
            return;
        }
        echo json_encode($this->Calendarios->updateDuraciones($this->input->post('id'), $minutos));
    }

    /** true si es aaaa-mm-dd */
    private function esFecha($valor)
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor);
    }
}
