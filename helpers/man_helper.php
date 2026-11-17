<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper del módulo MAN (AssetPlanner migrado a tools).
 *
 * Los datos de mantenimiento viven en la MariaDB `assetv2`, que numera las empresas distinto que
 * tools: la sesión de tools tiene `empr_id` (PostgreSQL, core.empresas) y las tablas de asset usan
 * `id_empresa` (empresas de assetv2). El vínculo es core.empresas.empr_id_mysql.
 *
 * Decisión MIG-ASSET-1 (2026-10-27): se resuelve una sola vez por sesión, por REST a
 * COREDataService (ADR-014: MAN no accede a ninguna base), y se guarda en la sesión.
 */

if (!function_exists('man_empresa')) {
    /**
     * Devuelve el id_empresa de asset de la empresa de la sesión, como string.
     * Devuelve null si no hay sesión o si la empresa no está vinculada con asset.
     */
    function man_empresa()
    {
        $ci =& get_instance();
        $emprId = empresa();
        if ($emprId === null) {
            return null;
        }

        $cache = $ci->session->userdata('man_empresa');
        if (is_array($cache) && isset($cache['empr_id']) && $cache['empr_id'] === $emprId) {
            return $cache['id_empresa'];
        }

        // callAPI directo y no wso2(): la respuesta es un objeto suelto ({"empresa":{...}}) y el
        // doble reset() de wso2() lo reduce a su primer campo.
        $rsp = $ci->rest->callAPI('GET', REST_CORE . '/empresa/' . $emprId);
        $aux = $rsp['status'] ? json_decode($rsp['data']) : null;
        $idEmpresa = null;
        if (isset($aux->empresa->empr_id_mysql) && $aux->empresa->empr_id_mysql !== '') {
            $idEmpresa = (string) $aux->empresa->empr_id_mysql;
        }

        if ($idEmpresa === null) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | man_helper | man_empresa() >> empresa ' . $emprId
                . ' sin vínculo con asset (core.empresas.empr_id_mysql)');
            return null;
        }

        $ci->session->set_userdata('man_empresa', array('empr_id' => $emprId, 'id_empresa' => $idEmpresa));
        return $idEmpresa;
    }
}

if (!function_exists('man_lista')) {
    /**
     * Convierte la respuesta de wso2() de una consulta que devuelve filas al formato que
     * devolvía result_array() de CodeIgniter: array de arrays asociativos, o null si no hay filas.
     */
    function man_lista($rsp)
    {
        if (!$rsp['status'] || empty($rsp['data'])) {
            return null;
        }
        $filas = json_decode(json_encode($rsp['data']), true);
        if (!is_array($filas) || empty($filas)) {
            return null;
        }
        // Una sola fila puede llegar como objeto y no como lista.
        if (array_keys($filas) !== range(0, count($filas) - 1)) {
            $filas = array($filas);
        }
        return $filas;
    }
}

if (!function_exists('man_dias_lanzamiento')) {
    /**
     * Días de anticipación con que se lanza el caso de Bonita de una OT preventiva o predictiva (MIG-8).
     *
     * Se lee de la lista de valores del sistema: core.tablas, tabla "configuraciones", días en valor2. El valor "man_dias_lanzamiento"
     * vale para todas las empresas y "man_dias_lanzamiento_<id de empresa de tools>" para una sola (pisa al general). La clave de
     * la fila es tabla + valor, por eso la empresa va en el valor y no en otra columna.
     * Si no hay fila válida (entero de 0 a 365) se usa MAN_DIAS_LANZAMIENTO de constants.php.
     *
     * @param int|string $emprId empresa de tools (core.empresas.empr_id)
     * @return int
     */
    function man_dias_lanzamiento($emprId)
    {
        $ci =& get_instance();
        $rsp = $ci->rest->callAPI('GET', REST_CORE . '/tablas/configuraciones');
        $aux = $rsp['status'] ? json_decode($rsp['data'], true) : null;
        $filas = (isset($aux['tablas']['tabla']) && is_array($aux['tablas']['tabla'])) ? $aux['tablas']['tabla'] : array();
        if ($filas && array_keys($filas) !== range(0, count($filas) - 1)) {
            $filas = array($filas);
        }
        $general = null;
        $propio = null;
        foreach ($filas as $f) {
            if (!isset($f['valor']) || !isset($f['valor2']) || !preg_match('/^\d{1,3}$/', trim($f['valor2'])) || (int) $f['valor2'] > 365) {
                continue;
            }
            if ($f['valor'] === 'man_dias_lanzamiento') {
                $general = (int) $f['valor2'];
            } elseif ($f['valor'] === 'man_dias_lanzamiento_' . $emprId) {
                $propio = (int) $f['valor2'];
            }
        }
        if ($propio !== null) {
            return $propio;
        }
        return $general !== null ? $general : (int) MAN_DIAS_LANZAMIENTO;
    }
}

if (!function_exists('man_escribir')) {
    /**
     * Escribe en MANDataService con el envoltorio JSON que exige el DataService
     * ("_<metodo><path con _>"). Devuelve la respuesta de wso2().
     */
    function man_escribir($metodo, $path, $params)
    {
        $path = ltrim($path, '/');
        $wrapper = '_' . strtolower($metodo) . str_replace('/', '_', $path);
        return wso2(REST_MAN . '/' . $path, strtoupper($metodo), array($wrapper => $params));
    }
}

/*
 * Cabecera con detalle de herramientas e insumos (Preventivo, Predictivo, Backlog).
 *
 * El alta es una operación encadenada (el detalle necesita el id de la cabecera), así que va por toolsMANAPI
 * (ADR-014, regla 3), que compensa si falla. En la edición el detalle se borra y se vuelve a grabar; cada lista
 * va en un pedido por lotes del DataService (una transacción): request_box no admite repetir una operación.
 */
if (!function_exists('man_alta_con_detalle')) {
    /**
     * @param string $entidad      preventivo | predictivo | backlog
     * @param array  $cabecera     parámetros de POST /<entidad> de MANDataService (con id_empresa de asset)
     * @param array  $herramientas lista de array(herrId, cantidad)
     * @param array  $insumos      lista de array(artId, cantidad)
     * @return string|false id de la cabecera creada
     */
    function man_alta_con_detalle($entidad, $cabecera, $herramientas, $insumos)
    {
        $ci =& get_instance();
        $rsp = $ci->rest->callAPI('POST', HOST . '/tools/man/' . $entidad, array(
            'cabecera'     => $cabecera,
            'herramientas' => array_values($herramientas),
            'insumos'      => array_values($insumos),
        ));
        $aux = $rsp['status'] ? json_decode($rsp['data'], true) : null;
        if (empty($aux['status']) || empty($aux['id'])) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | man_helper | man_alta_con_detalle(' . $entidad . ') >> ' . json_encode($rsp['data']));
            return false;
        }
        return (string) $aux['id'];
    }
}

if (!function_exists('man_lote_detalle')) {
    /**
     * Graba una lista de detalle en un solo pedido por lotes. Una lista vacía no hace nada y devuelve true.
     * @param string $entidad preventivo | predictivo | backlog
     * @param string $tipo    herramienta | insumo
     * @param array  $filas   filas con los parámetros de POST /<entidad>/<tipo>
     * @return int|bool cantidad de filas (lo que devolvía insert_batch() en asset), true si la lista está vacía, false si falla
     */
    function man_lote_detalle($entidad, $tipo, $filas)
    {
        if (empty($filas)) {
            return true;
        }
        $op = '_post_' . $entidad . '_' . $tipo;
        $rsp = wso2(REST_MAN . '/' . $entidad . '/' . $tipo . '_batch_req', 'POST', array($op . '_batch_req' => array($op => array_values($filas))));
        return $rsp['status'] ? count($filas) : false;
    }
}

if (!function_exists('man_detalle')) {
    /**
     * Arma la lista de detalle con los arrays paralelos que mandan las vistas de asset (ids y cantidades).
     * @return array lista de array($campoId => id, 'cantidad' => cantidad)
     */
    function man_detalle($ids, $cantidades, $campoId)
    {
        $out = array();
        if (empty($ids) || !is_array($ids)) {
            return $out;
        }
        $cantidades = is_array($cantidades) ? array_values($cantidades) : array();
        foreach (array_values($ids) as $i => $id) {
            $out[] = array($campoId => (string) $id, 'cantidad' => isset($cantidades[$i]) ? man_numero($cantidades[$i]) : '0');
        }
        return $out;
    }
}

if (!function_exists('man_usuario')) {
    /**
     * Devuelve el usrId de asset (sisusers) del usuario de la sesión de tools, como string.
     *
     * Decisión MIG-ASSET E15 (2026-10-28), provisoria hasta MIG-5: el vínculo es el correo (usrNick de
     * asset = usernick de tools) dentro de la empresa. Si no hay vínculo devuelve '0' y lo deja en el log,
     * para que el registro no se pierda; MIG-5 fija la clave definitiva y sanea los usuarios.
     */
    function man_usuario()
    {
        $ci =& get_instance();
        $nick = userNick();
        $emp = man_empresa();
        if (empty($nick) || $emp === null) {
            return '0';
        }

        $cache = $ci->session->userdata('man_usuario');
        if (is_array($cache) && $cache['nick'] === $nick && $cache['id_empresa'] === $emp) {
            return $cache['usrId'];
        }

        $filas = man_lista(wso2(REST_MAN . '/usuario/empresa/' . $emp . '?usrNick=' . rawurlencode($nick)));
        $usrId = $filas ? (string) $filas[0]['usrId'] : '0';
        if ($usrId === '0') {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | man_helper | man_usuario() >> ' . $nick
                . ' sin usuario en asset para la empresa ' . $emp . ' (se registra con usrId 0)');
        }

        $ci->session->set_userdata('man_usuario', array('nick' => $nick, 'id_empresa' => $emp, 'usrId' => $usrId));
        return $usrId;
    }
}

/*
 * Valores para columnas numéricas y de fecha de assetv2.
 *
 * Decisión MIG-ASSET E13: la conexión de WSO2 es estricta y rechaza lo que MariaDB convertía sola
 * (un texto vacío en una columna numérica o de fecha). Estas funciones hacen esa conversión antes de
 * mandar el dato, para que quede lo mismo que guardaba asset. null se respeta (campo que no llegó).
 */
if (!function_exists('man_numero')) {
    /** '' → '0'; '12abc' → '12' (como MariaDB); null → null. */
    function man_numero($valor)
    {
        if ($valor === null) {
            return null;
        }
        $valor = trim((string) $valor);
        if ($valor === '') {
            return '0';
        }
        return is_numeric($valor) ? $valor : (string) (0 + floatval($valor));
    }
}

if (!function_exists('man_fecha')) {
    /** '' → fecha cero de MariaDB ('0000-00-00', con hora si $conHora); null → null. */
    function man_fecha($valor, $conHora = false)
    {
        if ($valor === null) {
            return null;
        }
        $valor = trim((string) $valor);
        if ($valor === '') {
            return $conHora ? '0000-00-00 00:00:00' : '0000-00-00';
        }
        return $valor;
    }
}

/*
 * Archivos (PDF de componentes, adjuntos de equipos, imágenes de OT).
 *
 * Decisión MIG-ASSET E11 (2026-10-27): mientras asset siga en uso, MAN lee y escribe en la MISMA carpeta
 * de archivos de asset, para que las dos aplicaciones vean lo mismo y el corte por empresa se pueda
 * revertir sin perder archivos. En los servidores asset y tools comparten Apache (asset en
 * /tools/traz-prod-assetplanner y tools en /traz-tools), así que por defecto se resuelve relativo a tools.
 * Para otro layout (ej. local) se definen MAN_ASSET_PATH y MAN_ASSET_URL antes de cargar el módulo.
 */
if (!function_exists('man_asset_path')) {
    /** Carpeta raíz de asset en disco, con barra final. */
    function man_asset_path()
    {
        return defined('MAN_ASSET_PATH') ? MAN_ASSET_PATH : rtrim(dirname(rtrim(FCPATH, '/')), '/') . '/tools/traz-prod-assetplanner/';
    }
}

if (!function_exists('man_asset_url')) {
    /** URL raíz de asset, con barra final. */
    function man_asset_url()
    {
        if (defined('MAN_ASSET_URL')) {
            return MAN_ASSET_URL;
        }
        $base = rtrim(base_url(), '/');
        return substr($base, 0, strrpos($base, '/')) . '/tools/traz-prod-assetplanner/';
    }
}

/*
 * Bandeja: dispositivo y tiempo de recarga. Asset usaba la librería Mobile_Detect y la constante TIEMPO_RECARGA, que tools no tiene.
 */
if (!function_exists('man_dispositivo')) {
    /** "android" si el navegador es de un celular o tablet, "pc" si no (la vista oculta columnas en el celular). */
    function man_dispositivo()
    {
        $agente = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return preg_match('/Mobile|Android|iPhone|iPad|iPod|Tablet|Silk|Kindle/i', $agente) ? 'android' : 'pc';
    }
}

if (!function_exists('man_tiempo_recarga')) {
    /** Milisegundos entre actualizaciones automáticas de la bandeja. Asset: 1000000 (unos 17 minutos). */
    function man_tiempo_recarga()
    {
        return defined('MAN_TIEMPO_RECARGA') ? MAN_TIEMPO_RECARGA : 1000000;
    }
}
