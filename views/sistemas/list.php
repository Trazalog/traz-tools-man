<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="sistemas-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Sistemas</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalagregar" id="btnAdd">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="tbl-sistemas" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Descripción</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if($list) {
                foreach($list as $s)
                {
                  $idsistema = $s['sistemaid'];
                  echo '<tr data-idsistema="'.$idsistema.'">';
                  echo '<td>';
                  if (strpos($permission,'Del') !== false) {
                    echo '<a href="#" class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar sistema" data-toggle="modal" data-target="#modaleliminar"></a>';
                  }
                  if (strpos($permission,'Edit') !== false) {
                    echo '<a href="#" class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar sistema" data-toggle="modal" data-target="#modaleditar"></a>';
                  }
                  echo '</td>';
                  echo '<td>'.$s['descripcion'].'</td>';
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
</div><!-- /.sistemas-man -->

<!-- Modal agregar -->
<div class="modal" id="modalagregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar Sistema</h4>
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
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-pencil text-light-blue"></span> Editar Sistema</h4>
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
            <input type="hidden" id="idsistemaE" name="idsistemaE" value="">
            <input type="text" class="form-control" id="descripcionE" value="" >
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
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar sistema</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <p>¿Realmente desea Eliminar el sistema?</p>
      </div>  <!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave" data-dismiss="modal" onclick="eliminarSistema()" >Eliminar</button>
      </div>  <!-- /.modal footer -->
      
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal -->
<!-- / Modal -->

<script>
// agregar sistema
function guardar() { // Ok
  var descripcion = $('#descripcion').val();

  var hayError = false; 
  $('#error').hide();

  if($('#descripcion').val() == '')
  {
    hayError = true;
  }

  if(hayError == true){
    $('#error').fadeIn('slow');     
    return;
  }
  $.ajax({
    data:{descripcion:descripcion},
    // dataType: 'json',
    type:"POST",
    url: "<?php echo base_url(MAN); ?>SistemaABM/agregarSistema",
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
$(".fa-pencil").click(function (e) { // Ok
  var idsistema  = $(this).parent('td').parent('tr').data('idsistema');
  var descripcion = $(this).parent('td').next().html();
  //console.info("id sistema: "+idsistema);
  $('#idsistemaE').val(idsistema);
  $('#descripcionE').val(descripcion);
});
// edita sistema
function guardareditar(){
  var idsistema  = $('#idsistemaE').val();
  var descripcion = $('#descripcionE').val();
  var parametros = {
    'descripcion' : descripcion,
  };                                            
  //console.log("idsistema: "+idsistema);
  var hayError = false; 
  $('#errorE').hide();

  if($('#descripcionE').val() == '')
  {
    hayError = true;
  }

  if(hayError == true){
    $('#errorE').fadeIn('slow');     
    return;
  }

  $.ajax({
    type:"POST",
    url: "<?php echo base_url(MAN); ?>SistemaABM/editarSistema",
    data:{parametros:parametros, idsistema:idsistema},
    success: function(data){
      //console.log("data: "+data);
      $('#modaleditar').modal('hide');
      regresa();    
    },
    error: function(result){
      console.error("Error al editar sistema");
      console.table(result);
    },
  });
}

// Saca el id de componente a eliminar
$(".fa-times-circle").click(function (e) { // Ok
    idsistema = $(this).parent('td').parent('tr').data('idsistema');
    console.log("El id de sistema a eliminar es: "+idsistema);
});
// Elimina componente
function eliminarSistema() { // Ok
  WaitingOpen('Eliminando sistema');
  $.ajax({
    data: { idsistema:idsistema },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo base_url(MAN); ?>SistemaABM/bajaSistema',
    success: function(data){
      WaitingClose();
      regresa();
    },
    error: function(result){
      console.error("Error al eliminar sistema");
      console.table(result);
      WaitingClose();
    }
  });
} 

// recarga la vista del listado de componentes
function regresa(){ // Ok
  $('#content').empty();
  linkTo("<?php echo base_url(MAN); ?>SistemaABM/index/<?php echo $permission; ?>");
  WaitingClose();
}

$('#tbl-sistemas').DataTable({
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