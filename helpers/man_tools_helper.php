<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Integración de MAN con almacén y pañol de tools (MIG-2, punto 2 del plan de migración).
 *
 * Es la parte de lectura de application/helpers/tools_helper.php de asset (REQ-ASSET-ALM), con los mismos
 * nombres y formatos de salida para que el código portado no cambie. Diferencias:
 *   - tools_empr_id() es el empr_id de la sesión de tools: no hace falta buscarlo por el id de asset.
 *   - Los DataServices se llaman con las constantes de tools (REST_CORE, REST_ALM, REST_PAN), sin el
 *     HOST_TOOLS de asset.
 * Las escrituras (vales, entregas, devoluciones) se portan con las pantallas que las usan.
 */

if (!function_exists('tools_empr_id')) {
    /**
     * empr_id de tools de la sesión.
     *
     * @return int|null
     */
    function tools_empr_id()
    {
        $emprId = empresa();
        return $emprId === null || $emprId === '' ? null : (int) $emprId;
    }
}

if (!function_exists('tools_config')) {
    /**
     * Lee la config por empresa registrada en el setup del cliente.
     * Claves válidas: 'DEPO' (depósito único), 'PANO' (pañol único), 'ESTA' (establecimiento).
     * Cachea en sesión ('man_tools_cfg_<clave>_<empr_id>': en tools el usuario puede cambiar de empresa).
     *
     * @param  string $clave
     * @return string|null  el id guardado, o null si falta config
     */
    function tools_config($clave)
    {
        $ci =& get_instance();

        $emprId = tools_empr_id();
        if ($emprId === null) {
            return null;
        }

        $cacheKey = 'man_tools_cfg_' . $clave . '_' . $emprId;
        $cached   = $ci->session->userdata($cacheKey);
        if ($cached) {
            return $cached;
        }

        $aux  = $ci->rest->callAPI('GET', REST_CORE . '/tabla/ASSET/valor/' . $clave . '/empresa/' . $emprId);
        $resp = json_decode($aux['data']);

        if (empty($resp->configuracion->valor)) {
            log_message('ERROR', '#TRAZA | TRAZ-TOOLS-MAN | man_tools_helper | tools_config(' . $clave . ') >> sin config para empr_id '
                . $emprId . ' — correr el paso 6 del setup del cliente');
            return null;
        }

        $ci->session->set_userdata($cacheKey, $resp->configuracion->valor);
        return $resp->configuracion->valor;
    }
}

if (!function_exists('tools_articulos')) {
    /**
     * Catálogo de artículos de tools para la empresa logueada (getArticulos,
     * vía /articulos/empresa/{empr_id} — path SIN duplicados; /articulos/{empr_id}
     * está definido dos veces en el DataService y el que responde depende del
     * orden de los resources). Cache por request (static). Claves devueltas:
     * arti_id, barcode, titulo, descripcion, costo, cantidad_caja, punto_pedido,
     * estado, unidad_medida, es_loteado, batch_id, stock.
     *
     * @return array
     */
    function tools_articulos()
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $ci =& get_instance();

        $emprId = tools_empr_id();
        if ($emprId === null) {
            return $cache = array();
        }

        $aux  = $ci->rest->callAPI('GET', REST_ALM . '/articulos/empresa/' . $emprId);
        $resp = json_decode($aux['data'], true);

        if (empty($resp['articulos']['articulo'])) {
            return $cache = array();
        }

        $articulos = $resp['articulos']['articulo'];
        // El DataService devuelve un objeto (no una lista) cuando hay una sola fila.
        if (isset($articulos['arti_id'])) {
            $articulos = array($articulos);
        }

        return $cache = $articulos;
    }
}

if (!function_exists('tools_articulos_map')) {
    /**
     * Catálogo indexado por arti_id.
     *
     * @return array [arti_id => articulo]
     */
    function tools_articulos_map()
    {
        $map = array();
        foreach (tools_articulos() as $a) {
            $map[(int) $a['arti_id']] = $a;
        }
        return $map;
    }
}

if (!function_exists('tools_articulos_autocomplete')) {
    /**
     * Catálogo con el shape que esperan los autocompletes de asset
     * (reemplaza a los SELECT sobre `articles`): value / codigo / label.
     *
     * @return array de objetos {value, codigo, label}
     */
    function tools_articulos_autocomplete()
    {
        $out = array();
        foreach (tools_articulos() as $a) {
            if (isset($a['estado']) && $a['estado'] === 'AN') {
                continue;
            }
            $out[] = (object) array(
                'value'  => (int) $a['arti_id'],
                'codigo' => $a['barcode'],
                'label'  => $a['descripcion'],
            );
        }
        usort($out, function ($x, $y) {
            return strcasecmp($x->label, $y->label);
        });
        return $out;
    }
}

if (!function_exists('tools_merge_articulos')) {
    /**
     * Completa filas locales de insumos (tbl_preventivoinsumos / tbl_predictivoinsumos /
     * tbl_backloginsumos / tbl_otinsumos) con los datos del catálogo de tools,
     * conservando las claves que las vistas de asset ya esperan
     * (artBarCode, artDescription, id_empresa).
     *
     * @param  array $rows filas con al menos ['artId' => ...]
     * @return array mismas filas + artBarCode / artDescription / id_empresa
     */
    function tools_merge_articulos($rows)
    {
        if (empty($rows)) {
            return array();
        }

        $map    = tools_articulos_map();
        $emprId = tools_empr_id();

        foreach ($rows as &$r) {
            $artId = isset($r['artId']) ? (int) $r['artId'] : 0;
            if (isset($map[$artId])) {
                $r['artBarCode']     = $map[$artId]['barcode'];
                $r['artDescription'] = $map[$artId]['descripcion'];
            } else {
                // Referencia histórica a un id que no existe en el catálogo de tools
                // (dato anterior al setup del cliente). Se muestra, no se oculta.
                $r['artBarCode']     = '';
                $r['artDescription'] = '(articulo ' . $artId . ' no disponible en tools)';
            }
            $r['id_empresa'] = $emprId;
        }
        unset($r);

        return $rows;
    }
}

if (!function_exists('tools_herramientas')) {
    /**
     * Catálogo de herramientas de tools (pañol) para la empresa logueada
     * (PANDataservice herramientasGet, vía /herramientas/empresa/{empr_id}).
     * Cache por request (static). Claves devueltas: herr_id, codigo, marca
     * (nombre resuelto desde core.tablas), marca_id, modelo, tipo, descripcion,
     * pano_id, estado, pan_descrip.
     *
     * @return array
     */
    function tools_herramientas()
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $ci =& get_instance();

        $emprId = tools_empr_id();
        if ($emprId === null) {
            return $cache = array();
        }

        $aux  = $ci->rest->callAPI('GET', REST_PAN . '/herramientas/empresa/' . $emprId);
        $resp = json_decode($aux['data'], true);

        if (empty($resp['herramientas']['herramienta'])) {
            return $cache = array();
        }

        $herr = $resp['herramientas']['herramienta'];
        // El DataService devuelve un objeto (no una lista) cuando hay una sola fila.
        if (isset($herr['herr_id'])) {
            $herr = array($herr);
        }

        return $cache = $herr;
    }
}

if (!function_exists('tools_herramientas_map')) {
    /**
     * Catálogo de herramientas indexado por herr_id.
     *
     * @return array [herr_id => herramienta]
     */
    function tools_herramientas_map()
    {
        $map = array();
        foreach (tools_herramientas() as $h) {
            $map[(int) $h['herr_id']] = $h;
        }
        return $map;
    }
}

if (!function_exists('tools_merge_herramientas')) {
    /**
     * Completa filas locales (tbl_preventivoherramientas / tbl_predictivoherramientas /
     * tbl_backlogherramientas / tbl_otherramientas) con los datos del catálogo del
     * pañol de tools, conservando las claves que las vistas de asset esperan
     * (herrcodigo, herrmarca, herrdescrip).
     *
     * @param  array $rows filas con al menos ['herrId' => ...]
     * @return array mismas filas + herrcodigo / herrmarca / herrdescrip
     */
    function tools_merge_herramientas($rows)
    {
        if (empty($rows)) {
            return array();
        }

        $map = tools_herramientas_map();

        foreach ($rows as &$r) {
            $herrId = isset($r['herrId']) ? (int) $r['herrId'] : 0;
            if (isset($map[$herrId])) {
                $r['herrcodigo'] = $map[$herrId]['codigo'];
                $r['herrmarca']  = $map[$herrId]['marca'];
                $r['herrdescrip'] = $map[$herrId]['descripcion'];
            } else {
                // Referencia histórica a un id que no existe en el pañol de tools
                // (dato anterior al setup del cliente). Se muestra, no se oculta.
                $r['herrcodigo'] = '';
                $r['herrmarca']  = '';
                $r['herrdescrip'] = '(herramienta ' . $herrId . ' no disponible en tools)';
            }
        }
        unset($r);

        return $rows;
    }
}

if (!function_exists('tools_herramientas_disponibles')) {
    /**
     * Herramientas que se pueden OFRECER para usar: las del pañol asignado a
     * la empresa (config `PANO`) y con estado 'ACTIVO'. Se excluyen las que
     * están en 'TRANSITO' (salieron y no volvieron) y las de otros pañoles.
     * Ordenadas por nombre (descripción). Devuelve las filas crudas del
     * catálogo (mismas claves que tools_herramientas()).
     *
     * Si no hay pañol configurado, filtra solo por estado (no rompe).
     *
     * @return array
     */
    function tools_herramientas_disponibles()
    {
        $pano = tools_config('PANO');
        $out  = array();
        foreach (tools_herramientas() as $h) {
            $disponible = isset($h['estado']) && $h['estado'] === 'ACTIVO';
            $delPanol   = ($pano === null) || (isset($h['pano_id']) && (string) $h['pano_id'] === (string) $pano);
            if ($disponible && $delPanol) {
                $out[] = $h;
            }
        }
        usort($out, function ($x, $y) {
            return strcasecmp($x['descripcion'], $y['descripcion']);
        });
        return $out;
    }
}

if (!function_exists('tools_herramientas_autocomplete')) {
    /**
     * Catálogo de herramientas disponibles del pañol asignado, con el shape del
     * autocomplete de los planes (reemplaza getHerramientasB): value / codigo /
     * marca / label. Ordenado por nombre.
     *
     * @return array de arrays asociativos
     */
    function tools_herramientas_autocomplete()
    {
        $out = array();
        foreach (tools_herramientas_disponibles() as $h) {
            $out[] = array(
                'value'  => (int) $h['herr_id'],
                'codigo' => $h['codigo'],
                'marca'  => $h['marca'],
                'label'  => $h['descripcion'],
            );
        }
        return $out;
    }
}

if (!function_exists('tools_herramientas_informe')) {
    /**
     * Catálogo de herramientas disponibles del pañol asignado, con el shape que
     * espera el autocomplete del INFORME de servicio (distinto del de los
     * planes): label=descripción, value=marca, codherram=código, herrId=id.
     *
     * @return array de arrays asociativos
     */
    function tools_herramientas_informe()
    {
        $out = array();
        foreach (tools_herramientas_disponibles() as $h) {
            $out[] = array(
                'label'     => $h['descripcion'],
                'value'     => $h['marca'],
                'codherram' => $h['codigo'],
                'herrId'    => (int) $h['herr_id'],
            );
        }
        return $out;
    }
}

if (!function_exists('tools_herramientas_full')) {
    /**
     * Catálogo de herramientas disponibles del pañol asignado, con los nombres
     * de columna de la tabla `herramientas` de asset (para los consumidores que
     * esperaban result() del modelo viejo).
     *
     * @return array de objetos con herrId/herrcodigo/herrmarca/herrdescrip/...
     */
    function tools_herramientas_full()
    {
        $out = array();
        foreach (tools_herramientas_disponibles() as $h) {
            $out[] = (object) array(
                'herrId'      => (int) $h['herr_id'],
                'herrcodigo'  => $h['codigo'],
                'herrmarca'   => $h['marca'],
                'modid'       => isset($h['marca_id']) ? $h['marca_id'] : null,
                'tipoid'      => isset($h['tipo']) ? $h['tipo'] : null,
                'equip_estad' => isset($h['estado']) ? $h['estado'] : null,
                'herrdescrip' => $h['descripcion'],
                'depositoId'  => isset($h['pano_id']) ? $h['pano_id'] : null,
                'id_empresa'  => tools_empr_id(),
            );
        }
        return $out;
    }
}

/*
 * Entregas de pedidos y uso de herramientas de una OT (informe de servicio, MIG-ASSET-2). Mismas funciones que
 * tools_helper.php de asset (F4/F5 de REQ-ASSET-ALM), con las constantes de tools.
 */
if (!function_exists('tools_lista_rest')) {
    /**
     * GET a un DataService de tools; devuelve la lista $raiz.$fila de la respuesta (una sola fila llega como objeto).
     *
     * @return array
     */
    function tools_lista_rest($url, $raiz, $fila)
    {
        $ci =& get_instance();
        $aux  = $ci->rest->callAPI('GET', $url);
        $resp = json_decode($aux['data'], true);
        $out  = isset($resp[$raiz][$fila]) ? $resp[$raiz][$fila] : array();
        if ($out && !isset($out[0])) {
            $out = array($out);
        }
        return $out;
    }
}

if (!function_exists('tools_pedidos_ot')) {
    /** Pedidos de materiales de una OT de asset en el almacén de tools, de la empresa de la sesión. @return array */
    function tools_pedidos_ot($idOt)
    {
        $emprId = tools_empr_id();
        if ($emprId === null) {
            return array();
        }
        return tools_lista_rest(REST_ALM . '/pedidos/orden/' . (int) $idOt . '/' . $emprId, 'pedidos', 'pedido');
    }
}

if (!function_exists('tools_pedido_detalle')) {
    /** Detalle de un pedido de materiales. @return array */
    function tools_pedido_detalle($pema)
    {
        return $pema ? tools_lista_rest(REST_ALM . '/pedidos/detalle/' . (int) $pema, 'detalles', 'detalle') : array();
    }
}

if (!function_exists('tools_entregas_pedido')) {
    /** Entregas de un pedido. @return array */
    function tools_entregas_pedido($pema)
    {
        return $pema ? tools_lista_rest(REST_ALM . '/entregas/pedido/' . (int) $pema, 'entregas', 'entrega') : array();
    }
}

if (!function_exists('tools_entrega_detalle')) {
    /** Detalle de una entrega. @return array */
    function tools_entrega_detalle($enma)
    {
        return $enma ? tools_lista_rest(REST_ALM . '/entregas/detalle/' . (int) $enma, 'detalles', 'detalle') : array();
    }
}

if (!function_exists('tools_salida_fecha')) {
    /** Salida de pañol de un vale. @return array|null */
    function tools_salida_fecha($sapa)
    {
        $ci =& get_instance();
        if (!$sapa) {
            return null;
        }
        $aux  = $ci->rest->callAPI('GET', REST_PAN . '/salida/fecha/' . (int) $sapa);
        $resp = json_decode($aux['data'], true);
        return isset($resp['salida']) ? $resp['salida'] : null;
    }
}

if (!function_exists('tools_devolucion_herramienta')) {
    /** Fecha de la primera devolución de la herramienta desde $desde, o null. @return string|null */
    function tools_devolucion_herramienta($herrId, $desde)
    {
        $ci =& get_instance();
        if (!$herrId || !$desde) {
            return null;
        }
        $aux  = $ci->rest->callAPI('GET', REST_PAN . '/herramienta/' . (int) $herrId . '/devolucion/desde/' . rawurlencode($desde));
        $resp = json_decode($aux['data'], true);
        $f = isset($resp['devolucion']['fecha']) ? $resp['devolucion']['fecha'] : '';
        return ($f !== '' && $f !== null) ? $f : null;
    }
}

if (!function_exists('tools_dias_entre')) {
    /** Días enteros entre dos fechas (sin $hasta: hasta hoy); nunca negativo. @return int */
    function tools_dias_entre($desde, $hasta)
    {
        if (!$desde) {
            return 0;
        }
        $d1 = strtotime(substr($desde, 0, 10));
        $d2 = $hasta ? strtotime(substr($hasta, 0, 10)) : time();
        $dias = (int) floor(($d2 - $d1) / 86400);
        return $dias < 0 ? 0 : $dias;
    }
}
