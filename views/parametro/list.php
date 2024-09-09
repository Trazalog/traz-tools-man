<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          Revise que todos los campos obligatorios esten completos
      </div>
  </div>
</div>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Asociar Parámetros</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row" >
            <div class="col-xs-12">
                <div class="panel panel-default">

                <div class="panel-body">
                    <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label for="equipo">Equipos <strong style="color: #dd4b39">*</strong>:</label>
                        <select id="equipo" name="equipo" class="form-control"/>
                        <input type="hidden" id="id_equipo" name="id_equipo"/>
                    </div>

                    <div class="col-xs-12 col-sm-4">
                        <label for="parametro">Parámetro <strong style="color: #dd4b39">*</strong>:</label>
                        <select id="parametro" name="parametro" class="form-control" />
                        <input type="hidden" id="id_parametro" name="id_parametro">
                    </div>

                    <div class="col-xs-12 col-sm-4">
                        <br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalOrder"><i class="fa fa-plus"> Nuevo</i></button>
                    </div>
                    </div><!-- /.row -->
                    <br>
                    <div class="row">

                    <div class="col-xs-12 col-sm-6">
                        <label for="maximo">Máximo <strong style="color: #dd4b39">*</strong>:</label>
                        <input type="text" name="maximo" id="maximo" class="form-control" placeholder="Ingrese Valor Maximo" /> 
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <label for="minimo">Mínimo <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" name="minimo" id="minimo" class="form-control" placeholder="Ingrese Valor Minimo"  /> 
                    </div>

                    <br>
                    <div class="col-xs-12">
                        <br>
                        <button type="button" class="btn btn-primary" id="addcompo"><i class="fa fa-check"> Agregar</i></button>
                    </div>
                    </div><!-- /.row -->
                </div><!-- /.panel-body -->

                </div>
            </div>
        </div><!-- /.row -->
        <table id="tablaparametros" class="table table-bordered table-hover">
            <thead>
                <tr>
                <th>Acciones</th>
                <th>Equipo</th>
                <th>Parámetro</th>
                <th>Máximo</th>
                <th>Mínimo</th>
                </tr>
            </thead>
            <tbody>
                <!-- -->
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
$(document).ready(function(event) {

    edit     = 0;  
    datos    = Array();
    var j    = 1;
    id_equip = "";
    id_param = "";
    campo4   = "";

    $('#equipo').change(function() {
        var id_equipo = $(this).val();
        console.log(id_equipo);
        id_equip=id_equipo;
        console.log(id_equip);
        
        $.ajax({
        type: 'POST',
        data: { id_equipo: id_equipo},
        url: '<?php echo MAN; ?>Parametro/getparametros',
        success: function(data){

            tabla = $('#tablaparametros').DataTable();
            tabla.clear().draw();
            for(var i=0; i < data.length ; i++){
            
            id_equipo = data[i]['id_equipo'];
            descripcion = data[i]['descripcion'];
            id_parametro = data[i]['id_parametro'];
            parametro = data[i]['paramdescrip'];
            max = data[i]['maximo'];
            min = data[i]['minimo'];

            tablaCompleta = tabla.row.add( [
                '<i class="fa fa-fw fa-pencil text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>'+
                '<i class="fa fa-fw fa-times-circle text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>',
                descripcion,
                parametro,
                max,
                min
                ] );
            tablaCompleta.node().id = id_parametro;
            tabla.draw();  
            }
        
        },
        error: function(result){
            console.log(result);
        },
        dataType: 'json'
        });
        
    });

    var equipoglob="";
    var paramglob="";

    $("#addcompo").click(function (e) {
        //var equipo = $('#equipo').val();
        var $equipo = $("select#equipo option:selected").html();
        var id_equipo= $('#equipo').val();
        var $parametro = $("select#parametro option:selected").html();
        //var compo = $('#compo').val()
        var id_parametro= $('#parametro').val();
        var maximo = $('#maximo').val()
        var minimo = $('#minimo').val()
        equipoglob = id_equipo;
        paramglob = id_parametro;
        console.log("el id del equipo es :" +id_equipo);
        console.log("el id del parametro es :" +id_parametro);
        console.log("el maximo es :" +maximo);
        console.log("el minimo es :" +minimo);
    
        // var tr = "<tr id='"+id_equipo+"'>"+
        //   "<td > <i class='fa fa-fw fa-times-circle text-light-blue' style='cursor: 'pointer' margin-left: '15px' title='Eliminar'></i> "+
        //   "<i class='fa fa-fw fa-pencil text-light-blue' style='cursor: 'pointer' margin-left: '15px' title='Editar'</i></td>"+
        //   "<td>"+$equipo +"</td>"+
        //   "<td>"+$parametro+"</td>"+
        //   "<td>"+maximo+"</td>"+
        //   "<td>"+minimo+"</td>"+
        //   "</tr>";
        
        

        var hayError = false;
        if (id_equipo >0 && id_parametro >0) {
        if (maximo !=0 && minimo!= 0) {
            //$('#tablaparametros tbody').append(tr);
            guardar_todo();
        }
        else 
        { 
            var hayError = true;
            $('#error').fadeIn('slow');
            return;
        }
        }
        else 
        {
        var hayError = true;
        $('#error').fadeIn('slow');
        return;
        }

        if(hayError == false){
        $('#error').fadeOut('slow');
        // guardar_todo();
        }

        $('#equipo').val('');
        $('#parametro').val(''); 
        $('#maximo').val(''); 
        $('#minimo').val(''); 
    });

});
// llena modal para editar
$(document).on("click",".fa-pencil",function(e){
  // agregar esto para que no se repita el evento
  e.preventDefault();
  e.stopImmediatePropagation();
  
  var id_equipo = $(this).data("id_equipo");  
  var id_parametro =$(this).data("id_parametro");
  
  $('#modalSale').modal('show');

  $.ajax({
      type: 'POST',
      data: { id_equipo:id_equipo, id_param:id_parametro},
      url: 'index.php/Parametro/editar', 
      success: function(data){    
        datos={
          'id_equipo':id_equipo,
          'codigo':data['datos'][0]['codigo'],
          'parametro':data['datos'][0]['paramdescrip'],
          'maximo':data['datos'][0]['maximo'],
          'minimo':data['datos'][0]['minimo'],
          'id_param':data['datos'][0]['id_parametro'],
        };

        $('#equ').val(datos['codigo']);
        $('#id_equipo').val(datos['id_equipo']);
        $('#id_param').val(datos['id_param']);
        $('#pa').val(datos['parametro']);
        $('#maxi').val(datos['maximo']);
        $('#mini').val(datos['minimo']);          
      },
      error: function(result){
        console.log(result);
        console.log("hola entre por el error");          
      },
      dataType: 'json'
  });
});
// guarda edicion de parametros
function guardarmodif(){
  
  var datos={                                    
    'maximo': $('#maxi').val(),
    'minimo': $('#mini').val(),
    'id_parametro': $('#id_param').val(),
    'id_equipo':$('#id_equipo').val(),
  };
  console.log(datos);
  $.ajax({
    type:"POST",
    url: "index.php/Parametro/guardarmodif",
    data:{datos:datos},
    success: function(data){
      console.table(data);
            tabla = $('#tablaparametros').DataTable();
            tabla.clear().draw();
            for(var i=0; i < data.length ; i++){
            
              id_equipo = data[i]['id_equipo'];
              descripcion = data[i]['descripcion'];
              id_parametro = data[i]['id_parametro'];
              parametro = data[i]['paramdescrip'];
              max = data[i]['maximo'];
              min = data[i]['minimo'];

              tablaCompleta = tabla.row.add( [
                  '<i class="fa fa-fw fa-pencil text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>'+
                  '<i class="fa fa-fw fa-times-circle text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>',
                  descripcion,
                  parametro,
                  max,
                  min
                ] );
              tablaCompleta.node().id = id_parametro;
              tabla.draw();  
            }
    },
    error: function(result){
      console.log(result);
      console.log("hola entre por el error");
      //cargarVista();
    },
    dataType: 'json'
  });
  //cargarVista();                                                      
}
// eliminar asociacion 
$(document).on("click", ".fa-times-circle", function(e) {       
  // agregar esto para que no se repita el evento
  e.preventDefault();
  e.stopImmediatePropagation();

  var id_equipoElim = $(this).data("id_equipo");  
  var id_parametroElim =$(this).data("id_parametro");
  $('#id_equipoElim').val(id_equipoElim);
  $('#id_parametroElim').val(id_parametroElim);
  $('#modalaviso').modal('show');
});
// elimina asociacion de parametros
function eliminar(){
  var id_equipoElim = $('#id_equipoElim').val();
  var id_parametroElim = $('#id_parametroElim').val();
  $.ajax({
      type: 'POST',
      data: { id_equipoElim:id_equipoElim, id_parametroElim:id_parametroElim},
      url: 'index.php/Parametro/eliminar', 
      success: function(data){    
              tabla = $('#tablaparametros').DataTable();
              tabla.clear().draw();
              
              for(var i=0; i < data.length ; i++){
              
                id_equipo = data[i]['id_equipo'];
                descripcion = data[i]['descripcion'];
                id_parametro = data[i]['id_parametro'];
                parametro = data[i]['paramdescrip'];
                max = data[i]['maximo'];
                min = data[i]['minimo'];

                tablaCompleta = tabla.row.add( [
                    '<i class="fa fa-fw fa-pencil text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>'+
                    '<i class="fa fa-fw fa-times-circle text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>',
                    descripcion,
                    parametro,
                    max,
                    min
                  ] );
                tablaCompleta.node().id = id_parametro;
                tabla.draw();  
              }
      },
      error: function(result){
        console.log(result);
        console.log("hola entre por el error");          
      },
      dataType: 'json'
  });
}
// funcion guarda las nuevas asociaciones de equipo/parametro
function guardar_todo(){    
  
    var parametros = {
        'id_equipo': $('#equipo').val(),
        'id_parametro': $('#parametro').val(),
        'maximo': $('#maximo').val(),
        'minimo': $('#minimo').val(),
    };  
 
    $.ajax({
        type: 'POST',
        data: {data:parametros},
        url: '<?php echo MAN; ?>Parametro/guardar_todo',
        success: function(data){
            tabla = $('#tablaparametros').DataTable();
            tabla.clear().draw();
            
            for(var i=0; i < data.length ; i++){
                
                id_equipo = data[i]['id_equipo'];
                descripcion = data[i]['descripcion'];
                id_parametro = data[i]['id_parametro'];
                parametro = data[i]['paramdescrip'];
                max = data[i]['maximo'];
                min = data[i]['minimo'];

                tablaCompleta = tabla.row.add( [
                    '<i class="fa fa-fw fa-pencil text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>'+
                    '<i class="fa fa-fw fa-times-circle text-light-blue " style="cursor: pointer; margin-left: 15px;" data-id_equipo="'+id_equipo+'" data-id_parametro= "'+id_parametro +'"></i>',
                    descripcion,
                    parametro,
                    max,
                    min
                    ] );
                tablaCompleta.node().id = id_parametro;
                tabla.draw();  
            }
        },
        error: function(result){
        console.log("entro por el error");
        console.log(result);
        },
        dataType: 'json'
    });
}



// ttrae todos los equipos ok
traer_equipo();
function traer_equipo(){
    $.ajax({
        type: 'POST',
        data: { },
        url: '<?php echo MAN; ?>Parametro/getequipo',
        success: function(data){
            var opcion  = "<option value='-1'>Seleccione...</option>" ; 
            $('#equipo').append(opcion); 
            for(var i=0; i < data.length ; i++) 
            {    
                var nombre = data[i]['codigo'];
                var opcion  = "<option value='"+data[i]['id_equipo']+"'>" +nombre+ "</option>" ; 
                $('#equipo').append(opcion); 
            }
        },
        error: function(result){
            console.log(result);
        },
        dataType: 'json'
    });
}


traer_parametro();
function traer_parametro(){
  $('#parametro').html(""); 
  $.ajax({
    type: 'POST',
    data: {},
    url: '<?php echo MAN; ?>Parametro/traerparametro',
    success: function(data){
      console.log(data);
      //$('#parametro option').remove();
      var opcion  = "<option value='-1'>Seleccione...</option>" ; 
      $('#parametro').append(opcion); 
      for(var i=0; i < data.length ; i++){   
        //console.log( data[i]);
        var nombre = data[i]['paramdescrip']; 
        var opcion  = "<option value='"+data[i]['paramId']+"'>" +nombre+ "</option>" ; 
        $('#parametro').append(opcion);                
      }
    },
    error: function(result){
      console.log(result);
    },
    dataType: 'json'
  });
}
// funciona OK (BTN NUEVO)
function guardar(){ 
  var parametros = {
    'paramdescrip': $('#descripcion1').val(),
  };
  console.log(parametros);

  $.ajax({
    type: 'POST',
    data: {data:parametros},
    url: '<?php echo MAN; ?>Parametro/guardar', 
    success: function(data){
      hecho();
      console.log(data);
      $('#descripcion1').val("");
      if(data > 0){  
        var texto = '<option value="'+data+'">'+ parametros.paramdescrip +'</option>';
        $('#parametro').append(texto);
      }
    },
    error: function(result){
      console.log("entro por el error");
      console.log(result);
    },
    dataType: 'json'
  });
}




</script>

<!-- Modal -->
<div class="modal fade" id="modalOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Parametro</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <div role="tabpanel" class="tab-pane">
              <div class="form-group">
                <label for="descripcion1">Descripcion: <strong style="color: #dd4b39">*</strong></label>
                <input type="text" id="descripcion1" name="descripcion1" class="form-control" placeholder="Ingrese Descripcion">
              </div>
            </div>
          </div>
        </div>
      </div>  <!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button> 
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardar()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->


<!-- Modal Modificar Parametros -->
<div class="modal fade" id="modalSale" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerro()"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalActionSale" class="fa fa-fw fa-pencil text-light-blue"></span> Modificar Parametros</h4> 
      </div>

      <div class="modal-body" id="modalBodySale">
        <div class="row" >
          <div class="col-xs-12">
            <br>
            <div class="col-xs-3">Equipo
                <input type="text"   class="form-control input-md" id="equ"  name="equ"  disabled>
                <input type="hidden" class="form-control input-md" id="id_equipo"  name="id_equipo"  >
            </div>
            <div class="col-xs-3">Parametro
              <input type="text"   class="form-control input-md" id="pa"  name="pa"  disabled>
              <input type="hidden" class="form-control input-md" id="id_param"  name="id_param" >
            </div>
            <div class="col-xs-3">Maximo
              <input type="text"   class="form-control input-md" id="maxi"  name="maxi" >
            </div>
            <div class="col-xs-3">Minimo
              <input type="text"   class="form-control input-md" id="mini"  name="mini">
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerro()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal" onclick="guardarmodif()">Guardar</button>
      </div>

    </div>
  </div>
</div>


<!-- Modal aviso eliminar -->
<div class="modal fade" id="modalaviso">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" ><span class="fa fa-fw fa-times-circle" style="color:#A4A4A4"></span>  Eliminar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" >&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <center>
        <h4><p>¿ DESEA ELIMINAR ASOCIACIÓN ?</p></h4>
        </center>
      </div>     

      <input type="text" class="hidden" id="id_equipoElim"/>
      <input type="text" class="hidden" id="id_parametroElim"/> 

      <div class="modal-footer">
        <center>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminar()">SI</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
        </center>
      </div>
    </div>
  </div>
</div>