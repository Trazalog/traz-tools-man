<input type="hidden" id="permission" value="<?php echo $permission;?>">  
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          Revise que todos los campos obligatorios esten completos
      </div>
  </div>
</div>
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title ">Carga de Lectura</h2>
        </div><!-- /.box-header -->
        <div class="box-body">
          <!-- form  -->
          <form  id="form_order" action="" accept-charset="utf-8">
            <!-- nº factura  -->
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-6">
                <label for="">Equipos <strong style="color: #dd4b39">*</strong>:</label>
                <!-- <select id="equipo" name="equipo" class="form-control"  /> -->
                <input type="text" class="form-control" id="equipo" name="id_equipo"/>
                <input type="hidden" id="id_equipo" name="id_equipo"/>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6">
                <label for="">Parametro <strong style="color: #dd4b39">*</strong>:</label>
                <select id="parametro" name="parametro" class="form-control"  />
                <input type="hidden" id="id_parametro" name="id_parametro"/>
              </div> <br>              
              <div class="col-xs-12 col-sm-6 col-md-6">
                <label for="">Fecha <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" name="fecha" id="fecha" class="form-control" />
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6">
                <label for="">Valor <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" name="valor" id="valor" class="form-control" placeholder="Ingrese Valor"  />
              </div>
            </div><br>
            <div class="row">
              <div class="col-xs-12">
              <?php
                 if (strpos($permission,'Add') !== false) {
                  echo '<button type="button" class="btn btn-primary" id="add"><i class="fa fa-check"></i>Agregar</button>';     
                }
              ?>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <br><br>
                <table class="table table-bordered" id="tablaparametro"> 
                  <thead>
                    <tr>                      
                      <th></th>
                      <th>Equipo</th>
                      <th>Parámetro</th>                      
                      <th>Fecha</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- -->
                  </tbody>
                </table> 
              </div>
            </div>
            <div class="modal-footer">                   
              <button  type="button" class="btn btn-primary" style="width: 100px; margin-top: 10px;"  data-dismiss="modal"  onclick="regresa()">Limpiar</button>
            </div>  <!-- /.modal footer -->
          </form>
        </div>
      </div>
    </div>
  </div>  <!-- /.modal fade -->
           



<script>

// autocompletar equipos   
autocompEquipo();
function autocompEquipo() {  
  $( "#equipo" ).autocomplete({
    source: "<?php echo base_url(MAN); ?>Lectura/getEquipo",
    minLength: 1,
    select: function( event, ui ) {        
            $("#equipo").val(ui.item.label);
            $("#id_equipo").val(ui.item.id);     
            llenarSelect();
            getLecturasCargadas(); 
            }
  });  
}

// llena select de parametros
function llenarSelect(){
  $("#valor").val("");
  var selector = $('#parametro');
  var equipoId = $("#id_equipo").val();    
  $.ajax({
      async: true,
      global: false,
      url: "<?php echo base_url(MAN); ?>Lectura/getParametrosAsoc",
      type: 'POST',
      dataType : "json",
      data: {"equipoId" : equipoId },
      success: function (result) {
                
                selector.html('');
                //if(result!=null){
                  var opcion  = "<option value='-1'>Seleccione Parámetro...</option>" ; 
                  selector.append(opcion); 
                  for(var i=0; i < result.length ; i++){    
                    var parametro = result[i]['param'];
                    var opcion  = "<option value='"+result[i]['id']+"'>" + parametro + "</option>" ; 
                    selector.append(opcion); 
                  }
                  //selector.val(idEstablecimiento);
                // }
                // else{
                //   selector.append("<option value='-1'>No hay parametros asociados</option>");
                // }
              },
      error : function (result){
                console.log('Funcion: getParametrosAsoc ERROR');
                alert('no hay parametros asociados');
            }
  });
}

$('#fecha').daterangepicker({
    "autoApply": true,
    "singleDatePicker": true,
    "timePicker": true,
    "toggleActive":false,
    "todayHighlight":false,    
    "locale": {
              "format":'YYYY/MM/DD h:mm:ss',
              "applyLabel": "Aplicar",
              "cancelLabel": "Cancelar"
              //format: 'MM/DD/YYYY h:mm:ss'
            }
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD hh:mm:ss') + ' to ' + end.format('YYYY-MM-DD hh:mm:ss') + ' (predefined range: ' + label + ')');
});

// trae lecturas cargadas al cambiar de fecha
$('#fecha').on('change', function(){
  $("#tablaparametro tr").remove();
  getLecturasCargadas();
});

// dibuja en la tabla las lecturas cargadas en esa fecha
function getLecturasCargadas(){ 
  
  var idequipo = $("#id_equipo").val(); 
  var $equipo = $("#equipo").val();  
  var $fecha = $('#fecha').val();
  $.ajax({
      type: 'POST',
      data: { fecha: $fecha,
              idequipo: idequipo},
      url: '<?php echo base_url(MAN); ?>Lectura/getLecturasCargadas', 
      success: function(data){    
              var tabla = $("#tablaparametro").DataTable();
              tabla.clear().draw();              
              for(i = 0; i < data.length; i++) {
                var valor = data[i]['valor'];
                var id_insercion = data[i]['id'];
                var parametro = data[i]['paramdescrip']; 
                tabla.row.add( [
                  "<td ><i class='fa fa-fw fa-times-circle' style='color: #A4A4A4' cursor: 'pointer' margin-left: '15px' title='Eliminar' data-toggle='modal' data-target='#modalaviso' data-id-isercion='"+id_insercion+"'></td>",
                  $equipo,      
                  parametro,
                  $fecha,
                  valor
                  ]
                ).node().id = id_insercion;   
                tabla.draw();
              }
      },
      error: function(result){
        console.log(result);
      },
      dataType: 'json'
    });  
}

// guarda lectura nueva 
$("#add").click(function (e) {
  
  var id_equipo    = $('#id_equipo').val();      
  var parametro    = $('#parametro').val();   
  var fecha        = $('#fecha').val(); 
  var valor        = $('#valor').val(); 

  if ((id_equipo == "")||(parametro == -1)||(fecha == "")||(valor == "")) {
    $('#error').show();
    return;
  } else {
    $('#error').hide();
    $.ajax({
      type: 'POST',
      data: {id_equipo:id_equipo, 
              id_parametro:parametro, 
              fecha:fecha, 
              valor:valor },
      url: '<?php echo base_url(MAN); ?>Lectura/guardar_lectura', 
      success: function(data){
                console.log(data);
                console.log("lectura guarda con exito");                
                getLecturasCargadas();              
                $('#fecha').val('');
                $('#valor').val('');
              },
      error: function(result){
        console.log(result);
      },
      dataType: 'json'
    });
  }    
});

// levanta modal advertencia borrado y guarda el id de registro en tabla
$(document).on("click",".fa-times-circle",function(){
  //var idInserc = $(this).parents('tr').attr('id');
  var idInserc = $(this).data('id-isercion');
  //alert(idInserc);    
  $('#id_lectura').val(idInserc); 
});  

function deleteLectura(){
  var id = $('#id_lectura').val();
  $.ajax({
        type: 'POST',
        data: {id: id},
        url: '<?php echo base_url(MAN); ?>Lectura/deleteLectura', 
        success: function(data){
          getLecturasCargadas();
        },
        error: function(result){
          console.log("Error en borrado de Lectura...");
          console.log(result);
        },
        dataType: 'json'
      });
}

function regresa(){
 linkTo("<?php echo base_url(MAN); ?>Lectura/index/<?php echo $permission; ?>");
}

// datatable
$('#tablaparametro').DataTable({
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


<div class="modal fade" id="modalaviso">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Eliminar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" >&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <center>
        <h4><p>¿ DESEA ELIMINAR LECTURA ?</p></h4>
        </center>
        <input type="hidden" id="id_lectura" name="id_lectura"/>
      </div>
      <div class="modal-footer">
        <center>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="deleteLectura()">SI</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
        </center>
      </div>
    </div>
  </div>
</div>