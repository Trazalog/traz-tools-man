<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="contratistas-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Contratistas</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="contratista" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Nro:</th>
                <th>Nombre:</th>
                <th>Direccion:</th>
                <th>Email:</th>
                <th style="display:none">Email 2:</th>
                <th>Celular :</th>
                <th style="display:none">Celular 2:</th>
                <th>Telefono:</th>
                <th>Contacto:</th>
                <th style="display:none">Estado:</th>
                <th style="display:none">Id_empresa:</th>
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
                  echo '<td>'.$f['id_contratista'].'</td>';
                  echo '<td>'.$f['nombre'].'</td>';
                  echo '<td>'.$f['contradireccion'].'</td>';
                  echo '<td>'.$f['contramail'].'</td>';
                  echo '<td style="display:none">'.$f['contramail1'].'</td>';
                  echo '<td>'.$f['contracelular1'].'</td>';
                  echo '<td style="display:none">'.$f['contracelular2'].'</td>';
                  echo '<td>'.$f['contratelefono'].'</td>';
                  echo '<td>'.$f['contracontacto'].'</td>';
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
</div><!-- /.contratistas-man -->

<script>
// limpia modal agregar nuevo contratista
$('#btnAdd').click(function(){
  $('#nombre').val('');
  $('#contradireccion').val('');
  $('#contramail').val('');
  $('#contramail1').val('');
  $('#contracelular1').val('');
  $('#contracelular2').val('');
  $('#contratelefono').val('');
  $('#contracontacto').val('');
});

function guardarContratista(){
  var nombre          = $('#nombre').val();
  var contradireccion = $('#contradireccion').val();
  var contramail      = $('#contramail').val();
  var contramail1     = $('#contramail1').val();
  var contracelular1  = $('#contracelular1').val();
  var contracelular2  = $('#contracelular2').val();
  var contratelefono  = $('#contratelefono').val();
  var contracontacto  = $('#contracontacto').val();
  var hayError        = false;
  
  if($('#nombre').val() == '' || $('#contradireccion').val() == '' || $('#contramail').val() == '' || $('#contracelular1').val() == '')
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
    data: {"nombre":nombre,  "contradireccion":contradireccion,  "contramail":contramail,  "contramail1":contramail1,  "contracelular1":contracelular1,  "contracelular2":contracelular2,  "contratelefono":contratelefono,  "contracontacto":contracontacto },
    url: '<?php echo base_url(MAN); ?>Contratista/Guardar_Contratista', 
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

var id_ ="";
$(".fa-pencil").click(function (e) { 
  id_                 = $(this).parents('tr').find('td').eq(1).html();
  var nombre          = $(this).parents('tr').find('td').eq(2).html();
  var contradireccion = $(this).parents('tr').find('td').eq(3).html();
  var contramail      = $(this).parents('tr').find('td').eq(4).html();
  var contramail1     = $(this).parents('tr').find('td').eq(5).html();
  var contracelular1  = $(this).parents('tr').find('td').eq(6).html();
  var contracelular2  = $(this).parents('tr').find('td').eq(7).html();
  var contratelefono  = $(this).parents('tr').find('td').eq(8).html();
  var contracontacto  = $(this).parents('tr').find('td').eq(9).html();
  $('#cuerpoModalEditar').html(' <div class="row">'+
    '<div class="col-xs-12">'+
      '<div class="alert alert-danger alert-dismissable" id="errorE" style="display: none">'+
        '<h4><i class="icon fa fa-ban"></i> Error!</h4>'+
        'Revise que todos los campos esten completos'+
      '</div>'+
    '</div>'+
  '</div>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Nombre <strong style="color: #dd4b39">*</strong>: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="nombreE" value="'+nombre+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contradireccionE" value="'+contradireccion+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contramailE" value="'+contramail+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Email 2: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contramail1E" value="'+contramail1+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Celular <strong style="color: #dd4b39">*</strong>: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contracelular1E" value="'+contracelular1+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Celular 2: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contracelular2E" value="'+contracelular2+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Telefono: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contratelefonoE" value="'+contratelefono+'" >'+
    '</div>'+
  '</div><br>'+
  '<div class="row">'+
    '<div class="col-xs-4">'+
      ' <label style="margin-top: 7px;">Contacto: </label>'+
    '</div>'+
    '<div class="col-xs-5">'+
      '<input type="text" class="form-control"  id="contracontactoE" value="'+contracontacto+'" >'+
    '</div>'+
  '</div><br>');
  $('#modalEditar').modal('show');
});    

function editarContratista(){
  var id              = id_;
  var nombre          = $('#nombreE').val();
  var contradireccion = $('#contradireccionE').val();
  var contramail      = $('#contramailE').val();
  var contramail1     = $('#contramail1E').val();
  var contracelular1  = $('#contracelular1E').val();
  var contracelular2  = $('#contracelular2E').val();
  var contratelefono  = $('#contratelefonoE').val();
  var contracontacto  = $('#contracontactoE').val();
  var hayError        = false;
  if($('#nombreE').val() == '' || $('#contradireccionE').val() == '' || $('#contramailE').val() == '' || $('#contracelular1E').val()=='' )
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
    data: {"id_contratista" : id,  "nombre":nombre,  "contradireccion":contradireccion,  "contramail":contramail,  "contramail1":contramail1,  "contracelular1":contracelular1,  "contracelular2":contracelular2,  "contratelefono":contratelefono,  "contracontacto":contracontacto},
    url: '<?php echo base_url(MAN); ?>Contratista/Modificar_Contratista', 
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

function eliminarContratista(){
  WaitingOpen();
  $.ajax({
    type: 'POST',
    data: { "id_contratista": id_},
    url: '<?php echo base_url(MAN); ?>Contratista/Eliminar_Contratista', 
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
  linkTo("<?php echo base_url(MAN); ?>Contratista/index/<?php echo $permission; ?>");
}

$(function () {
  $('#contratista').DataTable({
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
        <h4 class="modal-title">Agregar Contratista</h4>
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
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Nombre <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="nombre" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contradireccion" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contramail" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Email 2: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contramail1" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Celular <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contracelular1" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Celular 2: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contracelular2" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Telefono: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contratelefono" >
          </div>
        </div><br>

        <div class="row"> 
          <div class="col-xs12 col-sm-4">
            <label style="margin-top: 7px;">Contacto: </label>
          </div>
          <div class="col-xs12 col-sm-8">
            <input type="text" class="form-control"  id="contracontacto" >
          </div>
        </div><br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarContratista()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Contratista</h4>
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
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="editarContratista()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->


<div class="modal" id="modalEliminar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Eliminar Contratista</h4>
      </div>

      <div class="modal-body" id="cuerpoModalEditar">
        <h5>¿Desea eliminar el registro?</h5> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarContratista()" >Eliminar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->