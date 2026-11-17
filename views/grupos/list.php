<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="grupos-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Grupos</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="grupo" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Nro:</th>
                <th>Descripcion:</th>
                <th style="display:none">id_empresa</th>
                <th style="display:none">estado</th>
                <th style="display:none">form_id</th>
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
                  echo '<td>'.$f['id_grupo'].'</td>';
                  echo '<td>'.$f['descripcion'].'</td>';
                  echo '<td style="display:none">'.$f['id_empresa'].'</td>';
                  echo '<td style="display:none">'.$f['estado'].'</td>';
                  echo '<td style="display:none">'.$f['form_id'].'</td>';
                  echo '</tr>';
                }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.grupos-man -->

<script>
function guardarGrupo(){
  var descripcion = $('#descripcion').val();
  var id_empresa  = $('#id_empresa').val();
  var estado      = $('#estado').val();
  var formulario =  ($('#tipoFormulario').val() == null) ? '0' : $('#tipoFormulario').val();
  var hayError    = false;
  if($('#descripcion').val() == '' || $('#id_empresa').val() == '' || $('#estado').val() == '' )
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
    data: {    "descripcion":descripcion,  "id_empresa":id_empresa,  "estado":estado , "form_id": formulario},
    url: '<?php echo base_url(MAN); ?>Grupo/Guardar_Grupo', 
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

getFormularios();

var id_ = "";
$(".fa-pencil").click(function (e) {

  id_ = $(this).parents('tr').find('td').eq(1).html();
  var descripcion = $(this).parents('tr').find('td').eq(2).html();
  var estado = $(this).parents('tr').find('td').eq(3).html();
  var id_empresa = $(this).parents('tr').find('td').eq(4).html();
  var form_id = $(this).parents('tr').find('td').eq(5).html();
  $('#descripcionE').val(descripcion);
  $('#errorE').append(id_empresa);
/*   $('#cuerpoModalEditar').html(' <div class="row">'+
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
        '<input type="text" class="form-control"  id="descripcionE" value="'+descripcion+'" >'+
      '</div>'+
      '<div class="col-xs-12 col-sm-4">'+
        ' <label style="margin-top: 7px;">Formulario: </label>'+
      '</div>'+
      '<div class="col-xs-12 col-sm-8">'+
      '<select class="form-control"  id="tipoFormulario"></select>'+
      '</div>'+
      
  '</div><br>'); */
  getFormulariosE(form_id);
  $('#modalEditar').modal('show');
});    

/*    trae formularios cargados en frm_formularios */
function getFormulariosE(form_id){
    $("#tipoFormularioE").html("");
    $.ajax({
      'data':{},
      'async': true,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo base_url(MAN); ?>Grupo/get_formularios",
      'success': function (data) {
        //console.table(data);
        if(data){
            var opcion = "<option value='0'>Ninguno</option>" ;
            $('#tipoFormularioE').append(opcion);
            for (var i = 0; i < data.length; i++) { 
            var selected = (data[i]['form_id'] == form_id) ? selected = 'selected' : selected = '';
            var nombre = data[i]['nombre'];
            var opcion = "<option value='"+data[i]['form_id']+"' "+ selected +">" +nombre+ "</option>" ; 
            $('#tipoFormularioE').append(opcion);  
          }
        }
      },
      'error' : function(data){
        console.log('Error en getFormularios');
        console.table(data);
    },
});
  }
/*    trae formularios cargados en frm_formularios */
function getFormularios(){
    $("#tipoFormulario").html("");
    $.ajax({
      'data':{},
      'async': true,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo base_url(MAN); ?>Grupo/get_formularios",
      'success': function (data) {
        //console.table(data);
        if(data){
            var opcion = "<option value='0'>Ninguno</option>" ;
            //$('#tipoFormularioE').append(opcion);
            $('#tipoFormulario').append(opcion);
            for (var i = 0; i < data.length; i++) {
            var nombre = data[i]['nombre'];
            var opcion = "<option value='"+data[i]['form_id']+"'>" +nombre+ "</option>" ; 
            $('#tipoFormulario').append(opcion);
          }
        }
      },
      'error' : function(data){
        console.log('Error en getFormularios');
        console.table(data);
    },
});
  }
function editarGrupo(){
  var id          = id_;
  var descripcion = $('#descripcionE').val();
  var formulario =  ($('#tipoFormularioE').val() == null) ? '0' : $('#tipoFormularioE').val();
  var hayError    = false;
  if($('#descripcionE').val() == '')
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
    data: {"id_grupo" : id,  "descripcion":descripcion, "formulario": formulario },
    url: '<?php echo base_url(MAN); ?>Grupo/Modificar_Grupo', 
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

function eliminarGrupo(){
  WaitingOpen();
  $.ajax({
    type: 'POST',
    data: { "id_grupo": id_},
    url: '<?php echo base_url(MAN); ?>Grupo/Eliminar_Grupo', 
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
  linkTo("<?php echo base_url(MAN); ?>Grupo/index/<?php echo $permission; ?>");
}

$(function () {
  $('#grupo').DataTable({
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
        <h4 class="modal-title">Agregar Grupo</h4>
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
            <input type="text" class="form-control" id="descripcion" >
          </div>
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Formulario: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
          <select class="form-control"  id="tipoFormulario"> 
           </select>
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarGrupo()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Grupo</h4>
      </div>

     <!--  <div class="modal-body" id="cuerpoModalEditar">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
          </div>
        </div>   
      </div> -->
      <div class="modal-body">
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
            <label style="margin-top: 7px;">Descripcion <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="descripcionE" >
          </div>
          <div class="col-xs-12 col-sm-4">
            <label style="margin-top: 7px;">Formulario: </label>
          </div>
          <div class="col-xs-12 col-sm-8">
          <select class="form-control"  id="tipoFormularioE"> 
           </select>
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="editarGrupo()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEliminar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Eliminar Grupo</h4>
      </div>

      <div class="modal-body" id="cuerpoModalEditar">
        <h5>¿Desea eliminar el registro?</h5> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarGrupo()" >Eliminar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
