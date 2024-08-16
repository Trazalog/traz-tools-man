<input type="hidden" id="permission" value="<?php echo $permission;?>">   
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          Revise que todos los campos obligatorios esten seleccionados
      </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-success" id="error2" style="display: none">
          <h4>EL EQUIPO POSEE COMPONENTES ASOCIADOS</h4>
      </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="alert alert-success" id="error3" style="display: none">
          <h4>EL EQUIPO NO POSEE COMPONENTES ASOCIADOS</h4>
      </div>
  </div>
</div>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title ">Asociar Componentes a Equipo</h2>
           <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">

          <form  id="form_comp" action="" accept-charset="utf-8">
            
            <div class="row" >
              <div class="col-xs-12">

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos de Equipo</h3>
                  </div><!-- /.panel-heading -->
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <label>Equipo <strong style="color: #dd4b39">*</strong> :</label>
                        <select id="equipo" name="equipo" class="form-control select" />
                        <input type="hidden" id="id_equipo" name="id_equipo">
                      </div>
                      <br>
                      <br>
                      <div class="col-xs-12">
                      </div>
                      <br>
                      <br>
                      <div class="col-xs-12 col-md-6"><label>Descripción:</label>
                        <textarea class="form-control" id="descrip" name="descrip"  cols="18" rows="3" disabled></textarea>
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <table class="table table-bordered table-responsive" id="tablacompo">
                          <thead>
                            <tr>
                              <!-- <th width="2%"></th>   -->                
                              <th>Componentes asociados:</th>
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                        </table>
                      </div>
                    </div><!-- /.row -->
                  </div><!-- /.panel-body -->
                </div><!-- /.panel-default -->

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title"><span class="fa fa-th-large"></span> Asociar Componentes</h3>
                  </div>
                  <div class="panel-body">
                    <div class="row" >
                      <div class="col-xs-12 col-md-6"><label>Componente <strong style="color: #dd4b39">*</strong> :</label>                        
                        <input type="text" name="componente" id="componente" class="form-control" placeholder="Buscar Componente...">
                        <input type="hidden" name="id_componente" id="id_componente" class="form-control">
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <br>
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalAddComp2"><i class="fa fa-plus"> Nuevo</i></a>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <br>
                        <label for="codigo">Código Trazable:</label>
                        <input type="text" name="codigo" id="codigo" class="form-control">
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <br>
                        <label for="sistema">Sistema <strong style="color: #dd4b39">*</strong> :</label>
                        <select id="sistema" name="sistema" class="form-control select2" />
                      </div>
                      <div class="col-xs-12">
                        <br>
                        <button type="button" class="btn btn-primary pull-right" id="addcompo"><i class="fa fa-check"> Asociar</i></button>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-xs-12">
                        <table class="table table-bordered" id="tablaequipos" border="1px">
                          <br>
                          <thead>
                            <tr>
                              <th>Acciones</th>
                              <th>ID</th>
                              <th>Equipo</th>
                              <th>Componente</th>
                              <th>Código</th>
                              <th>Sistema</th>
                              <th class="hidden"></th>
                              <th class="hidden"></th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- /.panel-body -->
                </div><!-- /.panel-default -->

              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default delete" id="listado2">Cancelar</button>
              <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
            </div>  <!-- /.modal footer -->
              <!-- / Modal -->
          </form>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</section><!-- /.content -->

<!-- Modal Agregar componente-->
<div class="modal" id="modalAddComp2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="fa fa-plus-square text-light-blue"></span> Agregar Componente</h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="alert alert-danger alert-dismissable" id="errorComponentes" style="display: none">
          <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
          Complete todos los datos obligatorios.
        </div>
        <form id="formComponentes" enctype="multipart/form-data">
          <div class="row" >
            <div class="alert alert-danger alert-dismissable" id="error1" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos...                  
            </div>
            <div class="col-xs-12 col-sm-6">
              <label>Marca <strong style="color: #dd4b39">*</strong>: </label>
              <select class="form-control input-md" id="ma" name="ma" />
            </div>
            <div class="col-xs-12 col-sm-6">
              <label>Descripción <strong style="color: #dd4b39">*</strong>: </label>
              <input type="text"   class="form-control input-md" id="descrip1"  name="descrip1" placeholder="Ingrese Descripcion" />
            </div>
            <div class="col-xs-12"><label>Información:</label>
              <textarea class="form-control" id="info" name="info" placeholder="Ingrese Informacion Adicional"></textarea>
            </div>
            <div class="col-xs-12">
              <label><span class="fa fa-file-pdf-o"></span> Adjuntar</label>
              <input id="inputPDF" name="inputPDF" type="file"  class="form-control input-md">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="guardarComponente">Guardar</button>
        </div>  <!-- /.modal footer -->
        </form>
      </div>  <!-- /.modal-body -->
    </div> <!-- /.modal-content -->

  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<script>



//si abre mas de dos modales...
$(document).on('show.bs.modal', '.modal', function () {
    if ($(".modal-backdrop").length > -1) {
        $(".modal-backdrop").not(':first').remove();
    }
});

var di="";
var ge="";
// Completa el select equipos
traer_equipo();
function traer_equipo(){
  $.ajax({
    type: 'POST',
    url: '<?php echo MAN; ?>Componente/traerequipo', 
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
      console.error(result);
    },
    dataType: 'json'
  });
}

// Trae componentes segun empresa (no equipos)

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


  var dataComponentes = {};
  traerComp();
  function traerComp(){
    dataComponentes = function() {
      $.ajax({
        'async': false,
        'type': "POST",
        'dataType': 'json',
        'url': '<?php echo MAN; ?>Componente/getcomponente',
        success: (data) => {return data;},
        error: () => error("Error","Error al traer componentes")
      });
      // return tmp;
    }();
  }

   

  // data busqueda por codigo de herramientas
  function dataCodigoCompo(request, response) {
    function hasMatch(s) {
      return s.toLowerCase().indexOf(request.term.toLowerCase())!==-1;
    }
    var i, l, obj, matches = [];

    if (request.term==="") {
      response([]);
      return;
    }
    
    //ordeno por codigo de herramientas
    dataComponentes = dataComponentes.sort(ordenaArregloDeObjetosPor("label"));

    for  (i = 0, l = dataComponentes.length; i<l; i++) {
      obj = dataComponentes[i];
      if (hasMatch(obj.codigo)) {
        matches.push(obj);
      }
    }
    response(matches);
  }

  autoCompletarComponentes();
function autoCompletarComponentes(){
  //busqueda por marcas de herramientas
  $("#componente").autocomplete({
    source:    dataComponentes,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_componente').val(ui.item.value); 
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#id_componente').val(ui.item.value);
    },
  })
  //muestro marca en listado de resultados
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
    .append( "<a>" + item.label + "</a>" )
    .appendTo( ul );
  };
}

// Trae sistemas
  traer_sistema();
  function traer_sistema(){
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo MAN; ?>Componente/getsistema',
      success: function(data){
        if(data['status']){
          $('#sistema').empty();
          var opcion  = "<option value='-1'>Seleccione...</option>" ; 
          $('#sistema').append(opcion); 
          for(var i=0; i < data.length ; i++){
            var nombre = data[i]['descripcion'];
            var opcion  = "<option value='"+data[i]['sistemaid']+"'>" +nombre+ "</option>" ; 
            $('#sistema').append(opcion); 
          }
        }
      },
      error: function(result){
        error();
        console.log(result);
      },
      dataType: 'json'
    });
  }

// Trae marcas para modal agregar componente - Chequeado
  traer_marca();
  function traer_marca(){
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo MAN;?>Componente/getmarca', 
      success: function(data){
        $('#ma').empty();
          var opcion  = "<option value='-1'>Seleccione...</option>" ; 
          $('#ma').append(opcion); 
          for(var i=0; i < data.length ; i++) {    
            var nombre = data[i]['marcadescrip'];
            var opcion  = "<option value='"+data[i]['marcaid']+"'>" +nombre+ "</option>" ; 
            $('#ma').append(opcion); 
          }
        },
      error: function(result){
        console.error(result);
      },
      dataType: 'json'
    });
  }

// Llena textarea Descrip segun id de euipo
  function traer_descripcion(idequipo){
    $.ajax({
      type: 'POST',
      data: { idequipo: idequipo},
      url: 'index.php/Componente/getequipo',
      success: function(data){
              //console.log(data);
              //console.log(data.datos);
              if(data=='nada'){
                var d='No hay Descripcion cargada';
                $('#descrip').append(d);
              }
              $('#descrip').val(data.datos);
          },
      error: function(result){
            console.error(result);
              },
            dataType: 'json'
          });   
  }

// Limpia modal agregar componente
  function limpiarModal(){
    $("#equipo").val("");
    $("#descrip").val("");
    $("#componente").val("");
    $("#codigo").val("");
    $('#tablacompo tbody tr').remove();
    $('#tablaequipos tbody tr').remove();
  }

// Guarda asociacion Equipo/componente 
  function guardar(){ 
    WaitingOpen("Guardando asociación a equipo");
    var id_equipo = new Array();     
    $("#tablaequipos tbody tr").each(function (index){
      var idequipo = $(this).attr('id');
      id_equipo.push(idequipo); 
    });  

    comp   = {};
    codigo = {};
    sistemaid = {};
    var j  = 1;
    var f  = 1;
    $("#tablaequipos tbody tr").each(function (index){
      var campo1, campo2, campo3, campo4, campo5, campo6,campo8;
      $(this).children("td").each(function (index2){
        switch (index2){
          case 0: 
            campo1 = $(this).text();
            break;
          case 1: 
            campo2 = $(this).text();
            break;
          case 2: 
            campo3 = $(this).text();
            break;
          case 3: 
            campo4    = $(this).text();
            //codigo[j] = campo4; 
            break;
          case 4: 
            campo5  = $(this).text();
            codigo[j] = campo5;
            //sistemaid[j] = campo5;
            break;
          case 5: 
            campo6  = $(this).text();
            break;
          case 6: 
            campo7  = $(this).text();
            comp[j] = campo7;
            break;
          case 7:
            campo8  = $(this).text();
            sistemaid[j] = campo8;
            j++;
            break;
        }
      });
      //console.log(codigo);
    });

    var idequipo = $('#equipo').val();
    var hayError = false;

    if( $('#tablaequipos').DataTable().data().any() ) {
      $.ajax({
        type: 'POST',
        data: {idequipo:idequipo, codigo:codigo, sistemaid:sistemaid, comp:comp, x:x, ge:ge},
        url: 'index.php/Componente/guardar_componente',
        success: function(data){
          console.log("entre por el guardado del componente equipo");
          //alert ("guardado con exito");
          cargarVista();
        },
        error: function(result){
          console.error(result);
        }
      });
      limpiarModal();
      WaitingClose();
    }
    else{
      hayError=true;
      $('#error').fadeIn('slow');
    }
    if(hayError == false){
      $('#error').fadeOut('slow');
    }
    WaitingClose();
  }



// Guarda un componente nuevo
  $('#guardarComponente').click(function(e) { //
    e.preventDefault();

    var descripcion = $('#descrip1').val();
    var informacion = $('#informacion').val();
    var marcaid     = $('#ma').val();
    //var pdf         = $('#input-4').val();
    var parametros  = {
      'descripcion' : descripcion,
      'informacion' : informacion,
      'marcaid'     : marcaid,
      
    };                                              
    //console.log("marcaid"+marcaid);
    var hayError = false; 
    $('#errorComponentes').hide();
    if ( marcaid < 0 ) {
      hayError = true;
      //console.log("entro x marcaid");
    }
    if( descripcion == "" ) {
      hayError = true;
      //console.log("entro x descrip");
    }

    if(hayError == true){
      $('#errorComponentes').fadeIn('slow');
    }
    else{
      var formData = new FormData(document.getElementById("formComponentes"));
      $.ajax({
        cache: false,
        contentType: false,
        data: formData,
        dataType: "html",
        processData: false,
        type: "POST",
        url: "index.php/Componente/agregarComponente", 
        success: function(data){
          traerComp();
          autoCompletarComponentes();
          //traer_componente();
          $("#modalAddComp2").modal("hide");
          //$("#modalAddComp2").css("display":"none");
          //$('.modal.in:visible').modal('hide');
          //$(".modal-backdrop.in").hide();
        },
        error: function(result){
            console.error("Error al crear componente");
            console.table(result);
        },
      });
    }
  });

// Crea la tabla con la asociacion de equipo/componente
  var equipoglob="";
  var x=0;
  $('#addcompo').click(function (e) {
    var $equipo       = $("select#equipo option:selected").html();
    var id_equipo     = $('#equipo').val();
    var codigo        = $('#codigo').val();
    var $componente   = $("#componente").val();
    var id_componente = $('#id_componente').val();
    var sistema       = $("select#sistema option:selected").html();
    var id_sistema    = $('#sistema').val();
    equipoglob        = id_equipo;
    
    var table   = $('#tablaequipos').DataTable();
    if(id_componente >0 && id_sistema >0 && equipoglob >0) {
      /*$('#tablaequipos tbody').append(tr);*/
      var rowNode = table.row.add( [
        "<i class='fa fa-ban elirow text-light-blue' style='cursor: 'pointer'></i>",
        id_equipo,
        $equipo,
        $componente,
        codigo,
        sistema,
        id_componente,
        id_sistema
      ] ).node();
      rowNode.id = id_equipo;
      table.draw();
      $( rowNode ).find('td').eq(5).addClass('hidden');
      $( rowNode ).find('td').eq(6).addClass('hidden');

      $('#error').fadeOut('slow');
      //$('#descrip').val('');
      //$('#tablacompo tbody tr').remove('');
      //$('#equipo').val('');
      $('#codigo').val('');
      $('#componente').val('');
      $('#sistema').val('');
    }
    else{
      var hayError = true;
      $('#error').fadeIn('slow')
    }
    if(hayError == false){ 
      $('#error').fadeOut('slow');
    }
    x++;
    //console.log(tr);
    $(document).on("click",".elirow",function(){
      //var parent = $(this).closest('tr');
      //$(parent).remove();
      table.row($(this).parents('tr')).remove().draw(false);
    });
  });

  // Vuelve al listado al guardar
  $('#listado, #listado2').click( function cargarVista(){
      wo();
      $('#content').empty();
      $("#content").load("<?php echo MAN; ?>Componente/asigna/<?php echo $permission; ?>");
      wc();
  });

  function cargarVista(){
      WaitingOpen();
      $('#content').empty();
      $("#content").load("<?php echo base_url(); ?>index.php/Componente/asigna/<?php echo $permission; ?>");
      WaitingClose();
  }

// Cuando selecciona equipo carga componentes asociados al equipo
var s=0; 
$('#equipo').change(function(){
  var idequipo = $(this).val();
  var d = $(this).parent('td').parent('tr').attr('id');
  di = d;
  ge = idequipo;
  //console.log("id de equipo: "+idequipo);
  $('#tablacompo tbody tr').html("");
  $('#descrip').html("");
  $.ajax({
    data: { idequipo:idequipo },
    dataType: 'json',
    type: 'POST',
    url: 'Componente/getcompo', 
    success: function(data){  
              console.table(data);
              if (data!= 0) {
                var de = data[0]['descripcion']; 
                var comp = data[0]['dee11'];
                $('#descrip').val(de); 
                for(var i=0; i < data.length ; i++){
                  if(data[i]['marcadescrip'] != null){
                    var  table = "<tr id='"+i+"'>"+   
                    "<td>"+data[i]['dee11']+" - "+data[i]['marcadescrip']+" - "+data[i]['informacion']+"</td>"+
                    "<td class='hidden' id='"+data[i]['id_componente']+"' >"+data[i]['id_componente']+"</td>"+
                    "</tr>";
                    $('#tablacompo').append(table); 
                    s++;
                  } else{
                    $('#tablacompo').append('<tr> <td>Equipo sin componentes asociados</td></tr>')
                  } 
                }  
                $('#tablacompo').val('');
              }
              else{
                traer_descripcion(idequipo); 
              } 
    },
    error: function(result){
          console.table(result);
          traer_descripcion(idequipo);
    },
  });

});

$('#tablaequipos').DataTable({
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
