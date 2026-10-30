<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Piezas comunes de las pantallas de tarea de la Bandeja (MIG-ASSET-3): cabecera con los datos del equipo, la solicitud y la OT, y
 * línea de tiempo del caso. Portadas de cabecera_helper y timeline_helper de asset: reciben los datos ya leídos (por MANDataService,
 * ADR-014) en vez de consultar la base, y escapan todo lo que muestran (asset imprimía los textos tal cual venían de la base y de Bonita).
 */

if (!function_exists('man_esc')) {
    /** Texto seguro para mostrar en HTML. */
    function man_esc($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('man_duracion')) {
    /** Tiempo entre dos fechas como "2 días 03 hs", "03 hs" o "15 min". */
    function man_duracion($desde, $hasta)
    {
        if (empty($desde) || empty($hasta)) {
            return '';
        }
        try {
            $d = new DateTime($desde);
            $r = $d->diff(new DateTime($hasta));
        } catch (Exception $e) {
            return '';
        }
        $horas = $r->format('%H');
        $out = ($r->days ? $r->days . ' días ' : '') . ($horas !== '00' ? $horas . ' hs' : '');
        return $out !== '' ? trim($out) : $r->format('%i') . ' min';
    }
}

if (!function_exists('man_fecha_tl')) {
    /** Fecha de Bonita (puede venir vacía) como d/m/Y H:i. */
    function man_fecha_tl($valor)
    {
        $t = strtotime((string) $valor);
        return $t ? date('d/m/Y H:i', $t) : '';
    }
}

if (!function_exists('man_cabecera')) {
    /**
     * Cajas plegables con los datos del equipo, de la solicitud de servicio y de la OT de la tarea.
     * @param array|null $equipo cabeceraEquipo; $solicitud cabeceraSolicitud (con adjuntos); $ot cabeceraOt
     */
    function man_cabecera($equipo, $solicitud, $ot)
    {
        $campo = function ($titulo, $valor, $col = 4) {
            return '<div class="col-xs-12 col-sm-' . $col . '"><div class="form-group"><label style="margin-top: 7px;">' . $titulo
                . ' </label><input type="text" class="form-control" value="' . man_esc($valor) . '" disabled/></div></div>';
        };
        $caja = function ($titulo, $cuerpo, $clase = '') {
            return '<div class="box box-default collapsed-box box-solid ' . $clase . '"><div class="box-header with-border">'
                . '<h3 class="box-title">' . $titulo . ' / Mas Detalles</h3><div class="box-tools pull-right">'
                . '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div></div>'
                . '<div class="box-body">' . $cuerpo . '</div></div>';
        };
        $estadoEquipo = array('AC' => 'Activo', 'IN' => 'Inactivo', 'AL' => 'Alta');
        $estadoOt = array('S' => 'Solicitada', 'PL' => 'Planificada', 'AS' => 'Asignada', 'C' => 'Curso', 'T' => 'Terminada',
            'CE' => 'Cerrada', 'CN' => 'Conforme');
        $out = '';
        if ($equipo) {
            $out .= $caja('Equipo: ' . man_esc($equipo['codigo']) . ' - ' . man_esc($equipo['descripcionEquipo']),
                $campo('Marca:', $equipo['marca']) . $campo('Código:', $equipo['codigo']) . $campo('Cliente:', $equipo['cliente'])
                . $campo('Ubicación:', $equipo['ubicacion']) . '<div class="clearfix"></div>'
                . $campo('Descripción:', $equipo['descripcionEquipo']) . $campo('Fecha Ingreso:', $equipo['fecha_ingreso'])
                . $campo('Estado:', isset($estadoEquipo[$equipo['estado']]) ? $estadoEquipo[$equipo['estado']] : ''));
        }
        if ($solicitud) {
            $galeria = '';
            foreach ($solicitud['adjuntos'] as $a) {
                $href = man_asset_url() . $a->url . $a->nombre;
                $galeria .= '<div class="col-md-3 col-xs-6"><a href="' . man_esc($href) . '" target="_blank"><img src="' . man_esc($href)
                    . '" class="img-responsive" style="width: 100%; height: 100px;"></a></div>';
            }
            $out .= $caja('Solicitud de Servicios Nº ' . man_esc($solicitud['id_solicitud']),
                $campo('Solicitante:', $solicitud['solicitante']) . $campo('Fecha:', $solicitud['f_solicitado'])
                . $campo('Causa:', $solicitud['causa']) . $campo('Equipo:', $solicitud['equipo'])
                . $campo('Grupo:', $solicitud['descripcionGrupo']) . $campo('Sector:', $solicitud['descripcionSector'])
                . '<div class="col-md-12"><div class="panel-group" id="panelAdj" role="tablist" aria-multiselectable="true">'
                . '<div class="panel panel-default"><div class="panel-heading" role="tab"><h4 class="panel-title">'
                . '<a role="button" data-toggle="collapse" data-parent="#panelAdj" href="#collapseadj" aria-expanded="false" '
                . 'aria-controls="collapseadj">Adjuntos</a></h4></div><div id="collapseadj" class="panel-collapse collapse" role="tabpanel">'
                . '<div class="panel-body"><div class="row"><div class="col-md-12 col-xs-12 col-lg-12"><div id="adjuntosGallery">' . $galeria
                . '</div></div></div></div></div></div></div></div>');
        }
        if ($ot) {
            $descripcionTarea = $ot['tareaDescrip'] !== null && $ot['tareaDescrip'] !== '' ? $ot['tareaDescrip'] : $ot['otDescrip'];
            $out .= $caja('Orden de Trabajo N°: ' . man_esc($ot['id_orden']),
                $campo('Nº Orden Trabajo:', $ot['id_orden']) . $campo('Descripción:', $ot['otDescrip']) . $campo('Cliente:', $ot['cliente'])
                . $campo('Fecha Programación:', $ot['fecha']) . $campo('Duración:', $ot['duracion'])
                . $campo('Estado:', isset($estadoOt[$ot['estado']]) ? $estadoOt[$ot['estado']] : '')
                . '<div class="col-xs-12 col-sm-12"><label style="margin-top: 7px;">Tarea: </label>'
                . '<textarea id="info-tarea" class="form-control" rows="7" disabled>' . man_esc($descripcionTarea) . '</textarea></div>',
                'info-ot');
        }
        return $out;
    }
}

if (!function_exists('man_timeline')) {
    /** Línea de tiempo del caso: actividades pendientes y terminadas, de Bonita. @param array $tl listAct y listArch */
    function man_timeline($tl)
    {
        $usuario = function ($f) {
            return isset($f['assigned_id']) && is_array($f['assigned_id'])
                ? man_esc($f['assigned_id']['firstname'] . ' ' . $f['assigned_id']['lastname']) : 'Sin Asignar';
        };
        $item = function ($clase, $icono, $f, $fechas, $duracion) use ($usuario) {
            return '<li><div class="timeline-badge ' . $clase . '"><i class="glyphicon glyphicon-' . $icono . '"></i></div>'
                . '<div class="timeline-panel"><div class="timeline-heading"><h4 class="timeline-title">' . man_esc($f['displayName']) . '</h4>'
                . '<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> ' . $fechas . '</small></p></div>'
                . '<div class="timeline-body"><p>Usuario: ' . $usuario($f) . '</p><p>Descripción: ' . man_esc(isset($f['displayDescription']) ? $f['displayDescription'] : '')
                . '</p><p>Duración: ' . man_esc($duracion) . '</p><p>Case: ' . man_esc($f['caseId']) . '</p></div></div></li>';
        };
        $out = '<div class="panel panel-primary"><div class="panel-heading">Línea de Tiempo</div>'
            . '<div class="panel-body" style="max-height: 500px;overflow-y: scroll;"><div class="container"><ul class="timeline">'
            . '<h2 style="margin-left:50px;">Actividades Pendientes</h2>';
        foreach ($tl['listAct'] as $f) {
            $inicio = isset($f['reached_state_date']) ? $f['reached_state_date'] : '';
            $out .= $item('info', 'time', $f, 'Fecha Inicio: ' . man_fecha_tl($inicio) . ' hs', man_duracion($inicio, date('Y-m-d H:i:s')));
        }
        $out .= '<h2 style="margin-left:50px;">Actividades Terminadas</h2>';
        foreach ($tl['listArch'] as $f) {
            $inicio = isset($f['assigned_date']) ? $f['assigned_date'] : '';
            $fin = isset($f['archivedDate']) ? $f['archivedDate'] : '';
            $out .= $item('success', 'check', $f, 'Fecha Inicio: ' . man_fecha_tl($inicio) . ' hs | Fecha Fin: ' . man_fecha_tl($fin) . ' hs',
                man_duracion($inicio, $fin));
        }
        return $out . '<li><div class="timeline-badge success"><i class="glyphicon glyphicon-flag"></i></div><div class="timeline-panel">'
            . '<div class="timeline-heading"><h4 class="timeline-title">Inicio del Proceso</h4></div></div></li></ul></div></div></div>'
            . '<style type="text/css">'
            . '.timeline{list-style:none;padding:0 0 20px;position:relative;margin-top:-15px;margin-left:70px}'
            . '.timeline:before{top:30px;bottom:25px;position:absolute;content:" ";width:3px;background-color:#ccc;left:25px;margin-right:-1.5px}'
            . '.timeline>li,.timeline>li>.timeline-panel{margin-bottom:5px;position:relative}'
            . '.timeline>li:after,.timeline>li:before{content:" ";display:table}.timeline>li:after{clear:both}'
            . '.timeline>li>.timeline-panel{margin-left:55px;float:left;top:19px;padding:4px 10px 8px 15px;border:1px solid #ccc;border-radius:5px;width:45%}'
            . '.timeline>li>.timeline-badge{color:#fff;width:36px;height:36px;line-height:36px;font-size:1.2em;text-align:center;position:absolute;'
            . 'top:26px;left:9px;margin-right:-25px;background-color:#fff;z-index:100;border-radius:50%;border:1px solid #d4d4d4}'
            . '.timeline-badge.success{background-color:#3f903f!important}.timeline-badge.info{background-color:#5bc0de!important}'
            . '.timeline-title{margin-top:0;color:inherit}.timeline-body>p{margin-bottom:0;margin-top:0}.timeline-body>p+p{margin-top:5px}'
            . '.timeline-badge>.glyphicon{margin-right:0;color:#fff}'
            . '</style>';
    }
}
