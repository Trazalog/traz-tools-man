<?php
/**
 * Materiales y herramientas de la OT (E65): la misma información en todos los pasos del ciclo de la OT (Ejecutar OT, informe de servicio,
 * edición y verificación). Viene de REQ-ASSET-ALM de asset (F4/F5 y M1): herramientas de la OT, pedido al almacén de tools con
 * pedido/entregado/pendiente, entregas con su detalle, y herramientas usadas de los vales del pañol de tools.
 * Datos: $idOt; $rolVales '' (solo lectura), 'MANTENEDOR' (busca vales por DNI y quita los que asoció) o 'SUPERVISOR' (busca sin DNI y
 * quita también los del pañolero) — def. 2, 3 y 8 de asset; los permisos se vuelven a controlar en el servidor.
 * Una sola vez por pantalla (los ids empiezan con "mat").
 */
$rolVales = isset($rolVales) ? $rolVales : '';
?>
<div id="matOT" data-ot="<?php echo (int) $idOt; ?>">
  <div class="panel panel-default">
    <div class="panel-heading"><span class="fa fa-wrench"></span> Herramientas de la orden <small>(del maestro del pañol o texto libre)</small></div>
    <div class="panel-body">
      <table class="table table-condensed" id="matHerrOt"><thead><tr><th>Código</th><th>Herramienta</th><th>Marca</th><th>Cantidad</th></tr></thead><tbody></tbody></table>
      <p class="text-muted" id="matSinHerrOt" style="display:none">La orden no tiene herramientas.</p>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><span class="fa fa-shopping-cart"></span> Insumos <small>(pedido al almacén de tools)</small></div>
    <div class="panel-body">
      <table class="table table-condensed" id="matInsumos"><thead><tr><th>Pedido N°</th><th>Estado</th><th>Código</th><th>Artículo</th><th>Pedido</th><th>Entregado</th><th>Pendiente</th></tr></thead><tbody></tbody></table>
      <p class="text-muted" id="matSinInsumos" style="display:none">La orden no tiene pedido de materiales.</p>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><span class="fa fa-truck"></span> Entregas del pedido <small>(del almacén · solo lectura)</small></div>
    <div class="panel-body">
      <table class="table table-condensed" id="matEntregas"><thead><tr><th>N° Entrega</th><th>Fecha</th><th>Comprobante</th><th>Retiró</th><th>DNI</th><th>Ítems</th><th></th></tr></thead><tbody></tbody></table>
      <p class="text-muted" id="matSinEntregas" style="display:none">Todavía no hay entregas para esta orden.</p>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><span class="fa fa-ticket"></span> Herramientas usadas <small>(de vales del pañol)</small>
      <?php if ($rolVales !== '') { ?><button type="button" class="btn btn-xs btn-primary pull-right" id="matAbrirVales"><span class="fa fa-plus"></span> Asociar vale</button><?php } ?>
    </div>
    <div class="panel-body">
      <?php if ($rolVales !== '') { ?>
      <div id="matBuscarVales" class="well well-sm" style="display:none">
        <div class="row">
          <?php if ($rolVales === 'MANTENEDOR') { ?>
          <div class="col-sm-3"><label class="small" for="matValeDni">DNI del vale</label><input type="text" class="form-control input-sm" id="matValeDni" placeholder="DNI cargado en el vale"></div>
          <?php } ?>
          <div class="col-sm-3"><label class="small" for="matValeDesde">Desde</label><input type="date" class="form-control input-sm" id="matValeDesde"></div>
          <div class="col-sm-3"><label class="small" for="matValeHasta">Hasta</label><input type="date" class="form-control input-sm" id="matValeHasta"></div>
          <div class="col-sm-3"><label class="small">&nbsp;</label><br><button type="button" class="btn btn-sm btn-default" id="matBuscar"><span class="fa fa-search"></span> Buscar</button></div>
        </div>
        <?php if ($rolVales === 'MANTENEDOR') { ?><p class="small text-muted" style="margin-top:6px">Sin DNI no se pueden asociar herramientas. El DNI es el que se cargó en el vale.</p><?php } ?>
        <table class="table table-condensed" id="matVales" style="display:none"><thead><tr><th>Vale</th><th>Fecha</th><th>Responsable</th><th>Destino</th><th></th></tr></thead><tbody></tbody></table>
        <p class="text-muted small" id="matSinVales" style="display:none">No se encontraron vales para ese filtro.</p>
        <p class="text-danger small" id="matMsgVales" style="display:none"></p>
      </div>
      <?php } ?>
      <table class="table table-condensed" id="matHerrUsadas"><thead><tr><th>Usó</th><th>Código</th><th>Herramienta</th><th>Marca</th><th>Vale</th><th>Origen</th><th>Uso</th><th>Devolución</th><th></th></tr></thead><tbody></tbody></table>
      <p class="text-muted" id="matSinHerrUsadas" style="display:none">Aún no se asociaron herramientas usadas a esta orden.</p>
    </div>
  </div>
</div>
<div class="modal fade" id="matModalEntrega" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Detalle de la entrega <span id="matEntregaNro"></span></h4></div>
    <div class="modal-body"><table class="table table-condensed"><thead><tr><th>Código</th><th>Artículo</th><th>Cant.</th><th>Lote</th><th>Depósito</th></tr></thead><tbody id="matEntregaBody"></tbody></table></div>
  </div></div>
</div>
<script>
(function () {
    var URL = '<?php echo base_url(MAN); ?>Ordenservicio/';
    var ROL = '<?php echo htmlspecialchars($rolVales, ENT_QUOTES, 'UTF-8'); ?>';
    var OT = $('#matOT').data('ot');
    var detEntregas = {};
    function esc(t) { return $('<div>').text(t == null ? '' : t).html(); }
    function post(accion, datos, ok) { return $.ajax({ type: 'POST', global: false, dataType: 'json', url: URL + accion, data: datos, success: ok }); }

    function herrOt() {
        post('getHerramientasOT', { id_ot: OT }, function (d) {
            var tb = $('#matHerrOt tbody').empty();
            $.each(d || [], function (i, h) {
                var libre = parseInt(h.herrId, 10) === 0;
                tb.append('<tr><td>' + esc(libre ? '(texto libre)' : h.herrcodigo) + '</td><td>' + esc(libre ? h.descripcion : h.herrdescrip) + '</td><td>'
                    + esc(libre ? '' : h.herrmarca) + '</td><td>' + esc(h.cantidad) + '</td></tr>');
            });
            $('#matSinHerrOt').toggle(!(d || []).length);
        });
    }
    function insumos() {
        post('getInsumosPorOT', { id_ot: OT }, function (d) {
            var tb = $('#matInsumos tbody').empty();
            $.each(d || [], function (i, x) {
                tb.append('<tr><td>' + esc(x.pema_id) + '</td><td>' + esc(x.estado) + '</td><td>' + esc(x.barcode) + '</td><td>' + esc(x.descripcion) + '</td><td>'
                    + esc(x.pedido) + '</td><td>' + esc(x.entregado) + '</td><td>' + esc(x.pendiente) + '</td></tr>');
            });
            $('#matSinInsumos').toggle(!(d || []).length);
        });
    }
    function entregas() {
        post('getEntregasPorOT', { id_ot: OT }, function (peds) {
            var tb = $('#matEntregas tbody').empty(), n = 0;
            detEntregas = {};
            $.each(peds || [], function (i, p) {
                $.each(p.entregas || [], function (j, e) {
                    n++;
                    detEntregas[e.enma_id] = e.detalle || [];
                    tb.append('<tr><td>N° ' + esc(e.enma_id) + '</td><td>' + esc(e.fecha) + '</td><td>' + esc(e.comprobante) + '</td><td>' + esc(e.solicitante) + '</td><td>'
                        + esc(e.dni) + '</td><td>' + (e.detalle || []).length + '</td><td><a href="#" class="matVerEntrega" data-enma="' + esc(e.enma_id) + '">ver detalle</a></td></tr>');
                });
            });
            $('#matSinEntregas').toggle(n === 0);
        });
    }
    function herrUsadas() {
        post('getHerramientasUsadas', { id_ot: OT }, function (d) {
            var tb = $('#matHerrUsadas tbody').empty();
            var puede = { MANTENEDOR: ['MANTENEDOR'], SUPERVISOR: ['SUPERVISOR', 'PANOLERO'] };
            $.each(d || [], function (i, h) {
                var editable = ROL !== '' && $.inArray(h.origen, puede[ROL] || []) > -1;
                var chk = '<input type="checkbox" ' + (editable ? 'class="matUso" data-herr="' + esc(h.herrId) + '" data-sapa="' + esc(h.sapa_id) + '"' : 'disabled') + (h.activa ? ' checked' : '') + '>';
                var dev = h.devolucion ? '<span class="label label-success">' + esc(h.devolucion) + '</span>' : '<span class="label label-warning">sin devolver</span>';
                var quitar = editable ? '<a href="#" class="matQuitar text-danger" data-sapa="' + esc(h.sapa_id) + '" title="Quitar este vale"><i class="fa fa-trash"></i></a>'
                    : (ROL !== '' ? '<span class="text-muted" title="Lo asoció el ' + esc(String(h.origen).toLowerCase()) + '"><i class="fa fa-lock"></i></span>' : '');
                tb.append('<tr><td>' + chk + '</td><td>' + esc(h.codigo) + '</td><td>' + esc(h.descripcion) + '</td><td>' + esc(h.marca) + '</td><td>N° ' + esc(h.sapa_id)
                    + '</td><td><span class="label label-default">' + esc(String(h.origen).toLowerCase()) + '</span></td><td>' + esc(h.dias) + (h.dias == 1 ? ' día' : ' días')
                    + '</td><td>' + dev + '</td><td>' + quitar + '</td></tr>');
            });
            $('#matSinHerrUsadas').toggle(!(d || []).length);
        });
    }
    function mensaje(t) { $('#matMsgVales').text(t || '').toggle(!!t); }

    $('#matOT').on('click', '.matVerEntrega', function (e) {
        e.preventDefault();
        var enma = $(this).data('enma'), b = $('#matEntregaBody').empty();
        $('#matEntregaNro').text('N° ' + enma);
        $.each(detEntregas[enma] || [], function (i, d) {
            b.append('<tr><td>' + esc(d.barcode) + '</td><td>' + esc(d.descripcion) + '</td><td>' + esc(d.cantidad) + '</td><td>' + esc(d.lote_id) + '</td><td>' + esc(d.deposito) + '</td></tr>');
        });
        $('#matModalEntrega').modal('show');
    });
    $('#matOT').on('click', '#matAbrirVales', function () {
        $('#matBuscarVales').toggle();
        if (!$('#matValeHasta').val()) {
            var hoy = new Date(), antes = new Date();
            antes.setMonth(antes.getMonth() - 3);
            $('#matValeHasta').val(hoy.toISOString().substr(0, 10));
            $('#matValeDesde').val(antes.toISOString().substr(0, 10));
        }
    });
    $('#matOT').on('click', '#matBuscar', function () {
        mensaje('');
        post('buscarVales', { rol: ROL, dni: $('#matValeDni').val() || '', desde: $('#matValeDesde').val(), hasta: $('#matValeHasta').val() }, function (r) {
            if (!r.ok) { mensaje(r.msg); return; }
            var tb = $('#matVales tbody').empty(), v = r.vales || [];
            $.each(v, function (i, x) {
                tb.append('<tr><td>N° ' + esc(x.sapa_id) + '</td><td>' + esc(x.fecha) + '</td><td>' + esc(x.responsable) + '</td><td>' + esc(x.destino) + '</td>'
                    + '<td><button type="button" class="btn btn-xs btn-success matAsociar" data-sapa="' + esc(x.sapa_id) + '">Asociar</button></td></tr>');
            });
            $('#matVales').toggle(v.length > 0);
            $('#matSinVales').toggle(v.length === 0);
        });
    });
    $('#matOT').on('click', '.matAsociar', function () {
        post('asociarVale', { id_ot: OT, sapa_id: $(this).data('sapa'), origen: ROL }, function (r) {
            mensaje(r.ok ? '' : r.msg);
            herrUsadas();
            if (r.ok) { $('#matBuscar').click(); }
        });
    });
    $('#matOT').on('click', '.matQuitar', function (e) {
        e.preventDefault();
        if (!confirm('¿Quitar este vale de la orden?')) { return; }
        post('quitarVale', { id_ot: OT, sapa_id: $(this).data('sapa'), rol: ROL }, function (r) { mensaje(r.ok ? '' : r.msg); herrUsadas(); });
    });
    $('#matOT').on('change', '.matUso', function () {
        var c = $(this);
        post('tildarHerramienta', { id_ot: OT, herr_id: c.data('herr'), sapa_id: c.data('sapa'), activa: c.is(':checked') ? 1 : 0 });
    });

    herrOt();
    insumos();
    entregas();
    herrUsadas();
})();
</script>
