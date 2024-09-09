<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="box box-primary">
  <div class="box-header with-border">
      <h3 class="box-title">Informe de Servicio</h3>
  </div><!-- /.box-header -->
  <div class="box-body">
    <table id="tblorden" class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Acciones</th> 
          <th>Nº de Informe</th>             
          <th>Nº de OT</th>
          <th>Descripción de OT</th>
          <th>Equipo</th> 
          <th>Fecha</th>  
          <th class="hidden">id equipo</th>              
          <th>Estado</th>                          
        </tr>
      </thead>
      <tbody>
        <?php
          if(count($list) > 0){                  
            foreach($list as $a){
              $id = $a['id_orden'];
              echo '<tr id="'.$id.'">';
                echo '<td class="icono">';
                  echo '<i class="fa fa-sticky-note-o text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Ver Informe"></i>'; 
                  // echo '<i class="text-light-blue fa fa-fw '.($a['estado'] == 'C' ? 'fa fa-toggle-on' : 'fa fa-toggle-off').'" title="'.($a['estado'] == 'C' ? 'Finalizar Informe' : 'Finalizado').'" style="cursor: pointer; margin-left: 15px;"></i>';
                echo '</td>';
                echo '<td>'.$a['id_orden'].'</td>';
                echo '<td>'.$a['id_ot'].'</td>';
                echo '<td>'.$a['descripcion_ot'].'</td>';
                echo '<td>'.$a['equipo'].'</td>';
                echo '<td>'.$a['fecha'].'</td>';
                echo '<td class="hidden">'.$a['id_equipo'].'</td>';
                //echo '<td>'.($a['estado'] == 'C' ? '<small class="label pull-left bg-green">Curso</small>' :($a['estado'] == 'T' ? '<small class="label pull-left bg-blue">Terminado</small>' : '<small class="label pull-left bg-red">Solicitado</small>')).'</td>';

                echo '<td>';           
                      
                if ($a['estado'] == 'S') {
                  echo  '<small class="label pull-left bg-red">Solicitada</small>';
                }
                if($a['estado'] == 'PL'){                           
                  echo '<small class="label pull-left bg-orange">Planificada</small>';
                }
                if($a['estado'] == 'AS'){
                  echo '<small class="label pull-left bg-yellow">Asignada</small>';
                }
                if ($a['estado'] == 'C') {
                  echo '<small class="label pull-left  bg-blue">Curso</small>' ;
                }
                if ($a['estado'] == 'T') {
                  echo  '<small class="label pull-left bg-navy">Terminada</small>';
                }
                if ($a['estado'] == 'CE') {
                  echo  '<small class="label pull-left bg-green">Cerrada</small>';
                }                            
                echo '</td>';
              echo '</tr>';
            }                  
          }
        ?>
      </tbody>
    </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->

<!--  MODAL INFORME DE SERVICIO  -->
<div class="modal fade bs-example-modal-lg" id="modalInforme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-12" id="modalInformeServicios">                               


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
<script>
function ver_informe_servicio (id_ot,id_eq){ 
    WaitingOpen();
    $('#modalInforme').modal('show');
    $('#modalInformeServicios').empty();
    $("#modalInformeServicios").load("<?php echo MAN; ?>Ordenservicio/verInforme/"+id_ot+"/"+id_eq+"/");
    WaitingClose();
}
// Resetea Nº de orden al recargar la pagina -->
$('#cargOrden').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Ordenservicio/getOrdenInactiva/<?php echo $permission; ?>");
    WaitingClose();
});
/////// Carga la tabla del Modal y valida que  no se duplique 
var $flag = 0;    
$(".fa-sticky-note-o").click(function () { 
    var id_ot = $(this).parents("tr").find("td").eq(2).html();
    var id_eq  = $(this).parents("tr").find("td").eq(6).html();	
    ver_informe_servicio (id_ot,id_eq);
});

// muestra el encabezado de la Orden de servicio en Modal
function mostrarOrd(row){
  $("#modOrden tr").remove();
  $("#modOrden tbody").append(row);      
}

// trae lecturas segun id de orden y arma tabla en modal 
function getLecturaOrden(id_ord){
  var dataF = function () {
    var tmp = null;
    $.ajax({
      'data' : {id_orden:id_ord },
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Ordenservicio/getLecturaOrden",
      'success': function (data) {
        tmp = data;
        //console.table(data);
      }
    });
    return tmp;
  }();  
  // Asigna opciones al select #tareas  
  tabla = $('#modLectura').DataTable(); 
  tabla.clear().draw();
  $.each(dataF, function(i, val){           
    $('#modLectura').DataTable().row.add( [
      val.horometroinicio,
      val.horometrofin,
      val.fechahorainicio,
      val.fechahorafin
    ]).draw();
  });
}

// trae tareas segun id de orden y arma tabla en modal 
function getTarOrden(id_ord){
  var dataF = function () {
    var tmp = null;
    $.ajax({
      'data' : {id_orden:id_ord },
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Ordenservicio/getTareaOrden",
      'success': function (data) {
        tmp = data;
        //console.table(data);
      }
    });
    return tmp;
  }();  
  // Asigna opciones al select #tareas  
  tabla = $('#modTarea').DataTable(); 
  tabla.clear().draw();
  $.each(dataF, function(i, val){           
    $('#modTarea').DataTable().row.add( [
      val.id_tarea
    ]).draw();
  });
}

// trae herramientas segun id de orden y arma tabla en modal 
function getHerrramOrden(id_ord){
  var dataF = function () {
    var tmp = null;
    $.ajax({
      'data' : {id_orden:id_ord },
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Ordenservicio/getHerramOrden",
      'success': function (data) {
        tmp = data;
        //console.table(data);
      }
    });
    return tmp;
  }();        
  tabla = $('#modHerram').DataTable(); 
  tabla.clear().draw();
  $.each(dataF, function(i, val){           
    $('#modHerram').DataTable().row.add( [
      val.herrdescrip,
      val.herrmarca,
      val.herrcodigo
    ]).draw();
  });
}

// trae Insumos segun id de orden y arma tabla en modal 
function getInsumOrd(id_ot){
  var dataF = function () {
    var tmp = null;
    $.ajax({
      'data' : {id_ot:id_ot },
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Ordenservicio/getInsumosPorOT",
      'success': function (data) {
        tmp = data;
        console.table(data);
      }
    });
    return tmp;
  }();
  tabla = $('#modInsum').DataTable(); 
  tabla.clear().draw();
  $.each(dataF, function(i, val){ 
         
    $('#modInsum').DataTable().row.add( [
      val.nroOT,
      val.fecha,
      val.nombre,
      val.codigo,
      val.descripcion,
      val.cantidad
    ]).draw();
  });      
}

// trae RRHH segun id de orden y arma tabla en modal 
function getRecOrden(id_ord){
  console.log('recursossss: ');
  var dataO = function () {
    var tmp = null;
    $.ajax({
      'data' : {id_orden:id_ord },
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Ordenservicio/getOperarioOrden",
      'success': function (data) {
        tmp = data;
        console.table(data);
      },
    });
    return tmp;
  }();
  tabla = $('#modRecurso').DataTable(); 
  tabla.clear().draw();
  $.each(dataO, function(i, val){           
    $('#modRecurso').DataTable().row.add( [
      val.usrLastName,
      val.usrName
    ]).draw();
  });     
}

// Cambia el estado de Orden servicio y de solicitud de servicio
$(".fa-toggle-on").click(function () {  

  var id_orden = $(this).parent('td').parent('tr').attr('id'); // guarda el id de orden en var global id_orden
  $.ajax({
        type: 'POST',
        data: {id_orden: id_orden},
        url: 'index.php/Ordenservicio/setEstado', 
        success: function(data){                   
                setTimeout("cargarView('Ordenservicio', 'index', '"+$('#permission').val()+"');",0);
              },            
        error: function(result){
              alert("Error en cambio de estado");
            },
            dataType: 'json'
        });
});

// Cambia el estado de solicitud de servicio 
$(".fa-thumbs-up").click(function () {  

  var id_solServ = $(this).parent('td').parent('tr').attr('id'); // guarda el id de orden en var global id_solServ
  $.ajax({
        type: 'POST',
        data: {id_solServ: id_solServ},
        url: 'index.php/Ordenservicio/setEstado', 
        success: function(data){                   
                setTimeout("cargarView('Ordenservicio', 'index', '"+$('#permission').val()+"');",0);
              },            
        error: function(result){
              alert("Error en cambio de estado");
            },
            dataType: 'json'
        });
});

//cierro todos los collapse
$('#modalOrder').on('shown.bs.modal', function () {
  $('.collapse-group').find('.collapse').collapse('hide');
});
//cierro collapse al abrir otro
$('.collapse-group').on('show.bs.collapse','.collapse', function() {
    $('.collapse-group').find('.collapse.in').collapse('hide');
});

// ajusto ancho de columnas
$('#collapseZero, #collapseOne, #collapseTwo, #collapseThree, #collapseFour').on('shown.bs.collapse', function () {
   $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust();
});

$('#tblorden').DataTable({
  <?php echo (!DT_SIZE_ROWS ? '"paging": false,' : null) ?>
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

$('#modLectura, #modTarea, #modHerram, #modInsum, #modRecurso').DataTable({
  "aLengthMenu": [ 10, 25, 50, 100 ],
  "order": [[0, "asc"]],
});
</script>
