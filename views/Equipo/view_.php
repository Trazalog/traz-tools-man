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
          <h3 class="box-title">Equipo/Sector</h3>
          <?php
            if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="listado">Ver Listado</button>';
            }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">

          <form id="formAgregarEquipo">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 class="panel-title"><span class="fa fa-globe"></span> Ubicación del Equipo / Sector</h2>
              </div>

              <div class="panel-body">
                <div class="row">
                  <input type="hidden" id="unin" name="unin" class="form-control" value="<?php echo $empresa ?>">

                  <div class="col-md-6 col-sm-12"> <!-- FIRST COLUMN -->
                    <div class="row">
                      <div class="col-xs-8"><label>Área<strong style="color: #dd4b39">*</strong>:</label>
                        <select id="area" name="area" class="form-control" value=""></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addarea" data-toggle="modal" data-target="#modalarea"><i class="fa fa-plus"> Agregar</i></button>
                      </div>

                      <div class="col-xs-8"><label>Proceso<strong style="color: #dd4b39">*</strong>:</label>
                        <select  id="proceso" name="proceso" class="form-control" value=""></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addproceso" data-toggle="modal" data-target="#modalproceso"><i class="fa fa-plus"> Agregar</i></button>
                      </div>

                      <div class="col-xs-8"><label>Criticidad<strong style="color: #dd4b39">*</strong>:</label>
                        <select id="criticidad" name="criticidad" class="form-control"></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addcriti" data-toggle="modal" data-target="#modalcrit"><i class="fa fa-plus"> Agregar</i></button> 
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-12"> <!-- FIRST COLUMN -->
                    <div class="row">
                      <div class="col-xs-8"><label>Sector/Etapa<strong style="color: #dd4b39">*</strong>:</label>
                        <select id="etapa" name="etapa" class="form-control" value=""></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addetapa" data-toggle="modal" data-target="#modaletapa"><i class="fa fa-plus"> Agregar</i></button> 
                      </div>

                      <div class="col-xs-8"><label>Grupo<strong style="color: #dd4b39">*</strong>:</label>
                        <select id="grupo" name="grupo" class="form-control"></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addgrupo" data-toggle="modal" data-target="#modalgrupo"><i class="fa fa-plus"> Agregar</i></button> 
                      </div>

                      <div class="col-xs-8"><label>Cliente<strong style="color: #dd4b39">*</strong>:</label>
                        <select id="cliente" name="cliente" class="form-control"></select>
                      </div>
                      <div class="col-xs-4">
                        <label>&emsp;</label><br>
                        <button type="button" class="btn btn-primary" id="addcliente" data-toggle="modal" data-target="#modalCliente"><i class="fa fa-plus"> Agregar</i></button> 
                      </div>
                    </div>
                  </div>
                </div><!-- /.row -->
              </div><!-- /.panel-body -->
            </div><!-- /.panel -->

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del Equipo / Sector</h3>
              </div>

              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Código de Equipo<strong style="color: #dd4b39">*</strong></label>:
                    <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Ingrese Código de Equipo">
                    <input type="hidden" id="id_equipo" name="id_equipo">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Marca</label> <strong style="color: #dd4b39">*</strong>:
                    <!--   <input type="text" id="marca" name="marca" class="form-control" placeholder="Ingrese Marca"> -->
                    <select id="marca" name="marca" class="form-control" value="" ></select>   
                  </div>
                  <div class="col-xs-4">
                    <label>&emsp;</label><br>
                    <button type="button" class="btn btn-primary" id="addcriti"  data-toggle="modal" data-target="#modalMarca"><i class="fa fa-plus"> Agregar</i></button> 
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Descripción</label> <strong style="color: #dd4b39">*</strong>:
                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ingrese breve Descripción (Tamaño Máx 255 caracteres) ..." cols="20" rows="3"></textarea>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Número de serie: <strong style="color: #dd4b39">*</strong></label>
                    <input type="text" id="numse"  name="numse" class="form-control input-md" placeholder="Ingrese Número de serie">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Ubicación (Georeferencial)</label>:
                    <input type="text" id="ubicacion" name="ubicacion" class="form-control" placeholder="Ingrese Ubicación">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Fecha de Ingreso:</label>
                    <input type="date" id="fecha_ingreso"  name="fecha_ingreso" class="form-control input-md">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Fecha de Garantía:</label>
                    <input type="date" id="fecha_garantia"  name="fecha_garantia" class="form-control input-md">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Fecha de Lectura Inicial:</label>
                    <input type="date" id="fecha_ultimalectura"  name="fecha_ultima" class="form-control input-md">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Lectura Inicial:</label>
                    <input type="text" id="ultima_lectura"  name="ultima_lectura" class="form-control input-md" placeholder="Ingrese Ultima Lectura">
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <label>Archivo Adjunto:</label>
                    <input type="file" id="inputPDF" name="inputPDF[]" class="form-control input-md" formenctype="multipart/form-data" multiple>
                  </div>
                  <div class="col-xs-12">
                    <label>Descripción Técnica:</label>
                    <textarea class="form-control" id="destec" name="destec" placeholder="Ingrese Descripción Técnica..."></textarea>
                  </div>
                </div>

              </div><!-- /.panel-body-->   
            </div><!-- /.panel -->
            
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<script>
$('#listado').click( function(){
  wo();
  $('#content').empty();
  $("#content").load("<?php echo MAN; ?>Equipo/index/<?php echo $permission; ?>");
  wc();
});

// Trae area y llena el select
traer_area();
function traer_area(){
  $.ajax({
    data: {},
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Equipo/getarea', 
    success: function(data){
      var opcion  = "<option value='-1'>Seleccione...</option>" ; 
      $('#area').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['descripcion'];
        var opcion = "<option value='"+data[i]['id_area']+"'>" +nombre+ "</option>" ; 
        $('#area').append(opcion); 
      }
    },
    error: function(result){
      console.log(result);
    },
  });
}

// Trae proceso y llena el select
traer_proceso();
function traer_proceso(){
 $.ajax({
   type: 'POST',
   data: { },
   url: '<?php echo MAN; ?>Equipo/getproceso',
   success: function(data){
     var opcion = "<option value='-1'>Seleccione...</option>" ; 
     $('#proceso').append(opcion); 
     for(var i=0; i < data.length ; i++) 
     {    
       var nombre = data[i]['descripcion'];
       var opcion = "<option value='"+data[i]['id_proceso']+"'>" +nombre+ "</option>" ; 
       $('#proceso').append(opcion); 
     }
   },
   error: function(result){
     console.log(result);
   },
   dataType: 'json'
 });
}

// Trae criticidad y llena el select
traer_criticidad();
function traer_criticidad(){
  $.ajax({
    type: 'POST',
    data: { },
    url: '<?php echo MAN; ?>Equipo/getcriti', 
    success: function(data){
      var opcion = "<option value='-1'>Seleccione...</option>" ; 
      $('#criticidad').append(opcion); 
      for(var i=0; i < data.length ; i++){    
        var nombre = data[i]['descripcion'];
        var opcion = "<option value='"+data[i]['id_criti']+"'>" +nombre+ "</option>" ; 
        $('#criticidad').append(opcion);                                    
      }
    },
    error: function(result){
      console.log(result);
    },
    dataType: 'json'
  });
} 

// Trae etapa/sector y llena el select
traer_etapa();
function traer_etapa(){
  $.ajax({
    type: 'POST',
    data: { },
    url: '<?php echo MAN; ?>Equipo/getetapa',
    success: function(data){
      var opcion  = "<option value='-1'>Seleccione...</option>" ; 
      $('#etapa').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['descripcion'];
        var opcion  = "<option value='"+data[i]['id_sector']+"'>" +nombre+ "</option>" ; 
        $('#etapa').append(opcion);                                    
      }                
    },
    error: function(result){
      console.log(result);
    },
    dataType: 'json'
  });
} 

// Trae grupo y llena el select
traer_grupo();
function traer_grupo(){
  $.ajax({
    data: { },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Equipo/getgrupo', 
    success: function(data){
      console.log("estoy en area");
      console.log(data);
      var opcion  = "<option value='-1'>Seleccione...</option>" ; 
      $('#grupo').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['descripcion'];
        var opcion  = "<option value='"+data[i]['id_grupo']+"'>" +nombre+ "</option>" ; 
        $('#grupo').append(opcion); 
      }
    },
    error: function(result){
      console.log(result);
    },
  });
}

// Trae cliente y llena el select
traer_cliente();
function traer_cliente(){
  $.ajax({
    data: { },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Equipo/getcliente', 
    success: function(data){
      console.log("estoy en cliente");
      console.log(data);
      var opcion = "<option value='-1'>Seleccione...</option>" ; 
      $('#cliente').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['cliRazonSocial'];
        var opcion = "<option value='"+data[i]['cliId']+"'>" +nombre+ "</option>" ; 
        $('#cliente').append(opcion); 
      }
      $('modalCliente').modal('hide');
    },
    error: function(result){
      console.log(result);
    },
  });
}
  
// Trae marca y llena el select
traer_marca();
function traer_marca(){
  $.ajax({
    data: { },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo MAN; ?>Equipo/getmarca', 
    success: function(data){
      var opcion  = "<option value='-1'>Seleccione...</option>" ; 
      $('#marca').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['marcadescrip'];
        var opcion  = "<option value='"+data[i]['marcaid']+"'>" +nombre+ "</option>" ; 
        $('#marca').append(opcion); 
      }
    },
    error: function(result){
      console.log(result);
    },
  });
}  

// Agrega las areas nuevas
function guardararea(){ 
  var descripcion = $('#nomarea').val(); 
  var parametros = {
    'descripcion': descripcion        
  };                                              
  console.log(parametros);
  var hayError = false; 

  if( parametros !=0){
    wo();
    $.ajax({
      data:{parametros:parametros},
      dataType: 'json',
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_area",
      success: function(data){
        var datos = parseInt(data);
        console.log("ID area: " + datos);
        if(datos > 0){
          var texto = '<option value="'+datos+'">'+ parametros.descripcion +'</option>';
          $('#area').append(texto);
          hecho();
        }else{
          error();
        }
      },        
      error: function(result){
        error();
        console.log(result);
      },
    });
  }else{ 
    error("Error","Por favor complete la descripción del área, es un campo obligatorio");
  }
}

// Agrega las procesos nuevos
function guardarproceso(){ 
  var descripcion= $('#nomproceso').val(); 
  var parametros = {
    'descripcion': descripcion
  }; 
  console.log(parametros);
  var hayError = false; 

  if(parametros != 0){
    wo();
    $.ajax({
      dataType: 'json',
      data:{parametros:parametros},
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_proceso", 
      success: function(data){
        var datos = parseInt(data);
        console.log("ID proceso: " + datos);
        if(datos > 0){ 
          var texto = '<option value="'+datos+'">'+ parametros.descripcion +'</option>';
          $('#proceso').append(texto);
          hecho();
        }else{
          error();
        }
      },
      error: function(result){
        error();
        console.log("entro por el error");
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });
  }else{ 
    error("Error","Por favor complete la descripción del proceso, es un campo obligatorio");
  }
}

// Agrega criticidad nueva
function guardarcri(){ 
  var descripcion= $('#de').val(); 
  var parametros = {
    'descripcion': descripcion 
  };
  console.log(parametros);
  var hayError = false; 

  if(parametros != 0){
    wo();
    $.ajax({
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_criti", 
      dataType: 'json',
      data:{parametros:parametros},
      success: function(data){
        var datos = parseInt(data);
        console.log("ID criticidad: " + datos);
        if(datos > 0){ 
          var texto = '<option value="'+datos+'">'+ parametros.descripcion +'</option>';
          $('#criticidad').append(texto);
          hecho();
        }else{
          error();
        }
      }, 
      error: function(result){
        console.log("entro por el error");
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });
  }else{ 
    error("Error","Por favor complete criticidad, es un campo obligatorio");
  }
}

// Agrega sector/etapa nuevos
function guardaretapa(){ 
  var descripcion= $('#nometapa').val(); 
  var parametros = {
    'descripcion': descripcion,
    'estado': 'AC',        
  };
  console.table(parametros);
  var hayError = false; 

  if(parametros != 0){
    wo();
    $.ajax({
      data:{parametros:parametros},
      dataType: 'json',
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_etapa", 
      success: function(data){
        var datos= parseInt(data);
        console.log("ID etapa: " + datos);
        if(datos > 0){  
          var texto = '<option value="'+datos+'">'+ parametros.descripcion +'</option>';
          $('#etapa').append(texto);
          hecho();
        }else{
          error();
        }            
      },        
      error: function(result){
        console.log("entro por el error");
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });     
  }else{ 
    error("Error","Por favor complete la descripción de la etapa, es un campo obligatorio");
  }
}

// Agrega las grupos nuevos
function guardargrupo(){ 
  var descripcion= $('#nomgrupo').val(); 
  var parametros = {
    'descripcion': descripcion,
    'estado': 'AC',
  };                                              
  console.table(parametros);
  var hayError = false; 

  if(parametros != 0){
    wo();
    $.ajax({
      data:{parametros:parametros},
      dataType: 'json',
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_grupo", 
      success: function(data){
        var datos = parseInt(data);
        console.log("ID grupo: " + datos);
        if(datos > 0){
          var texto = '<option value="'+datos+'">'+ parametros.descripcion +'</option>';
          $('#grupo').append(texto);
          hecho();
        }else{
          error();
        }  
      },
      error: function(result){
        console.log("entro por el error");
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });     
  }else{ 
    error("Error","Por favor complete la descripción del grupo, es un campo obligatorio");
  }
}

// Agrega sector/etapa nuevos
function guardarCliente(){
  wo();

  var cliName        = $('#cliName').val();
  var cliLastName    = $('#cliLastName').val();
  var cliDni         = $('#cliDni').val();
  var cliAddress     = $('#cliAddress').val();
  var cliPhone       = $('#cliPhone').val();
  var cliEmail       = $('#cliEmail').val();
  var cliRazonSocial = $('#cliRazonSocial').val();

  var parametros = {
    'cliName'     : cliName,
    'cliLastName' : cliLastName,
    'cliDni'      : cliDni,
    'cliAddress'  : cliAddress,
    'cliPhone'    : cliPhone,
    'cliEmail'    : cliEmail,
    'cliRazonSocial' : cliRazonSocial,
    'estado'      : 'AC',
  };
  console.table(parametros);

  if( cliName == '' || cliLastName == ''|| cliDni == ''|| cliAddress == ''|| cliPhone == ''|| cliEmail == ''|| cliRazonSocial == ''){
    wc();
    error('Error','Complete por favor los campos obligatorios.');
    return;
  }else{
    wo();
    $.ajax({
      data:{parametros:parametros},
      dataType: 'json',
      type:"POST",
      url: "<?php echo MAN; ?>Equipo/agregar_cliente",
      success: function(data){
        var datos= parseInt(data);
        console.log("ID cliente: " + datos);
        if(datos > 0){  
            var texto = '<option value="'+datos+'">'+ parametros.cliRazonSocial +'</option>';
            $('#cliente').append(texto);
            $('#modalCliente').modal('hide');
            hecho();
        }else{
          error();
        }            
      },        
      error: function(result){
        error();
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });
  }
}

// Agrega las grupos nuevos
function guardarmarca(){ 
  var descripcion = $('#nombreMarca').val(); 
  var parametros = {
    'marcadescrip': descripcion,
    'estado': 'AC',        
  };                                              
  console.table(parametros);
  var hayError = false; 
  if(parametros != 0){
    wo();
    $.ajax({
      data:{parametros:parametros},
      dataType: 'json',
      type: 'POST',
      url: "<?php echo MAN; ?>Equipo/agregar_marca", 
      success: function(data){
        var datos= parseInt(data);
        console.log("ID marca: " + datos);
        if(datos > 0){  
          var texto = '<option value="'+datos+'">'+ parametros.marcadescrip +'</option>';
          $('#marca').append(texto);
          hecho();
        }else{
          error();
        }
      },
      error: function(result){
        error();
        console.log(result);
      },
      complete: function(){
        wc();
      }
    });
  }else{ 
    error("Error","Por favor complete la descripcion de Marca, es un campo obligatorio");
  }
}

// Guarda equipo/sector nuevo - Chequeado
$("#formAgregarEquipo").submit( function (event){
  //debugger;
  event.preventDefault();
  $('span #respuesta').text('');

  var hayError = false;
  hayError     = validarCampos();

  if(hayError == true){
    $('#error').fadeIn('slow');
    WaitingClose();
    return;
  }else{
    $('#error').fadeOut('slow');

    var formData = new FormData($("#formAgregarEquipo")[0]);
    wo();
    $.ajax({
      cache:false,
      contentType:false,
      data:formData,
      dataType: 'json',
      processData:false,
      url: '<?php echo MAN; ?>Equipo/guardar_equipo',
      type: 'POST',
      success: (data) => {
        const confirm = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
        });
        if (data.code === '200') {
          confirm.fire({
              title: 'Perfecto!',
              text: "Se agrego el equipo correctamente!",
              type: 'success',
              showCancelButton: false,
              confirmButtonText: 'Hecho'
          }).then((result) => {
            regresa();        
          });
        } else {
          confirm.fire({
              title: 'Error',
              text: "Se produjo un error al agregar el equipo",
              type: 'error',
              showCancelButton: false,
              confirmButtonText: 'Hecho'
          }).then((result) => {
            regresa();        
          });
        }
      },
      error: (result) => {
        error("Error","Error al agregar equipo.");
        console.log(result);
      },
      complete: () => {
        wc();
      }
    })
  }             
});

// Chequea los campos llenos
function validarCampos(){
  var hayError = "";
  if ( $('#unin').val() == -1 ) {
      hayError = true;
  }
  if ( $('#area').val() == -1 ) {
      hayError = true;
  }
  if ( $('#proceso').val() == -1 ) {
      hayError = true;
  }
  if ( $('#criticidad').val() == -1 ) {
      hayError = true;
  }
  if ( $('#etapa').val() == -1 ) {
      hayError = true;
  }
  if ( $('#codigo').val() == "" ) {
      hayError = true;
  }
  if ( $('#marca').val() == -1 ) {
      hayError = true;
  }
  if ( $('#descripcion').val() == "" ) {
      hayError = true;
  }
  if ( $('#numse').val() == "" ) {
      hayError = true;
  }

  if($('#grupo').val() == -1){
    hayError = true;
  }

   if($('#cliente').val() == -1){
    hayError = true;
  }

  return hayError;
}

// Recarga vista list
function regresa(){
  $('#content').empty();
  $("#content").load("<?php echo MAN; ?>Equipo/index/<?php echo $permission; ?>");
  wc();    
}
</script>


<!-- Modal Codigo Existente-->
<div class="modal" id="modalResp">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Equipo</h4>
      </div>
      <div class="modal-body" id="cuerpoModalEditar">
       <h5><span id="respuesta"></span></h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<!-- /  Modal Codigo Existente-->


<!-- Modal area-->
<div class="modal" id="modalarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Área </h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Área: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomarea"  name="nomarea" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardararea()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Proceso-->
<div class="modal" id="modalproceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Proceso </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomproceso"  name="nomproceso" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarproceso()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal criticidad-->
<div class="modal" id="modalcrit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Sector </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Criticidad <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="de"  name="de" placeholder="Ingrese criticidad" class="form-control"/>
          </div>
        </div>
      </div>  <!-- /.modal-body -->
       
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarcri()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Etapa-->
<div class="modal" id="modaletapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Sector/Etapa de Proceso </h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Sector/Etapa de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nometapa"  name="nometapa" placeholder="Ingrese Nombre o Descripcion" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardaretapa()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Grupo-->
<div class="modal" id="modalgrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Grupo </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Grupo: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomgrupo"  name="nomgrupo" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardargrupo()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Clientes --> 
<div class="modal" id="modalCliente">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar Cliente</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos
            </div>
          </div>
        </div>

        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Nombre <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control" id="cliName" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Apellido <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control" id="cliLastName" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Dni <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control"  id="cliDni" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control"  id="cliAddress" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Telefono <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control"  id="cliPhone" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12">
            <input type="text" class="form-control"  id="cliEmail" >
          </div>
        </div><br>
        <div class="row"> 
          <div class="col-xs-12">
            <label style="margin-top: 7px;">Razon Social <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text" class="form-control"  id="cliRazonSocial" >
          </div>
        </div><br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="guardarCliente()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal fade -->
<!-- Modal -->

<!-- Modal Marca-->
<div class="modal" id="modalMarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Marca </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Marca: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nombreMarca"  name="nombreMarca" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarmarca()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->    

<!-- Modal empresa -->
<div class="modal fade" id="modalOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span>  Agregar Empresa </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de la empresa <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nombre"  name="nombre" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="guardaremp()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div>  <!-- /.modal-body -->
  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Unidad indus.-->
<div class="modal fade" id="modalunidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square" style="color: #A4A4A4"  ></span>     Agregar Unidad Industrial </h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        
        <div class="row" >
                               
            <div class="col-xs-12"><h4>Nombre de la unidad industrial <strong style="color: #dd4b39">*</strong>: </h4>
              <input type="text"  id="nombreunidad"  name="nombreunidad" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
            </div>
                    
                    
          </div>
        </div>
      </div>
       
     

      <div class="modal-footer">
       
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="guardarunidad()" >Guardar</button>
      </div>  <!-- /.modal footer -->

       </div>  <!-- /.modal-body -->
    </div> <!-- /.modal-content -->

  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal criticidad-->
<div class="modal fade" id="modalcrit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Sector </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Criticidad <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="de"  name="de" placeholder="Ingrese criticidad" class="form-control"/>
          </div>
        </div>
      </div>  <!-- /.modal-body -->
       
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarcri()" >Guardar</button>
      </div>  <!-- /.modal footer -->

    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal area-->
<div class="modal fade" id="modalarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Área </h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Área: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomarea"  name="nomarea" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardararea()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Proceso-->
<div class="modal fade" id="modalproceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Proceso </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomproceso"  name="nomproceso" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarproceso()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Etapa-->
<div class="modal fade" id="modaletapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Sector/Etapa de Proceso </h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Sector/Etapa de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nometapa"  name="nometapa" placeholder="Ingrese Nombre o Descripcion" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardaretapa()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Grupo-->
<div class="modal fade" id="modalgrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Grupo </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Grupo: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nomgrupo"  name="nomgrupo" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardargrupo()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Marca-->
<div class="modal fade" id="modalMarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-plus-square text-light-blue"></span> Agregar Marca </h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="row" >
          <div class="col-xs-12">
            <label>Nombre de Marca: <strong style="color: #dd4b39">*</strong>: </label>
            <input type="text"  id="nombreMarca"  name="nombreMarca" placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30"/>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarmarca()" >Guardar</button>
      </div>  <!-- /.modal footer -->
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Clientes --> 
<div class="modal" id="modalCliente">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar Cliente</h4>
      </div>
      <div class="modal-body">
								<form id="nuevoCliente">
										<div class="row">
												<div class="col-xs-12">
														<div class="alert alert-danger alert-dismissable" id="error" style="display: none">
																<h4><i class="icon fa fa-ban"></i> Error!</h4>
																Revise que todos los campos esten completos
														</div>
												</div>
										</div>
										<div class="row">
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Nombre <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido" id="cliName" >
												</div>
										</div><br>
										<div class="row">
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Apellido <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido" id="cliLastName" >
												</div>
										</div><br>
										<div class="row"> 
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Dni <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido"  id="cliDni" >
												</div>
										</div><br>
										<div class="row"> 
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido"  id="cliAddress" >
												</div>
										</div><br>
										<div class="row">
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Telefono <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido"  id="cliPhone" >
												</div>
										</div><br>
										<div class="row">
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>
												</div>
												<div class="col-xs-5">
														<input type="text" class="form-control requerido"  id="cliEmail" >
												</div>
										</div><br>
										<div class="row">
												<div class="col-xs-12">
														<label style="margin-top: 7px;">Razon Social <strong style="color: #dd4b39">*</strong>: </label>
														<input type="text" class="form-control requerido"  id="cliRazonSocial" >
												</div>
										</div><br>
								</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="guardarCliente()" >Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><!-- Modal -->