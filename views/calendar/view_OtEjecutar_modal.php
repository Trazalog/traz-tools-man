<?php
/**
 * Ventana de la orden de trabajo (Plan de Mantenimiento, listado de OT y solicitudes) — MIG-ASSET-4.
 * Detalle de la orden y, si su caso tiene pendiente "Asignar Recursos y Tareas", asignar responsable y tarea.
 * Sin las pestañas "Herramientas" (vales de pañol) y "Pedido Materiales" (almacén): no se migran (E34).
 * Datos: $idOt, $detaOT, $tareas, $componente, $infoSolicOrigen, $task, $btnVisibilidad, $reasignaOt.
 */
$this->load->helper('man_tarea');
$o = $detaOT[0];
$conComponente = isset($componente['descCompo']) && $componente['descCompo'] != null;
?>
<section class="content">
 <div class="row">
  <div class="col-xs-12">
    <div class="box">
      <input type="hidden" id="idOt" value="<?php echo (int) $idOt; ?>">
      <input type="hidden" id="task" value="<?php echo (int) $task; ?>">
      <input type="hidden" id="id_solicitud" value="<?php echo man_esc($o['id_solicitud']); ?>">
      <input type="hidden" id="tipo" value="<?php echo man_esc($o['tipo']); ?>">
      <div class="box-header with-border"></div>
      <div class="box-body">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-cogs"></span> Orden de Trabajo </h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#detalle" aria-controls="profile" role="tab" data-toggle="tab">Detalle</a></li>
                    <?php
                    if ($btnVisibilidad) {
                        echo '<li role="presentation"><a href="#responsable" aria-controls="responsable" role="tab" data-toggle="tab">Asignación Responsable</a></li>';
                        echo '<li role="presentation"><a href="#asignar-tareas" aria-controls="asignar-tareas" role="tab" data-toggle="tab">Agregar Tareas</a></li>';
                    }
                    if ($reasignaOt) {
                        echo '<li role="presentation"><a href="#responsable" aria-controls="messages" role="tab" data-toggle="tab">Asignación Responsable</a></li>';
                    }
                    ?>
                  </ul>
                  <div class="tab-content">
                    <!-- Detalle -->
                    <div role="tabpanel" class="tab-pane active" id="detalle">
                      <div class="row">
                        <table class="table" id="tblOrden">
                          <thead>
                            <th>F. Prog</th>
                            <th>Nº Orden</th>
                            <th>Tipo</th>
                            <th>Nº Tarea</th>
                            <th>Desc. Tarea</th>
                            <th>Duracion</th>
                            <th>Frec.</th>
                            <?php if ($conComponente) { ?><th>Desc. Comp.</th><?php } ?>
                            <th>Codigo de Equipo</th>
                            <th>Desc. Equipo</th>
                          </thead>
                          <tbody class="text-center">
                            <tr>
                            <?php
                            echo '<td class="izquierda">' . man_esc($o['fecha_program']) . '</td>';
                            echo '<td>' . man_esc($o['id_orden']) . '</td>';
                            echo '<td class="izquierda">' . man_esc($o['descrpcionSolicitud']) . '</td>';
                            echo '<td class="izquierda">' . man_esc($o['id_solicitud']) . '</td>';
                            echo '<td class="izquierda">' . (($o['descripcion'] != null && $o['descripcion'] !== '') ? man_esc($o['descripcion']) : ' - ') . '</td>';
                            if (isset($infoSolicOrigen[0]['duracionTarea'])) {
                                echo '<td class="izquierda">' . man_esc($infoSolicOrigen[0]['duracionTarea'] . ' ' . (isset($infoSolicOrigen[0]['unidaddescrip']) ? $infoSolicOrigen[0]['unidaddescrip'] : '')) . '</td>';
                            } else {
                                echo '<td class="izquierda"> - </td>';
                            }
                            if (isset($infoSolicOrigen[0]['frecuencia'])) {
                                echo '<td class="izquierda">' . man_esc($infoSolicOrigen[0]['frecuencia']) . '</td>';
                            } else {
                                echo '<td class="izquierda"> - </td>';
                            }
                            if ($conComponente) {
                                echo '<td class="izquierda">' . man_esc($componente['descCompo']) . '</td>';
                            }
                            echo '<td class="izquierda">' . man_esc($o['codigo']) . '</td>';
                            echo '<td class="izquierda">' . man_esc($o['descripcionEquipo']) . '</td>';
                            ?>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <!-- Responsable -->
                    <div role="tabpanel" class="tab-pane" id="responsable">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="alert alert-danger alert-dismissable" id="errorTable" style="display: none">
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            Por favor seleccione Responsable para esta Tarea...
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="operario">Apellido y Nombre <strong style="color: #dd4b39">*</strong> :</label>
                          <input type="text" class="form-control operario" id="operario" name="operario"
                              value="<?php echo ($o['usrLastName'] !== '') ? man_esc($o['usrName'] . ', ' . $o['usrLastName']) : ''; ?>" placeholder="Buscar...">
                          <input type="hidden" class="form-control operario" id="id_operario" name="id_operario" value="<?php echo man_esc($o['usrId']); ?>">
                        </div>
                      </div>
                    </div>
                    <!-- Tareas -->
                    <div role="tabpanel" class="tab-pane" id="asignar-tareas">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="alert alert-danger alert-dismissable" id="errorTarea" style="display: none">
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            Por favor seleccione una Tarea...
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                          <label for="tareaest">Tarea Estandar<strong style="color: #dd4b39">*</strong>:</label>
                          <select id="tareaest" name="tareaest" class="form-control">
                            <option value="0">"Seleccione..."</option>
                            <?php
                            foreach ($tareas as $tarea) {
                                $sel = ($tarea['id_tarea'] == $o['id_tarea']) ? ' selected="selected"' : '';
                                echo '<option value="' . man_esc($tarea['id_tarea']) . '"' . $sel . '>' . man_esc($tarea['descripcion']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                          <label for="tareaOpcional">Tarea No Estandar<strong style="color: #dd4b39">*</strong>:</label>
                          <input type="text" class="form-control" id="tareaOpcional" name="tareaOpcional" value="<?php echo man_esc($o['descripcion']); ?>"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <?php
        if ($btnVisibilidad) {
            echo '<button type="button" class="btn btn-success" id="ejecutar_ot" onclick="EjecutarOT()">Asignar OT</button>';
        }
        if ($reasignaOt) {
            echo '<button type="button" class="btn btn-success" id="reasignar_ot" onclick="reasignaOt()">Asignar OT</button>';
        }
        ?>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
 </div>
</section>
<script>
(function () {
    var MAN_URL = '<?php echo base_url(MAN); ?>';

    // Al cerrar la ventana del calendario se guarda el responsable elegido (como asset)
    $('#modalInforme').off('hidden.bs.modal.manot').on('hidden.bs.modal.manot', function () {
        var respons = $('#id_operario').val();
        if (respons != '' && respons !== undefined) {
            $.ajax({ type: 'POST', url: MAN_URL + 'Otrabajo/updateResponsable', dataType: 'json',
                     data: { id_usuario_a: respons, idOt: $('#idOt').val() } });
        }
    });

    // Operarios de la empresa para el autocompletar
    var operarios = null;
    $.ajax({ async: false, type: 'POST', global: false, dataType: 'json', url: MAN_URL + 'Ordenservicio/getOperario',
             success: function (data) { operarios = data; } });
    $('#operario').autocomplete({
        autoFocus: true, delay: 300, minLength: 1, source: operarios,
        select: function (event, ui) { event.preventDefault(); $(this).val(ui.item.label); $('#id_operario').val(ui.item.value); },
        change: function (event, ui) { if (!ui.item) { this.value = ''; } }
    });

    function activaTab(tab) { $('.nav-tabs a[href="#' + tab + '"]').tab('show'); }

    function fallo(jqXHR, textStatus, errorThrown, texto) {
        var msj = '';
        try { msj = JSON.parse(jqXHR.responseText).msj || errorThrown; } catch (e) { msj = errorThrown || textStatus; }
        alert(texto + msj);
    }

    // Termina: cierra las ventanas y vuelve a cargar lo que se esté viendo
    function terminar() {
        $('#modalInforme').modal('hide');
        $('#modalRespyTareas').modal('hide');
        linkTo();
    }

    // Asigna responsable y tarea. En computadoras pide la ubicación del navegador, como asset (en celulares no)
    window.EjecutarOT = function () {
        var responsable = $('#id_operario').val(), tareaOpcional = $('#tareaOpcional').val(), tareastd = $('#tareaest').val();
        if (responsable == '') { $('#errorTable').fadeIn('slow'); activaTab('responsable'); return; }
        $('#errorTable').fadeOut('slow');
        if (tareastd == 0 && tareaOpcional == '') { $('#errorTarea').fadeIn('slow'); activaTab('asignar-tareas'); return; }
        $('#errorTarea').fadeOut('slow');

        function enviar(lat, lon) {
            WaitingOpen();
            $.ajax({
                type: 'POST', url: MAN_URL + 'Otrabajo/EjecutarOT', dataType: 'json',
                data: { ot: $('#idOt').val(), responsable: responsable, tareastd: tareastd, tareaOpcional: tareaOpcional, latitud: lat, longitud: lon },
                success: function (data) {
                    if (data.status) { terminar(); } else { alert('Falla | No se pudo Ejecutar la Orden de Trabajo | ' + data.msj); }
                },
                error: function (jqXHR, textStatus, errorThrown) { fallo(jqXHR, textStatus, errorThrown, 'Error | No se pudo Ejecutar la Orden de Trabajo | '); },
                complete: function () { WaitingClose(); }
            });
        }
        if (window.mobileAndTabletcheck && window.mobileAndTabletcheck()) { enviar('', ''); return; }
        if (!navigator.geolocation) { alert('GPS | No se pudo Obtener Ubicación, Por favor Activar el GPS del Dispositivo.'); return; }
        navigator.geolocation.getCurrentPosition(
            function (p) { enviar(p.coords.latitude, p.coords.longitude); },
            function () { alert('GPS | No se pudo Obtener Ubicación, Por favor Activar el GPS del Dispositivo.'); },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    };

    // Cambia el responsable de "Ejecutar OT"
    window.reasignaOt = function () {
        var responsable = $('#id_operario').val();
        if (responsable == '') { $('#errorTable').fadeIn('slow'); activaTab('responsable'); return; }
        WaitingOpen();
        $.ajax({
            type: 'POST', url: MAN_URL + 'Otrabajo/reasignaOt', dataType: 'json', data: { ot: $('#idOt').val(), responsable: responsable },
            success: function (data) {
                if (data.status) {
                    $('#modalRespyTareas').modal('hide');
                    linkTo();
                    Swal.fire('¡Éxito!', 'Se actualizo el responsable correctamente.', 'success');
                } else {
                    alert('Falla | No se pudo Reasignar la Orden de Trabajo | ' + data.msj);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { fallo(jqXHR, textStatus, errorThrown, 'Error | No se pudo Reasignar la Orden de Trabajo | '); },
            complete: function () { WaitingClose(); }
        });
    };
})();
</script>
