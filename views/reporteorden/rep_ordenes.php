<?php
/**
 * Rep Ordenes de trabajo — MIG-REP-3 (views/reporteorden/view_.php de asset).
 * Filtros opcionales (equipo, estado, fechas) y una tabla con las órdenes de trabajo, exportable a Excel.
 * Dato: $permission.
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Reporte Ordenes de Trabajo</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="checkbox"><label><input type="checkbox" id="rot-selEquipo"><strong>Equipo</strong></label></div>
                            <select class="form-control" id="rot-equipo" disabled><option value="">Selecciona Equipo...</option></select>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="checkbox"><label><input type="checkbox" id="rot-selEstado"><strong>Estado</strong></label></div>
                            <select class="form-control" id="rot-estado" disabled><option value="">Selecciona Estado...</option></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="checkbox"><label><input type="checkbox" id="rot-selFecha"> <strong>Fecha</strong></label></div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label for="rot-desde">Desde</label>
                            <input type="text" class="form-control" id="rot-desde" placeholder="Selecciona Fecha" disabled autocomplete="off">
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label for="rot-hasta">Hasta</label>
                            <input type="text" class="form-control" id="rot-hasta" placeholder="Selecciona Fecha" disabled autocomplete="off">
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary pull-right" id="rot-consultar">Consultar</button>
                </div>
                <div class="box-footer">
                    <div id="rot-panel" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h3 class="panel-title">Consulta</h3></div>
                            <div class="panel-body">
                                <table id="rot-tabla" class="table table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Orden de Trabajo</th><th>Descripcion S.S.</th><th>Tarea Estándar</th><th>Equipo</th><th>Fecha</th>
                                            <th>Fecha de Programacion</th><th>Fecha Terminacion</th><th>Fecha de Entregada</th><th>Origen</th><th>Mantenedor</th><th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
(function () {
    var MAN_URL = '<?php echo base_url(MAN); ?>';
    function esc(t) { return $('<div>').text(t == null ? '' : t).html(); }

    // Cada filtro se habilita con su casilla y, al deshabilitarlo, se vacía
    function enlazar(casilla, campos) {
        $(casilla).on('change', function () {
            var on = this.checked;
            $(campos).prop('disabled', !on);
            if (!on) { $(campos).val(''); }
        });
    }
    enlazar('#rot-selEquipo', '#rot-equipo');
    enlazar('#rot-selEstado', '#rot-estado');
    enlazar('#rot-selFecha', '#rot-desde, #rot-hasta');
    $('#rot-desde, #rot-hasta').datepicker({ format: 'dd-mm-yyyy', autoclose: true });

    $.ajax({ type: 'POST', dataType: 'json', global: false, url: MAN_URL + 'Reporteorden/getEquipo' }).done(function (equipos) {
        var $s = $('#rot-equipo');
        $.each(equipos || [], function (i, e) { $s.append($('<option />', { value: e.id_equipo, text: e.codigo + ' - ' + e.descripcion })); });
    });
    // Como en asset, el estado Anulado no se ofrece
    $.ajax({ type: 'POST', dataType: 'json', global: false, url: MAN_URL + 'Reporteorden/getestado' }).done(function (estados) {
        var $s = $('#rot-estado');
        $.each(estados || [], function (i, e) { if (e.estado !== 'AN') { $s.append($('<option />', { value: e.estado, text: e.descripcion })); } });
    });

    var fecha = new Date();
    var nombre = fecha.getFullYear() + '-' + ('0' + (fecha.getMonth() + 1)).slice(-2) + '-' + ('0' + fecha.getDate()).slice(-2) + ' reporte OT';
    var tabla = $('#rot-tabla').DataTable({
        dom: 'Bfrtip', order: [[0, 'asc']],
        buttons: [
            { extend: 'print', className: 'btn btn-primary', text: '<i class="fa fa-print"></i>', title: 'Reporte de Orden de Trabajo' },
            { extend: 'excel', className: 'btn btn-primary', text: '<i class="fa fa-download"></i> Excel', title: 'Reporte de Orden de Trabajo', filename: nombre }
        ],
        columnDefs: [{ targets: [0], type: 'num' }],
        language: { url: '<?php echo base_url(); ?>lib/bower_components/datatables.net/js/es-ar.json' }
    });

    $('#rot-consultar').on('click', function () {
        var usaEquipo = $('#rot-selEquipo').prop('checked'), usaEstado = $('#rot-selEstado').prop('checked');
        $('#rot-panel').show();
        tabla.clear().draw();
        if (usaEquipo && $('#rot-equipo').val() === '') { error('Atención', 'Seleccione un equipo.'); return; }
        if (usaEstado && $('#rot-estado').val() === '') { error('Atención', 'Seleccione un estado.'); return; }
        var datos = { id_equipo: $('#rot-equipo').val(), estado: $('#rot-estado').val(), desde: $('#rot-desde').val(), hasta: $('#rot-hasta').val() };
        WaitingOpen('Cargando Reporte de OTs...');
        $.ajax({ type: 'POST', dataType: 'json', url: MAN_URL + 'Reporteorden/getDatosReporte', data: datos })
            .done(function (filas) {
                $.each(filas || [], function (i, r) {
                    // Sin tarea estándar, asset repite la descripción de la orden
                    tabla.row.add([esc(r.id_orden), esc(r.descripcionOT), esc(r.desc == null || r.desc === '' ? r.descripcionOT : r.desc),
                        esc(r.codigoEquipo + ' - ' + r.descripcionEquipo), esc(r.fecha), esc(r.fecha_program), esc(r.fecha_terminada),
                        esc(r.fecha_entregada), esc(r.origenOT), esc(r.nombre_usuario), esc(r.estado)]);
                });
                tabla.draw();
                if (!filas || !filas.length) { error('Atención', 'No hay datos para esa consulta'); }
            })
            .fail(function () { error('Atención', 'No se pudo traer el reporte'); })
            .always(function () { WaitingClose(); });
    });
})();
</script>
