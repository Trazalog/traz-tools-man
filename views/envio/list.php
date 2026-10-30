<?php
/**
 * Administrar Órdenes — órdenes terminadas por entregar o reprogramar. Portada desde asset (MIG-ASSET-4).
 * Datos: $list (filas), $permission.
 */
$this->load->helper('man_tarea');
$fecha = function ($v) {
    $t = strtotime((string) $v);
    return ($t && substr((string) $v, 0, 4) != '0000') ? date('d-m-Y', $t) : '';
};
?>
<input type="hidden" id="permission" value="<?php echo man_esc($permission); ?>">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Administrador de Ordenes</h3>
        </div>
        <div class="box-body">
          <table id="envio" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th><th>ID</th><th>Numero</th><th>Fecha Inicio</th><th>Fecha Entrega</th><th>Fecha Terminada</th><th>Fecha Aviso</th><th>Detalle</th><th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $a) { ?>
              <tr id="<?php echo (int) $a['id_orden']; ?>">
                <td>
                  <?php if (strpos($permission, 'Edit') !== false) {
                      if ($a['estado'] == 'T') {
                          echo '<i class="fa fa-thumbs-up text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Entregar"></i>';
                      } elseif ($a['estado'] == 'TE') {
                          echo '<i class="fa fa-calendar cous text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Reprogramación" data-toggle="modal" data-target="#modalfecha"></i>';
                      }
                  } ?>
                </td>
                <td><?php echo man_esc($a['id_orden']); ?></td>
                <td><?php echo man_esc($a['nro']); ?></td>
                <td><?php echo man_esc($fecha($a['fecha_inicio'])); ?></td>
                <td><?php echo man_esc($fecha($a['fecha_entrega'])); ?></td>
                <td><?php echo man_esc($fecha($a['fecha_terminada'])); ?></td>
                <td><?php echo man_esc($fecha($a['fecha_aviso'])); ?></td>
                <td><?php echo man_esc($a['descripcion']); ?></td>
                <td style="text-align: center"><?php echo $a['estado'] == 'T' ? '<small class="label pull-left bg-blue">Terminado</small>' : '<small class="label pull-left bg-red">Terminado Parcial</small>'; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  var MAN_URL = '<?php echo base_url(MAN); ?>';
  var ido = null;
  function regresa() { linkTo(MAN_URL + 'Envio/index/<?php echo man_esc($permission); ?>'); }

  $('.fa-thumbs-up').click(function () {
    var id = $(this).closest('tr').attr('id');
    $.ajax({ type: 'POST', data: { id_orden: id }, url: MAN_URL + 'Envio/entrega', dataType: 'json',
      success: function (ok) { if (ok) { alert('Se Entrego Orden'); regresa(); } else { alert('No se pudo entregar la orden'); } },
      error: function () { alert('No se pudo entregar la orden'); } });
  });
  $('.fa-calendar').click(function () { ido = $(this).closest('tr').attr('id'); });
  $('#fecha').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
  $('#envio').DataTable({ aLengthMenu: [10, 25, 50, 100], columnDefs: [{ targets: [0], searchable: false, orderable: false }], order: [[1, 'asc']] });

  window.guardarfecha = function () {
    $.ajax({ type: 'POST', data: { ido: ido, fe: $('#fecha').val() }, url: MAN_URL + 'Envio/ModificarFecha', dataType: 'json',
      success: function (ok) { if (ok) { regresa(); } else { alert('No se pudo reprogramar la orden'); } },
      error: function () { alert('No se pudo reprogramar la orden'); } });
  };
})();
</script>

<div class="modal fade" id="modalfecha" tabindex="-1" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="fa fa-user text-light-blue"></span> Reprogramar</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <label for="fecha">Fecha</label>
            <input type="text" id="fecha" name="fecha" value="<?php echo date('d-m-Y'); ?>" class="form-control datepicker" autocomplete="off">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarfecha()">Guardar</button>
      </div>
    </div>
  </div>
</div>
