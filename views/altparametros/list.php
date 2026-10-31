<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="altparametros-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Parámetros</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="Altparametro" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Nro:</th>
                <th>Descripcion:</th>
                <th style="display:none">min</th>
                <th style="display:none">id_empresa</th>
                <th style="display:none">estado</th>
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
                  echo '<td>'.$f['paramId'].'</td>';
                  echo '<td>'.$f['paramdescrip'].'</td>';
                  echo '<td style="display:none">'.$f['min'].'</td>';
                  echo '<td style="display:none">'.$f['id_empresa'].'</td>';
                  echo '<td style="display:none">'.$f['estado'].'</td>';
                  echo '</tr>';
                }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.altparametros-man -->

<script>
function guardarAltparametro(){
  var paramdescrip = $('#paramdescrip').val();
  var min          = $('#min').val();
  var id_empresa   = $('#id_empresa').val();
  var estado       = $('#estado').val();
  var hayError     = false;
  if($('#paramdescrip').val() == '' || $('#min').val() == '' || $('#id_empresa').val() == '' || $('#estado').val() == '' )
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
    data: { "paramdescrip":paramdescrip, "min":min, "id_empresa":id_empresa, "estado":estado },
    url: '<?php echo base_url(MAN); ?>Altparametro/Guardar_Altparametro', 
    success: function(result){
      WaitingClose();
      $('#modalAgregar').modal('hide');
      ActualizarPagina();
    },
    error: function(result){
      alert("OPERACIÓN FALLIDA");
    }
  });
}

var id_ = "";
$(".fa-pencil").click(function (e) { 
    id_              = $(this).parents('tr').find('td').eq(1).html();
    var paramdescrip = $(this).parents('tr').find('td').eq(2).html();
    var min          = $(this).parents('tr').find('td').eq(3).html();
    var id_empresa   = $(this).parents('tr').find('td').eq(4).html();
    var estado       = $(this).parents('tr').find('td').eq(5).html();
    $('#cuerpoModalEditar').html(' <div class="row">'+
      '<div class="col-xs-12">'+
        '<div class="alert alert-danger alert-dismissable" id="errorE" style="display: none">'+
         '<h4><i class="icon fa fa-ban"></i> Error!</h4>'+
         'Revise que todos los campos esten completos'+
       '</div>'+
     '</div>'+
   '</div>'+
      '<div class="row">'+
        '<div class="col-xs-12 col-sm-4">'+
           ' <label style="margin-top: 7px;">Descripcion <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-12 col-sm-8">'+
            '<input type="text" class="form-control"  id="paramdescripE" value="'+paramdescrip+'" >'+
        '</div>'+
    '</div><br>' );
    $('#modalEditar').modal('show');
});    


function editarAltparametro(){
  var id           = id_;
  var paramdescrip = $('#paramdescripE').val();
  var min          = $('#minE').val();
  var id_empresa   = $('#id_empresaE').val();
  var estado       = $('#estadoE').val();

  var hayError = false;
  if($('#paramdescripE').val() == '' || $('#minE').val() == '' || $('#id_empresaE').val() == '' || $('#estadoE').val() == '' )
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
    data: {"paramId" : id, "paramdescrip":paramdescrip, "min":min, "id_empresa":id_empresa, "estado":estado },
    url: '<?php echo base_url(MAN); ?>Altparametro/Modificar_Altparametro', 
    success: function(result){
      WaitingClose();
      $('#modalEditar').modal('hide');
      ActualizarPagina();
    },
    error: function(result){
      alert("OPERACIÓN FALLIDA");
      console.log(result);
    }
  });
}

$(".fa-times-circle").click(function (e) { 
  id_ = $(this).parents('tr').find('td').eq(1).html();
  $('#modalEliminar').modal('show');
});

function eliminarAltparametro(){
  WaitingOpen();
  $.ajax({
    type: 'POST',
    data: { "paramId": id_},
    url: '<?php echo base_url(MAN); ?>Altparametro/Eliminar_Altparametro', 
    success: function(data){
      WaitingClose();
      ActualizarPagina();                
    },
    error: function(result){
      alert("OPERACION FALLIDA");
    }
  });
}


function ActualizarPagina(){ //Funcion Resfresca
  $('#content').empty();
  linkTo("<?php echo base_url(MAN); ?>Altparametro/index/<?php echo $permission; ?>");
}

$(function () {
  $('#Altparametro').DataTable({
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

<div class="modal" id="modalAgregar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar Parametro</h4>
      </div>

      <div class="modal-body">
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
            <label style="margin-top: 7px;">Descripcion <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control"  id="paramdescrip" >
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarAltparametro()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Parametro</h4>
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
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="editarAltparametro()" >Guardar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEliminar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Eliminar Parametro</h4>
      </div>
      <div class="modal-body" id="cuerpoModalEditar">
       <h5>¿Desea eliminar el registro?</h5>
      </div>
      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarAltparametro()" >Eliminar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->