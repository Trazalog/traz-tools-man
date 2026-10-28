<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
      <h4><i class="icon fa fa-ban"></i> ERROR!</h4>
      INGRESE TAREA A REALIZAR!! 
    </div>
  </div>
</div>
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"> Asignación de tareas</h3>
            <?php
            if (strpos($permission,'Add') !== false) {
              echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';           
            }
            ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="row" >
            <input type="hidden" id="numord" name="numord" value="<?php echo $id_orden;?>"></input>
            <div class="col-xs-12 col-sm-8">
              <label for="tarea">Tarea</label>
              <input type="text" class="form-control" id="tarea" name="tarea" placeholder="Ingrese descripcion de tarea...">
            </div>
            <div class="col-xs-12 col-sm-4">
              <br>
              <button type="button" class="btn btn-primary" id="agregar"><i class="fa fa-plus"></i> Agregar</button>
            </div><br><br>
          </div>
            
          <div class="row" >
            <div class="col-xs-12">
              <table id="orden" class="table table-bordered table-hover">
                <!--<br>
                  <div class="col-xs-4" align="center"><label>Listado de tareas</label></div>-->
                  <thead>
                    <tr>
                      <th></th>
                      <th>Descripción</th>
                      <th>Usuario</th>
                      <th>Fecha</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>  
                    <?php
                    //echo "<pre>";  
                    //var_dump($list);
                    if( $list!=false && count($list) > 0) {
                      $usrId = man_usuario();  

                      foreach($list as $a)
                      {
                        if($a['estado']!=='IN')
                        {
                       
                          $id = $a['id_listarea'];
                          echo '<tr id="'.$id.'" class="'.$id.'">';
                          echo '<td>';

                          //(strpos($permission,'OP') !== false && ($a['usrId']==$usrId) && ($a['estado']!='RE'))
                          if ((strpos($permission,'OP') !== false && ($a['usrId']==$usrId) && ($a['estado']!== 'RE')) || (strpos($permission,'OP') !== false && ($a['usrId']==$a['id_usuario']) && ($a['estado']!== 'RE')) ){
                            echo '<i class="fa fa-check-circle-o text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Confirmar tarea"></i>';
                          }
                          else { 
                            if($a['estado']=='RE'){
                              echo '<i   class="fa fa-check-circle" style="color: #A9A9A9 ; cursor: pointer; margin-left: 15px;" title="Tarea finalizada"></i>';
                            }
                          }                                
                          echo '</td>';
                          echo '<td>'.$a['tareadescrip'].'</td>';
                          if($a['usrName']!= null ){
                              //echo '<td>'.$a['usrName'].'</td>';
                            echo '<td>'.$a['usrLastName'].' '.$a['usrName'].'</td>';
                          } else echo '<td></td>';
                          if($a['fecha']!= null){
                            echo '<td>'.date_format(date_create($a['fecha']), 'd-m-Y').'</td>';
                          } else echo '<td></td>';
                          echo '<td style="text-align: center">'.($a['estado'] == 'C' ? '<small class="label label-default">Curso</small>' : ($a['estado'] == 'RE' ? '<small class="label label-default">Finalizada</small>' : '<small class="label label-default">Eliminada</small>')).'</td>';
                          echo '<td>';
                          if (strpos($permission,'Del') !== false && ($a['id_usuario']==$usrId || $usrId==1) && ($a['estado']!='RE') ){
                            echo '<i class="fa fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar"></i>';
                          }     
                          if (strpos($permission,'Edit') !== false && ($a['id_usuario']==$usrId || $usrId==1) && ($a['estado']!='RE') ){
                            echo '<i class="fa fa-user text-light-blue" style="cursor: pointer; margin-left: 15px;"" title="Asignación de usuario" data-toggle="modal" data-target="#modalSale"></i>';
                          }
                          if (strpos($permission,'Add') !== false && ($a['id_usuario']==$usrId || $usrId==1) && ($a['estado']!='RE') ){
                            echo '<i class="fa fa-calendar cous text-light-blue" style="cursor: pointer; margin-left: 15px;"" title="Asignación de Fecha" data-toggle="modal" data-target="#modalfecha" id="cous"></i>';
                          }
                          echo '</td>';
                          echo '</tr>';
                        }
                      }
                    }
                    ?> 
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
<!--<style type="text/css">
  .no {
  background: green;
}
</style>
<script>
$("td").each(function() {
    var value = this.innerHTML;
    console.log(value);
    if (value === 'No' || value === 'NO' || value === 'no') {
        $(this).parent('tr').addClass('no');
    } 
});
</script>
<style>
    .table, .table>tr, .table>td { color: #A9A9A9 ;}
</style>-->

<script>
  var codglo= "";
  var tareaglob= "";
  var idglob= "";
  var idtarea="";
  var idtarea1="";
  var idu=";"
  var no="";
$(document).ready(function(event) {
  //Otrabajo/index
  $('#listado').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    linkTo("<?php echo base_url(MAN); ?>Otrabajo/listOrden/<?php echo $permission; ?>");
    WaitingClose();
  });

  $('#agregar').click(function (e) {
    console.log("Estoy agregando ");
    var numord= $('#numord').val();
    no=numord;
    console.log("El id de orden es:");
    console.log(numord);
    var tarea1= $('#tarea').val();  //fa fa-check-circle-   FF3349, 3333FF, 36B441
    tareaglob= tarea1;
    var tr = "<tr id='"+numord+"'>"+
              "<td  style='text-align: center' ><i class='fa fa-check-circle-o' style='color: #006400 '; cursor: 'pointer' title='Confirmar tarea'></i></td>"+
              "<td>"+tarea1+"</td>"+
              "<td></td>"+
              "<td></td>"+
              "<td style='text-align: center' ><small class='label label-default' >Curso</td>"+
              "<td><i class='fa fa-times-circle' style='color: #A9A9A9 '; cursor: 'pointer' title='Eliminar'></i></td>"+
              "<td><i class=' fa fa-user' style='color: #A9A9A9 '; cursor: 'pointer' title='Asignacion de usuario' data-toggle='modal' data-target='#modalSale'></i></td>"+
              "<td  ><i class=' fa fa-calendar' style='color: #A9A9A9    '; cursor: 'pointer' title='Fecha' data-toggle='modal' data-target='#modalfecha'></i></td>"+
                             
            "</tr>";          
    console.log(tr); 
    var hayError = false;
    if (tarea1){
      $('#orden tbody').append(tr);
      $('#error').fadeOut('slow');
    }
    else {
      var hayError = true;
      $('#error').fadeIn('slow');
      return;
    }
    if(hayError == false){
    
      $('#error').fadeOut('slow');
    }
    //var celda= $(this).parents("tr").find("td").eq(4).html();  
    $(document).on("click",".fa-times-circle",function(){
      //var parent = $(this).closest('tr');
      //$(parent).remove();
      $.ajax({
              type: 'POST',
              data: { idtarea: idtarea},
              url: '<?php echo base_url(MAN); ?>Otrabajo/EliminarTarea',
              success: function(data){
                      console.log("TAREA ELIMINADA");
                      console.log(data);
                      //alert("ORDEN DE TRABAJO Eliminada");
                      regresa1();
                    
                    },
                
              error: function(result){
                    console.log(result);
                 }
      });
    });

   


    var parametros = {
     
      'id_orden': numord,
      'tareadescrip': tarea1,
      'estado': 'C'   
    };
    

    $.ajax({
      type: 'POST',
      data: { parametros:parametros},
      url: '<?php echo base_url(MAN); ?>Otrabajo/agregar_tarea',
      success: function(data){
            console.log(data);
            var datos= parseInt(data);
            idtarea= datos;
                                 
              
            },
      error: function(result){
            
            console.log(result);
          }
         
    });  
     
    $('#tarea').val(''); 
  }); 
//check de tarea realizada
  $(".fa-check-circle-o").click(function (e) { 
      
    var id_orden = $(this).parent('td').parent('tr').attr('id'); 
    console.log("Estoy realizando una tarea");
    console.log("id de tarea es:");
    console.log(id_orden);  
    $.ajax({
      type: 'GET',
      data: { id_orden: id_orden},
      url: '<?php echo base_url(MAN); ?>Otrabajo/TareaRealizada',
      success: function(data){
              console.log(data);
              regresa1();
                           
            },
        
      error: function(result){
            
            console.log(result);
          }
         // dataType: 'json'
      });
    
  });

  $("#fecha").datepicker({
      changeMonth: true,
      changeYear: true
  });
  //ELIMINAR
  $(".fa-times-circle").click(function (e) { 
      
    console.log("Estoy eliminando tarea");
    var idt = $(this).parent('td').parent('tr').attr('id'); 
    console.log("id de tarea es:");
    console.log(idt); 
    idtarea=idt;  
    $.ajax({
            type: 'POST',
            data: { idtarea: idtarea},
            url: '<?php echo base_url(MAN); ?>Otrabajo/EliminarTarea',
            success: function(data){
                    console.log("TAREA ELIMINADA");
                    console.log(data);
                    //alert("TAREA ELIMINADA");
                    regresa1();
                  
                  },
              
            error: function(result){
                  console.log(result);
               }
    });
  });
//ASIGNAR FECHA 
  $(".fa-calendar").click(function (e) { 
      
    var idta2 = $(this).parent('td').parent('tr').attr('id'); 
    console.log("id de tarea es:");
    console.log(idta2); 
    idtarea=idta2; 
      
  });
//ASIGNAR USUARIO
  $(".fa-user").click(function (e) { 
      
    var idtar = $(this).parent('td').parent('tr').attr('id'); 
    console.log("id de tarea es: estoy asignando usuario");
    console.log(idtar); 
    idtarea=idtar; 
      
  });
});

traer_usuarios();
function traer_usuarios(){

      $.ajax({
        type: 'POST',
        data: { },
        url: '<?php echo base_url(MAN); ?>Otrabajo/getusuario',
        success: function(data){
               
                var opcion  = "<option value='-1'>Seleccione...</option>" ; 
                $('#nomusu').append(opcion); 
                for(var i=0; i < data.length ; i++) {

                      var nombre = data[i]['usrLastName']+' '+data[i]['usrName'];
                      //data[i]['usrName'];

                      var opcion  = "<option value='"+data[i]['usrId']+"'>" +nombre+ "</option>" ; 

                    $('#nomusu').append(opcion); 
                                   
                }
              },
        error: function(result){
              
              console.log(result);
            },
            dataType: 'json'
      });
}
 /*$( function() {
      var dataF = function () {
          var tmp = null;
          $.ajax({
              'async': false,
              'type': "POST",
              'global': false,
              'dataType': 'json',
              'url': "Otrabajo/getusuario",
              'success': function (data) {
                  tmp = data;
              }
          });
          return tmp;
      }();

      $(function() {
          $(".nomusu").autocomplete({
              source: dataF,
              delay: 100,
              minLength: 1,
              focus: function(event, ui) {
                  // prevent autocomplete from updating the textbox
                  event.preventDefault();
                  // manually update the textbox
                  $(this).val(ui.item.label);
              },
              select: function(event, ui) {
                  // prevent autocomplete from updating the textbox
                  event.preventDefault();
                  // manually update the textbox and hidden field
                  $(this).val(ui.item.value);//label
                  $("#nomusu").val(ui.item.label); //value
                  //console.log("id articulo de orden insumo: ") 
                  //console.log(ui.item.value);                
              },
              
          });
      });
  } );*/
//guardando usuario asignado
function guardarmodif(){

  console.log("Estoy guardando usuario asignado");
  var idusu= $('#nomusu').val();
  //idu=idusu;
  console.log("El id de usuario es:");
  console.log(idusu);
  console.log("El id de tarea es :");
  console.log(idtarea);
        $.ajax({
                type: 'POST',
                data: { idtarea: idtarea, idusu:idusu },
                url: '<?php echo base_url(MAN); ?>Otrabajo/ModificarUsuario',
                success: function(data){
                        console.log(data);
                        
                        regresa1();
                      
                      },
                  
                error: function(result){
                      console.log(result);
                   }
        });
   
}

function guardarfecha(){
  var idusu= $('#nomusu').val();
  idu=idusu;
  var fe= $('#fecha').val();
  var idt2 = $(this).parent('td').parent('tr').attr('id');     
  console.log(idusu);
  console.log("La fechaa a guardar es :");
  console.log(fe);
  console.log("El id de tarea es :");
  console.log(idt2);
  console.log(idtarea);

        $.ajax({
                type: 'POST',
                data: { idtarea: idtarea, idusu:idusu, fe:fe},
                url: '<?php echo base_url(MAN); ?>Otrabajo/ModificarFecha',
                success: function(data){
                        console.log(data);
                        
                        regresa1();
                      
                      },
                  
                error: function(result){
                      console.log(result);
                   }
        });
   
}

function regresa1(){
//   var idusu= $('#nomusu').val();
 // no=idusu;
  var numord= $('#numord').val();
  no=numord;
  console.log(no);

  //$('#content').empty(); //listOrden
  //$('#modalSale').empty();  
  //$('#modalfecha').empty(); 
  linkTo("<?php echo base_url(MAN); ?>Otrabajo/cargartarea/<?php echo $permission; ?>/"+no+"");
  //WaitingClose();
 // WaitingClose();
  //WaitingClose();
}

$('#orden').DataTable({
    "aLengthMenu": [ 10, 25, 50, 100 ],
    "columnDefs": [ {
      "targets": [ 0, 5 ], 
      "searchable": false
    },
    {
      "targets": [ 0, 5 ], 
      "orderable": false
    } ],
    "order": [[1, "asc"]],
  });
</script>

<!-- Modal modalSale -->
<div class="modal fade" id="modalSale" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
  <div class="modal-dialog" role="document" style="width: 40%">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerro()"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalActionSale" class="fa fa-user" style="color: #A9A9A9" > </span> Asignación de usuario</h4> 
      </div>

      <div class="modal-body" id="modalBodySale">
        <div class="row" >
          <div class="col-sm-12 col-md-12">
            <fieldset> </fieldset>
            <br>
            <div class="col-xs-6">Usuario
              <select id="nomusu" name="nomusu" value="" class="form-control "></select>
            <!--  <input type="text" id="nomusu" name="nomusu" value=""  class="nomusu">-->
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

<!-- Modal modalSale -->
<div class="modal fade" id="modalfecha" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
  <div class="modal-dialog" role="document" style="width: 40%">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerro()"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalActionSale" class="fa fa-user" style="color: #A9A9A9" > </span> Asignación de Fecha</h4> 
      </div>

      <div class="modal-body" id="modalBodySale">
        <div class="row" >
          <div class="col-sm-12 col-md-12">
            <fieldset> </fieldset>
            <br>
            <div class="col-xs-6">Fecha
              <input type="text" id="fecha" name="fecha" value="" class="datepicker">
            </div>
                                                        
          </div>
        </div>
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerro()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal" onclick="guardarfecha()">Guardar</button>
      </div>

    </div>
  </div>
</div>

