<?php
/**
 * Rep Informe de Servicios — MIG-REP-2 (views/reportes/view_.php de asset).
 * Filtros opcionales (sector, equipo, fechas) y una tabla con solicitudes, preventivos y backlog con su informe de servicio.
 * Dato: $permission.
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Reporte Ordenes de Servicio</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="checkbox"><label><input type="checkbox" id="rep-selSector"><strong>Sector</strong></label></div>
                            <input type="text" class="form-control" placeholder="Buscar Sector..." id="rep-buscSector" disabled autocomplete="off">
                            <input type="hidden" id="rep-idSector">
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="checkbox"><label><input type="checkbox" id="rep-selEquipo"><strong>Equipo</strong></label></div>
                            <select class="form-control" id="rep-equipo" disabled><option value=""></option></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="checkbox"><label><input type="checkbox" id="rep-selFecha"> <strong>Fecha</strong></label></div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label for="rep-desde">Desde</label>
                            <input type="text" class="form-control" id="rep-desde" disabled autocomplete="off">
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label for="rep-hasta">Hasta</label>
                            <input type="text" class="form-control" id="rep-hasta" disabled autocomplete="off">
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary pull-right" id="rep-consultar">Consultar</button>
                </div>
                <div class="box-footer">
                    <div id="rep-tabla" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h3 class="panel-title">Consulta</h3></div>
                            <div class="panel-body">
                                <table id="rep-sales" class="table table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Orden</th><th>Id</th><th>Tarea</th><th>Fecha Inicio</th><th>Fecha Fin</th>
                                            <th>Cant. Personas</th><th>H.H Ejecutadas</th><th>Equipo</th><th>Sector</th><th>Asignado</th><th>Estado</th>
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
    var ESTADOS = {
        'C': ['Curso', '#0dcaf0'], 'IN': ['Inactivo', '#6c757d'], 'RE': ['Reparación', '#ffc107'], 'AN': ['Anulado', '#dc3545'],
        'T': ['Terminada', '#198754'], 'AS': ['Asignada', '#0d6efd'], 'P': ['Pedido', '#0d6efd'], 'S': ['Solicitado', '#0d6efd'],
        'CN': ['Conforme', '#198754'], 'CE': ['Cerrado', '#212529'], 'PL': ['Planificada', '#ffc107']
    };
    function esc(t) { return $('<div>').text(t == null ? '' : t).html(); }
    function etiqueta(estado) {
        var v = String(estado == null ? '' : estado).trim().toUpperCase();
        var e = ESTADOS[v] || [v, '#6c757d'];
        return '<span class="badge estado" style="background-color:' + e[1] + ' !important; color: white !important;">' + esc(e[0]) + '</span>';
    }

    // Cada filtro se habilita con su casilla y, al deshabilitarlo, se vacía
    function enlazar(casilla, campos, hidden) {
        $(casilla).on('change', function () {
            var on = this.checked;
            $(campos).prop('disabled', !on);
            if (!on) { $(campos).val(''); if (hidden) { $(hidden).val(''); } }
        });
    }
    enlazar('#rep-selSector', '#rep-buscSector', '#rep-idSector');
    enlazar('#rep-selEquipo', '#rep-equipo');
    enlazar('#rep-selFecha', '#rep-desde, #rep-hasta');
    $('#rep-desde, #rep-hasta').datepicker({ format: 'dd-mm-yyyy', autoclose: true });

    // Sectores para la búsqueda y equipos para el desplegable
    $.ajax({ type: 'POST', dataType: 'json', global: false, url: MAN_URL + 'Sservicio/getSector' }).done(function (sectores) {
        $('#rep-buscSector').autocomplete({
            source: sectores, delay: 100, minLength: 1,
            focus: function (event, ui) { event.preventDefault(); $(this).val(ui.item.label); },
            select: function (event, ui) { event.preventDefault(); $(this).val(ui.item.label); $('#rep-idSector').val(ui.item.value); }
        });
    });
    $.ajax({ type: 'POST', dataType: 'json', global: false, url: MAN_URL + 'Reporte/getEquipo' }).done(function (equipos) {
        var $s = $('#rep-equipo');
        $.each(equipos || [], function (i, e) { $s.append($('<option />', { value: e.id_equipo, text: e.descripcion, title: e.codigo })); });
    });

    function limpiar() {
        $('#rep-selSector, #rep-selEquipo, #rep-selFecha').prop('checked', false).trigger('change');
    }

    $('#rep-consultar').on('click', function () {
        var datos = { id_equipo: $('#rep-equipo').val(), id_sector: $('#rep-idSector').val(), desde: $('#rep-desde').val(), hasta: $('#rep-hasta').val() };
        WaitingOpen('Cargando Solicitud de Servicios...');
        if ($.fn.dataTable.isDataTable('#rep-sales')) { $('#rep-sales').DataTable().destroy(); }
        $.ajax({ type: 'POST', dataType: 'json', url: MAN_URL + 'Reporte/getReporte', data: datos })
            .done(function (result) {
                limpiar();
                $('#rep-tabla').show();
                var filas = [];
                if (result !== 0) {
                    $.each(result, function (i, r) {
                        var horas = '';
                        if (r.cantidad_personas !== '0') {
                            horas = (((new Date(r.fecha_terminada) - new Date(r.fecha_inicio)) / 3600000) * r.cantidad_personas).toFixed(2);
                        }
                        filas.push([esc(r.origen), esc(r.id), esc(r.tarea || ''), esc(r.fecha_inicio), esc(r.fecha_terminada), esc(r.cantidad_personas),
                            horas, esc(r.equipo), esc(r.sector), esc(r.asignado), etiqueta(r.estado)]);
                    });
                } else {
                    error('Atención', 'No hay datos para esa consulta');
                }
                var cols = { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] };
                $('#rep-sales').DataTable({
                    data: filas, responsive: true, dom: 'lBfrtip',
                    buttons: [
                        { extend: 'excel', exportOptions: cols, text: '<button class="btn btn-success ml-2 mb-2 mt-3">Exportar a Excel <i class="fa fa-file-excel-o"></i></button>' },
                        { extend: 'pdf', exportOptions: cols, text: '<button class="btn btn-danger ml-2 mb-2 mt-3">Exportar a PDF <i class="fa fa-file-pdf-o"></i></button>' },
                        { extend: 'copy', exportOptions: cols, text: '<button class="btn btn-primary ml-2 mb-2 mt-3">Copiar <i class="fa fa-file-text-o"></i></button>' },
                        { extend: 'print', exportOptions: cols, text: '<button class="btn btn-default ml-2 mb-2 mt-3">Imprimir <i class="fa fa-print"></i></button>' }
                    ],
                    language: { url: '<?php echo base_url(); ?>lib/bower_components/datatables.net/js/es-ar.json' },
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]], paging: true, processing: true, order: [[2, 'asc']]
                });
            })
            .fail(function () { limpiar(); error('Atención', 'No se pudo traer el reporte'); })
            .always(function () { WaitingClose(); });
    });
})();
</script>
