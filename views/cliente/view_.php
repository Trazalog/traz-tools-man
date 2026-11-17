<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="cliente-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Cliente</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="Cliente" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="20%">Acciones</th>
                <th>Id</th>
                <th>Nombre</th>
                <th>Dni</th>
                <th>Direccion</th>
                <th>Telefono</th>
                <th>Email</th>
                <th>RazonSocial</th>
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
                  echo '<td style="text-align: left">'.$f['cliId'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliName'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliDni'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliAddress'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliPhone'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliEmail'].'</td>';
                  echo '<td style="text-align: left">'.$f['cliRazonSocial'].'</td>';
                  
                  echo '</tr>';

                }

              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.cliente-man -->

<script>
function guardarCliente(){
  var cliName=$('#cliName').val();
  var cliDni=$('#cliDni').val();
  var cliAddress=$('#cliAddress').val();
  var cliPhone=$('#cliPhone').val();
  var cliEmail=$('#cliEmail').val();
  var cliRazonSocial=$('#cliRazonSocial').val();
  var hayError = false;

  if($('#cliName').val() == '' || $('#cliDni').val() == '' || $('#cliAddress').val() == '' || $('#cliPhone').val() == '' || $('#cliEmail').val() == '' || $('#cliRazonSocial').val() == '' )
  {
    hayError = true;
  }
  if(hayError == true){
    $('#error').fadeIn('slow');
    return;
  }
  $('#error').fadeOut('slow');          
  
  $.ajax({
    type: 'POST',
    data: {    "cliName":cliName,  "cliDni":cliDni,  "cliAddress":cliAddress,  "cliPhone":cliPhone,  "cliEmail":cliEmail,  "cliRazonSocial":cliRazonSocial },
    url: '<?php echo base_url(MAN); ?>Cliente/Guardar_Cliente', 
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
    id_ = $(this).parents('tr').find('td').eq(1).html();
    var cliName = $(this).parents('tr').find('td').eq(2).html();
    var cliDni = $(this).parents('tr').find('td').eq(3).html();
    var cliAddress = $(this).parents('tr').find('td').eq(4).html();
    var cliPhone = $(this).parents('tr').find('td').eq(5).html();
    var cliEmail = $(this).parents('tr').find('td').eq(6).html();
    var cliRazonSocial = $(this).parents('tr').find('td').eq(7).html();

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
            '<input type="text" class="form-control"  id="cliNameE" value="'+cliName+'" >'+
        '</div>'+
    '</div><br>'+
      '<div class="row">'+
        '<div class="col-xs-4">'+
            ' <label style="margin-top: 7px;">Dni <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-5">'+
            '<input type="text" class="form-control"  id="cliDniE" value="'+cliDni+'" >'+
        '</div>'+
    '</div><br>'+
      '<div class="row">'+
        '<div class="col-xs-4">'+
            ' <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-5">'+
            '<input type="text" class="form-control"  id="cliAddressE" value="'+cliAddress+'" >'+
        '</div>'+
    '</div><br>'+
      '<div class="row">'+
        '<div class="col-xs-4">'+
            ' <label style="margin-top: 7px;">Telefono <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-5">'+
            '<input type="text" class="form-control"  id="cliPhoneE" value="'+cliPhone+'" >'+
        '</div>'+
    '</div><br>'+
      '<div class="row">'+
        '<div class="col-xs-4">'+
            ' <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-5">'+
            '<input type="text" class="form-control"  id="cliEmailE" value="'+cliEmail+'" >'+
        '</div>'+
    '</div><br>'+
      '<div class="row">'+
        '<div class="col-xs-4">'+
            ' <label style="margin-top: 7px;">Razon Social <strong style="color: #dd4b39">*</strong>: </label>'+
        '</div>'+
        '<div class="col-xs-5">'+
            '<input type="text" class="form-control"  id="cliRazonSocialE" value="'+cliRazonSocial+'" >'+
        '</div>'+
    '</div><br>'        );
    $('#modalEditar').modal('show');
  });

  function editarCliente(){
    var id=id_;
    var cliName=$('#cliNameE').val();
    var cliDni=$('#cliDniE').val();
    var cliAddress=$('#cliAddressE').val();
    var cliPhone=$('#cliPhoneE').val();
    var cliEmail=$('#cliEmailE').val();
    var cliRazonSocial=$('#cliRazonSocialE').val();
    var hayError = false;
    if($('#cliNameE').val() == '' || $('#cliDniE').val() == '' || $('#cliAddressE').val() == '' || $('#cliPhoneE').val() == '' || $('#cliEmailE').val() == '' || $('#cliRazonSocialE').val() == '' )
    {
      hayError = true;
    }
    if(hayError == true){
      $('#errorE').fadeIn('slow');
      return;
    }
    $('#errorE').fadeOut('slow');
    $.ajax({
      type: 'POST',
      dataType : "json",
      data: {"cliId" : id,  "cliName":cliName,  "cliDni":cliDni,  "cliAddress":cliAddress,  "cliPhone":cliPhone,  "cliEmail":cliEmail,  "cliRazonSocial":cliRazonSocial },
      url: '<?php echo base_url(MAN); ?>Cliente/Modificar_Cliente', 
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

  function eliminarCliente(){
    $.ajax({
      type: 'POST',
      data: { "cliId": id_},
      url: '<?php echo base_url(MAN); ?>Cliente/Eliminar_Cliente', 
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
    linkTo("<?php echo base_url(MAN); ?>Cliente/index/<?php echo $permission; ?>");
  }
  
  $(function () {        
    $('#Cliente').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "language": {
        "lengthMenu": "Ver MENU filas por página",
        "zeroRecords": "No hay registros",
        "info": "Mostrando pagina PAGE de PAGES",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrando de un total de MAX registros)",
        "sSearch": "Buscar:  ",
        "oPaginate": {
          "sNext": "Sig.",
          "sPrevious": "Ant."
        }
      }
    });
  });    
</script>

<div class="modal" id="modalAgregar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar Cliente</h4>
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
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Nombre <strong style="color: #dd4b39">*</strong>: </label>
          </div>
            <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliName" >
            </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Dni <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliDni" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliAddress" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Telefono <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliPhone" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliEmail" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-4">
            <label style="margin-top: 7px;">Razon Social <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control"  id="cliRazonSocial" >
          </div>
        </div><br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="guardarCliente()" >Guardar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->
<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Cliente</h4>
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
        <button type="button" class="btn btn-primary" onclick="editarCliente()" >Guardar</button>
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
        <h4 class="modal-title">Eliminar Cliente</h4>
      </div>
      <div class="modal-body" id="cuerpoModalEditar">
        <h5>¿Desea eliminar el registro?</h5> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarCliente()" >Eliminar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
        
<div class="modal" id="modalEditar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editar Cliente</h4>
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
        <button type="button" class="btn btn-primary" onclick="editarCliente()" >Guardar</button>
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
        <h4 class="modal-title">Eliminar Cliente</h4>
      </div>
      <div class="modal-body" id="cuerpoModalEditar">
        <h5>¿Desea eliminar el registro?</h5> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarCliente()" >Eliminar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->