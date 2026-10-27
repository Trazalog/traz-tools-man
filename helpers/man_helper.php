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
