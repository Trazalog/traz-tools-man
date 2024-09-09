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
          <h3 class="box-title">Preventivo</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
          }
          ?>
        </div><!-- /.box-header -->

        <div class="box-body">
          <form id="formPreventivo" role="form" action="<?php echo MAN; ?>Preventivo/guardar_preventivo" method="POST" >
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><div class="fa fa-cogs"></div> Datos del Equipo</h3>
              </div>                  

              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6">Sector <strong style="color: #dd4b39">*</strong>
                    <select id="sector" name="sector" class=" selectpicker form-control input-md">
                      <option value="-1" selected disabled>Seleccione opción</option>
                    </select>
                  </div>
                  <div class="col-xs-12 col-sm-6">Equipos <strong style="color: #dd4b39">*</strong>
                    <select  id="equipo" name="id_equipo" class="form-control id_equipo" disabled>
                      <option value="-1" selected disabled>Seleccione opción</option>
                    </select>
                  </div>
                </div><!-- /.row -->
                <div class="row">
                  <div class="col-xs-12 col-sm-4">Fecha:
                    <input type="text" id="fecha_ingreso" name="" class="form-control input-md" disabled />
                  </div>
                  <div class="col-xs-12 col-sm-4">Marca:
                    <input type="text" id="marca" name="" class="form-control input-md"  disabled />
                  </div>
                  <div class="col-xs-12 col-sm-4">Ubicación:
                    <input type="text" id="ubicacion" name="" class="form-control input-md" disabled/>
                  </div>
                  <div class="col-xs-12">Descripción: 
                    <textarea class="form-control" id="descripcion" name="" disabled></textarea>
                  </div>
                </div> <!-- /.row -->
              </div> <!-- panel-body -->                    
            </div><!-- panel-default -->

            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title"><div class="fa fa-building-o"></div> Programación</h4>
              </div><!-- /.panel-heading -->

              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">Tarea <strong style="color: #dd4b39">*</strong>:
                    <select id="tarea" name="tarea" class=" selectpicker form-control input-md">
                      <option value="-1" selected disabled>Seleccione opción</option>
                    </select>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-4">Componente <strong style="color: #dd4b39">*</strong>:
                    <select id="componente" name="id_componente" class="form-control input-md"   />
                    <!--<input type="hidden" id="id_componente" name="id_componente" />-->
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">Fecha Base:
                    <input type="text" class="form-control ultimo" id="ultimo" name="ultimo" value="<?php echo date("Y-m-d"); ?>" size="27"/>
                  </div> 
                  <div class="col-xs-12 col-sm-6">Periodo <strong style="color: #dd4b39">*</strong>:
                    <select id="periodo" name="periodo" class=" selectpicker form-control input-md">
                    </select>
                  </div>
                  <div class="col-xs-12 col-sm-6">Frecuencia <strong style="color: #dd4b39">*</strong>:
                    <input type="number" min="1" step="1" id="cantidad" name="cantidad" class="form-control input-md" placeholder="Ingrese valor..."/>
                  </div>
                  <div class="col-xs-12 col-sm-6">Lectura base <strong style="color: #dd4b39">*</strong>:
                    <input type="text"  id="lectura_base" name="lectura_base" class="form-control input-md" placeholder="Ingrese valor..." disabled/>
                  </div>
                  <div class="col-xs-12 col-sm-6">Alerta <strong style="color: #dd4b39">*</strong>:
                    <input type="text"  id="alerta" name="alerta" class="form-control input-md" placeholder="Ingrese valor..." disabled/>
                  </div>
                </div> <!-- /.row -->
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">Duración <strong style="color: #dd4b39">*</strong>:
                    <input type="text" class="form-control" id="duracion" name="duracion" placeholder="Ingrese valor..."/>
                  </div> 
                  <div class="col-xs-12 col-sm-6 col-md-4">U. de tiempo <strong style="color: #dd4b39">*</strong>
                    <select id="unidad" name="unidad" class="form-control">
                    </select>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">Cant. Operarios <strong style="color: #dd4b39">*</strong>:
                    <input type="text" class="form-control" id="cantOper" name="cantOper" placeholder="Ingrese valor..."/>
                  </div>          
                  <div class="col-xs-12" id="dato" name="" style="margin-top: 19px;"></div>
                  <input type="hidden" name="hshombre" id="hshombre">
                </div><!-- /.row -->
              </div><!-- /.panel-body -->
            </div><!-- /.panel-default -->

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
                          <label for="herramienta">Código<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="herramienta"  name="herramienta" class="form-control" placeholder="Buscar Código..." />
                          <input type="hidden" id="id_herramienta" name="id_herramienta">
                        </div>                          
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="marcaherram">Marca<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="marcaherram"  name="marcaherram" class="form-control" />
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="descripcionherram">Descripción<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="descripcionherram"  name="descripcionherram" class="form-control" />
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="cantidadherram">Cantidad<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="cantidadherram"  name="cantidadherram" class="form-control" placeholder="Ingrese Cantidad..." />
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
                                <th>Descripción</th>
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
                          <label for="insumo">Código<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="insumo" name="insumo" class="form-control" placeholder="Buscar Código..." />
                          <input type="hidden" id="id_insumo" name="">
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="">Descripción:</label>
                          <input type="text" id="descript"  name="descript" class="form-control" />
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <label for="">Cantidad<!-- <strong style="color: #dd4b39">*</strong> -->:</label>
                          <input type="text" id="cant"  name="cant" class="form-control" placeholder="Ingrese Cantidad"/>
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
                                <th>Descripción</th>
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

                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
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
        url: 'index.php/Backlog/getequipo',
        dataType: 'json',
        success: function(data){
            var opcion  = "<option value='-1'>Seleccione...</option>" ; 
            $('#equipo').append(opcion); 
            for(var i=0; i < data.length ; i++){    
                var nombre = data[i]['codigo'];
                var opcion  = "<option value='"+data[i]['id_equipo']+"'>" +nombre+ "</option>" ; 

                $('#equipo').append(opcion); 
                                
            }
        },
        error: function(result){ 
            console.log(result);
        },
    });
}

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
        error('Error','Error en getSector');
        console.log(data);
      },
    });
    return tmp;
  }();
  
  $("#sector").change(function(){
      var idSector = $("#sector").val();
      getEquiSector(idSector);
  });

// Trae Sectores y autocompleta el campo

  //  llena select de equipos segun sector
  function getEquiSector(idSect){
    $("#equipo").empty();
    var id =  idSect;
    $("#fecha_ingreso").val("");
    $("#marca").val("");
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
      'success': function (data) {
        console.table(data)
        $('#equipo').attr('disabled', false);
        if(data){
          var opcion = "<option value='-1'>Seleccione...</option>" ;
          $('#equipo').append(opcion);
          
            for (var i = 0; i < data.length; i++) {
            var nombre = data[i]['descripcion'];
            var opcion = "<option value='"+data[i]['id_equipo']+"'>" +nombre+ "</option>" ; 
            $('#equipo').append(opcion);  
          }
        }else{
          var opcion = "<option value='-1'>Sin equipos asociados</option>" ;
          $('#equipo').append(opcion);  
        }
      },
      'error' : function(data){
        console.log('Error Preventivo en getEquiSector');
        console.table(data);
      },
    });
  }

// Volver al listado
$('#listado').click( function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Preventivo/index/<?php echo $permission; ?>", () => wc());
});

$('#ultimo').datetimepicker({
  format: 'YYYY-MM-DD', 
  locale: 'es',
});

// // Trae equipos
// Con equipo seleccionado llama funcion para traer sus componentes
$('#equipo').change(function(){
  wo();
  var id_equipo = $(this).val();
  $.ajax({
    type: 'POST',
    data: { id_equipo: id_equipo},
    dataType: 'json',
    url: '<?php echo MAN; ?>Preventivo/getEquipoNuevoPrevent', 
  })
  .done( (data) => {
    var fecha_ingreso = data['fecha_ingreso']; 
    var marca         = data['marcadescrip']; 
    var ubicacion     = data['ubicacion']; 
    var criterio1     = data['criterio1']; 
    var descripcion   = data['descripcion']; 
    $('#fecha_ingreso').val(fecha_ingreso);       
    $('#marca').val(marca);   
    $('#descripcion').val(descripcion);       
    $('#ubicacion').val(ubicacion);  
    
    traer_componente(id_equipo); 
  })
  .fail( () => alert("Error al traer Equipos.") )
  .always( () => wc() );
});
// Trae componente segun equipo seleccionado
function traer_componente(id_equipo){
  $('#componente').html("");
  $.ajax({
    async:false,
    type: 'POST',
    data: {id_equipo:id_equipo },
    dataType: 'json',
    url: '<?php echo MAN; ?>Preventivo/getcomponente',
    success: function(data){
      //console.log(data);
      $('#componente option').remove();
      if(data == false){
        //alert("El equipo no tiene componentes");
        var opcion = "<option value='-1'>No tiene componente asociado</option>" ; 
      } else {
        var opcion = "<option value='-1'>Seleccione...</option>" ; 
      }
      $('#componente').append(opcion); 
      for(var i=0; i < data.length ; i++){    
        var nombre = data[i]['descripcion'];
        var opcion  = "<option value='"+data[i]['id_componente']+"'>" +nombre+ "</option>" ;
        $('#componente').append(opcion);                                  
      }                         
    },
    error: function(result){              
      console.log(result);
    },
  });
}


//Trae tareas y permite busqueda en el input
var dataTarea = function() {
  var tmp = null;
  $.ajax({
    'async': false,
    'type': "POST",
    'dataType': 'json',
    'url': '<?php echo MAN; ?>Preventivo/gettarea',
    success: (data) => { tmp = data;
    var $select = $("#tarea");
        for (var i = 0; i < data.length; i++) {
          $select.append($('<option>', { value: data[i]['value'], text: data[i]['label'] }, '</option>'));
    }},
    error: (result) => error('Error',"Error al traer tareas")
  })
  return tmp;
}();

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
        error('Error',"Error al traer periodo");
        console.log(result);
    },
  });
}

//Habilita lectura base y alerta si el periodo es horas ó ciclos
$('#periodo').change(function(){
  //alert('hola');
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
    dataType: 'json',
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
        error('Error',"Error al traer Unidades de Tiempo");
        console.log(result);
    },
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
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Preventivo/index/<?php echo $permission; ?>", () => wc());
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

  /*
  var insumo   = $('#insumo').val();
  var cant   = $('#cant').val();

  var herramienta = $('#herramienta').val();
  var cantidadherram = $('#cantidadherram').val();
  var marcaherram      = $('#marcaherram').val();
  var descripcionherram = $('#descripcionherram').val();
  
  console.log("cant: "+cant);
  */

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
        console.log('resp prenevt: ');
        console.log(respuesta.resPrenvent);
        if (respuesta) {
          hecho("Hecho","Los datos han sido guardados correctamente");
          cargarVista();
        }
        else if(respuesta==="error"){
          erroe("Error","Los datos no se han podido guardar");
        }else{
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
////// HERRAMIENTAS //////

  //Trae herramientas
  var dataHerramientas = function() {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'dataType': 'json',
      'url': '<?php echo MAN; ?>Preventivo/getHerramientasB',
      success: (data) => { tmp = data },
      error: () => error('Error',"Error al traer Herramientas")
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
    },
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
      'url': '<?php echo MAN;?>Preventivo/getinsumo',
      success: (data) => { tmp = data },
      error: (result) => error('Error',"Error al traer Insumos")
    })
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


</script>
