<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          Revise que todos los campos obligatorios esten seleccionados
      </div>
  </div>
</div>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Programación Backlog</h3>
    </div><!-- /.box-header -->
    <form id="formBacklog" role="form" action="<?php echo MAN;?>Backlog/guardar_backlog" method="POST" onKeypress="if (event.keyCode == 13) event.returnValue = false;" >
        <div class="box-body">
            <?php
                if (strpos($permission,'Add') !== false) {
                    echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
                }
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del equipo</h3>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="idSector">Sector <strong style="color: #dd4b39">*</strong></label>
                            <input type="text" class="form-control buscSector" placeholder="Buscar Sector..." id="buscSector" name="buscSector">
                            <input type="text" class="hidden idSector" id="idSector" name="idSector">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="equipo">Equipo <strong style="color: #dd4b39">*</strong></label>
                            <select  id="equipo" name="equipo" class="form-control equipo">
                                <option value="-1" selected disabled>Seleccione opción</option>
                            </select>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="fecha_ingreso">Fecha:</label>
                            <input type="text" id="fecha_ingreso" name="fecha_ingreso" class="form-control input-md" disabled />
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="marca">Marca:</label>
                            <input type="text" id="marca" name="marca" class="form-control input-md"  disabled />
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="ubicacion">Ubicación:</label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control input-md" disabled/>
                        </div>

                        <div class="col-xs-8">
                            <label for="descripcion">Descripción: </label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control input-md" disabled/>
                            <!-- <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea> -->
                        </div> 

                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="codigo_componente">Código de componente-equipo :</label>
                            <input type="text" id="codigo_componente" name="codigo_componente" class="form-control input-md" placeholder="Ingrese código de componente..."/>
                            <input type="hidden" id="idcomponenteequipo" name="idcomponenteequipo" value=""/>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="descrip_componente">Descripción de componente:</label>
                            <input type="text" id="descrip_componente" name="descrip_componente" class="form-control input-md"  disabled />
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="sistema_componente">Sistema:</label>
                            <input type="text" id="sistema_componente" name="sistema_componente" class="form-control input-md"  disabled />
                        </div>
                    </div><!-- /.row -->
                </div><!-- /.panel-body -->
            </div><!-- /.panel -->

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><span class="fa fa-building-o"></span> Programación</h4>
                </div>

                <div class="panel-body">  
                    <div class="row">
                        <div class="col-xs-12 col-md-6">                    
                            <label for="tarea">Tarea Estandar<strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="tarea" name="tarea" class="form-control" placeholder="Buscar Tarea...">
                            <input type="hidden" id="id_tarea" name="id_tarea">
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <label for="tareaOpcional">Tarea Personalizada<strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" class="form-control" id="tareaOpcional" name="tareaOpcional" placeholder="Ingrese Tarea..." value="<?php echo $info[0]["tarea_opcional"] ?>" />
                        </div>     
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-3">
                            <label for="vfecha">Fecha Creación:</label>
                            <input type="text" class="datepicker form-control fecha" id="fecha" name="vfecha" value="<?php echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>" size="27"/>                         
                        </div>                    
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <label for="duracion">Duración Estandar <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" class="form-control" id="duracion" name="duracion" placeholder="Ingrese valor..."/>
                            <input type="hidden" class="form-control" id="back_duracion" name="back_duracion"/>
                        </div> 
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <label for="unidad">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                            <select  id="unidad" name="unidad" class="form-control" />
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <label for="cantOper">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" class="form-control" id="cantOper" name="cantOper" placeholder="Ingrese valor..."/>
                        </div>                    

                      </div><!-- /.row -->
                      <div class="row">
                        <div class="col-xs-12 col-md-6" id="dato" name="" style="margin-top: 19px;"></div>
                        <input type="hidden" name="hshombre" id="hshombre">                
                        <!-- <div id="dato"></div> -->
                      </div><!-- /.row -->
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

                <!-- Tab pane #herramin -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="herramin">
                      <div class="row">
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="herramienta">Código <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="herramienta"  name="" class="form-control" placeholder="Buscar Código..."/>
                            <input type="hidden" id="id_herramienta" name="id_herramienta">
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="marcaherram">Marca <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="marcaherram"  name="" class="form-control" />
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="descripcionherram">Descripción <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="descripcionherram"  name="" class="form-control" />
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-4">
                            <label for="cantidadherram">Cantidad <strong style="color: #dd4b39">*</strong>:</label>
                            <input type="text" id="cantidadherram"  name="" class="form-control" placeholder="Ingrese Cantidad..." />
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
                        <input type="text" id="cant"  name="" class="form-control" placeholder="Ingrese Cantidad..."/>
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
        </div>

        <div class="modal-footer">
            <!-- <button type="button" class="btn btn-danger btn-sm delete" onclick="limpiar()">Cancelar</button> -->
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
<script>
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
        alert("Debe seleccionar un Sector");
      }else{
        $("#idSector").val(ui.item.value);
        $(this).val(ui.item.label);
        $("#equipo").html('<option value="-1" disabled selected>Seleccione opción</option>');
        // guardo el id de sector
        var idSect =  $("#idSector").val();
        if(idSect && idSect != '')
          getEquiSector(idSect);
        else
          alert("Debe seleccionar un sector");
      }
    },
    select: function(event, ui) {
      // prevent autocomplete from updating the textbox
      console.log('Select');
      event.preventDefault();
      // manually update the textbox and hidden field
      console.log(ui.item);
      if(ui.item === null){
        $("#equipo").html('<option value="-1" disabled selected>Seleccione opción</option>');
        $("#idSector").val('');
      }else{
        $("#idSector").val(ui.item.value);
        $(this).val(ui.item.label);
        $("#equipo").html('<option value="-1" disabled selected>Seleccione opción</option>');
        // guardo el id de sector
        var idSect =  $("#idSector").val();
        getEquiSector(idSect);
      }
      /*$(this).val(ui.item.label);
      $("#idSector").val(ui.item.value);
      $("#equipo").html('<option value="-1" disabled selected>Seleccione opcion</option>');
      // guardo el id de sector
      var idSect =  $("#idSector").val();
      getEquiSector(idSect);
      //console.log("id sector en autocompletar: ");
      //console.log(ui.item.value);
      */
    },
  }); 
  //  llena select de equipos segun sector
  function getEquiSector(idSect){
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
        if(data !== null){
          // Asigna opciones al select Equipo en modal
          //console.log("length: "+data.length);
          var $select = $("#equipo");
          for (var i = 0; i < data.length; i++) {
            $select.append($('<option />', { value: data[i]['id_equipo'], text: data[i]['descripcion'] }));
          }
        }else{
          notificar('Alerta','No se encontraron equipos para este sector','warning');
        }
      },
      'error' : function(data){
        console.log('Error en getEquiSector');
        console.table(data);
      },
    });
  }
  
var codhermglo  = "";
var codinsumolo = "";
var preglob     = "";
  
//carga listado backlog(desde boton) - Chequeado
$('#listado').click( function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Backlog/index/<?php echo $permission; ?>", function() {
      wc();
    });
});

$(".datepicker").datepicker({
    changeMonth: true,
    changeYear: true
});

// trae info de equipo por id para completar los campos - Chequeado
$('#equipo').change(function(){
  var id_equipo = $(this).val();
  $.ajax({
    data: { id_equipo:id_equipo },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Backlog/getInfoEquipo',
    success: function(data){
      //console.table(data);
     limpiarInfoEquipos();
      var fecha_ingreso = data['fecha_ingreso']; 
      var marca         = data['marcadescrip']; 
      var ubicacion     = data['ubicacion']; 
      var criterio1     = data['criterio1']; 
      var descripcion   = data['descripcion']; 
      $('#fecha_ingreso').val(fecha_ingreso);       
      $('#marca').val(marca);   
      $('#descripcion').val(descripcion);       
      $('#ubicacion').val(ubicacion);
      refrescarAutocompletar();
      $('#codigo_componente').val("");
      $('#descrip_componente').val("");
      $('#sistema_componente').val("");
    },
    error: function(result){
      console.error("Error al traer info de equipo.");
      console.table(result);
    },
  });   
});

function limpiarInfoEquipos(){

     $('#fecha_ingreso').val("");       
      $('#marca').val("");   
      $('#descripcion').val("");       
      $('#ubicacion').val("");
}

$("#fecha").datepicker({
  format: 'dd/mm/yy',
  startDate: '-3d'
  //firstDay: 1
}).datepicker("setDate", new Date());

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

//Trae tareas y permite busqueda en el input
var dataTarea = function() {
  var tmp = null;
  $.ajax({
    'async': false,
    'type': "POST",
    'dataType': 'json',
    'url': '<?php echo MAN; ?>Preventivo/gettarea',
    success: (data) => { tmp = data },
    error: () => error('Error',"Error al traer tareas"),
  })
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
  },
});


// autocomplete para componente
function dataC() {
  var tmp = null;
  var idEquipo = $('#equipo').val();
  console.info("id equipo: "+idEquipo);
  $.ajax({
    'async': false,
    'data': {idEquipo:idEquipo},
    'dataType': 'json',
    'global': false,
    'type': "POST",
    'url': "<?php echo MAN; ?>Backlog/getComponente",
    success: function(data) {
      //console.info("trae componentes para autocomplete");
      console.log(data);
      if(data==0){
        data = "0: { value: null, label: null, descrip: null, sistema:null }";
      }
      tmp = data;
    },
    error: (result) => {error('Error',"Error al traer componentes")},
  });
  return tmp;
};

refrescarAutocompletar();
function refrescarAutocompletar(){
  $("#codigo_componente").autocomplete({
    source: dataC(),
    delay: 100,
    minLength: 1,
    messages: {
      noResults: function(count) {
        $('#codigo_componente').val("");
        $('#codigo_componente').attr("placeholder", "No se encontraron resultados");
      },
      results: function(count) {
        console.log("There were " + count + " matches")
      },
    },
    focus: function(event, ui) {
      //console.table(ui.item);
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      // manually update the textbox
      $(this).val(ui.item.value);
      $('#descrip_componente').val(ui.item.descrip);
      $('#sistema_componente').val(ui.item.sistema);
      $('#idcomponenteequipo').val(ui.item.idce);
    },
    select: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      // manually update the textbox and hidden field
      $(this).val(ui.item.value);//label
      $("#descrip_componente").val(ui.item.descrip);
      $("#sistema_componente").val(ui.item.sistema);
      $('#idcomponenteequipo').val(ui.item.idce);
    }
  });
  
}

// Carga lista de backlog - Chequeado
function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Backlog/index/<?php echo $permission; ?>");
    wc();
}

/* nuevo */
// Guarda Backlog nuevo
$("#formBacklog").submit(function (event){   
  
  event.preventDefault();  
  wo();
  var equipo   = $('#equipo').val();
  var tarea    = $('#tarea').val();
  var compon   = $('#codigo_componente').val(); 
  var duracion = $('#duracion').val();
  var unidad   = $('#unidad').val();
  var oper     = $('#cantOper').val();
  
  if ((equipo < 0)||(tarea < 0)|| (duracion == "")||(unidad < 0)||(oper == "")) {
    $('#error').fadeIn('slow');
    wc();
  }else{
    $('#error').fadeOut('slow');
    var formData = new FormData($("#formBacklog")[0]);
    $.ajax({
      url:$("form").attr("action"),
      type:$("form").attr("method"),
      data:formData,
      cache:false,
      contentType:false,
      processData:false,
      success:function(respuesta){
        const confirm = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
        });
        confirm.fire({
              title: 'Perfecto!',
              text: "Se agrego el backlog correctamente!",
              type: 'success',
              showCancelButton: false,
              confirmButtonText: 'Hecho'
          }).then((result) => {
            cargarVista();      
        });     
      },
      error:function(respuesta){
        error('Error','Se produjo un error guardando el Backlog.');
      },
      complete: () => wc(),
    });
  }
});

// Trae unidades de tiempo y calcula hs hombre
$(function(){  
  $.ajax({
    type: 'POST',
    data: { },
    url: '<?php echo MAN;?>Preventivo/getUnidTiempo', 
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
        dataType: 'json'
  });
}); 
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
  var mens=$("<h4>HH calculadas: <span class='hh'>" + hsHombre + "</span></h4>"); 
  $('#dato').html(mens);
  $('#hshombre').val(hsHombre);
}
// Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
$('#duracion, #unidad, #cantOper').change(function(){
  if( $('#duracion').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!=""){
    calcularHsHombre();
    //calcDuracionBack();    
  }
});

// ordena los items de las busquedas
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
      success: function(data) { tmp = data },
      error: function() { error("Error","Error al traer Herramientas") },
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
      'url': '<?php echo MAN; ?>Preventivo/getinsumo',
      success: function(data) { tmp = data },
      error: function(result) { error("Error","Error al traer Herramientas") },
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
