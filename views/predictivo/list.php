<input type="hidden" id="permission" value="<?php echo $permission;?>">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Predictivo</h3>            
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
            <?php
                if (strpos($permission,'Add') !== false) {
                    echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="btnAgre">Agregar</button>';
                }
            ?>
            </div>
        </div><!-- /.row -->
        <table id="sales" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Id Pred.</th>
                    <th>Equipo</th>
                    <th>Tarea</th>
                    <th>Fecha</th>
                    <th>Periodo</th>
                    <th>Cantidad</th>
                    <th>Horas.H</th>
                    <!-- <th>Estado</th>                 -->
                </tr>
            </thead>
            <tbody>
            <?php
                if(count($list['data']) > 0){                  
                foreach($list['data'] as $a){

                    //if ($a['estado'] == "C") {
                    
                    $id  = $a['predId'];
                    $ide = $a['id_equipo'];

                    echo '<tr id="'.$id.'" class="'.$ide.'">';
                    echo '<td >';
                    
                    if (strpos($permission,'Add') !== false) {
                        echo '<i class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar" data-toggle="modal" data-target="#modalaviso"></i>';
                        
                        if( ($a['estado'] == 'S') || ($a['estado'] == 'PL') ) {
                        echo '<i class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar" ></i>';
                        }
                    
                    } 
                    if ($a['pred_adjunto']) {
                        echo '<a href="'.base_url().'assets/filespredictivos/'.$a['pred_adjunto'].'" target="_blank"><i class="fa fa-file-pdf-o text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Ver Archivo"></i></a>';
                    }     
                    echo '</td>';
                    echo '<td>'.$a['predId'].'</td>';
                    echo '<td>'.$a['codigo'].'</td>';
                    echo '<td>'.$a['de1'].'</td>';
                    echo '<td>'.date_format(date_create($a['fecha']),'d-m-Y').'</td>';
                    echo '<td>'.$a['periodo'].'</td>';
                    echo '<td>'.$a['cantidad'].'</td>';
                    echo '<td>'.$a['horash'].'</td>';
                    // echo '<td>';     
                    //   if($a['estado'] == 'PL'){
                    //   echo '<small class="label pull-left bg-yellow">Planificada</small>';
                    //   }
                    //   if($a['estado'] == 'AS'){
                    //   echo '<small class="label pull-left bg-purple">Asignada</small>';
                    //   }
                    //   if ($a['estado'] == 'C') {
                    //     echo '<small class="label pull-left bg-green">Curso</small>' ;
                    //   }
                    //   if ($a['estado'] == 'T') {
                    //   echo  '<small class="label pull-left bg-blue">Terminada</small>';
                    //   }
                    //   if ($a['estado'] == 'CE') {
                    //     echo  '<small class="label pull-left bg-primary">Cerrada</small>';
                    //   }
                    //   if ($a['estado'] == 'S') {
                    //     echo  '<small class="label pull-left bg-red">Solicitada</small>';
                    //   }
                    // echo '</td>';                                        
                    echo '</tr>';
                    //}                    
                }
                } 
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>

var gloid="";
var globi="";

$(document).ready(function(event) {

  edit=0;  datos=Array();
  $('#btnAgre').click( function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Predictivo/cargarpredictivo/<?php echo $permission; ?>", () => wc());
  });

  //Eliminar
  $(".fa-times-circle").click(function (e) { 
                 
    var idpre = $(this).parent('td').parent('tr').attr('id');
    console.log("ESTOY ELIMINANDO , el id de predictivo es:");
    console.log(idpre);
    gloid=idpre;
                                
  });    
  //Editar
  $(".fa-pencil").click(function (e) { 
            
    $('#modalSale').modal('show');
    var idpred = $(this).parent('td').parent('tr').attr('id');
    var ide = $(this).parent('td').parent('tr').attr('class');
    //guardo id de predictivo en modal para editar 
    $('#id_Predictivo').val(idpred);
    datos= parseInt(ide);

    $.ajax({
      type: 'POST',
      data: { idpred: idpred, datos:datos},
      url: 'index.php/Predictivo/getEditar', 
      success: function(data){             
              
              datos = {             
                        'id_equipo':data['datos'][0]['id_equipo'], 
                        'ubicacion':data['datos'][0]['ubicacion'],
                        'codigo':data['datos'][0]['codigo'],
                        'marca':data['datos'][0]['marca'],
                        'descripcion':data['datos'][0]['descripcion'],
                        'fecha_ingreso':data['datos'][0]['fecha_ingreso'],
                        'tarea': data['datos'][0]['tarea_descrip'],
                        'id_tarea': data['datos'][0]['id_tarea'],  
                        'fecha':data['datos'][0]['fecha'],
                        'periodo':data['datos'][0]['periodo'],
                        'cantidad':data['datos'][0]['cantidad'], 
                        'duracion':data['datos'][0]['duracion'],
                        'unidtiempo':data['datos'][0]['unidtiempo'],
                        'operarios':data['datos'][0]['operarios'],
                        'hh':data['datos'][0]['hh'],
                        'pred_adjunto':data['datos'][0]['pred_adjunto']
                      };   
              var herram = data['herramientas'];             
              var insum  = data['insumos'];           
              completarEdit(datos, herram, insum);   
            },
        
      error: function(result){
              
              console.log(result);
            },
      dataType: 'json'
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
      success: function(data) {
        tmp = data;
      },
      error: function(result){
        error("Error","No se pudieron traer las tareas");
        console.log(result);
      }
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

  //Trae herramientas
$(function(){
    $('#herramienta').html("");
    $.ajax({
        type: 'POST',
        data: { },
        url: '<?php echo MAN; ?>Preventivo/getherramienta',
        dataType: 'json',
        success: function(data){                   
            var opcion  = "<option value='-1'>Seleccione...</option>" ; 
            $('#herramienta').append(opcion); 
            for(var i=0; i < data.length ; i++){    
            var nombre = data[i]['herrcodigo'];
            var opcion  = "<option value='"+data[i]['herrId']+"'>" +nombre+ "</option>" ;
            $('#herramienta').append(opcion);                                    
        }
        },
        error: function(result){
            console.log(result);
        },
    });
});
  $("#herramienta").change(function(){     
    var id_herramienta = $(this).val();
    console.log("El id de la herramienta que seleccione es:");
    console.log(id_herramienta); 
    codhermglo=id_herramienta;
    $.ajax({
      type: 'POST',
      data: { id_herramienta: id_herramienta},
        url: 'index.php/Preventivo/getdatos', //index.php/
        success: function(data){    

          console.log(data);
          var marca = data[0]['herrmarca']; 
          $('#marcaherram').val(marca); 
          var des = data[0]['herrdescrip'];
          $('#descripcionherram').val(des); 
          var codigo = data[0]['herrcodigo'];
        },

        error: function(result){

          console.log(result);
        },
        dataType: 'json'
      });
  }); 
  var cod="";
  $("#agregarherr").click(function (e) {   

    var id_herramienta= $("#herramienta").val(codhermglo);    
    var id_her=codhermglo;   
    var id_herramienta1= $("#herramienta").val();
    console.log("herramienta de prueba :"+id_herramienta1);

    var $herramienta = $("select#herramienta option:selected").html(); 
    var marcaherram = $('#marcaherram').val();
    var descripcionherram = $('#descripcionherram').val();
    var cantidadherram = $('#cantidadherram').val();

    var tr = "<tr id='"+id_her+"'>"+
    "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
    "<td>"+$herramienta+"</td>"+
    "<td>"+marcaherram+"</td>"+
    "<td>"+descripcionherram+"</td>"+
    "<td>"+cantidadherram+"</td>"+                    
    "</tr>";
    console.log(tr);        
    $('#tablaherramienta tbody').append(tr);   

    $(document).on("click",".elirow",function(){
      var parent = $(this).closest('tr');
      $(parent).remove();
    });

    $('#herramienta').val('');
    $('#marcaherram').val(''); 
    $('#descripcionherram').val(''); 
    $('#cantidadherram').val('');
  });
  // trae insumos
$(function(){
    $('#insumo').html("");
    $.ajax({
        type: 'POST',
        data: { },
        url: '<?php echo MAN; ?>Preventivo/getinsumo',
        dataType: 'json',
        success: function(data){
            var opcion  = "<option value='-1'>Seleccione...</option>" ; 
            $('#insumo').append(opcion); 
            for(var i=0; i < data.length ; i++) 
            {    
            var nombre = data[i]['codigo'];
            var opcion  = "<option value='"+data[i]['value']+"'>" +nombre+ "</option>" ;
            $('#insumo').append(opcion); 
            }
        },
        error: function(result){
            console.log(result);
        },
    });
});
  $("#insumo").change(function(){

    var id_insumo = $(this).val();
    codinsumolo=id_insumo;
    console.log("El id de insumo que seleccione es:");
    console.log(id_insumo);
    console.log(codinsumolo);
    $.ajax({
      type: 'POST',
      data: { id_insumo: id_insumo},
          url: 'index.php/Preventivo/getinsumo', //index.php/
          success: function(data){
            console.log(data);
            var d = data[0]['label']; 
            $('#descript').val(d);  
            var insumo = data[0]['value'];             
          },
          error: function(result){
            console.log(result);
          },
          dataType: 'json'
    });
  });
  $("#agregarins").click(function (e) {

    var id_in = $('#insumo').val();
    alert(id_in);
    var $insumo = $("select#insumo option:selected").html();
    var descript = $('#descript').val();
    var cant = $('#cant').val();     
    var tr = "<tr id='"+id_in+"'>"+
    "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
    "<td>"+$insumo+"</td>"+
    "<td>"+descript+"</td>"+
    "<td>"+cant+"</td>"+
    "</tr>";
    $('#tablainsumo tbody').append(tr);
    $(document).on("click",".elirow",function(){
      var parent = $(this).closest('tr');
      $(parent).remove();
    });
    $('#insumo').val('');
    $('#descript').val(''); 
    $('#cant').val('');  
  });


  $(".datepicker").datepicker({
      
      changeMonth: true,
      changeYear: true
  });

  $('#sales').DataTable({
              <?php echo (!DT_SIZE_ROWS ? '"paging": false,' : null) ?>

                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "language": {
                      "lengthMenu": "Ver _MENU_ filas por página",
                      "zeroRecords": "No hay registros",
                      "info": "Mostrando página _PAGE_ de _PAGES_",
                      "infoEmpty": "No hay registros disponibles",
                      "infoFiltered": "(filtrando de un total de _MAX_ registros)",
                      "sSearch": "Buscar:  ",
                      "oPaginate": {
                          "sNext": "Sig.",
                          "sPrevious": "Ant."
                                  }
                            }
  });

}); 


//abrir modal eliminar adjunto
$(document).on("click",".eliminaAdjunto",function(){
  $('#modalEliminarAdjunto').modal('show');
  var idprev = $('#id_Predictivo').val();
  $('#idAdjunto').val(idprev);
});
//eliminar adjunto
function eliminarAdjunto() {
  $('#modalEliminarAdjunto').modal('hide');
  var idprev = $('#idAdjunto').val();
  $.ajax({
    data: { idprev: idprev },
    dataType: 'json',
    type: 'POST',
    url: 'index.php/Preventivo/eliminarAdjunto',
  }) 
  .done( function(data){     
    //console.table(data); 
    let prevAdjunto = '';
    recargaTablaAdjunto(prevAdjunto);
  })                
  .error( function(result){                      
    console.error(result);
  }); 
}

//abrir modal agregar adjunto
$(document).on("click",".agregaAdjunto",function(){
  $('#btnAgregarEditar').text("Agregar");
  $('#modalAgregarAdjunto .modal-title').html('<span class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar');

  $('#modalAgregarAdjunto').modal('show');
  var idprev = $('#id_Predictivo').val();
  $('#idAgregaAdjunto').val(idprev);
});
//abrir modal editar adjunto
$(document).on("click",".editaAdjunto",function(){
  $('#btnAgregarEditar').text("Editar");
  $('#modalAgregarAdjunto .modal-title').html('<span class="fa fa-fw fa-pencil text-light-blue"></span> Editar');

  $('#modalAgregarAdjunto').modal('show');
  var idprev = $('#id_Predictivo').val();
  $('#idAgregaAdjunto').val(idprev);
});
//eliminar adjunto
$("#formAgregarAdjunto").submit(function (event){
  $('#modalAgregarAdjunto').modal('hide');

  event.preventDefault();  
  if (document.getElementById("inputPDF").files.length == 0) {
    $('#error').fadeIn('slow');
  }
  else{
    $('#error').fadeOut('slow');
    var formData = new FormData($("#formAgregarAdjunto")[0]);
    //debugger
    $.ajax({
      cache:false,
      contentType:false,
      data:formData,
      dataType:'json',
      processData:false,
      type:'POST',
      url:'index.php/Predictivo/agregarAdjunto',
    })
    .done( function(data){     
      console.table(data['pred_adjunto']); 
      recargaTablaAdjunto( data['pred_adjunto'] );
    })                
    .error( function(result){                      
      console.error(result);
    }); 
  }
});

function recargaTablaAdjunto(prevAdjunto) {
  //console.info( "adjunto: "+prevAdjunto );
  $('#adjunto').text(prevAdjunto);
  $('#adjunto').attr('href', 'assets/filespredictivos/'+prevAdjunto);
  if( prevAdjunto == null || prevAdjunto == '') {
    var accion = '<i class="fa fa-plus-square agregaAdjunto text-light-blue" style="color:#f39c12; cursor:pointer; margin-right:10px" title="Agregar Adjunto"></i>';
  } else {
    var accion = '<i class="fa fa-times-circle eliminaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Eliminar Adjunto"></i>'+'<i class="fa fa-pencil editaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Editar Adjunto"></i>';
  }
  $('#accionAdjunto').html(accion);
}

// Calcula horas hombre por tiempo y unidades - Chequeado
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

  hsHombre =  parseFloat(hs * operarios);
  $('#hshombre').val(hsHombre);
  
}

// Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
$('#duracion, #unidad, #cantOper').change(function(){
  if( $('#duracion').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!="")
    calcularHsHombre();
});

// Completa modal con datos para editar - Chequeado     
function completarEdit(datos, herram, insum){
  // console.table(datos);
  $('#equipo').val(datos['codigo']);
  $('#id_equipo').val(datos['id_equipo']);
  $('#fecha_ingreso').val(datos['fecha_ingreso']);
  $('#marca').val(datos['marca']);
  $('#ubicacion').val(datos['ubicacion']);
  $('#descripcion').val(datos['descripcion']);
  $('#tarea').val(datos['tarea']);
  $('#id_tarea').val(datos['id_tarea']);
  $('#fecha').val(datos['fecha']);
  $('#cantidad').val(datos['cantidad']);  
  traer_periodo( datos['periodo'] );
  $('#duracion').val(datos['duracion']);
  $('#unidad').val(datos['unidtiempo']);
  $('#cantOper').val(datos['operarios']); 
  $('#hshombre').val(datos['hh']);  

  $('#tablaherramienta tbody tr').remove();
  for (var i = 0; i < herram.length; i++) {
    var tr = "<tr id='"+herram[i]['herrId']+"'>"+
    "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
    "<td>"+herram[i]['herrcodigo']+"</td>"+
    "<td>"+herram[i]['herrmarca']+"</td>"+
    "<td>"+herram[i]['herrdescrip']+"</td>"+
    "<td>"+herram[i]['cantidad']+"</td>"+                   
    "</tr>";
    $('#tablaherramienta tbody').append(tr);
  }

  $('#tablainsumo tbody tr').remove();
  for (var i = 0; i < insum.length; i++){                                             
    var tr = "<tr id='"+insum[i]['artId']+"'>"+
    "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>"+
    "<td>"+insum[i]['artBarCode']+"</td>"+
    "<td>"+insum[i]['artDescription']+"</td>"+
    "<td>"+insum[i]['cantidad']+"</td>"+                   
    "</tr>";
    $('#tablainsumo tbody').append(tr);
  }

  recargaTablaAdjunto(datos['pred_adjunto']);

  $(document).on("click",".elirow",function(){
    var parent = $(this).closest('tr');
    $(parent).remove();
  });
}

function traer_periodo(periodoE) {
    if (periodoE === undefined) {
      periodoE = null;
    }
    console.info(periodoE + 'periodo en traer periodo');
    $('#periodo').html(""); 
    $.ajax({
      data: {periodoE:periodoE },
      dataType: 'json',
      type: 'POST',
      url: 'index.php/Calendario/getperiodo',
      success: function(data){
        var opcion = "<option value='-1'>Seleccione...</option>" ; 
        $('#periodo').append(opcion); 
        for(var i=0; i < data.length ; i++) 
        {    
          let selectAttr = '';
          if( (typeof periodoE !== 'undefined') && (data[i]['idperiodo'] == periodoE) ) { selectAttr = 'selected';}
          let nombre = data[i]['descripcion'];
          let opcion = "<option value='"+data[i]['idperiodo']+"' "+selectAttr+">" +nombre+ "</option>";
          $('#periodo').append(opcion);                        
        }
        console.info(periodoE);
      },
      error: function(result){  
        console.log(result);
      },
    });
  }
// Guarda Predictivo Editado
function guardar(){    
 
  console.log("guardando");
  var id_Pred = $('#id_Predictivo').val(); // Guarda id de predictivo
  var tarea = $('#id_tarea').val();        // Guarda id de tarea en tarea_descrip
  var fecha = $('#fecha').val();        // Fecha actual de creacion de la tarea
  var periodo = $('#periodo').val();    // diario siempre (especie de tiempo)  
  var cantidad = $('#cantidad').val();  // Frecuencia (cantidad de dias)  
  var horash = $('#hshombre').val(); // cantidad de horas hombre
  var duracion = $('#duracion').val();  // Duracion de la tarea en minutos(guarda en pred_duracion)
  var unidad = $('#unidad').val();      // id de unidad de tiempo
  var operarios = $('#cantOper').val(); // Cantidad de operarios(guarda en pred_canth)

  // Arma array de herramientas y cantidades
  var idsherramienta = new Array();     
  $("#tablaherramienta tbody tr").each(function (index){
    var id_her = $(this).attr('id');
    idsherramienta.push(id_her);        
    });    
  var cantHerram = new Array(); 
  $("#tablaherramienta tbody tr").each(function (index){         
    var cant_herr = $(this).find("td").eq(4).html();
    cantHerram.push(cant_herr);                   
  });

  // Arma array de insumos y cantidades
  var idsinsumo = new Array();     
  $("#tablainsumo tbody tr").each(function (index){
    var id_ins = $(this).attr('id');
    idsinsumo.push(id_ins);        
  });
  var cantInsum = new Array(); 
  $("#tablainsumo tbody tr").each(function (index){         
    var cant_insum = $(this).find("td").eq(3).html();
    cantInsum.push(cant_insum); 
  }); 


  if((fecha !=='') || (cantidad !=='') || (duracion !=='') || (cantOper !=='') || (tarea > 0) || (unidad > 0) || (periodo > 0 )){
    WaitingOpen("Guardando");

    $.ajax({
          type: 'POST',
          data: { id_Predictivo: id_Pred,
                  tarea: tarea, 
                  fecha: fecha,
                  periodo: periodo,  
                  cantidad: cantidad,
                  horash:horash, 
                  duracion: duracion,
                  unidad: unidad,
                  operarios: operarios,
                  idsherramienta: idsherramienta,
                  cantHerram: cantHerram,
                  idsinsumo: idsinsumo, 
                  cantInsum: cantInsum                  
                },
                
          url: 'index.php/Predictivo/updatePredictivo',  
          success: function(data){
            WaitingClose("Guardando...");
            $('#modalSale').modal('hide');
            console.log("exito");   
            Refrescar();
          },
          error: function(result){
            WaitingClose("Error en guardado...");
            console.log(result);                     
          },
          dataType: 'json'
    });
  } else{
      var hayError = true;
      $('#error').fadeIn('slow');
      return;
  }

  if(hayError == false){
    $('#error').fadeOut('slow');
  }
}

function eliminarpred(){

  //gloid vaariable que guarda el id de predictivo
  WaitingOpen("Eliminando Predictivo...");        
  $.ajax({
    type: 'POST',
    data: { idpre: gloid},
    url: 'index.php/Predictivo/baja_predictivo', 
    success: function(data){  
            console.log(data);  
            Refrescar();
          },      
    error: function(result){
          WaitingClose("Error eliminando predictivo...");     
          console.log(result);
        },
    dataType: 'json'
  });
}

function Refrescar(){

  $('#content').empty();
  $("#content").load("<?php echo base_url(); ?>index.php/Predictivo/index/<?php echo $permission; ?>");
  WaitingClose('Cargando...');
}
  
$("#fecha").datetimepicker({
    format: 'YYYY-MM-DD',
    locale: 'es',
  });
</script>
<!-- Datepicker -->


<!-- Modal Editar -->
<div class="modal fade" id="modalSale" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerro()"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalActionSale" class="fa fa-fw fa-pencil text-light-blue"></span> Predictivo</h4> 
      </div>

      <div class="modal-body" id="modalBodySale">

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del Equipo </h3>
          </div>

          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">Equipos <strong style="color: #dd4b39">*</strong></label>
                <input type="text" class="form-control input-md" id="equipo" name="equipo" disabled />
                <input type="hidden" id="id_equipo" name="id_equipo">
                <input type="hidden" id="id_Predictivo" name="id_Predictivo">
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="fecha_ingreso">Fecha:</label>
                <input type="text" id="fecha_ingreso"  name="fecha_ingreso" class="form-control input-md" disabled />
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="marca">Marca:</label>
                <input type="text" id="marca"  name="marca" class="form-control input-md"  disabled />
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="ubicacion">Ubicacion:</label>
                <input type="text" id="ubicacion"  name="ubicacion" class="form-control input-md" disabled/>
              </div>

              <div class="col-xs-12">
                <label for="descripcion">Descripcion:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea>
              </div>
            </div><!-- /.row -->
          </div><!-- /.panel-body -->
        </div><!-- /.panel -->

        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title fa fa-building-o">   Programación</h4>
          </div>

          <div class="panel-body">  
            <div class="row">
              <div class="col-xs-12">
                <label for="tarea">Tarea <strong style="color: #dd4b39">*</strong>:</label> 
                  <input type="text" id="tarea" name="tarea" class="form-control">
                  <input type="hidden" id="id_tarea" name="id_tarea">
              </div> 
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="vfecha">Fecha <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control fecha" id="fecha" name="vfecha" size="27"/>
              </div>                        
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="periodo">Periodo <strong style="color: #dd4b39">*</strong>:</label>
                <select id="periodo"  name="periodo" class=" selectpicker form-control input-md" value="">
                </select>                   
              </div> 

              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="cantidad">Frecuencia <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" id="cantidad" name="cantidad"/>
              </div>                         
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="duracion">Duración <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" id="duracion" name="duracion"/>
              </div> 
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="unidad">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                <select  id="unidad" name="unidad" class="form-control" />
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="cantOper">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" id="cantOper" name="cantOper"/>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">Horas Hombre <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" name="hshombre" id="hshombre" disabled>
              </div>
            </div><!-- /.row -->            
          </div><!-- /.panel-body -->           
        </div><!-- /.panel -->

        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#herramin" aria-controls="profile" role="tab" data-toggle="tab">Herramientas</a></li>
            <li role="presentation"><a href="#insum" aria-controls="messages" role="tab" data-toggle="tab">Insumos</a></li>
            <li role="presentation"><a href="#TabAdjunto" aria-controls="home" role="tab" data-toggle="tab">Adjunto</a></li>            
          </ul>
        </div>

        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="herramin">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="herramienta">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                    <select  id="herramienta"  name="herramienta" class="form-control input-md" value=""></select>
                    <input type="hidden" id="id_herramienta" name="id_herramienta">
                  </div>       
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="marcaherram">Marca:</label>
                    <input type="text" id="marcaherram" name="marcaherram" class="form-control input-md" />
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="descripcionherram">Descripcion:</label>
                    <input type="text" id="descripcionherram" name="descripcionherram" class="form-control input-md" />
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="cantidadherram">Cantidad <strong style="color: #dd4b39">*</strong>:</label>
                    <input type="text" id="cantidadherram" name="cantidadherram" class="form-control input-md" placeholder="Ingrese Cantidad" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12"> 
                    <br>
                    <button type="button" class="btn btn-primary" id="agregarherr"><i class="fa fa-check">Agregar</i></button>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-xs-12">
                    <table class="table table-bordered" id="tablaherramienta"> 
                      <thead>
                        <tr>                      
                          <th></th>
                          <th>Código</th>
                          <th>Marca</th>
                          <th>Descripcion</th>
                          <th>Cantidad</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>  
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- cierre div herram -->

          <div role="tabpanel" class="tab-pane" id="insum">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="insumo">Codigo:</label>
                    <select  id="insumo"  name="insumo" class="form-control input-md" value=""></select>
                    <!-- <input type="hidden" id="id_insumo" name="id_insumo"> -->
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="descript">Descripcion:</label>
                    <input type="text" id="descript"  name="descript" class="form-control input-md" />
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="cant">Cantidad:</label>
                    <input type="text" id="cant"  name="cant" class="form-control input-md" placeholder="Ingrese Cantidad"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12">
                    <br>
                    <button type="button" class="btn btn-primary" id="agregarins"><i class="fa fa-check">Agregar</i></button>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-xs-12">
                    <table class="table table-bordered" id="tablainsumo"> 
                      <thead>
                        <tr>
                          <th></th>
                          <th>Código</th>
                          <th>Descripcion</th>
                          <th>Cantidad</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>  
                  </div>
                </div>
              </div>
            </div>
          </div><!--cierre div insum--> 

          <div role="tabpanel" class="tab-pane" id="TabAdjunto">
            <div class="row" >
              <div class="col-xs-12">
                <table class="table table-bordered" id="tablaadjunto"> 
                  <thead>
                    <tr>
                      <th></th>
                      <th>Archivo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td id="accionAdjunto">
                          <!-- -->
                      </td>
                      <td>
                        <a id="adjunto" href="" target="_blank"></a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div><!--cierre de TabAdjunto--> 

        </div><!--tab-content-->

      </div><!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerro()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal" onclick="guardar()">Guardar</button>
      </div>
    </div>
  </div>
</div>


<!--------------- MODALES ADJUNTO ------------->

<!-- Modal Eliminar Adjunto -->
<div class="modal" id="modalEliminarAdjunto">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idAdjunto">
          <h4>¿Desea eliminar Archivo Adjunto?</h4>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarAdjunto();">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal Agregar adjunto -->
  <div class="modal" id="modalAgregarAdjunto">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><span class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar</h4>
        </div>
  
        <form id="formAgregarAdjunto">
          <div class="modal-body">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Seleccione un Archivo Adjunto
            </div>
            <input type="hidden" id="idAgregaAdjunto" name="idAgregaAdjunto">
            <input id="inputPDF" name="inputPDF" type="file" class="form-control input-md">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" id="btnAgregarEditar">Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!--------------- MODALES ADJUNTO ------------->


  <!-- Modal Eliminar Warning -->
  <div class="modal fade" id="modalaviso">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" ><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" >&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4>¿Desea eliminar Predictivo?</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminarpred()">Eliminar</button>
      </div>
    </div>
  </div>
</div>


