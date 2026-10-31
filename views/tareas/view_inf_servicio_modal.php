 <!-- form  -->

 <form class="form-horizontal" role="form" id="form_order" action="" accept-charset="utf-8">

<input class="form-control" type="hidden" name="idTarBonita" id="idTarBonita" value="<?php echo $idTarBonita;?>" disabled/>


  <!--  ORDEN SERVICIO  -->
  <div class="row">        
  <div class="col-xs-12">
    <h3>Informe de Servicios</h3>
  </div>        
    <div class="col-xs-12 col-sm-6">
      
      <label for="numSolic">Número de OT</label>
      <input class="form-control numSolic form_equipos" name="numSolic" id="numSolic" value="<?php echo $id_ot;?>" disabled/>
    </div>
    <div class="col-xs-12 col-sm-6">
      <label for="causa">Descripción de la Tarea</label>
      <input type="text" name="causa" class="form-control causa form_equipos" id="causa" value="<?php echo $causa;?>" disabled>
    </div>
    <div class="col-xs-12 col-sm-6">
      <label for="fecha">Fecha</label>
      <input type="text" name="fecha" class="form-control fecha" id="fechaOrden" disabled>
    </div>
    <input type="hidden" name="id_ordenservicio" class="id_ordenservicio" id="id_ordenservicio" value="<?php echo $id_solicitudServicio;?>">
    <input type="hidden" name="id_ot" class="id_ot" id="id_ot" value="<?php echo $id_ot;?>">
    <input type="hidden" name="id_equipoSolic" class="id_equipoSolic" id="id_equipoSolic" value="<?php echo $id_eq; ?>">
  </div>
  <br>

  <!--  EQUIPOS  -->
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="fa fa-cogs icotitulo"></span> Datos de Equipo
    </div>

    <div class="panel-body">
      <div class="row">
        
        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="nomEquipo">Nombre Equipo</label>
          <input type="text" name="nomEquipo" class="form-control nomEquipo form_equipos" id="nomEquipo" value="" disabled>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="descEquipo">Descripción</label>
          <input type="text" name="descEquipo" class="form-control descEquipo form_equipos" id="descEquipo" value="" disabled>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="estado">Estado</label>
          <input type="text" name="estado" class="form-control estado form_equipos" id="estado" value="" disabled>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="sector">Sector</label>
          <input type="text" name="sector" class="form-control sector form_equipos" id="sector" value="" disabled>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="grupo">Grupo</label>
          <input type="text" name="grupo" class="form-control grupo form_equipos" id="grupo" value="" disabled>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
          <label for="ubicacion">Ubicación</label>
          <input type="text" name="ubicacion" class="form-control ubicacion form_equipos" id="ubicacion" value="" disabled>
        </div>

      </div>
    </div><!-- end .panel-body -->
  </div><!--  / <div class="panel panel-default"> -->   

  <!-- TABS -->
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-justified" role="tablist">
    <li role="presentation" class="active"><a href="#lecturaTab" aria-controls="lecturaTab" role="tab" data-toggle="tab">Lecturas</a></li>
    <li role="presentation"><a href="#tar" aria-controls="tar" role="tab" data-toggle="tab">Tareas</a></li>
    <li role="presentation"><a href="#rrhh" aria-controls="rrhh" role="tab" data-toggle="tab">Recursos Humanos</a></li> 
    <li role="presentation"><a href="#evidencias" aria-controls="evidencias" role="tab" data-toggle="tab">Evidencias </a></li>   
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="lecturaTab">
      <!--  LECTURAS REALIZADAS  -->
      <div class="panel panel-default">
        <div class="panel-heading"><span class="fa fa-file-text-o icotitulo" aria-hidden="true"></span> Lecturas Realizadas
        </div>
        <div class="panel-body">

          <div class="row">
            <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissable" id="errorLecturas" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                Por favor complete los campos obligatorios.
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissable" id="errorLecturas2" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                Por favor ingrese un valor de lectura correcto.
              </div>
            </div>
          </div>

          
          <input type="hidden" name="id-comp" class="id-comp" id="id-comp" value="" disabled>
          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <label for="lectura_inicio">Horómetro inicio:</label>
              <input type="number" class="form-control Horómetro_inicio" name="lectura_inicio" id="lectura_inicio" step="any" lang="en-150" placeholder="">
            </div>
            <div class="col-xs-12 col-sm-6">
              <label for="fecha_inicio">Fecha inicio <strong style="color: #dd4b39">*</strong> :</label>
              <input type="text" class="form-control fecha_inicio" id="fecha_inicio" value="" placeholder="">
            </div>
            <div class="col-xs-12 col-sm-6">
              <label for="lectura_fin">Horómetro fin:</label>
              <input type="number" class="form-control lectura_fin" name="lectura_fin" id="lectura_fin" step="any" lang="en-150" placeholder="">
              <span>Ingrese valor mayor a: </span><span id="spanNuevaLectura"></span>
            </div>
            <div class="col-xs-12 col-sm-6">
              <label for="fecha_fin">Fecha fin <strong style="color: #dd4b39">*</strong> :</label>
              <input type="text" class="form-control fecha_fin" id="fecha_fin" value="" placeholder="">
            </div>
          </div>
          <br><br>

        </div><!-- end .panel-body -->
      </div><!-- end .panel-default -->
    </div><!-- end .tabpanel -->

    <div role="tabpanel" class="tab-pane" id="tar">
      <!--  TAREAS REALIZADAS  -->
      <div class="panel panel-default">
        <div class="panel-heading"><span class="fa fa-file-text-o icotitulo" aria-hidden="true"></span> Tareas Realizadas
        </div>
        <div class="panel-body">
          
          <div class="row">
            <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissable" id="errorTareas" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
               Al menos una tarea debe ser cargada en la tabla.
              </div>
            </div>
          </div>

          <input type="hidden" name="id-comp" class="id-comp" id="id-comp" value="" disabled>
          <div class="row">
            <div class="col-xs-12">
              <label for="tareas">Tarea <strong style="color: #dd4b39">*</strong> :</label>
              <input type="text" class="form-control tareas" name="tareas" id="tareas" placeholder="Ingrese tarea realizada">
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <br>
              <button type="button" class="botones btn btn-primary" onclick="armarTablaTareas()">Agregar</button>
            </div>

          </div>

          <div class="row">
            <div class="col-xs-12">
              <hr>
              <div class="row">
                <div class="col-xs-12">
                  <div class="alert alert-danger alert-dismissable" id="errorTable" style="display: none">
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    Agregue al menos una tarea.
                  </div>
                </div>
              </div>
              <table class="table table-condensed table-responsive" id="tablalistareas">
                <thead>
                  <tr>
                    <th>Acciones</th>
                    <th>Tareas</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <br/><br/>
          </div>
        </div><!-- end .panel-body -->
      </div><!-- end .panel-default -->
    </div><!-- end .tabpanel -->

    <div role="tabpanel" class="tab-pane" id="rrhh">
      <!--  ORDEN DE RECURSOS HUMANOS  -->
      <div class="panel panel-default">
        <div class="panel-heading"><span class="fa fa-file-text-o icotitulo" aria-hidden="true"></span> Orden de Recursos Humanos</div>

        <div class="panel-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissable" id="errorRRHH" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                Revise que todos los campos obligatorios de Recursos Humanos estén seleccionados.
              </div>
            </div>
          </div>



          <input type="hidden" class="id-Operario" id="id-Operario" value="">
          <div class="row">
            <div class="col-xs-12">
              <label for="responsable">Responsable<strong style="color: #dd4b39">*</strong> :</label>
              <input type="text" class=" form-control responsable" id="responsable" name="responsable" value="<?php echo $nom_responsable;?>" disabled> 
              <input type="hidden" value="<?php echo $idresponsable;?>" id="id_responsable" name="id_responsable">
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
              <label for="operario">Apellido y Nombre <strong style="color: #dd4b39">*</strong> :</label>
              <input type="text" class=" form-control operario" id="operario" name="operario" value="" placeholder="Buscar...">
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <br>
              <button type="button" class="botones btn btn-primary" onclick="armarTablaRecursos()">Agregar</button>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <hr>
              <table class="table table-condensed table-responsive tabModRecursos" id="tabModRecursos">
                <thead>
                  <tr>
                    <th>Acciones</th>
                    <th>Apellido y Nombre</th>                           
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div><!-- end .panel-default -->
    </div><!-- end .tabpanel -->

    <div role="tabpanel" class="tab-pane" id="evidencias">
      <!--  EVIDENCIAS  -->
      <div class="panel panel-default">
        <div class="panel-heading"><span class="fa fa-camera icotitulo" aria-hidden="true"></span> Evidencias
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissable" id="errorEvidencias" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                Por favor seleccione un archivo para cargar.
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="archivoEvidencia">Seleccionar archivo:</label>
                <input type="file" class="form-control" id="archivoEvidencia" accept="image/*,.pdf,.doc,.docx">
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <button type="button" class="btn btn-primary" style="margin-top: 25px;" onclick="agregarEvidencia()">Agregar</button>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <hr>
              <div id="galeriaEvidencias" class="row">
                <!-- Aquí se mostrarán las miniaturas -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>      

<div class="pull-right">
  <!-- <button type="button" class="botones btn btn-primary" onclick="enviarOrden()">Guardar</button>  -->
  <button type="button" class="btn btn-success" id="hecho" onclick="enviarOrden()">Hecho</button>
</div>   

 

<script>
  
  // Datepicker 
    $("#fechaOrden").datepicker({
      dateFormat: 'yy-mm-dd',
      firstDay: 1
    }).datepicker("setDate", new Date());
    
    //datetimepicker
    $( "#fecha_inicio, #fecha_fin" ).datetimepicker({
      format: 'YYYY-MM-DD H:mm:ss',
      locale: 'es',
    });    
 
  // Trae datos de equipo
    var id_eq = $("#id_equipoSolic").val(); 
    $.ajax({
      'data' : { id_equipo:id_eq },
      'async': true,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo base_url(MAN); ?>Ordenservicio/getEquipo",
      'success': function (data) {
        //console.table(data);
        $("#nomEquipo").val(data.nomb_equipo);              
        $("#descEquipo").val(data.desc_equipo);
        $("#estado").val(data.estado);
        $("#marca").val(data.marca);
        $("#sector").val(data.sector);
        $("#ubicacion").val(data.ubicacion)
        $("#fecha_ingreso").val(data.fecha_ingreso);
        $("#fecha_baja").val(data.fecha_baja);
        $("#fecha_garantia").val(data.fecha_garantia);                 
        $("#grupo").val(data.grupo_desc);
      }
    });

    //obtener ultima lectura del equipo segun id_eq
    $.ajax({
        data: {
            idequipo: id_eq
        },
        dataType: 'json',
        type: 'POST',
        url: '<?php echo base_url(MAN); ?>Equipo/getEqPorId',
        success: function(data) {
            console.table(data);
            //console.log(data[0].lectura);
            if((data[0].lectura == '')||(data[0].lectura == null)){
              $("#spanNuevaLectura").text(0);
              $('#lectura_fin').val(0);
            }else{
              $("#spanNuevaLectura").text(data[0].lectura);
            }
        },
        error: function(result) {
            console.log(result);
        },
    });

  // Llenar tabla tareas
    var regTar = 0;              // variable incrementable en func, para diferenciar los inputs
    function armarTablaTareas(){ // inserta valores de inputs en la tabla 
      var tareas       = $("#tareas").val();
    
      var hayError = false;
      if (tareas == '') {
        hayError = true;
      }
    
      if(hayError == true){
        $('#errorTareas').fadeIn('slow');
        $('#tar').tab('show');
        return;
      }
      else{
        $('#errorTareas').fadeOut('slow');
        $('#errorTarea').fadeOut('slow');
        //agrego valores a la tabla
        $('#tablalistareas').DataTable().row.add( [
            "<i class ='fa fa-ban elirow text-primary' style='cursor:pointer'></i>",
            tareas
          ]
        ).draw();
    
        //limpio formulario
        $('#tareas').val('');
      }
    }    
    // elimina fila de la tabla listareas
    $(document).on("click",".elirow",function(){
      $('#tablalistareas').DataTable().row( $(this).closest('tr') ).remove().draw();
    });  
  
      
  // Trae Operarios
    var dataO = function () {
      var tmp = null;
      $.ajax({
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "<?php echo base_url(MAN); ?>Ordenservicio/getOperario",
        'success': function (data) {
            tmp = data;
        }
      });
      return tmp;
    }();
    $("#operario").autocomplete({
      autoFocus: true,
      delay: 300,
      minLength: 1,
      source: dataO,
      /*focus: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault();
        // manually update the textbox
        $(this).val(ui.item.label);
        $("#id-Operario").val(ui.item.value);
      },*/
      select: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault();
        // manually update the textbox and hidden field
        $(this).val(ui.item.label); 
        $("#id-Operario").val(ui.item.value);                 
      },
      /*open: function( event, ui ) {
        $("#ui-id-3").css('z-index',1050);
      },*/
      change: function (event, ui) {
        if (!ui.item) {
          this.value = '';
        }
      }
    });    
    // Llenar tabla operario
    function armarTablaRecursos() {   // inserta valores de inputs en la tabla 
      var operarioId = $("#id-Operario").val();
      var operario = $("#operario").val();
    
      var hayError = false;
      if (operario == '') {
        hayError = true;
      }
    
      if(hayError == true){
        $('#errorRRHH').fadeIn('slow');
        return;
      }
      else{
        $('#errorRRHH').fadeOut('slow');
        //agrego valores a la tabla
        $('#tabModRecursos').DataTable().row.add( [
            "<i class ='fa fa-ban elirow text-primary' id='delRRHH' style='cursor:pointer'></i>",
            operario
          ]
        ).node().id = operarioId;
        $('#tabModRecursos').DataTable().draw();
    
        //limpio formulario
        $('#operario').val('');
      }
    }    
    getRRHHOrdenTrabajo();
    function getRRHHOrdenTrabajo(){
      var idOT = $('#numSolic').val();
      $.ajax({
        'data' : { idOT:idOT },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "<?php echo base_url(MAN); ?>Ordenservicio/getRRHHOrdenTrabajo",
        'success': function (data) {
            var recursos = data['recursos'];
            if (!recursos || recursos.length === 0) return;
            for(i = 0; i < recursos.length; i++) {
              var idRRHH = recursos[i].value;
              var nameRRHH = recursos[i].label;
              $('#tabModRecursos').DataTable().row.add( [
              "<i class ='fa fa-ban elirow text-primary' id='delRRHH' style='cursor:pointer'></i>",
              nameRRHH
                ]
              ).node().id = idRRHH;
              $('#tabModRecursos').DataTable().draw();
            }    
        },
        'error': function(){
          //alert('Fallo la carga de RRHH...');
        }
        
      });
    }  
    // elimina fila de la tabla RRHH
    $(document).on("click","#delRRHH",function(){
      $('#tabModRecursos').DataTable().row( $(this).closest('tr') ).remove().draw();
    });  
      
  // Guarda orden de servicio
    function enviarOrden() {
      
      $('#error3').fadeOut('slow');
      $('#error2').fadeOut('slow');          
      $('#errorLecturas').fadeOut('slow');
      // Validar fechas antes de continuar
      if (!validarFechasInicioFin()) {
        return;
      }
      
      var hayError = false;
      var hayError2 = false;
      var hayError3 = false;
      var hayError4 = false;
      // validacion Lecturas Realizadas
      // if ($('#lectura_inicio').val() == '') {
      //   hayError3 = true;       
      // }
      // if ($('#lectura_fin').val() == '') {
      //   hayError3 = true;        
      // }
      // if ($("#spanNuevaLectura").text() != 0) {
      //   if($('#lectura_fin').val() < $("#spanNuevaLectura").text()){
      //        hayError3 = "lectura";
      //    }
      // }
      if ($("#spanNuevaLectura").text() != 0) {
        if($('#lectura_fin').val() <= $("#spanNuevaLectura").text()){
             hayError3 = "lectura";
         }
      }
      if ($("#spanNuevaLectura").text() != 0) {
        if($('#lectura_inicio').val() < $("#spanNuevaLectura").text()){
             hayError4 = "lectura";
         }
      }
      if ($('#fecha_inicio').val() == '') {
        hayError3 = true;      
      }
      if ($('#fecha_fin').val() == '') {
        hayError3 = true;       
      }
      // vallidacion tareas
      if( ! $('#tablalistareas').DataTable().data().any() ) {
        console.info("tabla tarea vacia");
        hayError2 = true;
      }

      // Error Lecturas Realizadas
      if(hayError3 == 'lectura'){
        $('#errorLecturas2').fadeIn('slow');
        activaTab('lecturaTab');
        return;
      }
      if(hayError4 == 'lectura'){
        $('#errorLecturas2').fadeIn('slow');
        activaTab('lecturaTab');
        return;
      }
      // Error Lecturas Realizadas
      if(hayError3 == true){
        $('#errorLecturas').fadeIn('slow');
        activaTab('lecturaTab');
        return;
      }
      // Error Lecturas Realizadas
      if(hayError2 == true) {
        $('#errorTareas').fadeIn('slow');
        activaTab('tar');
        return;
      }
      // Validación de recursos humanos vacíos
      if( ! $('#tabModRecursos').DataTable().data().any() ) {
        Swal.fire({
          title: 'Recursos Humanos',
          text: 'Ud. no ha cargado Recursos Humanos en el Informe, ¿Desea continuar de todas maneras?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Sí, guardar',
          cancelButtonText: 'No, ir a RRHH'
        }).then(function(result) {
          if (result.value) {
            guardarInformeServicio();
          } else {
            activaTab('rrhh');
          }
        });
        return;
      }
      guardarInformeServicio();
    }

    function guardarInformeServicio() {
      var tarea = new Array(); 
      var j = 0;
      $("#tablalistareas tbody tr").each(function (index) {
          var act = new Array();              
          var i = 0;
          $(this).children("td").each(function (index2) {
            if (index2) {
              act[i] = $(this).text();
              i++;
            }
          });             
          tarea[j] = act;
          j++;
      });        
      // operario 
      var operario = new Array(); 
      var j = 0;
      $("#tabModRecursos tbody tr").each(function (index) {
          var act = new Array();
          var id_operario = $(this).attr('id');
          var i = 0;
          $(this).children("td").each(function (index2) {
            if (index2) {
              act[i] = $(this).text();
              i++;
            }
          });
          act[i]=id_operario;
          operario[j] = act;
          j++;
      });         
      // Recolectar todas las evidencias
      const evidencias = [];
      document.querySelectorAll('.base64-data').forEach((input) => {
        // Buscar el nombre del archivo asociado a este input base64
        let nombre = '';
        // Para imágenes, el nombre esta en el siguiente input oculto con clase 'nombre-archivo'
        const nombreInput = input.nextElementSibling;
        if (nombreInput && nombreInput.classList.contains('nombre-archivo')) {
          nombre = nombreInput.value;
        } else {
          // Para otros tipos, el nombre esta en el parrafo anterior
          const nombreP = input.previousElementSibling;
          if (nombreP && nombreP.tagName === 'P') {
            nombre = nombreP.innerText;
          }
        }

        evidencias.push({
          valor_base64: input.value,
          nombre_archivo: nombre
        });
      });

      // Agregar las evidencias a los datos que se envían
      var datosInfoServicio = {
        'id_equipo'              : $("#id_equipoSolic").val(),
        'fecha'                  : $("#fechaOrden").val(),           
        'id_solicitudreparacion' : $("#id_ordenservicio").val(),           
        'id_ot'                  : $("#id_ot").val(),
        'horometro_inicio'       : $("#lectura_inicio").val(),
        'horometro_fin'          : $("#lectura_fin").val(),
        'fecha_inicio'           : $("#fecha_inicio").val(),
        'fecha_fin'              : $("#fecha_fin").val(),
        'idTarBonita'            : $("#idTarBonita").val(),
        'evidencias'             : evidencias
      };          
      WaitingOpen('Guardando cambios');  
      var func = 'guardar';        
      $.ajax({
        data: {
          datosInfoServicio: datosInfoServicio, 
          tarea: tarea,  
          operario: operario,
          func: func
        },
        type: 'POST',
        dataType: 'json',
        url: '<?php echo base_url(MAN); ?>Ordenservicio/setOrdenServ',
        success: function(result){
          WaitingClose();
          if(result['status']){
            $('#modalInforme').modal('hide');
            if ($('.man-panel').length) {
              // En el panel de la bandeja de tools: se cierra el panel y se actualiza la lista
              cargarVista();
            } else {
              linkTo("<?php echo base_url(MAN); ?>Tarea/index/<?php echo $permission; ?>");
            }
          }else{
            WaitingClose();
            $('#modalInforme').modal('hide');
            alert(result['status']+ 'resultado cierre tarea: ');   
          }            
        },
        error: function(result){
          console.error("Error en guardado...");
          console.table(result);
          WaitingClose();
          $('#modalInforme').modal('hide');
          alert(result['status']+ 'resultado cierre tarea: ');            
        },
      });
    }
    
  
  //activa el tab= tab
    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    };
  
  // datatables 
    /* ajusto el anocho de la cabecera de la tabla */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // https://datatables.net/reference/api/columns.adjust() states that this function is trigger on window resize
        $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
    });
    
    $('#tablalistareas').DataTable({
      "aLengthMenu": [ 10, 25, 50, 100 ],
        "columnDefs": [ {
          "targets": [ 0 ], 
          "searchable": false
        },
        {
          "targets": [ 0 ], 
          "orderable": false
        } ],
        "order": [[0, "asc"]],
    });  

    // Funciones para manejar evidencias
    function agregarEvidencia() {
      const archivo = document.getElementById('archivoEvidencia').files[0];
      
      if (!archivo) {
        $('#errorEvidencias').fadeIn('slow');
        return;
      }

      $('#errorEvidencias').fadeOut('slow');

      // Crear miniatura
      const reader = new FileReader();
      reader.onload = function(e) {
        const esImagen = archivo.type.startsWith('image/');
        let miniaturaHTML = '';
        
        // Obtener el base64
        const base64Data = e.target.result.split(',')[1];
        
        if (esImagen) {
          miniaturaHTML = `
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" style="margin-bottom: 20px;">
              <div class="thumbnail">
                <img src="${e.target.result}" style="width: 100%; height: 150px; object-fit: cover;">
                <div class="caption text-center">
                  <input type="hidden" class="base64-data" value="${base64Data}">
                  <input type="hidden" class="nombre-archivo" value="${archivo.name}">
                  <button class="btn btn-danger btn-sm" onclick="eliminarEvidencia(this)">
                    <i class="fa fa-trash"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>
          `;
        } else {
          miniaturaHTML = `
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" style="margin-bottom: 20px;">
              <div class="thumbnail">
                <div class="text-center" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                  <i class="fa fa-file fa-3x"></i>
                </div>
                <div class="caption text-center">
                  <p>${archivo.name}</p>
                  <input type="hidden" class="base64-data" value="${base64Data}">
                  <button class="btn btn-danger btn-sm" onclick="eliminarEvidencia(this)">
                    <i class="fa fa-trash"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>
          `;
        }
        
        document.getElementById('galeriaEvidencias').insertAdjacentHTML('beforeend', miniaturaHTML);
        
        // Limpiar formulario
        document.getElementById('archivoEvidencia').value = '';
      };
      
      reader.readAsDataURL(archivo);
    }

    function eliminarEvidencia(boton) {
      boton.closest('.col-xs-12').remove();
    }

// Función para validar fechas sin moment.js
function parseFechaInforme(str) {
  // Parseo manual de 'YYYY-MM-DD H:mm:ss' (H puede ser 1 o 2 dígitos)
  if (!str) return null;
  var m = str.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{1,2}):(\d{2}):(\d{2})$/);
  if (!m) return null;
  return new Date(+m[1], +m[2]-1, +m[3], +m[4], +m[5], +m[6]);
}

function validarFechasInicioFin() {
  var fechaInicio = $("#fecha_inicio").val();
  var fechaFin = $("#fecha_fin").val();
  // Solo validar si ambos campos tienen valor
  if (fechaInicio !== '' && fechaFin !== '') {
    var inicio = parseFechaInforme(fechaInicio);
    var fin    = parseFechaInforme(fechaFin);

    if (!inicio || !fin) {
      return true; // no se puede parsear, se deja pasar (otro bloque lo detecta como vacío)
    }
    // La fecha/hora de inicio debe ser estrictamente menor a la de fin
    if (inicio >= fin) {
      $('#errorLecturas').fadeIn('slow');
      $('#errorLecturas').find('h4').text('¡Error!');
      $('#errorLecturas').find('.fecha-error').remove();
      $('#errorLecturas').append('<div class="fecha-error">La fecha y hora de inicio debe ser menor a la fecha y hora de fin.</div>');
      activaTab('lecturaTab');
      return false;
    } else {
      $('#errorLecturas').fadeOut('slow');
      $('#errorLecturas').find('.fecha-error').remove();
      return true;
    }
  } else {
    $('#errorLecturas').fadeOut('slow');
    $('#errorLecturas').find('.fecha-error').remove();
    return true;
  }
}

// Asociar la validación a los eventos change y blur de los campos de fecha
$("#fecha_inicio, #fecha_fin").on('change blur', function() {
  validarFechasInicioFin();
});

</script>