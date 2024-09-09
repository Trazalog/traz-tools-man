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
        <form id="formPredictivo" role="form" action="<?php echo MAN; ?>Predictivo/guardar_predictivo" method="POST" >
          <div class="box-header">
          <h3 class="box-title">Programación Predictivo</h3>
          <?php
            if (strpos($permission,'Add') !== false) {
              echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
            }
            ?>
          </div><!-- /.box-header -->
          
          <div class="box-body">            
              
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del equipo </h3>
                </div>

                <div class="panel-body">
                  <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <label for="idSector">Sector <strong style="color: #dd4b39">*</strong></label>
                        <input type="text" class="form-control buscSector" placeholder="Buscar Sector..." id="buscSector" name="buscSector">
                        <input type="text" class="hidden idSector" id="idSector" name="idSector">
                    </div>
                    <div class="col-xs-12 col-sm-6 com-md-4">
                      <label for="equipo">Equipos <strong style="color: #dd4b39">*</strong></label>
                      <select  id="equipo" name="equipo" class="form-control equipo">
                        <option value="-1" selected disabled>Seleccione opción</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12 col-sm-6 com-md-4">
                      <label for="fecha_ingreso">Fecha:</label>
                      <input type="text" id="fecha_ingreso"  name="fecha_ingreso" class="form-control input-md" disabled />
                    </div>
                    <div class="col-xs-12 col-sm-6 com-md-4">
                      <label for="marca">Marca:</label>
                      <input type="text" id="marcadesc"  name="marcadesc" class="form-control input-md"  disabled />
                      <input type="hidden" id="marca"  name="marca" class="form-control input-md"  disabled />
                    </div>
                    <div class="col-xs-12 col-sm-6 com-md-4">
                      <label for="ubicacion">Ubicacion:</label>
                      <input type="text" id="ubicacion"  name="ubicacion" class="form-control input-md" disabled/>
                    </div>
                    <div class="col-xs-12">
                      <label for="descripcion">Descripcion: </label>
                      <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea>
                    </div> 
                  </div>
                </div>
              </div>

              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4 class="panel-title"><span class="fa fa-building-o"></span> Programación</h4>
                </div>

                <div class="panel-body"> 
                  <div class="row"> 
                    <div class="col-xs-12 col-sm-6">Tarea <strong style="color: #dd4b39">*</strong>:
                      <input type="text" id="tarea" name="tarea" class="form-control" placeholder="Buscar Tarea...">
                      <input type="hidden" id="id_tarea" name="id_tarea">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="vfecha">Fecha:</label>
                      <input type="text" class="datepicker form-control fecha" id="fecha" name="vfecha" value="<?php echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>" size="27"/>
                    </div> 
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="periodo">Periodo:                       </label>
                      <select id="periodo" name="periodo" class=" selectpicker form-control">
                      </select>
                    </div> 
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="cantidad">Frecuencia <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" class="form-control" id="cantidad" name="cantidad" onkeypress="return valideKey(event);" placeholder="Ingrese valor..."/>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="duracion">Duración <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" class="form-control" id="duracion" onkeypress="return valideKey(event);" name="duracion" placeholder="Ingrese valor..."/>
                    </div> 
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="unidad">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                      <select  id="unidad" name="unidad" class="form-control" />
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="cantOper">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" class="form-control" id="cantOper" name="cantOper" onkeypress="return valideKey(event);" placeholder="Ingrese valor..."/>
                    </div>
                    <div class="col-xs-12" id="dato" name="" style="margin-top: 19px;"></div>
                    <input type="hidden" name="hshombre" id="hshombre">                
                    <div class="col-xs-12" id="dato"></div> 
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-12">
                  <div class="nav-tabs-custom">
                    <!--tabs -->
                    <ul class="nav nav-tabs" role="tablist">                
                      <li role="presentation" class="active"><a href="#herramin" aria-controls="profile" role="tab" data-toggle="tab">Herramientas</a></li>
                      <li role="presentation"><a href="#insum" aria-controls="messages" role="tab" data-toggle="tab">Insumos</a></li>
                      <li role="presentation"><a href="#adj" aria-controls="messages" role="tab" data-toggle="tab">Adjunto</a></li>                        
                    </ul>
                    <!-- /tabs -->

                    <!-- Tab panes -->
                    <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active" id="herramin">
                        <div class="row">
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="herramienta">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="herramienta"  name="" class="form-control" placeholder="Buscar Código..."/>
                            <input type="hidden" id="id_herramienta" name="id_herramienta">
                          </div>                          
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="marcaherram">Marca <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="marcaherram"  name="" class="form-control" />
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="descripcionherram">Descripcion <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="descripcionherram"  name="" class="form-control" />
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="cantidadherram">Cantidad <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="cantidadherram"  name="" class="form-control" onkeypress="return valideKey(event);" placeholder="Ingrese Cantidad..." />
                          </div>
                          <br>
                          <div class="col-xs-12">
                            <label></label> 
                            <br>
                            <button type="button" class="btn btn-primary" id="agregarherr"><i class="fa fa-check">Agregar</i></button>
                          </div>
                        </div><!-- /.row -->
                        <div class="row">
                          <div class="col-xs-12">
                            <br>
                            <table class="table table-bordered" id="tablaherramienta"> 
                              <thead>
                                <tr>                      
                                  <th>Acciones</th>
                                  <th>Código</th>
                                  <th>Marca</th>
                                  <th>Descripcion</th>
                                  <th>Cantidad</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>  
                          </div>
                        </div><!-- /.row -->
                      </div> <!-- /.tabpanel #herramin-->

                      <div role="tabpanel" class="tab-pane" id="insum">
                        <div class="row">
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="insumo">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="insumo" name="insumo" class="form-control" placeholder="Buscar Código..."/>
                            <input type="hidden" id="id_insumo" name="">
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="">Descripcion:</label>
                            <input type="text" id="descript"  name="" class="form-control" />
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="cant">Cantidad <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="cant"  name="" class="form-control" onkeypress="return valideKey(event);" placeholder="Ingrese Cantidad..."/>
                          </div>
                        </div><!-- /.row -->
                        <div class="row">
                          <div class="col-xs-12">
                            <br>
                            <button type="button" class="btn btn-primary" id="agregarins"><i class="fa fa-check">Agregar</i></button>
                          </div>
                        </div><!-- /.row -->
                        <div class="row">
                          <div class="col-xs-12">
                            <table class="table table-bordered" id="tablainsumo"> 
                              <thead>
                                <tr>                           
                                  <th>Acciones</th>
                                  <th>Código</th>
                                  <th>Descripcion</th>
                                  <th>Cantidad</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>  
                          </div>
                        </div><!-- /.row -->
                      </div><!--/#insum -->

                      <div role="tabpanel" class="tab-pane" id="adj">
                        <div class="row">
                          <div class="col-xs-12">
                            <input id="inputPDF" name="inputPDF" type="file" class="form-control input-md">
                            <style type="text/css">
                              #inputPDF {
                                padding-bottom: 40px;
                              }
                            </style>
                          </div> 
                        </div><!-- /.row -->
                      </div> <!-- /.tab-pane #adj -->
                    </div>  <!-- tab-content -->

                    <!-- <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>  -->
                  </div><!-- /.nav-tabs-custom -->
                </div>
              </div>
            
          </div><!-- /.box-body -->

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form> <!--cierre form-->
      </div>
    </div>
  </div>
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

// impide que se vya la pantalla al apretar enter
$(document).ready(function() {
    $("#formPredictivo input").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
});

// Trae Sectores y autocompleta el campo
var dataF = function () {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo MAN; ?>Sservicio/getSector",
      success: function (data) {
        tmp = data;
      }
    });
    return tmp;
  }();  
  $(".buscSector").autocomplete({
    source: dataF,
    delay: 100,
    minLength: 1,
    /*
    focus: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      // manually update the textbox
      $(this).val(ui.item.label);
    },
    */
    change: function(event,ui){
      // prevent autocomplete from updating the textbox
      console.log('Change');
      event.preventDefault();
      console.log(ui.item);      
      if(ui.item === null){
        $("#equipo").html('<option value="-1" disabled selected>Seleccione opción</option>');
        $("#idSector").val('');
        notificar("Alerta","Debe seleccionar un Sector","warning");
      }else{
        $("#idSector").val(ui.item.value);
        $(this).val(ui.item.label);
        // guardo el id de sector
        var idSect =  $("#idSector").val();
        if(idSect && idSect != '')
          getEquiSector(idSect);
        else
          notificar("Alerta","Debe seleccionar un sector","warning");
      }
    },
    select: function(event, ui) {
      // prevent autocomplete from updating the textbox
      console.log('Select');
      event.preventDefault();
      if(ui.item === null){
        $("#equipo").html('<option value="-1" disabled selected>Seleccione opción</option>');
        $("#idSector").val('');
      }else{
        $("#idSector").val(ui.item.value);
        $(this).val(ui.item.label);
        // guardo el id de sector
        var idSect =  $("#idSector").val();
        getEquiSector(idSect);
      }
    },
  }); 
  //  llena select de equipos segun sector
function getEquiSector(idSect){
    wo();
    var id =  idSect;
    $("#fecha_ingreso").val("");
    $("#marcadesc").val("");
    $("#ubicacion").val("");
    $("#descripcion").val("");
    $("#componente").html("<option value='-1'>Seleccione..</option>");
    console.log("id de sector para traer equipos: "+id);
    $.ajax({
        'data' : {id_sector : id },
        'async': true,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "<?php echo MAN; ?>Sservicio/getEquipSector",
        success: function (data) {
            if(data !== null){
                // Asigna opciones al select Equipo en modal
                var $select = $("#equipo");
                $select.empty();
                $select.html('<option value="-1" disabled selected>Seleccione opción</option>');
                for (var i = 0; i < data.length; i++) {
                    $select.append($('<option />', { value: data[i].id_equipo, text: data[i].descripcion }));
                }
            }else{
                notificar('Alerta','No se encontraron equipos para este sector','warning');
            }
        },
        error: function(data){
            console.log('Error en getEquiSector');
            console.table(data);
        },
        complete: () => wc()
    });
}

$("#formPredictivo").submit(function (event){   
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
    if ((equipo < 0)||(tarea < 0)||(periodo < 0)||(unidad < 0)||(duracion == "")||(freq == "")||(oper == "")||(hh == "")) {
        $('#error').fadeIn('slow');
    }else{
        $('#error').fadeOut('slow');
        var formData = new FormData($("#formPredictivo")[0]);
        wo();
        $.ajax({
            url:$("form").attr("action"),
            type:$("form").attr("method"),
            data:formData,
            cache:false,
            contentType:false,
            processData:false,
            success: function (respuesta){
                if (respuesta) {
                    confRefresh(cargarVista,'',"Los datos han sido guardados correctamente");
                }else if(respuesta==="error"){
                    error("Error","Los datos no se han podido guardar");
                }
            },
            error: function (result){
                error("Error","Los datos no se han podido guardar");
                console.log(result);
            },
            complete: () => wc()
        });
    }
});

$('#listado').click( function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Predictivo/index/<?php echo $permission; ?>", function() {
      wc();
    });
});

$(".datepicker").datepicker({
    
    changeMonth: true,
    changeYear: true
});
// Trae info de equipos por ID - Chequeado 
$('#equipo').change(function(){ 
    wo();      
    var id_equipo = $(this).val();
    $.ajax({
        type: 'POST',
        data: { id_equipo: id_equipo},
        url: '<?php echo MAN; ?>Predictivo/getInfoEquipo',
        dataType: 'json',
        success: function(data){    
            console.log(data);                     
            var fecha_ingreso = data[0]['fecha_ingreso']; 
            var marca = data[0]['marca'];
            var marcadesc = data[0]['marcadescrip']; 
            var ubicacion = data[0]['ubicacion']; 
            var criterio1 = data[0]['criterio1']; 
            var descripcion = data[0]['descripcion']; 
            $('#fecha_ingreso').val(fecha_ingreso);       
            $('#marca').val(marca);   
            $('#descripcion').val(descripcion);       
            $('#ubicacion').val(ubicacion);
            $('#marcadesc').val(marcadesc);
        }, 
        error: function(result){
            error();
            console.log(result);
        },
        complete: () => wc()
    });    
});

//Trae tareas y permite busqueda en el input
var dataTarea = function() {
  var tmp = null;
  $.ajax({
    'async': false,
    'type': "POST",
    'dataType': 'json',
    'url': '<?php echo MAN; ?>Preventivo/gettarea',
    success: function(data){
      tmp = data;
    },
    error: function(result){
        error('Error','Error al traer tareas');
        console.log(result);
    }
  });
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
        $(this).val((ui.item ? ui.item.label : ""));
      }
});


// Trae unidades de tiempo - Chequeado
$(function(){  
    $.ajax({
        type: 'POST',
        data: { },
        url: '<?php echo MAN; ?>Preventivo/getUnidTiempo',
        dataType: 'json',
        success: function(data){
            var opcion  = "<option value='-1'>Seleccione...</option>" ; 
            $('#unidad').append(opcion); 
            for(var i=0; i < data.length ; i++){    
                    var nombre = data[i]['unidaddescrip'];
                    var opcion  = "<option value='"+data[i]['id_unidad']+"'>" +nombre+ "</option>" ; 
                $('#unidad').append(opcion);                                
            }
        },
        error: function(result){
            console.log(result);
        },
    });
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
        error('Error','Error al traer periodo'); 
      console.log(result);
    },
  });
}

// Calcula horas hombre por tiempo y unidades
function calcularHsHombre(){
  
  var entrada = $('#duracion').val();
  var unidad = $('#unidad').val();
  var operarios = $('#cantOper').val();
  var hs = 0;
  var hsHombre = 0;
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
  var mens=$("<h4>HH: <span class='hh'>" + hsHombre + "</span></h4>"); 
  $('#dato').html(mens);
  $('#hshombre').val(hsHombre);
}

// Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
$('#duracion, #unidad, #cantOper').change(function(){
  if( $('#duracion').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!="")
    calcularHsHombre();
});


// Carga Lista predicitivos - Chequeado
function cargarVista(){
  wo();
  $('#content').empty();
  $("#content").load("<?php echo MAN; ?>Predictivo/index/<?php echo $permission; ?>", () => wc());
}

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

////// HERRAMIENTAS //////
//Trae herramientas
var dataHerramientas = function() {
var tmp = null;
$.ajax({
    'async': false,
    'type': "POST",
    'dataType': 'json',
    'url': '<?php echo MAN; ?>Preventivo/getHerramientasB',
    success: function(data){
    tmp = data;
    },
    error: function(result){
    error('Error',"Error al traer Herramientas");
    console.log(result);
    }
})
return tmp;
}();

  // data busqueda por codigo de herramientas
  function dataCodigoHerr(request, response) {
    function hasMatch(s) {
      return s.toLowerCase().indexOf(request.term.toLowerCase())!==-1;
    }
    var i, l, obj, matches = [];

    if (request.term==="") {
      response([]);
      return;
    }
    
    //ordeno por codigo de herramientas
    dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("codigo"));

    for  (i = 0, l = dataHerramientas.length; i<l; i++) {
      obj = dataHerramientas[i];
      if (hasMatch(obj.codigo)) {
        matches.push(obj);
      }
    }
    response(matches);
  }
  // data busqueda por marca de herramientas
  function dataMarcaHerr(request, response) {
    function hasMatch(s) {
      return s.toLowerCase().indexOf(request.term.toLowerCase())!==-1;
    }
    var i, l, obj, matches = [];

    if (request.term==="") {
      response([]);
      return;
    }

    //ordeno por marca de herramientas
    dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("marca"));

    for  (i = 0, l = dataHerramientas.length; i<l; i++) {
      obj = dataHerramientas[i];
      if (hasMatch(obj.marca)) {
        matches.push(obj);
      }
    }
    response(matches);
  }


  //busqueda por marcas de herramientas
  $("#herramienta").autocomplete({
    source:    dataCodigoHerr,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.codigo);
      $('#id_herramienta').val(ui.item.value);
      $('#marcaherram').val(ui.item.marca);
      $('#descripcionherram').val(ui.item.label);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.codigo);
      $('#id_herramienta').val(ui.item.value);
      $('#marcaherram').val(ui.item.marca);
      $('#descripcionherram').val(ui.item.label);
    }
  })
  //muestro marca en listado de resultados
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
    .append( "<a>" + item.codigo + "</a>" )
    .appendTo( ul );
  };

  //busqueda por marcas de herramientas
  $("#marcaherram").autocomplete({
    source:    dataMarcaHerr,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.marca);
      $('#id_herramienta').val(ui.item.value);
      $('#herramienta').val(ui.item.codigo);
      $('#descripcionherram').val(ui.item.label);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.marca);
      $('#id_herramienta').val(ui.item.value);
      $('#herramienta').val(ui.item.codigo);
      $('#descripcionherram').val(ui.item.label);
    },
  })
  //muestro marca en listado de resultados
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
    .append( "<a>" + item.marca + "</a>" )
    .appendTo( ul );
  };

  //busqueda por descripcion de herramientas
  $("#descripcionherram").autocomplete({
    source:    dataHerramientas,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_herramienta').val(ui.item.value);
      $('#herramienta').val(ui.item.codigo);
      $('#marcaherram').val(ui.item.marca);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_herramienta').val(ui.item.value);
      $('#herramienta').val(ui.item.codigo);
      $('#marcaherram').val(ui.item.marca);
    },
  });

  // Agrega herramientas a la tabla - Chequeado
  var nrofila = 0;  // hace cada fila unica
  $("#agregarherr").click(function (e) {
    // FALTA HACER VALIDACION
    var id_her            = $('#id_herramienta').val();
    var herramienta       = $("#herramienta").val(); 
    var marcaherram       = $('#marcaherram').val();
    var descripcionherram = $('#descripcionherram').val();
    var cantidadherram    = $('#cantidadherram').val();
    
    nrofila = nrofila + 1;
    var tr = "<tr id='"+id_her+"' data-nrofila='"+nrofila+"'>"+
                "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
                "<td class='herr'>"+herramienta+"</td>"+
                "<td class='marca'>"+marcaherram+"</td>"+
                "<td class='descrip'>"+descripcionherram+"</td>"+
                "<td class='cant'>"+cantidadherram+"</td>"+ 
                // guardo id de herram y cantidades
                "<input type='hidden' name='id_her["+nrofila+"]' value='"+id_her+"'>" +                
                "<input type='hidden' name='cant_herr["+nrofila+"]' value='"+cantidadherram+"'>" +
              "</tr>";
    if(id_her > 0 && cantidadherram > 0){
      $('#tablaherramienta tbody').append(tr);
    }
    else{
      return;
    } 

    $(document).on("click",".elirow",function(){
      var parent = $(this).closest('tr');
      $(parent).remove();
    });

    $('#herramienta').val('');
    $('#marcaherram').val(''); 
    $('#descripcionherram').val(''); 
    $('#cantidadherram').val('');        
  });
////// HERRAMIENTAS //////

////// INSUMOS //////

  //Trae insumos
  var dataInsumos = function() {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'dataType': 'json',
      'url': '<?php echo MAN; ?>Preventivo/getinsumo',
      success: function(data){
        tmp = data;
      },
      error: function(result){
        error('Error',"Error al traer Insumos");
        console.log(result);
      }
    });
    return tmp;
  }();

  // data busqueda por codigo de herramientas
  function dataCodigoInsumo(request, response) {
    function hasMatch(s) {
      return s.toLowerCase().indexOf(request.term.toLowerCase())!==-1;
    }
    var i, l, obj, matches = [];

    if (request.term==="") {
      response([]);
      return;
    }

    //ordeno por codigo de herramientas
    dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("codigo"));

    for  (i = 0, l = dataInsumos.length; i<l; i++) {
      obj = dataInsumos[i];
      if (hasMatch(obj.codigo)) {
        matches.push(obj);
      }
    }
    response(matches);
  }


  //busqueda por marcas de herramientas
  $("#insumo").autocomplete({
    source:    dataCodigoInsumo,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.codigo);
      $('#id_insumo').val(ui.item.value);
      $('#descript').val(ui.item.label);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.codigo);
      $('#id_insumo').val(ui.item.value);
      $('#descript').val(ui.item.label);
    },
  })
  //muestro marca en listado de resultados
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
    .append( "<a>" + item.codigo + "</a>" )
    .appendTo( ul );
  };

  //busqueda por descripcion de herramientas
  $("#descript").autocomplete({
    source:    dataInsumos,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_insumo').val(ui.item.value);
      $('#insumo').val(ui.item.codigo);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_herramienta').val(ui.item.value);
      $('#herramienta').val(ui.item.codigo);
      $('#marcaherram').val(ui.item.marca);
    },
  });

  // Agrega insumos a la tabla 
  var nrofilaIns = 0; 
  $("#agregarins").click(function (e) {
      var id_insumo = $('#id_insumo').val(); 
      var $insumo   = $("#insumo").val();
      var descript = $('#descript').val();
      var cant = $('#cant').val();     
      console.log("El id  del insumo");
      console.log(id_insumo);
      var hayError = false;
      var tr = "<tr id='"+id_insumo+"'>"+
                    "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
                    "<td>"+$insumo+"</td>"+
                    "<td>"+descript+"</td>"+
                    "<td>"+cant+"</td>"+

                    // guardo id de insumos y cantidades
                    "<input type='hidden' name='id_insumo["+nrofilaIns+"]' value='"+id_insumo+"'>" +
                    "<input type='hidden' name='cant_insumo["+nrofilaIns+"]' value='"+cant+"'>" +
                "</tr>";
      nrofilaIns = nrofilaIns + 1;          
      if(id_insumo > 0 && cant > 0){
        $('#tablainsumo tbody').append(tr); 
      }
      else {
            return;
      }    

      $(document).on("click",".elirow",function(){
        var parent = $(this).closest('tr');
        $(parent).remove();
      });
      
      $('#insumo').val('');
      $('#descript').val(''); 
      $('#cant').val(''); 
  });
////// INSUMOS //////

function limpiar(){
  
  $("#equipo").val("");
  $("#tarea").val("");
  $("#fecha").val("");
  $("#periodo").val("");
  $("#cantidad").val("");   

}
    
$("#fecha").datepicker({
  dateFormat: 'dd/mm/yy',
  firstDay: 1
}).datepicker("setDate", new Date());

function valideKey(evt){
   // code is the decimal ASCII representation of the pressed key.
   var code = (evt.which) ? evt.which : evt.keyCode;
			
			if(code==8) { // backspace.
			  return true;
			} else if(code>=48 && code<=57) { // is a number.
			  return true;
			} else{ // other keys.
			  return false;
			}
 }

</script>

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
        <h4><p id="mensaje">¿ DESEA ELIMINAR ASOCIACIÓN ?</p></h4>
        </center>
      </div>
      <div class="modal-footer">
        <center>
        <!-- <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminar()">SI</button> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
        </center>
      </div>
    </div>
  </div>
</div>