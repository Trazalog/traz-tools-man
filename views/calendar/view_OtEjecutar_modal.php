<?php $this->load->view(MAN . '_autocompletar'); ?>
<?php
/**
 * Ventana de la orden de trabajo (Plan de Mantenimiento, listado de OT y solicitudes) — MIG-ASSET-4.
 * Detalle de la orden y, si su caso tiene pendiente "Asignar Recursos y Tareas", asignar responsable y tarea.
 * Pestañas "Herramientas" (lista de la OT: del maestro del pañol de tools o en texto libre, más las herramientas usadas de los vales) y
 * "Pedido Materiales" (pedido al almacén de tools: borrador prearmado con los insumos de la OT que se envía al asignar; si ya hay pedido,
 * se ve con sus entregas). E65, a partir de REQ-ASSET-ALM de asset y de lo que decidió el PM.
 * Datos: $idOt, $detaOT, $tareas, $componente, $infoSolicOrigen, $task, $btnVisibilidad, $reasignaOt, $pedido, $insumosOt, $herramientasOt.
 */
$this->load->helper('man_tarea');
$o = $detaOT[0];
$conComponente = isset($componente['descCompo']) && $componente['descCompo'] != null;
// El pedido se arma acá si la OT no tiene uno: al asignarla o, si ya está asignada o en curso, con "Enviar pedido"
$pedidoEditable = empty($pedido) && ($btnVisibilidad || in_array($o['estado'], array('AS', 'C'), true));
$herrEditable = (bool) $btnVisibilidad;
$borrador = array();
foreach ((array) $insumosOt as $i) {
    $borrador[] = array('arti_id' => (string) $i['artId'], 'cantidad' => (string) $i['cantidad'],
        'texto' => trim($i['artBarCode'] . ' - ' . $i['artDescription'], ' -'));
}
$listaHerr = array();
foreach ((array) $herramientasOt as $h) {
    $libre = (int) $h['herrId'] === 0;
    $listaHerr[] = array('herrId' => (string) (int) $h['herrId'], 'cantidad' => (string) $h['cantidad'],
        'codigo' => $libre ? '(texto libre)' : $h['herrcodigo'], 'descripcion' => $libre ? $h['descripcion'] : $h['herrdescrip'],
        'marca' => $libre ? '' : $h['herrmarca'], 'descripcion_libre' => $libre ? $h['descripcion'] : '');
}
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
                    <li role="presentation"><a href="#herrEjec" aria-controls="herrEjec" role="tab" data-toggle="tab">Herramientas</a></li>
                    <li role="presentation"><a href="#pedidomateriales" aria-controls="pedidomateriales" role="tab" data-toggle="tab">Pedido Materiales</a></li>
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
                    <!-- Herramientas: lista de la OT y herramientas usadas (vales del pañol) -->
                    <div role="tabpanel" class="tab-pane" id="herrEjec">
                      <div class="panel panel-default">
                        <div class="panel-heading"><span class="fa fa-wrench"></span> Herramientas de la orden <small>(del maestro del pañol o texto libre)</small></div>
                        <div class="panel-body">
                          <?php if ($herrEditable) { ?>
                          <div class="row">
                            <div class="col-xs-12 col-sm-7"><label for="herrBusca">Herramienta del pañol</label><input type="text" class="form-control" id="herrBusca" placeholder="Buscar..." autocomplete="off"></div>
                            <div class="col-xs-6 col-sm-2"><label for="herrCant">Cantidad</label><input type="number" min="1" class="form-control" id="herrCant" value="1"></div>
                            <div class="col-xs-6 col-sm-3"><label>&nbsp;</label><button type="button" class="btn btn-primary btn-block" id="herrAgregar"><i class="fa fa-plus"></i> Agregar</button></div>
                          </div>
                          <div class="row" style="margin-top:8px">
                            <div class="col-xs-12 col-sm-7"><label for="herrLibre">Herramienta fuera del maestro (texto libre)</label><input type="text" class="form-control" id="herrLibre" maxlength="200"></div>
                            <div class="col-xs-6 col-sm-2"><label for="herrLibreCant">Cantidad</label><input type="number" min="1" class="form-control" id="herrLibreCant" value="1"></div>
                            <div class="col-xs-6 col-sm-3"><label>&nbsp;</label><button type="button" class="btn btn-default btn-block" id="herrLibreAgregar"><i class="fa fa-plus"></i> Agregar texto libre</button></div>
                          </div>
                          <?php } ?>
                          <table class="table table-condensed" id="tablaHerrOt" style="margin-top:10px">
                            <thead><tr><th>Código</th><th>Herramienta</th><th>Marca</th><th>Cantidad</th><?php echo $herrEditable ? '<th></th>' : ''; ?></tr></thead>
                            <tbody></tbody>
                          </table>
                          <p class="text-muted" id="sinHerrOt" style="display:none">La orden no tiene herramientas.</p>
                        </div>
                      </div>
                      <div class="panel panel-default">
                        <div class="panel-heading"><span class="fa fa-ticket"></span> Herramientas usadas <small>(de vales de pañol · solo lectura)</small></div>
                        <div class="panel-body">
                          <table class="table table-condensed" id="tablaHerrUsadasEjec">
                            <thead><tr><th>Usó</th><th>Código</th><th>Herramienta</th><th>Marca</th><th>Vale</th><th>Origen</th><th>Uso</th><th>Devolución</th></tr></thead>
                            <tbody></tbody>
                          </table>
                          <p class="text-muted" id="sinHerrUsadasEjec" style="display:none">Aún no se asociaron herramientas usadas a esta OT.</p>
                        </div>
                      </div>
                    </div>
                    <!-- Pedido Materiales: pedido al almacén de tools -->
                    <div role="tabpanel" class="tab-pane" id="pedidomateriales">
                      <?php if (!empty($pedido)) { ?>
                      <div class="panel panel-default">
                        <div class="panel-heading"><span class="fa fa-shopping-cart"></span> Pedido N° <?php echo (int) $pedido['pema_id']; ?>
                          <span class="label label-info"><?php echo man_esc($pedido['estado']); ?></span> <small><?php echo man_esc($pedido['fecha']); ?></small></div>
                        <div class="panel-body">
                          <table class="table table-condensed">
                            <thead><tr><th>Código</th><th>Artículo</th><th>Pedido</th><th>Pendiente</th></tr></thead>
                            <tbody>
                            <?php foreach ((array) $pedido['detalle'] as $d) {
                                echo '<tr><td>' . man_esc($d['barcode']) . '</td><td>' . man_esc($d['descripcion']) . '</td><td>' . man_esc($d['cantidad'])
                                    . '</td><td>' . man_esc(isset($d['resto']) ? $d['resto'] : '') . '</td></tr>';
                            } ?>
                            </tbody>
                          </table>
                          <p class="text-muted" style="margin:0">El pedido ya se envió al almacén: no se le agregan artículos.</p>
                        </div>
                      </div>
                      <?php } elseif ($pedidoEditable) { ?>
                      <div class="panel panel-default">
                        <div class="panel-heading"><span class="fa fa-shopping-cart"></span> Pedido al almacén <small>(prearmado con los insumos de la orden; se envía <?php echo $btnVisibilidad ? 'al asignar la OT' : 'con el botón Enviar pedido'; ?>)</small></div>
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-xs-12 col-sm-7"><label for="artBusca">Artículo</label><input type="text" class="form-control" id="artBusca" placeholder="Buscar..." autocomplete="off"></div>
                            <div class="col-xs-6 col-sm-2"><label for="artCant">Cantidad</label><input type="number" min="0" step="any" class="form-control" id="artCant" value="1"></div>
                            <div class="col-xs-6 col-sm-3"><label>&nbsp;</label><button type="button" class="btn btn-primary btn-block" id="artAgregar"><i class="fa fa-plus"></i> Agregar</button></div>
                          </div>
                          <table class="table table-condensed" id="tablaPedido" style="margin-top:10px">
                            <thead><tr><th>Artículo</th><th style="width:140px">Cantidad</th><th></th></tr></thead>
                            <tbody></tbody>
                          </table>
                          <p class="text-muted" id="sinPedido" style="display:none">El pedido no tiene artículos: si se deja así, no se pide nada al almacén.</p>
                          <?php if (!$btnVisibilidad) { ?><button type="button" class="btn btn-success" id="enviarPedido">Enviar pedido</button><?php } ?>
                        </div>
                      </div>
                      <?php } else { ?>
                      <p class="text-muted">La orden no tiene pedido de materiales.</p>
                      <?php } ?>
                      <div class="panel panel-default" style="margin-top:12px">
                        <div class="panel-heading"><span class="fa fa-truck"></span> Entregas ya realizadas <small>(del almacén · solo lectura)</small></div>
                        <div class="panel-body">
                          <table class="table table-condensed" id="tablaEntregasEjec">
                            <thead><tr><th>N°</th><th>Fecha</th><th>Comprobante</th><th>Solicitante</th><th>Destino</th></tr></thead>
                            <tbody></tbody>
                          </table>
                          <p class="text-muted" id="sinEntregasEjec" style="display:none">Aún no hay entregas para esta OT.</p>
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

    function esc(t) { return $('<div>').text(t == null ? '' : t).html(); }

    // Herramientas de la orden (E65)
    var herrEditable = <?php echo $herrEditable ? 'true' : 'false'; ?>;
    var herrLista = <?php echo json_encode($listaHerr); ?>;
    function pintarHerr() {
        var tb = $('#tablaHerrOt tbody');
        tb.empty();
        $.each(herrLista, function (i, h) {
            tb.append('<tr><td>' + esc(h.codigo) + '</td><td>' + esc(h.descripcion) + '</td><td>' + esc(h.marca) + '</td><td>' + esc(h.cantidad) + '</td>'
                + (herrEditable ? '<td><a href="#" class="herrQuitar" data-i="' + i + '" title="Quitar"><i class="fa fa-trash text-danger"></i></a></td>' : '') + '</tr>');
        });
        $('#sinHerrOt').toggle(herrLista.length === 0);
    }
    pintarHerr();
    if (herrEditable) {
        var herrElegida = null;
        $.ajax({ type: 'POST', global: false, dataType: 'json', url: MAN_URL + 'Preventivo/getherramienta' }).done(function (cat) {
            var fuente = $.map(cat || [], function (h) {
                return { label: h.herrcodigo + ' - ' + h.herrdescrip + (h.herrmarca ? ' (' + h.herrmarca + ')' : ''), value: h.herrId, h: h };
            });
            $('#herrBusca').autocomplete({ source: fuente, minLength: 1,
                focus: function (e, ui) { e.preventDefault(); $(this).val(ui.item.label); },
                select: function (e, ui) { e.preventDefault(); $(this).val(ui.item.label); herrElegida = ui.item.h; } });
        });
        $('#herrAgregar').on('click', function () {
            var c = parseInt($('#herrCant').val(), 10);
            if (!herrElegida || !(c > 0)) { alert('Elegí una herramienta del pañol y una cantidad'); return; }
            if ($.grep(herrLista, function (x) { return x.herrId === String(herrElegida.herrId); }).length) { alert('La herramienta ya está en la lista'); return; }
            herrLista.push({ herrId: String(herrElegida.herrId), cantidad: String(c), codigo: herrElegida.herrcodigo, descripcion: herrElegida.herrdescrip, marca: herrElegida.herrmarca, descripcion_libre: '' });
            herrElegida = null; $('#herrBusca').val(''); $('#herrCant').val(1); pintarHerr();
        });
        $('#herrLibreAgregar').on('click', function () {
            var t = $.trim($('#herrLibre').val()), c = parseInt($('#herrLibreCant').val(), 10);
            if (t === '' || !(c > 0)) { alert('Escribí la herramienta y una cantidad'); return; }
            herrLista.push({ herrId: '0', cantidad: String(c), codigo: '(texto libre)', descripcion: t, marca: '', descripcion_libre: t });
            $('#herrLibre').val(''); $('#herrLibreCant').val(1); pintarHerr();
        });
        $('#tablaHerrOt').on('click', '.herrQuitar', function (e) { e.preventDefault(); herrLista.splice($(this).data('i'), 1); pintarHerr(); });
    }

    // Pedido de materiales (E65): borrador prearmado con los insumos de la OT; se envía al asignar o con "Enviar pedido"
    var pedidoEditable = <?php echo $pedidoEditable ? 'true' : 'false'; ?>;
    var pedLista = <?php echo json_encode($pedidoEditable ? $borrador : array()); ?>;
    function pintarPedido() {
        var tb = $('#tablaPedido tbody');
        tb.empty();
        $.each(pedLista, function (i, a) {
            tb.append('<tr><td>' + esc(a.texto) + '</td><td><input type="number" min="0" step="any" class="form-control input-sm pedCant" data-i="' + i + '" value="' + esc(a.cantidad) + '"></td>'
                + '<td><a href="#" class="pedQuitar" data-i="' + i + '" title="Quitar"><i class="fa fa-trash text-danger"></i></a></td></tr>');
        });
        $('#sinPedido').toggle(pedLista.length === 0);
    }
    function itemsPedido() {
        return $.map(pedLista, function (a) { return parseFloat(a.cantidad) > 0 ? { arti_id: a.arti_id, cantidad: a.cantidad } : null; });
    }
    if (pedidoEditable) {
        pintarPedido();
        var artElegido = null;
        $.ajax({ type: 'POST', global: false, dataType: 'json', url: MAN_URL + 'Preventivo/getinsumo' }).done(function (cat) {
            // Se busca por código o por descripción
            var fuente = $.map(cat || [], function (x) { return { label: x.codigo + ' - ' + x.label, value: x.value }; });
            $('#artBusca').autocomplete({ source: fuente, minLength: 1,
                focus: function (e, ui) { e.preventDefault(); $(this).val(ui.item.label); },
                select: function (e, ui) { e.preventDefault(); $(this).val(ui.item.label); artElegido = ui.item; } });
        });
        $('#artAgregar').on('click', function () {
            var c = parseFloat($('#artCant').val());
            if (!artElegido || !(c > 0)) { alert('Elegí un artículo y una cantidad'); return; }
            var ya = $.grep(pedLista, function (x) { return x.arti_id === String(artElegido.value); });
            if (ya.length) { ya[0].cantidad = String(parseFloat(ya[0].cantidad) + c); } else {
                pedLista.push({ arti_id: String(artElegido.value), cantidad: String(c), texto: artElegido.label });
            }
            artElegido = null; $('#artBusca').val(''); $('#artCant').val(1); pintarPedido();
        });
        $('#tablaPedido').on('change', '.pedCant', function () { pedLista[$(this).data('i')].cantidad = $(this).val(); })
            .on('click', '.pedQuitar', function (e) { e.preventDefault(); pedLista.splice($(this).data('i'), 1); pintarPedido(); });
        $('#enviarPedido').on('click', function () {
            var items = itemsPedido();
            if (!items.length) { alert('El pedido no tiene artículos'); return; }
            WaitingOpen('Enviando pedido...');
            $.ajax({ type: 'POST', dataType: 'json', url: MAN_URL + 'Otrabajo/enviarPedido', data: { ot: $('#idOt').val(), pedido: JSON.stringify(items) },
                success: function (r) { if (r.status) { alert('Pedido N° ' + r.pema_id + ' enviado al almacén'); terminar(); } else { alert('No se pudo enviar el pedido | ' + r.msj); } },
                error: function (jqXHR, textStatus, errorThrown) { fallo(jqXHR, textStatus, errorThrown, 'No se pudo enviar el pedido | '); },
                complete: function () { WaitingClose(); } });
        });
    }

    // Entregas del almacén y herramientas usadas de la OT (lectura)
    $.ajax({ type: 'POST', global: false, dataType: 'json', url: MAN_URL + 'Ordenservicio/getEntregasPorOT', data: { id_ot: $('#idOt').val() },
        success: function (peds) {
            var tb = $('#tablaEntregasEjec tbody'), n = 0;
            tb.empty();
            $.each(peds || [], function (i, p) {
                $.each(p.entregas || [], function (j, e) {
                    n++;
                    tb.append('<tr><td>N° ' + esc(e.enma_id) + '</td><td>' + esc(e.fecha) + '</td><td>' + esc(e.comprobante) + '</td><td>' + esc(e.solicitante) + '</td><td>' + esc(e.destino) + '</td></tr>');
                });
            });
            $('#sinEntregasEjec').toggle(n === 0);
        } });
    $.ajax({ type: 'POST', global: false, dataType: 'json', url: MAN_URL + 'Ordenservicio/getHerramientasUsadas', data: { id_ot: $('#idOt').val() },
        success: function (d) {
            var tb = $('#tablaHerrUsadasEjec tbody');
            tb.empty();
            d = d || [];
            $.each(d, function (i, h) {
                var dev = h.devolucion ? '<span class="label label-success">' + esc(h.devolucion) + '</span>' : '<span class="label label-warning">sin devolver</span>';
                tb.append('<tr><td><input type="checkbox" ' + (h.activa ? 'checked' : '') + ' disabled></td><td>' + esc(h.codigo) + '</td><td>' + esc(h.descripcion) + '</td><td>'
                    + esc(h.marca) + '</td><td>N° ' + esc(h.sapa_id) + '</td><td><span class="label label-default">' + esc(String(h.origen).toLowerCase()) + '</span></td><td>'
                    + esc(h.dias) + (h.dias == 1 ? ' día' : ' días') + '</td><td>' + dev + '</td></tr>');
            });
            $('#sinHerrUsadasEjec').toggle(d.length === 0);
        } });

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
        var items = pedidoEditable ? itemsPedido() : [];
        // Como asset: avisa si la OT se asigna sin pedido de materiales
        if (pedidoEditable && !items.length && !confirm('No se Realizarán Pedido de Materiales, ¿Desea Continuar?')) { return; }

        function enviar(lat, lon) {
            WaitingOpen();
            $.ajax({
                type: 'POST', url: MAN_URL + 'Otrabajo/EjecutarOT', dataType: 'json',
                data: { ot: $('#idOt').val(), responsable: responsable, tareastd: tareastd, tareaOpcional: tareaOpcional, latitud: lat, longitud: lon,
                        herramientas: herrEditable ? JSON.stringify(herrLista) : '', pedido: items.length ? JSON.stringify(items) : '' },
                success: function (data) {
                    if (data.status) {
                        if (data.pedido && !data.pedido.status) {
                            alert('La orden se asignó, pero el pedido de materiales no se pudo enviar | ' + data.pedido.msj + ' | Podés enviarlo desde la pestaña Pedido Materiales.');
                        }
                        terminar();
                    } else { alert('Falla | No se pudo Ejecutar la Orden de Trabajo | ' + data.msj); }
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

