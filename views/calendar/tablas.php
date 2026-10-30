<?php
/**
 * Tablas de lo que falta programar en el mes (Plan de Mantenimiento).
 * Datos: $mes, $year, $permission, $list0 (preventivo por contador), $list1 (predictivo), $list2 (backlog), $list3 (preventivo por
 * tiempo), $list4 (solicitudes urgentes). Los datos de cada fila viajan en atributos data-*; la pantalla solo manda tipo y número.
 */
$this->load->helper('man_tarea');
$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$mostrarFecha = $meses[$mes - 1] . ' ' . $year;

/** Caja plegada de una tabla */
$cabecera = function ($titulo) use ($mostrarFecha) {
    echo '<div class="col-md-6"><div class="box collapsed-box"><div class="box-header">';
    echo '<h3 class="box-title">' . man_esc($titulo) . ' (' . man_esc($mostrarFecha) . ')</h3>';
    echo '<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="">';
    echo '<i class="fa fa-plus"></i></button></div></div><div class="box-body">';
};
$pie = function () {
    echo '</div></div></div>';
};
$celda = function ($valor) {
    echo "<td style='text-align: center'>" . man_esc($valor) . '</td>';
};
$iconoOT = 'style="color: #A4A4A4; cursor: pointer; margin-left: 15px;" title="Orden de Trabajo" data-toggle="modal"';

if (strpos($permission, 'Correctivo') !== false) {
    $cabecera('Solicitud de Servicios');
    ?>
    <table id="correctivo" class="table table-bordered table-hover">
        <thead><tr>
            <th style="text-align: center">OT</th><th style="text-align: center">ID Solic.</th><th style="text-align: center">Codigo</th>
            <th style="text-align: center">Causa</th><th style="text-align: center">F.Solicitado</th>
        </tr></thead>
        <tbody>
        <?php
        if ($list4) {
            foreach ($list4 as $c) {
                echo '<tr id="' . (int) $c['id_solicitud'] . '-' . (int) $c['id'] . '" data-idequipo="' . (int) $c['id_equipo'] . '"><td>';
                if ($c['estado'] != 'OT') {
                    echo '<i class="fa fa-stop-circle" style="color: #A4A4A4; cursor: pointer; margin-left: 15px;" title="Orden de Trabajo" onclick="planCorrectivo(' . (int) $c['id_solicitud'] . ',' . (int) $c['id'] . ')"></i>';
                } else {
                    echo '<i class="fa fa-stop-circle-o" style="color: #A4A4A4; margin-left: 15px;" title="Orden Generada"></i>';
                }
                echo '</td>';
                $celda($c['id_solicitud']);
                $celda($c['codigo']);
                $celda($c['causa']);
                $celda($c['f_solicitado']);
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    $pie();
}

if (strpos($permission, 'Preventivos') !== false) {
    $cabecera('Preventivo');
    ?>
    <table id="preventivo" class="table table-bordered table-hover">
        <thead><tr>
            <th style="text-align: center">OT</th><th style="text-align: center">Equipo</th><th style="text-align: center">Tarea</th>
            <th style="text-align: center">Fecha</th><th style="text-align: center">Periodo</th><th style="text-align: center">Frec.</th>
        </tr></thead>
        <tbody>
        <?php
        if ($list3) {
            foreach ($list3 as $p) {
                echo '<tr id="' . (int) $p['prevId'] . '"><td>';
                echo '<i class="fa fa-square" ' . $iconoOT . ' data-target="#modal-preventivo" onclick="planPreventivo(' . (int) $p['prevId'] . ')"></i></td>';
                $celda($p['codigo']);
                $celda($p['descripcion']);
                $celda($p['ultimo']);
                $celda($p['desc_periodo']);
                $celda($p['cantidad']);
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    $pie();

    $cabecera('Preventivo por ciclos/horas');
    ?>
    <table id="preventivo_horas" class="table table-bordered table-hover">
        <thead><tr>
            <th style="text-align: center">OT</th><th style="text-align: center">Equipo</th><th style="text-align: center">Tarea</th>
            <th style="text-align: center">Fecha</th><th style="text-align: center">Periodo</th><th style="text-align: center">Frec.</th>
        </tr></thead>
        <tbody>
        <?php
        if ($list0) {
            foreach ($list0 as $p) {
                echo '<tr data-prevId="' . (int) $p['prevId'] . '"><td>';
                if ($p['alerta'] == 'R') {
                    echo '<i class="fa fa-exclamation-triangle" style="color: #FF0000; margin-left: 15px; font-size: 1.1em;" title="Alerta: se supero el nivel critico"></i>';
                } else {
                    echo '<i class="fa fa-info-circle" style="color: #FFD700; margin-left: 15px; font-size: 1.1em;" title="Alerta: se esta por llegar al nivel critico"></i>';
                }
                echo '<i class="fa fa-history" style="color: #A4A4A4; cursor: pointer; margin-left: 15px; font-size: 1.1em;" title="Orden de Trabajo" '
                    . 'data-toggle="modal" data-target="#modal-preventivoH" onclick="planHoras(' . (int) $p['prevId'] . ')"></i></td>';
                $celda($p['codigo']);
                $celda($p['descripcion']);
                $celda($p['prox']);
                $d = $p['desc_periodo'];
                $celda($d == 'Horas' ? 'Horas' : ($d == 'Ciclos' ? 'Ciclos' : 'Km'));
                $celda($p['cantidad']);
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    $pie();
}

if (strpos($permission, 'Backlog') !== false) {
    $cabecera('Backlog');
    ?>
    <table id="backlog" class="table table-bordered table-hover">
        <thead><tr>
            <th style="text-align: center">OT</th><th style="text-align: center">ID Solic.</th><th style="text-align: center">Codigo</th>
            <th style="text-align: center">Causa</th><th style="text-align: center">Fecha</th>
        </tr></thead>
        <tbody>
        <?php
        if ($list2) {
            foreach ($list2 as $b) {
                echo '<tr><td>';
                if ($b['estado'] != 'OT') {
                    echo '<i class="fa fa-check-square" ' . $iconoOT . ' data-target="#modal-backlog" onclick="planBacklog(' . (int) $b['backId'] . ')"></i>';
                } else {
                    echo '<i class="fa fa-check-square-o" style="color: #A4A4A4; margin-left: 15px;" title="Orden Generada"></i>';
                }
                echo '</td>';
                $celda($b['sore_id']);
                $celda($b['codigo']);
                $celda(($b['tarea'] != null) ? $b['tarea'] : $b['tarea_opcional']);
                $celda($b['fecha']);
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    $pie();
}

if (strpos($permission, 'Predictivo') !== false) {
    $cabecera('Predictivo');
    ?>
    <table id="predictivo" class="table table-bordered table-hover">
        <thead><tr>
            <th style="text-align: center">OT</th><th style="text-align: center">Código</th><th style="text-align: center">Descrip</th>
            <th style="text-align: center">Fecha</th><th style="text-align: center">Período</th><th style="text-align: center">Frec.</th>
        </tr></thead>
        <tbody>
        <?php
        if ($list1) {
            foreach ($list1 as $p) {
                echo '<tr><td>';
                echo '<i class="fa fa-circle-thin predictivo" ' . $iconoOT . ' data-target="#modal-fecha" onclick="planPredictivo(' . (int) $p['predId'] . ')"></i></td>';
                $celda($p['codigo']);
                $celda($p['descripcion']);
                $celda($p['fecha']);
                $celda($p['desc_periodo']);
                $celda($p['cantidad']);
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    $pie();
}
