<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="sucursales-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Sucursales</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalagregar" id="btnAdd">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="tbl-sucursales" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Descripción</th>
                <th>Dirección</th>
                <th>Zona</th>
                <th>Teléfono</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if($list) {
                foreach($list as $s)
                {
                  $idSucursal = $s['id_sucursal'];
                  echo '<tr data-idsucursal="'.$idSucursal.'">';
                  echo '<td>';
                  if (strpos($permission,'Del') !== false) {
                    echo '<a href="#" class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar sucursal" data-toggle="modal" data-target="#modaleliminar"></a>';
                  }
                  if (strpos($permission,'Edit') !== false) {
                    echo '<a href="#" class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar sucursal" data-toggle="modal" data-target="#modaleditar"></a>';
                  }
                  echo '</td>';
                  echo '<td>'.$s['descripc'].'</td>';
                  echo '<td>'.$s['dire'].'</td>';
                  echo '<td>'.$s['zonas'].'</td>';
                  echo '<td>'.$s['telefono'].'</td>';
                  echo '</tr>';
                }
              }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.sucursales-man -->

<!-- Modal agregar -->
<div class="modal" id="modalagregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar Sucursal</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Descripción <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="descripcion" name="descripcion">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Dirección <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="direccion" name="direccion">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Zona <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="zona" name="zona">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Teléfono <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="telefono" name="telefono">
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave" onclick="guardar()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal -->

<!-- Modal editar-->
<div class="modal" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-pencil text-light-blue"></span> Editar Sucursal</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="errorE" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Descripción <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="hidden" id="idsucursalE" name="idsucursalE" value="">
            <input type="text" class="form-control" id="descripcionE" value="" >
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Dirección <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="direccionE" name="direccionE">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Zona <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="zonaE" name="zonaE">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Teléfono <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="telefonoE" name="telefonoE">
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave" onclick="guardareditar()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal -->

<!-- Modal eliminar-->
<div class="modal" id="modaleliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar Sucursal</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <p>¿Realmente desea Eliminar la Sucursal?</p>
      </div>  <!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave" data-dismiss="modal" onclick="eliminarSucursal()" >Eliminar</button>
      </div>  <!-- /.modal footer -->
      
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal -->
<!-- / Modal -->

<script>
// agregar sucursal
function guardar() {
  var descripcion = $('#descripcion').val();
  var direccion   = $('#direccion').val();
  var zona        = $('#zona').val();
  var telefono    = $('#telefono').val();

  var hayError = false; 
  $('#error').hide();

  if($('#descripcion').val() == '')
  {
    hayError = true;
  }
  if($('#direccion').val() == '')
  {
    hayError = true;
  }
  if($('#zona').val() == '')
  {
    hayError = true;
  }
  if($('#telefono').val() == '')
  {
    hayError = true;
  }

  if(hayError == true){
    $('#error').fadeIn('slow');     
    return;
  }
  $.ajax({
    data:{descripcion:descripcion, direccion:direccion, zona:zona, telefono:telefono},
    // dataType: 'json',
    type:"POST",
    url: "<?php echo base_url(MAN); ?>Sucursal/agregarSucursal",
    success: function(data){
      //console.log("data: "+data);
      $('#modalagregar').modal('hide');
      regresa();
    },
    error: function(result){
        console.error("Error al agregar herramientas");
        console.table(result);
    },
  });
}

// Carga datos del modal editar
$(".fa-pencil").click(function (e) { 
  var idsucursal  = $(this).parent('td').parent('tr').data('idsucursal');
  var descripcion = $(this).parent('td').next().html();
  var direccion   = $(this).parent('td').next().next().html();
  var zona        = $(this).parent('td').next().next().next().html();
  var telefono    = $(this).parent('td').next().next().next().next().html();
  //console.info("id sucursal: "+idsucursal);
  $('#idsucursalE').val(idsucursal);
  $('#descripcionE').val(descripcion);
  $('#direccionE').val(direccion);
  $('#zonaE').val(zona);
  $('#telefonoE').val(telefono);
});
// edita sucursal
function guardareditar(){
  var idsucursal  = $('#idsucursalE').val();
  var descripcion = $('#descripcionE').val();
  var direccion   = $('#direccionE').val();
  var zona        = $('#zonaE').val();
  var telefono    = $('#telefonoE').val();
  var parametros = {
    'descripc' : descripcion,
    'dire'     : direccion,
    'zonas'    : zona,
    'telefono' : telefono,
  };                                            
  //console.log("idsucursal: "+idsucursal);
  var hayError = false; 
  $('#errorE').hide();

  if($('#descripcionE').val() == '')
  {
    hayError = true;
  }
  if($('#direccionE').val() == '')
  {
    hayError = true;
  }
  if($('#zonaE').val() == '')
  {
    hayError = true;
  }
  if($('#telefonoE').val() == '')
  {
    hayError = true;
  }

  if(hayError == true){
    $('#errorE').fadeIn('slow');     
    return;
  }

  $.ajax({
    type:"POST",
    url: "<?php echo base_url(MAN); ?>Sucursal/editarSucursal",
    data:{parametros:parametros, idsucursal:idsucursal},
    success: function(data){
      //console.log("data: "+data);
      $('#modaleditar').modal('hide');
      regresa();    
    },
    error: function(result){
      console.error("Error al editar sucursal");
      console.table(result);
    },
  });
}

// Saca el id de componente a eliminar
$(".fa-times-circle").click(function (e) { // Ok
    idSucursal = $(this).parent('td').parent('tr').data('idsucursal');
    console.log("El id de sucursal a eliminar es: "+idSucursal);
});
// Elimina componente
function eliminarSucursal() { // Ok
  WaitingOpen('Eliminando sucursal');
  $.ajax({
    data: { idSucursal:idSucursal },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo base_url(MAN); ?>Sucursal/bajaSucursal',
    success: function(data){
      WaitingClose();
      regresa();
    },
    error: function(result){
      console.error("Error al eliminar sucursal");
      console.table(result);
      WaitingClose();
    }
  });
} 

// recarga la vista del listado de componentes
function regresa(){ // Ok
  $('#content').empty();
  linkTo("<?php echo base_url(MAN); ?>Sucursal/index/<?php echo $permission; ?>");
  WaitingClose();
}

$('#tbl-sucursales').DataTable({
    "aLengthMenu": [ 10, 25, 50, 100 ],
    "columnDefs": [ {
      "targets": [ 0 ], 
      "searchable": false
    },
    {
      "targets": [ 0 ], 
      "orderable": false
    } ],
    "order": [[1, "asc"]],
});
</script>