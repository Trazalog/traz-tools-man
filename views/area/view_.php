<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="area-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Areas</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAgregar" id="btnAdd" title="Nueva">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="area" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="20%">Acciones</th>
                <th>Id Area:</th>
                <th>Descripcion:</th>
                <th style="display:none">id_empresa</th>
                <th style="display:none">Estado:</th>
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
                  echo '<td style="text-align: left">'.$f['id_area'].'</td>';
                  echo '<td style="text-align: left">'.$f['descripcion'].'</td>';
                  echo '<td style="text-align: left ; display:none">'.$f['id_empresa'].'</td>';
                  echo '<td style="text-align: left ; display:none">'.$f['estado'].'</td>';
                  
                  echo '</tr>';

                }

              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.area-man -->

<script>
function guardararea(){
    var descripcion=$('#descripcion').val();
    var id_empresa=$('#id_empresa').val();

    var hayError = false;
    if($('#descripcion').val() == '' || $('#id_empresa').val() == '' )
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
             data: {    "descripcion":descripcion,  "id_empresa":id_empresa },
             url: '<?php echo base_url(MAN); ?>Area/Guardar_area', 
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
        var descripcion = $(this).parents('tr').find('td').eq(2).html();
        var id_empresa = $(this).parents('tr').find('td').eq(3).html();

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
               ' <label style="margin-top: 7px;">Descripcion: <strong style="color: #dd4b39">*</strong>: </label>'+
            '</div>'+
            '<div class="col-xs-5">'+
                '<input type="text" class="form-control"  id="descripcionE" value="'+descripcion+'" >'+
            '</div>'+
        '</div><br>');
              
        $('#modalEditar').modal('show');

    });    
    
    function editararea(){

      var id=id_;
      var descripcion=$('#descripcionE').val();
      var id_empresa=$('#id_empresaE').val();
     
      var hayError = false;

        if($('#descripcionE').val() == '' || $('#id_empresaE').val() == '' )
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
        data: {"id_area" : id,  "descripcion":descripcion,  "id_empresa":id_empresa },
        url: '<?php echo base_url(MAN); ?>Area/Modificar_area', 
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

  function eliminararea(){

    $.ajax({
      type: 'POST',
      data: { "id_area": id_},
      url: '<?php echo base_url(MAN); ?>Area/Eliminar_area', 
      success: function(data){
        WaitingClose();
        ActualizarPagina();                
      },

      error: function(result){
        alert("OPERACION FALLIDA");
      }
    });

  }function ActualizarPagina(){ //Funcion Resfresca
  linkTo("<?php echo base_url(MAN); ?>Area/index/<?php echo $permission; ?>");

}$(function () {
      
      $('#area').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "language": {
                "lengthMenu": "Ver _MENU_ filas por página",
                "zeroRecords": "No hay registros",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrando de un total de _MAX_ registros)",
                "sSearch": "Buscar:  ",
                "oPaginate": {
                    "sNext": "Sig.",
                    "sPrevious": "Ant."
                  }
          }
      });
    });</script>
<div class="modal" id="modalAgregar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar Area</h4>
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
                        <label style="margin-top: 7px;">Descripcion: <strong style="color: #dd4b39">*</strong>: </label>
                        </div>
                        <div class="col-xs-5">
                        <input type="text" class="form-control"  id="descripcion" >
                        </div>
</div><br>
        
      </div>
      <div class="modal-footer">

        <button type="button" class="btn btn-primary" onclick="guardararea()" >Guardar</button>
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
        <h4 class="modal-title">Editar Area</h4>
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

        <button type="button" class="btn btn-primary" onclick="editararea()" >Guardar</button>
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
        <h4 class="modal-title">Eliminar Area</h4>
      </div>
      <div class="modal-body" id="cuerpoModalEditar">
       <h5>¿Desea eliminar el registro?</h5> 
      </div>
      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminararea()" >Eliminar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->