<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          Revise que todos los campos obligatorios esten seleccionados
      </div>
  </div>
</div>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Solicitud de Servicio</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
          }
          ?>
        </div><!-- /.box-header -->

        <div class="modal-body" id="modalBodyservicio">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos...
            </div>
          </div>
        </div>
        <style>
          .ui-autocomplete{
              z-index:1050;
          }
        </style>

        <div class="box-body">
          <form id="formSS" role="form" method="POST" >
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><div class="fa fa-cogs"></div> Datos del Equipo</h3>
              </div>                  

              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6">Sector <strong style="color: #dd4b39">*</strong>
                      <select  id="sector" name="sector" class="form-control">
                        <option value="-1" selected disabled>Seleccione opción</option>
                      </select>                  
                  </div>
                  <div class="col-xs-12 col-sm-6">Equipos <strong style="color: #dd4b39">*</strong>
                    <select  id="equipo" name="equipo" class="form-control equipo">
                      <option value="-1" selected disabled>Seleccione opción</option>
                    </select>
                  </div>
                </div><!-- /.row -->
                <div class="row">
                  <div class="col-xs-12 col-sm-6">Área:
                    <input type="text" id="area" name="area" class="form-control input-md" disabled />
                  </div>
                  <div class="col-xs-12 col-sm-6">Proceso:
                    <input type="text" id="proceso" name="proceso" class="form-control input-md"  disabled />
                  </div>
                  <div class="col-xs-12">Descripción: 
                    <textarea class="form-control" id="descripcion" name="" disabled></textarea>
                  </div>
                </div> <!-- /.row -->
              </div> <!-- panel-body -->                    
            </div><!-- panel-default -->
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><div class="fa fa-window-close"></div> Falla</h3>
              </div>                  

              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12"> 
                    <textarea class="form-control" id="falla" name="falla"></textarea>
                  </div>
                </div> <!-- /.row -->
              </div> <!-- panel-body -->                    
            </div><!-- panel-default -->
            <div class="row">
              <div class="col-md-4">
                <!-- <label for="btnsubirarch">Subir archivo</label><br> -->
                <input type="file" id="inputPDF" name="inputPDF" class="btn btn-primary input-md"></input>
              </div>
              <div class="col-md-6">
                <button type="button" id="btnsacarfoto" class="btn btn-primary"><i class="fa fa-camera" aria-hidden="true"></i> Sacar foto</button>
              </div>
            </div><br>
            <div class="modal-footer">
                <button type="button" id="btnSave" class="btn btn-primary">Guardar</button>
            </div> 
            </div><!-- /.nav-tabs-custom -->
          </div>
        </div> <!-- /.row -->
      </form>
      </div> <!-- box-body --> 
      </div> <!-- box -->    
    </div> <!-- col-xs-12 -->  
  </div>  <!-- row -->        
</section>

<script>

// Trae equipos llena select - Chequeado
//traer_equipo();
/** Desactivamos esta funcion para que no cargue a la primera */
function traer_equipo(){
  $('#equipo').html('');
    $.ajax({
      type: 'POST',
      data: { },
      url: 'index.php/Backlog/getequipo', //index.php/
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

// Trae Sectores y autocompleta el campo
var dataF = function () {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo MAN; ?>Sservicio/getSector",
      'success': function (data) {
        tmp = data;
        var $select = $("#sector");
        for (var i = 0; i < data.length; i++) {
          $select.append($('<option />', { value: data[i]['value'], text: data[i]['label'] }));
        }
      },
      'error' : function(data){
        console.log('Error en getSector');
        console.table(data);
      },
    });
    return tmp;
  }();
  
  $("#sector").change(function(){
      var idSector = $("#sector").val();
      getEquiSector(idSector);
  });

  //  llena select de equipos segun sector
  function getEquiSector(idSect){
    var id =  idSect;
    $("#equipo").html("");
    $("#area").val("");
    $("#proceso").val("");
    $("#descripcion").val("");
    console.log("id de sector para traer equipos: "+id);
    $.ajax({
      'data' : {id_sector : id },
      'async': true,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo MAN; ?>Sservicio/getEquipSector",
      'success': function (data) {
        console.table(data);
        // Asigna opciones al select Equipo en modal
        var opcion = "<option value='-1'>Seleccione...</option>" ;
        $('#equipo').append(opcion);
        if(data){
            for (var i = 0; i < data.length; i++) {
            var nombre = data[i]['descripcion'];
            var opcion = "<option value='"+data[i]['id_equipo']+"'>" +nombre+ "</option>" ;
            $('#equipo').append(opcion); 
          }
        }
      },
      'error' : function(data){
        console.log('Error en getEquiSector');
        console.table(data);
      },
    });
  }

// Volver al listado
$('#listado').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Sservicio/index/<?php echo $permission; ?>");
    WaitingClose();
});

// Guardado de datos y validaciones
$("#btnSave").click(function(){

  WaitingOpen('Generando Solcitud');
  var hayError = false;
  console.log(" Eqquipo: "+$('#equipo').val() +" Sector: "+ $('#sector').val());
  if($('#equipo').val() == '' || $('#sector').val() == '' || $('#equipo').val() == null || $('#sector').val() == null || $('#equipo').val() == -1 || $('#sector').val() == -1){
    hayError = true;
    WaitingClose();
  }
  if(hayError == true){
    $('#error').fadeIn('slow');
    
    return;
  }

  $('#error').fadeOut('slow');
  $('#modalservicio').modal('hide');
  var formData = new FormData($("#formSS")[0]);
  var permisos = $('#permission').val();
  console.log(formData);
  console.log(permisos);

    $.ajax({
          type: 'POST',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          url: '<?php echo MAN; ?>Sservicio/lanzarProcesoBPM',
          success: function(data){
                  WaitingClose();
                  console.log(data);
                  if (data.status == true){
                    //alert("Solicitud generada exitosamente");
                  
                    cargarView('Sservicio', 'index', permisos) ;           
                  } else{             
                      alert("Falla: "+data.msj);
                  }                   
                },
          error: function(data){
                  WaitingClose();
                  alert("Error: "+data.msj);         
              },
          dataType: 'json'
      });
});

$('#ultimo').datetimepicker({
  format: 'YYYY-MM-DD', 
  locale: 'es',
});

// // Trae equipos
// WaitingOpen("Cargando Equipos...");
// $.ajax({
//   data: { },
//   dataType: 'json',
//   url: 'index.php/Predictivo/getEquipo', 
//   type: 'POST',
// })
// .done( (data) => {
//   let opcion  = "<option value='-1'>Seleccione...</option>" ;
//   $('#equipo').append(opcion);
//   for(let i=0; i < data.length ; i++){
//     let nombre = data[i]['codigo'];
//     let opcion  = "<option value='"+data[i]['id_equipo']+"'>" +nombre+ "</option>";
//     $('#equipo').append(opcion);
//   }
// })
// .fail( () => alert("Error al traer Equipos.") )
// .always( () => WaitingClose() );
// Con equipo seleccionado llama funcion para traer sus componentes
$('#equipo').change(function(){
  WaitingOpen("Cargando datos de Equipo...");
  var id_equipo = $(this).val();
  $.ajax({
    type: 'POST',
    data: { id_equipo: id_equipo},
    dataType: 'json',
    url: '<?php echo MAN;?>Preventivo/getEquipoNuevoPrevent', 
  })
  .done( (data) => {
    console.log(data);

    if(!data) return;
    
    var fecha_ingreso = data['fecha_ingreso']; 
    var marca         = data['marca']; 
    var ubicacion     = data['ubicacion']; 
    var criterio1     = data['criterio1']; 
    var descripcion   = data['descripcion']; 
    var id_area = data['id_area']; 
    var id_proceso = data['id_proceso'];

    getArea(id_area);
    getProceso(id_proceso);

    $('#descripcion').val(descripcion);       
  })
  .fail( () => alert("Error al traer Equipos.") );
});

function getArea($id_area){
  console.log('ID_AREA: ' + $id_area);
  $.ajax({
    type: 'POST',
    data: { id_area: $id_area},
    dataType: 'json',
    url: '<?php echo MAN; ?>Area/Obtener_area'
  }).done( (data) => {

    $("#area").val(data[0].descripcion);
  })
  .fail( () => alert("Error al traer Area.") )
  .always( () => WaitingClose() );
}
function getProceso($id_proceso){
  console.log('ID_PROCESO: '+ $id_proceso);
  
  $.ajax({
    type: 'POST',
    data: { id_proceso: $id_proceso},
    dataType: 'json',
    url: '<?php echo MAN; ?>Proceso/Obtener_proceso', 
  }).done( (data) => {
    
    $("#proceso").val(data[0].descripcion);
  })
  .fail( () => alert("Error al traer Proceso.") )
  .always( () => WaitingClose() );
}

//Trae tareas y permite busqueda en el input
var dataTarea = function() {
  var tmp = null;
  $.ajax({
    'async': false,
    'type': "POST",
    'dataType': 'json',
    'url': '<?php echo MAN; ?>Preventivo/gettarea',
  })
  .done( (data) => { tmp = data } )
  .fail( () => alert("Error al traer tareas") );
  return tmp;
}();
$("#tarea").autocomplete({
  source:    dataTarea,
  delay:     500, 
  minLength: 1,
  focus: function(event, ui) {
    event.preventDefault();
    $(this).val(ui.item.label);
    $('#id_tarea').val(ui.item.value);
  },
  select: function(event, ui) {
    event.preventDefault();
    $(this).val(ui.item.label);
    $('#id_tarea').val(ui.item.value);
  },change: function(event,ui){
    $(this).val(ui.item == null ? "" : ui.item.label);
  }
});

// Trae periodo y llena select
traer_periodo();
function traer_periodo(periodoId){
  if (periodoId === undefined) {
    periodoId = null;
  }
  $('#periodo').html(""); 
  $.ajax({
    data: {periodoId:periodoId },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Calendario/getperiodo',
    success: function(data){
      var opcion = "<option value='-1'>Seleccione...</option>" ;
      $('#periodo').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre   = data[i]['descripcion'];
        var selected = (periodoId == data[i]['idperiodo']) ? 'selected' : '';
        var opcion   = "<option value='"+data[i]['idperiodo']+"' " +selected+ "'>" +nombre+ "</option>" ; 
        $('#periodo').append(opcion);                        
      }
    },
    error: function(result){  
      console.log(result);
    },
  });
}

//Habilita lectura base y alerta si el periodo es horas ó ciclos
$('#periodo').change(function(){
  let optionText = $('#periodo option:selected').text().toLowerCase();
  console.info( optionText );
  if( optionText=='horas' || optionText=='ciclos' ) { //horas=5 ciclos=6
    $('#alerta').prop('disabled', false);
    $('#lectura_base').prop('disabled', false);
  } else {
    $('#alerta').prop('disabled', 'disabled');
    $('#lectura_base').prop('disabled', 'disabled');
  }
});

// Trae unidades de tiempo y llena select
$('#unidad').html("");
$.ajax({
  type: 'POST',
  data: { },
  url: '<?php echo MAN; ?>Predictivo/getUnidTiempo', 
  success: function(data){
    var opcion  = "<option value='-1'>Seleccione...</option>" ; 
    $('#unidad').append(opcion); 
    for(var i=0; i < data.length ; i++){    
      var nombre = data[i]['unidaddescrip'];
      var opcion = "<option value='"+data[i]['id_unidad']+"'>" +nombre+ "</option>" ; 
      $('#unidad').append(opcion);                                
    }
  },
  error: function(result){
    console.log(result);
  },
  dataType: 'json'
});

// Calcula horas hombre por tiempo y unidades
function calcularHsHombre(){
  var entrada   = $('#duracion').val();
  var unidad    = $('#unidad').val();
  var operarios = $('#cantOper').val();
  var hs        = 0;
  var hsHombre  = 0;
  //minutos
  if (unidad == 1) {
    hs = entrada / 60;
  }
  // horas
  if (unidad == 2) {
    hs = entrada;
  }
  // dias
  if (unidad == 3) {
    hs = entrada * 24;
  }

  hsHombre = hs * operarios;
  hsHombre = Math.round(hsHombre * 100) / 100;
  var mens = $("<h4 name='hshombre' class='before'>HH: <span class='hh'>" + hsHombre + "</span></h4>");
  $('#dato').html(mens);
  $('#hshombre').val(hsHombre);
}

// Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
$('#duracion, #unidad, #cantOper').change(function(){
  if( $('#duracion').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!="")
    calcularHsHombre();
});

// Vuelve a la vista de listado de preventivos
function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Preventivo/index/<?php echo $permission; ?>");
    WaitingClose();
}

// Guarda Preventivo  
$("#formPreventivo").submit(function (event){   
  event.preventDefault();  

  var equipo   = $('#equipo').val();
  var tarea    = $('#tarea').val();
  var compon   = $('#componente').val();
  var periodo  = $('#periodo').val();
  var freq     = $('#cantidad').val();
  var lectbase = $('#lectura_base').val();
  var alerta   = $('#alerta').val();
  var duracion = $('#duracion').val();
  var unidad   = $('#unidad').val();
  var oper     = $('#cantOper').val();
  var hh       = $('#hshombre').val();

  if((periodo=='horas') || (periodo=='ciclos')){
    if ((lectbase < 0)||(alerta < 0)) {
      $('#error').fadeIn('slow');
    }
  }
  if ((equipo < 0)||(tarea < 0)||(periodo < 0)||(unidad < 0)||(duracion == "")||(freq == "")||(oper == "")||(hh == "" || compon < 0)) {
      $('#error').fadeIn('slow');
  }
  else{
    $('#error').fadeOut('slow');
    var formData = new FormData($("#formPreventivo")[0]);
    $.ajax({
      url:$("form").attr("action"),
      type:$("form").attr("method"),
      data:formData,
      cache:false,
      contentType:false,
      processData:false,
      success:function(respuesta){
        //alert(respuesta);
        console.log('resp prenevt: ');
        console.log(respuesta.resPrenvent);
        if (respuesta) {
          //alert("Los datos han sido guardados correctamente");
          cargarVista();
        }
        else if(respuesta==="error"){
          alert("Los datos no se han podido guardar");
        }
        else{
          //$("#msg-error").show();
          //$(".list-errors").html(respuesta);
        }
      }
    });
  }
});


function ordenaArregloDeObjetosPor(propiedad) {  
  return function(a, b) {  
    if (a[propiedad] > b[propiedad]) {  
      return 1;  
    } else if (a[propiedad] < b[propiedad]) {  
      return -1;  
    }  
    return 0;  
  }  
} 

</script>
