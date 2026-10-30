<?php
/**
 * Tarea "Ejecutar OT" de la Bandeja (MIG-ASSET-3). Portada de asset sin lo que no se migra:
 *  - el pedido de materiales (nota_pedido, de almacenes);
 *  - los formularios de las subtareas (tablas frm_* de asset, que no existen en assetv2) y su validación;
 *  - el modo sin conexión (service worker), que guardaba el estado en el navegador.
 * Variables: permission, device, TareaBPM, idTarBonita, id_OT, cabecera, lineaTiempo, comentarios, subtareas.
 */
?>
<input type="hidden" id="permission" value="<?php echo man_esc($permission); ?>">
<input type="text" class="hidden" id="task" value="<?php echo (int) $idTarBonita; ?>">
<input type="text" class="hidden" id="case" value="<?php echo man_esc($TareaBPM['caseId']); ?>">
<input type="text" class="hidden" id="id_OT" value="<?php echo (int) $id_OT; ?>">
<input type="text" class="hidden" id="estadoTarea" value="">
<style>
.img-responsive { display: block; max-width: 100%; height: auto; }
</style>
<section class="content">
    <?php echo $cabecera; ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div role="tabpanel" class="tab-pane">
                                <div class="form-group">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Tareas</a></li>
                                        <li role="presentation"><a href="#info2" aria-controls="info2" role="tab" data-toggle="tab">Info </a></li>
                                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Comentarios</a></li>
                                        <li <?php echo ($device == 'android' ? 'class="hidden"' : ''); ?> role="presentation">
                                            <a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Trazabildad</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="home">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <button class="btn btn-block btn-success" id="btontomar" style="width: 100px; margin-top: 10px; display: inline-block;" onclick="tomarTarea()">Tomar tarea</button>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <button class="btn btn-block btn-danger grupNoasignado" id="btonsoltr" style="width: 100px; margin-top: 10px; display: inline-block;" onclick="soltarTarea()">Soltar tarea</button>
                                                    </div>
                                                    <div class="col-md-4 col-md-offset-4 oculto" id="llave">
                                                        <button type="button" class="btn btn-success" id="iniciarTarea"><i class="fa fa-play"> </i> Iniciar Tarea</button>
                                                        <button type="button" class="btn btn-success disabled" id="tareaIniciada"><i class="fa fa-play"> </i> Tarea Iniciada</button>
                                                    </div>
                                                </div>
                                                <br>
                                                <input type="text" class="form-control hidden" id="asignado" value="<?php echo man_esc($TareaBPM['assigned_id']); ?>">
                                                <h4><b>Tarea: </b><?php echo man_esc($TareaBPM['displayName']); ?></h4>
                                                <?php if (!empty($subtareas)) { ?>
                                                    <div class="panel panel-default">
                                                        <table id="subtask" class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th width="2%">Estado</th>
                                                                    <th width="10%">Subtarea</th>
                                                                    <th width="10%">Duración</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($subtareas as $s) { ?>
                                                                    <tr>
                                                                        <td><input class="check" type="checkbox" name="estado" id="<?php echo (int) $s['id_listarea']; ?>" <?php echo $s['estado'] == 'T' ? 'checked' : ''; ?>></td>
                                                                        <td><?php echo man_esc($s['subtareadescrip']); ?></td>
                                                                        <td><?php echo man_esc($s['duracion_prog']); ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="info2">
                                            <div class="panel-body">
                                                <form>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">INFORMACION:</div>
                                                        <div class="form-group">
                                                            <div class="col-sm-6 col-md-6">
                                                                <label for="tarea">Tarea</label>
                                                                <input type="text" class="form-control" id="tarea" value="<?php echo man_esc($TareaBPM['displayName']); ?>" disabled>
                                                                <input type="text" class="hidden" id="idTarBonita" value="<?php echo (int) $idTarBonita; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-6 col-md-6">
                                                                <label for="fecha">Fecha de Creación</label>
                                                                <input type="text" class="form-control" id="fecha" value="<?php echo man_esc($TareaBPM['last_update_date']); ?>" disabled>
                                                            </div>
                                                        </div><br>
                                                        <div class="form-group">
                                                            <div class="col-sm-6 col-md-6">
                                                                <label for="ot">Orden de Trabajo:</label>
                                                                <input type="text" class="form-control" id="ot" value="<?php echo (int) $id_OT; ?>" disabled>
                                                            </div>
                                                        </div><br>
                                                        <div class="form-group">
                                                            <div class="col-sm-12 col-md-12">
                                                                <label for="detalle">Detalle</label>
                                                                <textarea class="form-control" id="detalle" rows="3" disabled><?php echo man_esc($TareaBPM['displayDescription']); ?></textarea>
                                                            </div>
                                                        </div><br><br><br><br><br>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="profile">
                                        <div class="panel-body"><?php echo $comentarios; ?></div>
                                    </div>
                                    <div role="tabpanel" <?php echo ($device == 'android' ? 'class="hidden"' : 'class="tab-pane"'); ?> id="messages">
                                        <div class="panel-body"><?php echo $lineaTiempo; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" style="float: left;" id="procedimiento" onclick="procedimiento()"><i class="fa fa-file-text"> </i> Procedimiento</button>
                            <button type="button" class="btn btn-success" id="hecho" onclick="validarSubtareas()">Terminar Tarea</button>
                            <button type="button" class="btn btn-primary" id="cerrar">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Procedimiento</h4>
            </div>
            <div class="modal-body" id="content_adjunto"></div>
        </div>
    </div>
</div>

<script>
(function () {
    var acciones = '<?php echo base_url(MAN); ?>Tarea/';
    var idOt = $('#id_OT').val();

    // ¿La OT ya empezó? Muestra "Iniciar Tarea" o "Tarea Iniciada"
    function validaInicio() {
        $('#iniciarTarea, #tareaIniciada').hide();
        $.ajax({ type: 'GET', url: acciones + 'confInicioTarea', data: { id_OT: idOt }, dataType: 'json' })
            .done(function (iniciada) {
                $(iniciada ? '#tareaIniciada' : '#iniciarTarea').show();
                $('#estadoTarea').val(iniciada ? 1 : 0);
            })
            .fail(function () { alert('Error en Validacion Inicio Tareas'); });
    }

    // Inicia la OT con la posición del dispositivo (si no hay posición, queda -00000 como en asset)
    $('#iniciarTarea').on('click', function () {
        WaitingOpen('Iniciando Tarea...');
        function iniciar(lat, lon) {
            $.ajax({ type: 'POST', url: acciones + 'inicioTarea', data: { id_OT: idOt, lat: lat, lon: lon }, dataType: 'json' })
                .done(function (ok) {
                    WaitingClose();
                    if (ok) { $('#iniciarTarea').hide(); $('#tareaIniciada').show(); $('#estadoTarea').val(1); }
                    else { alert('Error al iniciar a Tarea...'); }
                })
                .fail(function () { WaitingClose(); alert('NO iniciada'); });
        }
        if (!navigator.geolocation) { alert('GPS | No se pudo Obtener Ubicación'); iniciar('-00000', '-00000'); return; }
        navigator.geolocation.getCurrentPosition(
            function (p) { iniciar(p.coords.latitude, p.coords.longitude); },
            function () {
                alert('GPS | No se pudo Obtener Ubicación, Por favor Activar el GPS del Dispositivo o verifica que el sitio sea seguro (https)');
                iniciar('-00000', '-00000');
            }, { timeout: 15000 });
    });

    // Procedimiento del preventivo: imagen en pantalla, PDF en otra pestaña
    window.procedimiento = function () {
        $.ajax({ type: 'POST', url: acciones + 'procedimiento', data: { ot: $('#ot').val() }, dataType: 'json' })
            .done(function (data) {
                var archivo = (data && data[0] && data[0]['prev_adjunto']) ? data[0]['prev_adjunto'] : '';
                if (archivo === '') { alert('Esta solicitud no contiene archivo adjunto'); return; }
                var url = '<?php echo man_asset_url(); ?>assets/filespreventivos/' + encodeURIComponent(archivo);
                var ext = archivo.substr(archivo.lastIndexOf('.') + 1).toLowerCase();
                if (ext === 'jpg' || ext === 'png') {
                    $('#content_adjunto').empty().append($('<img class="img-responsive">').attr('src', url));
                    $('#myModal').modal('show');
                } else {
                    window.open(url, '_blank');
                }
            })
            .fail(function () { alert('No se pudo obtener el procedimiento'); });
    };

    // Todas las subtareas deben estar hechas y la OT iniciada para terminar
    window.validarEstSubTareas = function () {
        var todas = true;
        $('#subtask .check').each(function () { todas = todas && this.checked; });
        $('#hecho').attr('disabled', !todas);
        return todas;
    };
    window.validarSubtareas = function () {
        if (!validarEstSubTareas()) { alert('Por favor cierre las Tareas que faltan antes de Terminar'); return; }
        if ($('#estadoTarea').val() != 1) {
            alert('Para Terminar Tarea, esta debe estar inicializada con anterioridad desde el boton Iniciar Tarea');
            return;
        }
        ejecutarOT();
    };

    // Marca la subtarea como terminada (T) o en curso (C)
    $('.check').on('change', function () {
        var estado = this.checked ? 'T' : 'C';
        var caja = this;
        WaitingOpen();
        $.ajax({ type: 'POST', url: acciones + 'cambiarEstadoSubtask', data: { estado: estado, idListarea: $(this).attr('id'), idTarBonita: $('#idTarBonita').val() }, dataType: 'json' })
            .done(function (r) { if (!r || !r.status) { caja.checked = !caja.checked; alert('No se pudo cambiar el estado de la subtarea'); } validarEstSubTareas(); })
            .fail(function () { caja.checked = !caja.checked; alert('No se pudo cambiar el estado de la subtarea'); validarEstSubTareas(); })
            .always(function () { WaitingClose(); });
    });

    validaInicio();
    validarEstSubTareas();
})();
</script>
