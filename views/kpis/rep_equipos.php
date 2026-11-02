<?php
/**
 * Reporte de Disponibilidad de Equipos — MIG-REP-1 (Rep Equipo de asset, views/test.php).
 * Datos: $list (equipos), $permission.
 */
$etiquetas = array(
    'AC' => '<small class="label pull-left bg-green">Activo</small>',
    'IN' => '<small class="label pull-left bg-yellow">Inhabilitado</small>',
    'RE' => '<small class="label pull-left bg-orange">Reparación</small>',
    'AL' => '<small class="label pull-left bg-blue">Alta</small>',
);
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-title">Reporte de Disponibilidad de Equipos</div>
        </div>
        <div class="box-body">
            <div class="panel panel-default">
                <div class="panel-heading">Rango de Fechas</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Fecha Inicio:</label>
                                <input id="rep-fi" class="form-control" type="text" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Fecha Fin:</label>
                                <input id="rep-ff" class="form-control" type="text" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top: 23px">
                            <button class="btn btn-primary" id="rep-calcular">Calcular</button>
                        </div>
                    </div>
                    <hr>
                    <table id="tbl-equipos" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Descripción</th>
                                <th>Área</th>
                                <th>Proceso</th>
                                <th>Sector</th>
                                <th>Criticidad</th>
                                <th>Estado</th>
                                <th>% Disponibilidad</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $a): ?>
                            <tr id="<?php echo (int) $a['id_equipo']; ?>">
                                <td><?php echo htmlspecialchars($a['codigo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($a['deeq'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($a['dear'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($a['depro'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($a['desec'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($a['decri'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo isset($etiquetas[$a['estadoEquipo']]) ? $etiquetas[$a['estadoEquipo']] : ''; ?></td>
                                <td id="dsp-<?php echo (int) $a['id_equipo']; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
(function () {
    var url = '<?php echo base_url(MAN); ?>Kpi/dspRangoFecha';
    $('#rep-fi, #rep-ff').datepicker({ format: 'dd-mm-yyyy', autoclose: true });

    var tabla = $('#tbl-equipos');
    if ($.fn.dataTable.isDataTable(tabla)) { tabla.DataTable().destroy(); }
    tabla = tabla.DataTable({ dom: 'Bfrtip', buttons: ['excel'] });
    $('.buttons-excel').removeClass('btn-default').addClass('btn-primary').html('Exportar Excel');

    $('#rep-calcular').on('click', function () {
        var fi = $('#rep-fi').val(), ff = $('#rep-ff').val();
        if (fi === '' || ff === '') { return; }
        WaitingOpen();
        $.ajax({ type: 'GET', dataType: 'json', url: url, data: { fi: fi, ff: ff } })
            .done(function (rsp) {
                // Se actualiza la celda por el modelo de la tabla, así también sale en el Excel y en las filas de otras páginas
                var filas = {};
                tabla.rows().every(function () { filas[this.node().id] = this; });
                $.each(rsp || [], function (i, e) {
                    var fila = filas[e.id_equipo];
                    if (fila) { tabla.cell(fila.node(), 7).data(e.dsp === false ? 'Sin historial' : e.dsp + ' %'); }
                });
                tabla.draw(false);
            })
            .fail(function () { error('Atención', 'No se pudo calcular la disponibilidad'); })
            .always(function () { WaitingClose(); });
    });
})();
</script>
