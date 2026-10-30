<?php
/**
 * Plan de Mantenimiento (Calendario) — MIG-ASSET-4. Se ve como el de asset; usa el FullCalendar 5 del layout de tools (sin jQuery 2).
 * Dato: $permission (acciones; las cuatro tablas laterales se agregan siempre, como en asset).
 */
?>
<input type="hidden" id="permission" value="<?php echo htmlspecialchars($permission, ENT_QUOTES, 'UTF-8'); ?>">

<section class="content">
<style>
    /* Orden sin responsable: borde azul */
    .fc-event.tipo-3-border { border: 2px solid blue; }
    .man-cal-modal .col-xs-4 { margin-bottom: 10px; }
    @media print {
        body.imprimiendo-calendario * { visibility: hidden; }
        body.imprimiendo-calendario #calendar, body.imprimiendo-calendario #calendar * { visibility: visible; }
        body.imprimiendo-calendario #calendar { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

<!-- CALENDARIO -->
<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-body">
            <div class="fa fa-fw fa-print text-light-blue" id="btnImprimir" style="cursor: pointer; margin-left:10px;" title="Imprimir"></div>
            <div class="fa fa-fw fa-file-excel-o text-light-blue" style="cursor: pointer; margin-left:10px;" title="Exportar a Excel" id="btnExportExcel"></div>
            <button class="btn btn-link" title="Filtrar OT" onclick="$('#caja-filtro').toggle();">
                <i class="fa fa-fw fa-filter text-light-blue"></i>
            </button>
            <!-- Filtro (en asset vive en un panel derecho propio de su layout) -->
            <div id="caja-filtro" class="box box-default" style="display:none; margin-top:10px;">
                <div class="box-body" id="filtro-body"></div>
            </div>
            <div class="d-flex align-items-center" style="margin-top: 15px;">
                <strong style="margin-left: 10px;">Referencias de colores:</strong>
            </div>
            <div style="margin-top: 5px;">
                <ul style="list-style-type: none; padding-left: 0; display: flex; gap: 10px; flex-wrap: wrap;">
                    <li><span style="background-color: #f56954; color: white; padding: 2px 5px; border-radius: 3px;">Urgente</span></li>
                    <li><span style="background-color: #ff851b; color: white; padding: 2px 5px; border-radius: 3px;">Backlog</span></li>
                    <li><span style="background-color: #39CCCC; color: white; padding: 2px 5px; border-radius: 3px;">Preventivo</span></li>
                    <li><span style="background-color: #00a65a; color: white; padding: 2px 5px; border-radius: 3px;">Predictivo</span></li>
                    <li><span style="background-color: #3c8dbc; color: white; padding: 2px 5px; border-radius: 3px;">Orden Trabajo</span></li>
                    <li><span style="background-color: #D81B60; color: white; padding: 2px 5px; border-radius: 3px;">Correctivo programado</span></li>
                </ul>
            </div>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<div id="tablas"></div>
</section>

<script>
(function () {
    var MAN_URL = '<?php echo base_url(MAN); ?>';
    var PERMISO = $('#permission').val();
    var COLORES = {'1': '#3c8dbc', '2': '#f56954', '3': '#39CCCC', '4': '#ff851b', '5': '#00a65a', '6': '#D81B60'};
    var calendar, cache = null, mesTablas = '';

    function dos(n) { return (n < 10 ? '0' : '') + n; }
    function fechaLocal(d) { return d.getFullYear() + '-' + dos(d.getMonth() + 1) + '-' + dos(d.getDate()); }
    function fechaHoraLocal(d) { return fechaLocal(d) + ' ' + dos(d.getHours()) + ':' + dos(d.getMinutes()) + ':' + dos(d.getSeconds()); }

    /* ---------- filtro (en el navegador, sobre los eventos ya cargados) ---------- */
    function pasaFiltro(e) {
        var c = [['flt-equipos', e.codigo], ['flt-areas', e.area], ['flt-grupos', e.grupo], ['flt-sectores', e.sector], ['flt-origenes', e.origen]];
        for (var i = 0; i < c.length; i++) {
            var v = $('#' + c[i][0]).val();
            if (v && v !== 'all' && v !== c[i][1]) { return false; }
        }
        return true;
    }
    window.filtroSelect = function () { if (calendar) { calendar.refetchEvents(); } };

    function armarEventos(filas) {
        var out = [];
        $.each(filas || [], function (i, f) {
            var ini = new Date(String(f.fecha_program).replace(' ', 'T'));
            if (isNaN(ini.getTime())) { return; }
            var min = parseInt(f.duracion, 10);
            if (isNaN(min)) { min = 0; }
            var e = {
                id: f.id_orden, start: ini, end: new Date(ini.getTime() + min * 60000), allDay: false,
                title: f.id_orden + ' | ' + f.codigo + ' | ' + f.descripcion,
                backgroundColor: COLORES[f.tipo] || '', borderColor: COLORES[f.tipo] || '',
                classNames: (!f.id_usuario_a || f.id_usuario_a == 0) ? ['tipo-3-border'] : [],
                extendedProps: {id_orden: f.id_orden, equipo: f.codigo, area: f.area, grupo: f.grupo, sector: f.sector, origen: f.origen}
            };
            e.codigo = f.codigo; e.area = f.area; e.grupo = f.grupo; e.sector = f.sector; e.origen = f.origen;
            if (pasaFiltro(e)) { out.push(e); }
        });
        return out;
    }

    /* ---------- tablas de la derecha ---------- */
    function cargarTablas() {
        var d = calendar.getDate();
        var mes = d.getMonth() + 1, year = d.getFullYear();
        mesTablas = year + '-' + mes;
        WaitingOpen();
        $.ajax({
            url: MAN_URL + 'Calendario/getTablas', type: 'POST', dataType: 'html', cache: false,
            data: {mes: mes, year: year, permission: PERMISO},
            success: function (html) { $('#tablas').html(html); },
            error: function (xhr) { console.error(xhr.responseText); alert('Error al cargar las tablas'); },
            complete: function () { WaitingClose(); }
        });
    }

    /* ---------- calendario ---------- */
    function iniciar() {
        calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            locale: 'es', initialView: 'dayGridMonth', allDaySlot: false, editable: true, eventDisplay: 'block', displayEventTime: false,
            headerToolbar: {left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay'},
            buttonText: {today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día'},
            events: function (info, ok, fail) {
                var desde = info.startStr.substr(0, 10), hasta = info.endStr.substr(0, 10);
                if (cache && cache.desde === desde && cache.hasta === hasta) { ok(armarEventos(cache.filas)); return; }
                $.ajax({
                    url: MAN_URL + 'Calendario/getcalendarot', type: 'POST', dataType: 'json',
                    data: {desde: desde, hasta: hasta},
                    success: function (filas) { cache = {desde: desde, hasta: hasta, filas: filas}; ok(armarEventos(filas)); },
                    error: function (xhr) { console.error(xhr.responseText); fail(xhr); alert('Sin datos para este mes'); }
                });
            },
            datesSet: function () {
                var d = calendar.getDate();
                if (mesTablas !== d.getFullYear() + '-' + (d.getMonth() + 1)) { cargarTablas(); }
            },
            eventClick: function (info) { verDetalleOT(info.event.extendedProps.id_orden); },
            eventDrop: function (info) {
                if (!confirm('Realmente desea hacer este cambio?')) { info.revert(); return; }
                $.ajax({
                    url: MAN_URL + 'Calendario/updateDiaProg', type: 'POST', dataType: 'json',
                    data: {id: info.event.id, prog: fechaHoraLocal(info.event.start)},
                    success: function (r) {
                        if (r !== true) { info.revert(); alert('No pudo realizarse el cambio'); } else { cache = null; }
                    },
                    error: function () { info.revert(); alert('No pudo realizarse el cambio'); }
                });
            },
            eventResize: function (info) {
                if (!confirm('Realmente desea hacer este cambio?')) { info.revert(); return; }
                var minutos = Math.round((info.event.end.getTime() - info.oldEvent.end.getTime()) / 60000);
                $.ajax({
                    url: MAN_URL + 'Calendario/updateDuracion', type: 'POST', dataType: 'json',
                    data: {id: info.event.id, duracion: minutos},
                    success: function (r) {
                        if (r !== true) { info.revert(); alert('No pudo realizarse el cambio'); } else { cache = null; alert('Se ha guardado Correctamente'); }
                    },
                    error: function () { info.revert(); alert('No pudo realizarse el cambio'); }
                });
            }
        });
        calendar.render();
    }

    /* ---------- ventana de la orden ---------- */
    window.verDetalleOT = function (idOrden) {
        WaitingOpen();
        $('#modalInformeServicios').empty().load(MAN_URL + 'Calendario/verEjecutarOT/' + parseInt(idOrden, 10) + '/', function () { WaitingClose(); });
        $('#modalInforme').modal('show');
    };

    /* ---------- imprimir y Excel ---------- */
    $('#btnImprimir').on('click', function () {
        $('body').addClass('imprimiendo-calendario');
        window.print();
        $('body').removeClass('imprimiendo-calendario');
    });

    $('#btnExportExcel').on('click', function () {
        var eventos = calendar.getEvents();
        if (eventos.length === 0) { alert('No hay eventos para exportar'); return; }
        function dmy(d) { return dos(d.getDate()) + '/' + dos(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + dos(d.getHours()) + ':' + dos(d.getMinutes()); }
        function exportar() {
            var datos = [];
            eventos.forEach(function (ev) {
                datos.push({'Nº OT': ev.extendedProps.id_orden || '', 'Equipo': ev.extendedProps.equipo || '', 'Descripción': ev.title || '',
                    'Inicio': ev.start ? dmy(ev.start) : '', 'Fin': ev.end ? dmy(ev.end) : '', 'Área': ev.extendedProps.area || '',
                    'Grupo': ev.extendedProps.grupo || '', 'Sector': ev.extendedProps.sector || '', 'Origen': ev.extendedProps.origen || ''});
            });
            var ws = XLSX.utils.json_to_sheet(datos), wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Calendario');
            ws['!cols'] = Object.keys(datos[0]).map(function (k) {
                var m = k.length;
                datos.forEach(function (r) { var l = (r[k] || '').toString().length; if (l > m) { m = l; } });
                return {wch: m + 2};
            });
            XLSX.writeFile(wb, 'calendario_OT.xlsx');
        }
        if (typeof XLSX === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js';
            s.onload = exportar;
            document.head.appendChild(s);
        } else { exportar(); }
    });

    /* ---------- generar orden ---------- */
    var actual = null; // {tipo, id_origen, id_tarea_bonita, horas, modal, fecha, hora, evento, meses}

    function abrir(cfg) { actual = cfg; }
    window.planCorrectivo = function (idSol, idTarea) {
        abrir({tipo: 2, id_origen: idSol, id_tarea_bonita: idTarea, horas: 0, modal: '#modal-correctivo', fecha: '#fecha_progr_correct', hora: '#hora_progr_correct'});
        $('#modal-correctivo').modal('show');
    };
    window.planPreventivo = function (id) {
        abrir({tipo: 3, id_origen: id, horas: 0, modal: '#modal-preventivo', fecha: '#fecha_progr_prevent', hora: '#hora_prog_prevent',
               evento: '#event_Preventivo', meses: '#cant_meses_prev'});
    };
    window.planHoras = function (id) {
        abrir({tipo: 3, id_origen: id, horas: 1, modal: '#modal-preventivoH', fecha: '#fecha_progr_prevent_horas', hora: '#hora_progr_prevH'});
    };
    window.planBacklog = function (id) {
        abrir({tipo: 4, id_origen: id, horas: 0, modal: '#modal-backlog', fecha: '#fecha_progr_back', hora: '#hora_progr_back'});
    };
    window.planPredictivo = function (id) {
        abrir({tipo: 5, id_origen: id, horas: 0, modal: '#modal-fecha', fecha: '#fecha_progr_pred', hora: '#hora_progr_pred',
               evento: '#event_Predictivo', meses: '#cant_meses_predic'});
    };

    window.generarOrden = function () {
        if (!actual) { return; }
        var c = actual, fecha = $(c.fecha).val(), hora = $(c.hora).val() || '00:00';
        if (hora === '00:00' && !confirm('Esta Seguro de la Asignación? Hora: ' + hora + ' Para la Fecha: ' + fecha)) {
            alert('Escoga un nuevo horario o fecha');
            return;
        }
        WaitingOpen();
        $.ajax({
            url: MAN_URL + 'Calendario/programar', type: 'POST', dataType: 'json',
            data: {tipo: c.tipo, id_origen: c.id_origen, id_tarea_bonita: c.id_tarea_bonita || '', horas: c.horas,
                   fecha_progr: fecha, hora_progr: hora,
                   evento: c.evento ? $(c.evento).val() : 1, cant_meses: c.meses ? $(c.meses).val() : 0},
            success: function (r) {
                if (r && r.status) {
                    $(c.modal).modal('hide');
                    cache = null;
                    calendar.refetchEvents();
                    cargarTablas();
                    alert('Se ha guardado Correctamente');
                } else {
                    alert('No se pudo generar la orden: ' + ((r && r.msj) ? r.msj : 'error del servidor'));
                }
            },
            error: function (xhr) {
                var m = '';
                try { m = JSON.parse(xhr.responseText).msj; } catch (e) { m = xhr.statusText; }
                alert('No se pudo generar la orden: ' + m);
            },
            complete: function () { WaitingClose(); }
        });
    };
    window.cancelarOrden = function () { actual = null; };

    /* ---------- arranque ---------- */
    $(document).ready(function () {
        $('#filtro-body').load(MAN_URL + 'Calendario/panelFiltro', function () {
            $(document).on('click', '.flt-clear', function (e) {
                e.preventDefault();
                $(this).closest('.form-group').find('input').val('');
                window.filtroSelect();
            });
        });
        ['#fecha_progr_pred', '#fecha_progr_correct', '#fecha_progr_prevent', '#fecha_progr_back', '#fecha_progr_prevent_horas'].forEach(function (id) {
            $(id).datepicker({format: 'dd-mm-yyyy', autoclose: true, zIndexOffset: 1100}).datepicker('setDate', new Date());
        });
        // Con evento único no se usa la cantidad de meses
        $('#event_Preventivo').on('change', function () { $('#cant_meses_prev').prop('disabled', $(this).val() == 1); });
        $('#event_Predictivo').on('change', function () { $('#cant_meses_predic').prop('disabled', $(this).val() == 1); });
        iniciar();
    });
})();
</script>

<!--  MODAL VER OT Y ASIGNAR  -->
<div class="modal bs-example-modal-lg" id="modalInforme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row"><div class="col-sm-12"><div class="box"><div class="box-body"><div class="row">
                <div class="col-sm-12 col-md-12" id="modalInformeServicios"></div>
            </div></div></div></div></div>
        </div>
    </div>
</div>

<?php
/** Ventana "Creación de Orden de Trabajo": $id, $fecha, $hora, $extra (HTML de programación) */
$ventana = function ($id, $fecha, $hora, $extra) {
?>
<div class="modal fade man-cal-modal" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelarOrden()"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Creación de Orden de Trabajo</h4>
            </div>
            <div class="modal-body">
                <h5>Seleccione la fecha de Programación</h5>
                <div class="col-xs-4">Fecha:
                    <input type="text" id="<?php echo $fecha; ?>" class="form-control input-md" autocomplete="off" />
                </div>
                <div class="col-xs-4">Hora:
                    <input type="time" id="<?php echo $hora; ?>" class="form-control input-md">
                </div>
                <div class="clearfix"></div>
                <?php echo $extra; ?>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancelarOrden()">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="generarOrden()">Generar Orden</button>
            </div>
        </div>
    </div>
</div>
<?php
};
$programacion = function ($evento, $meses) {
    return '<br><h5>Seleccione programación</h5>'
        . '<div class="col-xs-4">Tipo de Evento <select class="form-control input-md" id="' . $evento . '">'
        . '<option value="1">Unico</option><option value="2">Mensual</option></select></div>'
        . '<div class="col-xs-4">Cantidad Meses <input type="text" id="' . $meses . '" class="form-control input-md" disabled="disabled"></div>';
};
$ventana('modal-correctivo', 'fecha_progr_correct', 'hora_progr_correct', '');
$ventana('modal-preventivo', 'fecha_progr_prevent', 'hora_prog_prevent', $programacion('event_Preventivo', 'cant_meses_prev'));
$ventana('modal-preventivoH', 'fecha_progr_prevent_horas', 'hora_progr_prevH', '');
$ventana('modal-backlog', 'fecha_progr_back', 'hora_progr_back', '');
$ventana('modal-fecha', 'fecha_progr_pred', 'hora_progr_pred', $programacion('event_Predictivo', 'cant_meses_predic'));
