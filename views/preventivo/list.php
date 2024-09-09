<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Tareas Preventivas por frecuencia</h3>
  </div><!-- /.box-header -->
  <div class="box-body">
    <?php
        if (strpos($permission,'Add') !== false) {
    ?>
        <button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="btnAgre">Agregar</button>             
    <?php 
        }
    ?>
    <table id="tabprev" class="table table-bordered table-hover">
      <thead>
          <tr>
          <th>Acciones</th>
          <th>Id tarea</th>
          <th>Tarea</th>
          <th>Equipo</th>
          <th>Grupo</th>
          <th>Componente</th>
          <th>Periodo</th>
          <th>Frecuencia</th>
          <th>Fecha Base</th>
          <th>Horas Hombre</th>               
          <!-- <th>Estado</th> -->
          </tr>
      </thead>
      <tbody>
        <?php
        if(count($list['data']) > 0){
        foreach($list['data'] as $a){
            //if ($a['estadoprev'] !== "AN") {
            $id  = $a['prevId'];
            $ide = $a['id_equipo'];
            echo '<tr id="'.$id.'" class="'.$ide.'">';
            echo '<td>';
            if (strpos($permission,'Add') !== false) {
            echo '<i class="fa fa-fw fa-times-circle eliminarPreventivo text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar"></i>';
            
            if( ($a['estado'] == 'S') || ($a['estado'] == 'PL') ) {
                echo '<i class="fa fa-fw fa-pencil editarPreventivo text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar"></i>';
            }   
            
            if ($a['prev_adjunto']) {
                echo '<a href="./assets/filespreventivos/'.$a['prev_adjunto'].'" target="_blank"><i class="fa fa-file-pdf-o text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Ver Archivo"></i></a>';                    
            } 

            }
            echo '</td>';
            echo '<td>'.$a['prevId'].'</td>';
            echo '<td>'.$a['deta'].'</td>';
            echo '<td>'.$a['des'].'</td>';
            echo '<td>'.$a['des1'].'</td>';
            echo '<td>'.$a['descripcion'].'</td>';
            echo '<td>'.$a['periodoDesc'].'</td>';
            echo '<td>'.$a['cantidad'].'</td>';
            echo '<td>'.date_format(date_create($a['ultimo']), 'd-m-Y').'</td>';
            echo '<td>'.$a['horash'].' h.h</td>';                            
            echo '</tr>';                    
          }
        }
        ?>
      </tbody>
    </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
<script>

  var isOpenWindow = false;
  var codhermglo="";
  var codinsumolo="";
  var preglob="";

  edit=0;  datos=Array();
  // Agregar Preventivo Nuevo
  $('#btnAgre').click( function cargarVista(){
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Preventivo/cargarpreventivo/<?php echo $permission; ?>");
    wc();
  });
  //Eliminar
  $(".eliminarPreventivo").click(function (e) {                 
    $('#modalaviso').modal('show');
    var idprev = $(this).parent('td').parent('tr').attr('id');
    $('#id').val(idprev);
  });  

  //Trae tareas y permite busqueda en el input
  var dataTarea = function() {
                  var tmp = null;
                  $.ajax({
                    'async': false,
                    'type': "POST",
                    'dataType': 'json',
                    'url': '<?php echo MAN; ?>Preventivo/gettarea',
                    success: (data) => {return tmp = data;},
                    error: () => {error("Error","Error al traer tareas");}
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
    $('#unidad').html(""); 
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo MAN; ?>Preventivo/getUnidTiempo', 
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
        error("Error","Error al traer unidades de tiempo");
        console.log(result);
      },
      dataType: 'json'
    });
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
      success: (data) => {return tmp = data;},
      error: () => {error("Error","Error al traer herramientas");}
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
      success: (data) => {return tmp = data;},
      error: () => {error("Error","Error al traer componentes");}
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

////// Edicion 

  // Editar Preventivo
  $(".editarPreventivo").click(function (e) { 
    $('#error').fadeOut('fast');        
    $('#modalSale').modal('show');
    $('#tarea').autocomplete( "option", "appendTo", ".eventInsForm" );

    var idprev = $(this).parent('td').parent('tr').attr('id');
    $('#id_preventivo').val(idprev);  
    console.info('id de prev a editar: ' + idprev);  

    $.ajax({
      type: 'POST',
      data: { idprev:idprev },
      dataType: 'json',
      url: 'index.php/Preventivo/geteditar',
      success: function(data){                         
        datos = {
          'idprev'       :data['datos'][0]['idprev'],
          'codigo'       :data['datos'][0]['codigo'],
          'id_equipo'    :data['datos'][0]['id_equipo'], // id_equipo
          'fecha_ingreso':data['datos'][0]['fecha_ingreso'],
          'marca'        :data['datos'][0]['marca'],
          'codigo'       :data['datos'][0]['codigo'],  // nombre del equipo
          'ubicacion'    :data['datos'][0]['ubicacion'],
          'descripcion'  :data['datos'][0]['descripcion'],                
          'id_tarea'     :data['datos'][0]['id_tarea'], //iria  id_tarea descripta
          'descrip_tarea':data['datos'][0]['descripta'],
          'perido'       :data['datos'][0]['perido'],
          'cantidad'     :data['datos'][0]['cantidad'],
          'ultimo'       :data['datos'][0]['ultimo'],
          'id_componente':data['datos'][0]['id_componente'],
          'critico1'     :data['datos'][0]['critico1'],
          'horash'       :data['datos'][0]['horash'],
          'prev_duracion':data['datos'][0]['prev_duracion'],
          'id_unidad'    :data['datos'][0]['id_unidad'],
          'prev_canth'   :data['datos'][0]['prev_canth'],
          'prev_adjunto' :data['datos'][0]['prev_adjunto'],
          'ultimo'       :data['datos'][0]['ultimo']
        };             
        var herram = data['herramientas'];             
        var insum  = data['insumos'];
        completarEdit(datos, herram, insum);                        
      },        
      error: function(result){            
        console.log(result);
      },
    });
  }); 
  // commpleta los campos de modal de edicion
  function completarEdit(datos, herram, insum){

    $('#id_equipo').val(datos['id_equipo']);
    $('#equipo option').remove();
    $('#equipo').append( '<option value="'+datos['id_equipo']+'" selected>'+datos['codigo']+'</option>' );
    $('#fecha_ingreso').val(datos['fecha_ingreso']);
    $('#marca').val(datos['marca']);
    $('#ubicacion').val(datos['ubicacion']);
    $('#descripcion').val(datos['descripcion']);
    $('#id_tarea').val(datos['id_tarea']);
    $('#tarea').val(datos['descrip_tarea']);
    traer_componente(datos['id_equipo'], datos['id_componente']);
    $('#ultimo').val(datos['ultimo']);    
    traer_periodo( datos['perido'] );
    $('#cantidad').val(datos['cantidad']);

    $('#hshombre').val(datos['horash']);    
    $('#duracion').val(datos['prev_duracion']);
    $('#unidad').val(datos['id_unidad']);
    $('#cantOper').val(datos['prev_canth']);

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

    recargaTablaAdjunto(datos['prev_adjunto']);

    $(document).on("click",".elirow",function(){
      var parent = $(this).closest('tr');
      $(parent).remove();
    });
  }
  // Calcula horas hombre por tiempo y unidades -chequeado
  function calcularHsHombre(){

    var entrada = $('#duracion').val(); 
    var unidad = $('#unidad').val();
    var operarios = $('#cantOper').val(); 
    var hs = 0;
    var hsHombre = 0;
    if ((entrada > 0)&&(unidad!= '-1')&&(operarios> 0)) {      
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
        $('#hshombre').val(hsHombre);      
      }  
    }

  // Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
  $('#duracion, #unidad, #cantOper').change(function(){
    if( $('#duracion').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!="")
      calcularHsHombre();
  });
  //Trae tareas y permite busqueda en el input
  var dataTarea = function() {
                    var tmp = null;
                    $.ajax({
                      'async': false,
                      'type': "POST",
                      'dataType': 'json',
                      'url': '<?php echo MAN; ?>Preventivo/gettarea',
                      success: (data) => {return tmp = data;},
                      error: () => {error("Error","Error al traer tareas");}
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
    },
  });

  // Trae unidades de tiempo - Chequeado
  $(function(){  
    $('#unidad').html(""); 
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo MAN; ?>Preventivo/getUnidTiempo', 
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
        error("Error","Error al traer unidades de tiempo");
        console.log(result);
      },
      dataType: 'json'
    });
  }); 
  // trae periodos de tiempo para edicion
  function traer_periodo(periodoE) {
    if (periodoE === undefined) {
      periodoE = null;
    }
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
      },
      error: function(result){  
        console.log(result);
      },
    });
  }
  // trae componentes segun id deequipo para edicion
  function traer_componente(id_equipo, id_componente){
    console.info("id de equipo: "+id_equipo);
    $('#componente').html("");
    $.ajax({
      data: {id_equipo:id_equipo },
      dataType: 'json',
      type: 'POST',
      url: 'index.php/Preventivo/getcomponente',
      async:false,
      success: function(data){
        $('#componente option').remove();
        var opcion  = "<option value='-1'>Seleccione...</option>" ; 
        $('#componente').append(opcion); 
        for(var i=0; i < data.length ; i++) {
          let selectAttr = '';
          if( (typeof id_componente !== 'undefined') && (data[i]['id_componente'] == id_componente) ) { selectAttr = 'selected';}
          let nombre = data[i]['descripcion'];
          let opcion = "<option value='"+data[i]['id_componente']+"' "+selectAttr+">" +nombre+ "</option>";
          $('#componente').append(opcion);    
        }
      },
      error: function(result){
        console.log(result);
      },
    });
  }
  // Guarda la Edicion completa de Preventivo
  function guardarEdicion(){
    var id_prevent = $('#id_preventivo').val();//
    var id_equipo  = $('#id_equipo').val();//
    var id_tarea   = $('#id_tarea').val();//
    var componente = $('#componente').val();
    var ultimo     = $('#ultimo').val();
    var periodo    = $('#periodo').val();    
    var cantidad   = $('#cantidad').val();
    var cantidadhm = $('#hshombre').val();    
    var duracion   = $('#duracion').val();
    var unidad     = $('#unidad').val();
    var cantOper   = $('#cantOper').val();   
      
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
    $.ajax({
      type: 'POST',
      data: { id_equipo: id_equipo,
        id_tarea: id_tarea,
        perido: periodo,
        cantidad: cantidad,
        ultimo : ultimo,
        id_componente: componente, 
        horash: cantidadhm,  
        prev_duracion: duracion,                                    
        cantOper: cantOper,
        unidad: unidad,
        id_prevent: id_prevent,
        idsherramienta: idsherramienta,
        cantHerram: cantHerram, 
        idsinsumo: idsinsumo, 
        cantInsum: cantInsum
      },
      url: 'index.php/Preventivo/editar_preventivo',  
      success: function(data){                 
        $('#modalSale').modal('hide');                    
        console.log("resp preventivo: ");
        console.log(data.resPrenvent);
        if (data.resPrenvent == false) {
          alert("Preventivo no se ha guardado correctamente...");
        }     
        if(data.respHerram == false){
          alert("Herramientas no se ha guardado correctamente...");
        }
        if (data.respInsumo == false) {
          alert("Insumos no se ha guardado correctamente...");
        }
        cargarVista();                     
      },
      error: function(result){      
        $('#modalSale').modal('hide');                      
        console.log(result);
        console.log("Entre por el error");            
      },
      dataType: 'json'
    });
  }
 
//Elimina Preventivo
function eliminaPrevent(){

  $('#modalaviso').modal('hide');
  var idP = $('#id').val();
  console.log(idP);    
  $.ajax({
    type: 'POST',
    data: { idprev: idP},
    url: '<?php echo MAN; ?>Preventivo/baja_preventivo',
    success: function(data){     
      hecho('Hecho','Preventivo eliminado correctamente');
      cargarVista();
    },                  
    error: function(result){
      error('Error','No se ha eliminado el preventivo');               
      console.log(result);
    },
    dataType: 'json'
  }); 
}

function cargarVista(){
  wo();
  $('#content').empty();  
  $("#content").load("<?php echo MAN; ?>Preventivo/index/<?php echo $permission; ?>", function() {
    wc();
  });
}

$('#tabprev').DataTable({
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

</script>

<!-- Modal editar-->
<div class="modal" id="modalSale" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="row">
        <div class="col-xs-12">
          <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            Revise que todos los campos obligatorios esten seleccionados
          </div>
        </div>
      </div><!-- /.row -->

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Preventivo</h4> 
      </div>

      <div class="modal-body" id="modalBodySale">

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del Equipo </h3>
          </div>

          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="equipo">Equipos <strong style="color: #dd4b39">*</strong></label>
                <select  id="equipo" name="componente" class="form-control" value="" disabled></select>
                <input type="hidden" id="id_equipo" name="id_equipo">
                <input type="hidden" id="id_preventivo" name="id_preventivo">
              </div>
            </div><!-- /.row -->
            <div class="row">
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
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea>
              </div>
            </div><!-- /.row -->
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">Nueva Tarea </h4>
          </div>

          <div class="panel-body">
            <div class="row">
              <input type="hidden" id="id" name="id">
              <div class="col-xs-12 col-sm-6 col-md-4">Tarea <strong style="color: #dd4b39">*</strong>:
                <input type="text" id="tarea" name="tarea" class="form-control">
                <input type="hidden" id="id_tarea" name="id_tarea">
              </div>  
              <input type="hidden" id="id" name="id">
              <div class="col-xs-12 col-sm-6 col-md-4">Componente <strong style="color: #dd4b39">*</strong>:
                <select id="componente" name="equipo" class="form-control input-md"   />
                <input type="hidden" id="id_componente" name="id_componente" />
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4">Fecha:
                <input type="date" id="ultimo"  name="ultimo" class="form-control " />
              </div>
            </div><!-- /.row -->
            <div class="row">
              <div class="col-xs-12 col-sm-6">Periodo <strong style="color: #dd4b39">*</strong>:
                <select id="periodo" name="periodo" class=" selectpicker form-control input-md">
                  <!--<option >Anual</option>- ->
                  <option value="0">Diario</option>
                  <option >Mensual</option>
                  <option >Periodos</option>
                  <option >Ciclos</option>
                  <option >Semestral</option>-->
                </select>
              </div>
              <div class="col-xs-12 col-sm-6">Frecuencia <strong style="color: #dd4b39">*</strong>:
                <input type="text"  id="cantidad" name="cantidad" class="form-control input-md" placeholder="Ingrese valor" />
              </div>
            </div><!-- /.row -->
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">Duración <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" id="duracion" name="duracion"/>
              </div> 
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                <select  id="unidad" name="unidad" class="form-control" />
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" id="cantOper" name="cantOper"/>
              </div>  
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label for="">Horas Hombre <strong style="color: #dd4b39">*</strong>:</label>
                <input type="text" class="form-control" name="hshombre" id="hshombre" disabled>
              </div> 
            </div>
          </div>
        </div>

        <div class="nav-tabs-custom">
          <!--tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#herramin" aria-controls="profile" role="tab" data-toggle="tab">Herramientas</a></li>
            <li role="presentation"><a href="#insum" aria-controls="messages" role="tab" data-toggle="tab">Insumos</a></li>
            <li role="presentation"><a href="#TabAdjunto" aria-controls="home" role="tab" data-toggle="tab">Adjunto</a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="herramin">
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="herramienta">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" id="herramienta"  name="" class="form-control" />
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
                      <input type="text" id="cantidadherram"  name="" class="form-control" placeholder="Ingrese Cantidad" />
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
                      <label for="insumo">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" id="insumo" name="insumo" class="form-control" />
                      <input type="hidden" id="id_insumo" name="">
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="">Descripcion:</label>
                      <input type="text" id="descript"  name="" class="form-control" />
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                      <label for="cant">Cantidad <strong style="color: #dd4b39">*</strong>:</label>
                      <input type="text" id="cant"  name="" class="form-control" placeholder="Ingrese Cantidad"/>
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

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal" onclick="guardarEdicion()">Guardar</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Eliminar Warning -->
<div class="modal" id="modalaviso">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" ><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" >&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id">
          <h4>¿Desea eliminar Preventivo?</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="eliminaPrevent();">Eliminar</button>
      </div>
    </div>
  </div>
</div>

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
