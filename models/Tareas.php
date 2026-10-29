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
