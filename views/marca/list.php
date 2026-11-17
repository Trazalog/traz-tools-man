<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="marca-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Marca</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="marca" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Nro:</th>
                <th>Marca:</th>
                <th style="display:none">estado</th>
                <th style="display:none">id_empresa</th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach($list as $f)
                {
                  echo '<tr>';
                  echo '<td>';
                  if (strpos($permission,'Edit') !== false) {
                    echo '<i class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar"></i>';
                  }
                  if (strpos($permission,'Del') !== false) {
                   echo '<i class="fa fa-fw fa-times-circle text-light-blue" title="Eliminar" style="cursor: pointer; margin-left: 15px;" ></i>';
                  }
                  echo '</td>';
                  echo '<td>'.$f['marcaid'].'</td>';
                  echo '<td>'.$f['marcadescrip'].'</td>';
                  echo '<td style="display:none">'.$f['estado'].'</td>';
                  echo '<td style="display:none">'.$f['id_empresa'].'</td>';
                  echo '</tr>';
                }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.marca-man -->

<script>
//cierro el backdrop del modal 
$('#modalAgregar').on('shown.bs.modal', function () {
  $(".modal-backdrop.in").hide();
});

function guardarMarca(){
  var marcadescrip = $('#marcadescrip').val();
  var estado       = $('#estado').val();
  var id_empresa   = $('#id_empresa').val();
  var hayError     = false;
  if($('#marcadescrip').val() == '' || $('#estado').val() == '' || $('#id_empresa').val() == '' )
  {
    hayError = true;
  }
  if(hayError == true){
    $('#error').fadeIn('slow');
    return;
  }
  $('#error').fadeOut('slow');          
  WaitingOpen();
  $.ajax({
    type: 'POST',
    data: { "marcadescrip":marcadescrip, "estado":estado, "id_empresa":id_empresa },
    url: '<?php echo base_url(MAN); ?>Marca/Guardar_Marca', 
    success: function(result){
      WaitingClose();
      $('#modalAgregar').modal('hide');
      ActualizarPagina();
    },
    error: function(result){
      WaitingClose();
      alert("OPERACIÓN FALLIDA");
    }
  });
}

var id_ = "";
$(".fa-pencil").click(function (e) { 
  id_              = $(this).parents('tr').find('td').eq(1).html();
  var marcadescrip = $(this).parents('tr').find('td').eq(2).html();
  var estado       = $(this).parents('tr').find('td').eq(3).html();
  var id_empresa   = $(this).parents('tr').find('td').eq(4).html();
  $('#cuerpoModalEditar').html(''+
    '<div class="row">'+
      '<div class="col-xs-12">'+
        '<div class="alert alert-danger alert-dismissable" id="errorE" style="display: none">'+
          '<h4><i class="icon fa fa-ban"></i> Error!</h4>'+
          'Revise que todos los campos esten completos'+
        '</div>'+
      '</div>'+
    '</div>'+
    '<div class="row">'+
      '<div class="col-xs-12">'+
        '<div class="form-group">'+
          '<label>Marca <strong style="color: #dd4b39">*</strong>: </label>'+
          '<input type="text" id="marcadescripE" class="form-control" value="'+marcadescrip+'">'+
        '</div>'+
      '</div>'+
    '</div><br>');
  $('#modalEditar').modal('show');
});    

function editarMarca(){
  var id           = id_;
  var marcadescrip = $('#marcadescripE').val();
  var estado       = $('#estadoE').val();
  var id_empresa   = $('#id_empresaE').val();
  var hayError     = false;
  if($('#marcadescripE').val() == '' || $('#estadoE').val() == '' || $('#id_empresaE').val() == '' )
  {
    hayError = true;
  }
  if(hayError == true){
    $('#errorE').fadeIn('slow');
    return;
  }
  $('#errorE').fadeOut('slow');
  WaitingOpen();
  $.ajax({
    type: 'POST',
    dataType : "json",
    data: {"marcaid" : id,  "marcadescrip":marcadescrip,  "estado":estado,  "id_empresa":id_empresa },
    url: '<?php echo base_url(MAN); ?>Marca/Modificar_Marca', 
    success: function(result){
      WaitingClose();
      $('#modalEditar').modal('hide');
      ActualizarPagina();
    },
    error: function(result){
      WaitingClose();
      alert("OPERACIÓN FALLIDA");
      console.log(result);
    }
  });
}

$(".fa-times-circle").click(function (e) { 
  id_ = $(this).parents('tr').find('td').eq(1).html();
  $('#modalEliminar').modal('show');
});

function eliminarMarca(){
  WaitingOpen();
  $.ajax({
    type: 'POST',
    data: { "marcaid": id_},
    url: '<?php echo base_url(MAN); ?>Marca/Eliminar_Marca', 
    success: function(data){
      WaitingClose();
      ActualizarPagina();
    },
    error: function(result){
      WaitingClose();
      alert("OPERACION FALLIDA");
    }
  });
}

function ActualizarPagina(){ //Funcion Resfresca
  $('#content').empty();
  linkTo("<?php echo base_url(MAN); ?>Marca/index/<?php echo $permission; ?>");
}

$(function () {
  $('#marca').DataTable({
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
});
</script>

<!-- Modal alta de marca -->
<div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Marca</h4> 
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
            <div class="form-group">
              <label for="marcadescrip">Marca:</label>
              <input type="text" id="marcadescrip" name="marcadescrip" class="form-control" placeholder="Ingrese Nombre o Descripción...">
            </div>
          </div>
        </div>
      </div>  <!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarMarca()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog modal-lg -->
</div><!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal editar marca -->
<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Marca</h4>
      </div>

      <div class="modal-body" id="cuerpoModalEditar">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="editarMarca()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->

<!-- Modal eliminar marca -->
<div class="modal" id="modalEliminar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Eliminar Marca</h4>
      </div>

      <div class="modal-body" id="cuerpoModalEditar">
        <h5>¿Desea eliminar el registro?</h5> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarMarca()">Eliminar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->