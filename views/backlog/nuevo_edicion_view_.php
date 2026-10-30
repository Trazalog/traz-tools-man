<input type="hidden" id="permission" value="<?php echo $permission; ?>">
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            Revise que todos los campos obligatorios esten seleccionados
        </div>
    </div>
</div>
<section class="content">
  <?php echo $cabecera; ?>
  <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Edición Nuevo Backlog</h3>

                    <div class="clearfix"></div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <!-- botones Tomar y soltar tareas -->

                        <!-- id de task en bonita -->
                        <input type="text" class="hidden" id="idTarBonita" value="<?php echo $TareaBPM['id'] ?>">
                        <input type="text" class="hidden" id="idBacklog" value="<?php echo $info[0]['backId'] ?>">
                        <input type="hidden" id="id_backlog" name="id_backlog" value="<?php echo $info[0]['backId'] ?>">
                        <input type="hidden" id="back_adjunto" name="back_adjunto" value="<?php echo $info[0]['back_adjunto'] ?>">

                    </div>

                </div><!-- /.box-header -->
                
                
                  <div class="panel panel-default">

                  <div class="box-body">
                      <div class="row">
                          <div class="col-sm-12 col-md-12">
                              <div role="tabpanel" class="tab-pane">
                                  <div class="form-group">

                                      <!-- Nav tabs -->
                                      <ul class="nav nav-tabs" role="tablist">
                                          <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Tareas</a></li>
                                          <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Comentarios</a></li>
                                          <li <?php echo ($device == 'android' ? 'class= "hidden"' : 'class= ""') ?>role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Vista
                                                  Global
                                              </a></li>
                                      </ul>

                                      <!-- Tab panes -->
                                      <div class="tab-content">

                                          <div role="tabpanel" class="tab-pane active" id="home"> 
                                            <div class="panel-body">
                                                <!-- botones Tomar y soltar tareas -->
                                                <?php
                                                echo "<button class='btn btn-block btn-success' id='btontomar' style='width: 100px; margin-top: 10px ;display: inline-block;' onclick='tomarTarea()'>Tomar tarea</button>";
                                                echo "&nbsp";
                                                echo "&nbsp";
                                                echo "&nbsp";
                                                echo "<button class='btn btn-block btn-danger grupNoasignado' id='btonsoltr' style='width: 100px; margin-top: 10px; display: inline-block;' onclick='soltarTarea()'>Soltar tarea</button>";
                                                echo "</br>";
                                                echo "</br>";
                                                ?>
                                                <input type="text" class="form-control hidden" id="asignado" value="<?php echo $TareaBPM["assigned_id"] ?>"> 

                                                <!-- <h4 class="panel-heading">INFORMACION:</h4> -->
                                                <div class="panel-heading">INFORMACION:</div>

                                                <div class="form-group">
                                                    <div class="col-sm-6 col-md-6">
                                                        <label for="tarea">Tarea</label>
                                                        <input type="text" class="form-control" id="tareaBac" value="<?php echo $TareaBPM['displayName'] ?>" disabled><!-- id de listarea -->
                                                        <input type="text" class="hidden" id="tbl_listarea" value="<?php echo $datos[0]['id_listarea'] ?>">
                                                        <input type="text" class="hidden" id="idform" value="<?php echo $idForm ?>">
                                                        <!-- id de task en bonita -->
                                                        <input type="text" class="hidden" id="idTarBonita" value="<?php echo $idTarBonita ?>">
                                                        <input type="text" class="hidden" id="esTareaStd" value="<?php echo $infoTarea['visible'] ?>">
                                                        <input type="text" class="hidden" id="case_id" value="<?php echo $TareaBPM['caseId'] ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-6 col-md-6">
                                                        <label for="fecha">Fecha de Creación</label>
                                                        <input type="text" class="form-control" id="fecha" placeholder="" value="<?php echo $TareaBPM['last_update_date'] ?>" disabled>
                                                    </div>
                                                </div><br>

                                                <div class="form-group ">
                                                    <div class="col-sm-6 col-md-6 ">
                                                        <label for="ot ">Orden de Trabajo:</label>
                                                        <input type="text " class="form-control " id="ot" placeholder=" " value="<?php echo $datos[0]['id_orden'] ?>" disabled>
                                                    </div>
                                                </div><br>

                                                <div class="form-group">
                                                    <div class="col-sm-6 col-md-6">
                                                        <label for="duracion_std">Duracion Estandar (minutos):</label>
                                                        <input type="text" class="form-control" id="duracion_std" placeholder="" value="<?php echo $datos[0]['duracion_std']  ?>" disabled>
                                                    </div></br>
                                                </div><br>

                                                <div class="form-group">
                                                    <div class="col-sm-12 col-md-12">
                                                        <label for="detalle">Detalle</label>
                                                        <textarea class="form-control" id="detalle" rows="3" disabled><?php echo $TareaBPM['displayDescription'] ?></textarea>
                                                    </div>
                                                </div></br> </br> </br> </br> </br>                                               
                                                
                                            </div>    <!-- /.panel-body -->

                                          
                                          
                                          
                                          <!--  Datos del equipo -->
                                          <div class="panel panel-default oculto">
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del equipo</h3>
                                            </div>
                                            <div class="panel-body">
                                              <div class="row">
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="equipo">Equipos <strong style="color: #dd4b39">*</strong></label>
                                                      <input id="equipoDescrip" name="equipoDescrip" class="form-control" value="<?php echo $info[0]["codigo"] ?>" disabled />
                                                      <input type="hidden" id="equipo" name="equipo" class="form-control" value="<?php echo $info[0]["id_equipo"] ?>" />
                                                      <!-- <input type="hidden" id="id_equipo" name="id_equipo">-->
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="fecha_ingreso">Fecha:</label>
                                                      <input type="text" id="fecha_ingreso" name="fecha_ingreso" class="form-control input-md" value="<?php echo $info[0]["fecha_ingreso"] ?>" disabled />
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="marca">Marca:</label>
                                                      <input type="text" id="marca" name="marca" class="form-control input-md" value="<?php echo $info[0]["marca"] ?>" disabled />
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="ubicacion">Ubicación:</label>
                                                      <input type="text" id="ubicacion" name="ubicacion" class="form-control input-md" value="<?php echo $info[0]["ubicacion"] ?>" disabled />
                                                  </div>
                                                  <div class="col-xs-8">
                                                    <label for="descripcion">Descripción: </label>
                                                    <input type="text" id="descripcion" name="descripcion" class="form-control input-md" value="<?php echo $info[0]["descripcion"] ?>" disabled/>
                                                    <!-- <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea> -->
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="codigo_componente">Código de componente-equipo :</label>
                                                      <input type="text" id="codigo_componente" name="codigo_componente" class="form-control input-md" value="<?php echo $info[0]["componentecodigo"] ?>" placeholder="Ingrese código de componente" />
                                                      <input type="hidden" id="idcomponenteequipo" name="idcomponenteequipo" value="<?php echo $info[0]["idcomponenteequipo"] ?>" />
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="descrip_componente">Descripción de componente:</label>
                                                      <input type="text" id="descrip_componente" name="descrip_componente" class="form-control input-md" value="<?php echo $info[0]["componentedescrip"] ?>" disabled />
                                                  </div>
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                      <label for="sistema_componente">Sistema:</label>
                                                      <input type="text" id="sistema_componente" name="sistema_componente" class="form-control input-md" value="<?php echo $info[0]["sistemadescrip"] ?>" disabled />
                                                  </div>
                                              </div><!-- /.row -->
                                            </div>
                                          </div><!-- /.panel -->

                                          <div class="panel panel-default oculto">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title"><span class="fa fa-building-o"></span> Programación</h4>
                                              </div>

                                              <div class="panel-body">
                                                  <div class="row">
                                                                                                            
                                                      <div class="col-xs-12 col-md-6">                    
                                                        <label for="tarea">Tarea Estandar<strong style="color: #dd4b39">*</strong>:</label>
                                                        <input type="text" id="tarea" name="tarea" class="form-control" placeholder="Buscar Tarea...">
                                                        <input type="hidden" id="idtarea" name="idtarea">
                                                      </div>

                                                      <div class="col-xs-12 col-sm-6">
                                                          <label for="tareaOpcional">Tarea No Estandar<strong style="color: #dd4b39">*</strong>:</label>
                                                          <input type="text" class="form-control" id="tareaOpcional" name="tareaOpcional" value="<?php echo $info[0]["tarea_opcional"] ?>" />
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-xs-12 col-sm-3">
                                                          <label for="vfecha">Fecha Creación<strong style="color: #dd4b39">*</strong>:</label>
                                                          <!-- <input type='text' class="form-control" id="fechaEdit" name="denunciasfecha" value="<?php echo date("Y-m-d") ?>"> -->
                                                          <input type='text' class="form-control" id="fechaEdit" name="denunciasfecha" value="<?php echo $info[0]["fecha"] ?>">
                                                      </div>  

                                                      <div class="col-xs-12 col-sm-6 col-md-3">
                                                        <label for="duracionBack">Duración Estandar <strong style="color: #dd4b39">*</strong>:</label>
                                                        <input type="text" class="form-control" id="duracionBack" name="duracionBack"/>
                                                        <!-- <input type="hidden" class="form-control" id="back_duracion" name="back_duracion"/> -->
                                                      </div>

                                                      <div class="col-xs-12 col-sm-6 col-md-3">
                                                        <label for="unidad">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                                                        <select  id="unidad" name="unidad" class="form-control" />
                                                      </div>
                                                      <div class="col-xs-12 col-sm-6 col-md-3">
                                                        <label for="cantOper">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                                                        <input type="text" class="form-control" id="cantOper" name="cantOper"/>
                                                      </div>                    

                                                      <div class="col-xs-12 col-md-4" id="dato" name="" style="margin-top: 19px;"></div>
                                                      <input type="hidden" name="hshombre" id="hshombre">                
                                                      <div class="col-xs-12" id="dato"></div> 

                                                  </div><!-- /.row -->
                                              </div>
                                          </div>    
                                        

                                        <!-- bloque herram ins y adj -->	
                                          <!-- Tab nav -->
                                          <div class="nav-tabs-custom oculto">
                                            <ul class="nav nav-tabs" role="tablist">
                                              <li role="presentation" class="active"><a href="#herramin" aria-controls="profile" role="tab" data-toggle="tab">Herramientas</a></li>
                                              <li role="presentation"><a href="#insum" aria-controls="messages" role="tab" data-toggle="tab">Insumos</a></li>
                                              <li role="presentation"><a href="#TabAdjunto" aria-controls="home" role="tab" data-toggle="tab">Adjunto</a></li>            
                                            </ul>
                                          </div>
                                          <!-- Tab panes -->
                                          <div class="tab-content oculto">
                                          <div role="tabpanel" class="tab-pane active" id="herramin">
                                            <div class="panel panel-default">
                                              <div class="panel-body">
                                                <div class="row">
                                                  <div class="col-xs-12 col-sm-6 col-md-4">
                                                     <label for="herramienta">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                                                    <select  id="herramienta"  name="herramienta" class="form-control input-md" value=""></select> 
                                                    <input type="hidden" id="id_herramienta" name="id_herramienta"> 


                                                    <!-- <label for="herramienta">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                                                    <input type="text" id="herramienta"  name="" class="form-control" placeholder="Buscar Código..."/>
                                                    <input type="hidden" id="id_herramienta" name="id_herramienta"> -->




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
                                                     <input type="hidden" id="id_insumo" name="id_insumo">

                                                    <!-- <label for="insumo">Codigo <strong style="color: #dd4b39">*</strong>:</label>
                                                    <input type="text" id="insumo" name="insumo" class="form-control" placeholder="Buscar Código..."/>
                                                    <input type="hidden" id="id_insumo" name=""> -->
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
                                        <!--	/  bloque herram ins y adj -->

                                          </div>
                                        <!-- / bloque herram ins y adj -->  
                                      </div>   <!-- /.tabpanel-body -->                     
                                        <!-- Comentarios -->
                                          <div role="tabpanel" class="tab-pane" id="profile">
                                            <?php  
                                              echo $comentarios;																			
                                            ?>	
                                          </div>
                                        <!--  / Comentarios -->  

                                        <!-- Linea de Tiempo -->
                                          <div role="tabpanel" <?php echo ($device == 'android' ? 'class= "hidden"' : 'class= "tab-pane"') ?> id="messages">
                                              <!-- <div role="tabpanel" class="tab-pane" id="messages" > -->

                                              <div class="panel-body">
                                                  <div class="panel panel-primary">
                                                      <?php echo $lineaTiempo; ?>
                                                  </div>
                                              </div>

                                          </div>
                                        <!-- / Linea de Tiempo -->  
                                      </div>    <!-- tab content -->
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="modal-footer">
                          <button type="button" id="cerrar" class="btn btn-primary" onclick="">Cerrar</button>
                          <button type="button" class="btn btn-success" id="hecho" onclick="guardar()">Hecho</button>
                          <!-- <button type="submit" class="btn btn-primary">Guardar</button> -->
                      </div>
                  </div><!-- /.row -->
                                                      
          </div>

            
        </div>
    </div>
 
  </div>
</section>

<script>
    var codhermglo = "";
    var codinsumolo = "";
    var preglob = "";

  // cargo plugin DateTimePicker
  $('#fechaEdit').datetimepicker({
      format: 'YYYY-MM-DD',
      locale: 'es',
  });
		
 ////// TAREAS //////

  // limpia un input al seleccionar o llenar otro
  $('#tarea').change(function(){    
    $('#tareaOpcional').val(''); 
  });
  $('#tareaOpcional').change(function(){
    $('#tarea').val('');
    $('#idtarea').val('');
  }); 
  //Trae tareas y permite busqueda en el input
  var dataTarea = function() {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'dataType': 'json',
      'url': '<?php echo base_url(MAN); ?>Preventivo/gettarea',
    })
    .done( (data) => { tmp = data } )
    .fail( () => alert("Error al traer tareas") );
    return tmp;
  }();
  $("#tarea").autocomplete({
    source:    dataTarea,
    delay:     500,
    minLength: 1,
    focus: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#idtarea').val(ui.item.value);
    },
    select: function(event, ui) {
      event.preventDefault();
      $(this).val(ui.item.label);
      $('#idtarea').val(ui.item.value);
    },
  });
////// TAREAS //////  

////// COMPONENTES //////
  function dataC() {
        var tmp = null;
        var idEquipo = $('#equipo').val();
        console.info("id equipo: " + idEquipo);
        $.ajax({
            'async': false,
            'data': {
                idEquipo: idEquipo
            },
            'dataType': 'json',
            'global': false,
            'type': "POST",
            'url': "<?php echo base_url(MAN); ?>Backlog/getComponente",
            'success': function(data) {
                //console.info("trae componentes para autocomplete");
                console.log(data);
                if (data == 0) {
                    data = "0: { value: null, label: null, descrip: null, sistema:null }";
                }
                tmp = data;
            }
        });
        return tmp;
    };
  refrescarAutocompletar();
  function refrescarAutocompletar() {
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
              $(this).val(ui.item.value); //label
              $("#descrip_componente").val(ui.item.descrip);
              $("#sistema_componente").val(ui.item.sistema);
              $('#idcomponenteequipo').val(ui.item.idce);
          }
      });

      $('#tareaest').change(function() {
          $('#tareaOpcional').val('');
      });
      $('#tareaOpcional').click(function() {
          $('#tareaest').val(-1);
      });
  }
////// COMPONENTES //////

////// DURACION HH //////

  // Trae unidades de tiempo y calcula hs hombre
  $(function(){  
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo base_url(MAN); ?>Predictivo/getUnidTiempo', 
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
    var entrada = $('#duracionBack').val();   
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
    var mens=$("<h4 class='before'>HH: <span class='hh'>" + hsHombre + "</span></h4>"); 
    $('#dato').html(mens);
    $('#hshombre').val(hsHombre);
  }
  // Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
  $('#duracionBack, #unidad, #cantOper').change(function(){
    if( $('#duracionBack').val()!="" && $('#unidad').val()!="-1" && $('#cantOper').val()!=""){
      calcularHsHombre();
    }
  });
////// DURACION HH //////

////// HERRAMIENTAS CON SELECT //////
  $(function(){
    $('#herramienta').html("");
    $.ajax({
      type: 'POST',
      data: { },
            url: '<?php echo base_url(MAN); ?>Preventivo/getherramienta',
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
          dataType: 'json'
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
        url: '<?php echo base_url(MAN); ?>Preventivo/getdatos',
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
    //console.log("herramienta de prueba :"+id_herramienta1);

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
    //console.log(tr);        
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
////// HERRAMIENTAS CON SELECT //////

////// INSUMOS CON SELECT //////
  $(function(){
    $('#insumo').html("");
    $.ajax({
      type: 'POST',
      data: { },
      url: '<?php echo base_url(MAN); ?>Preventivo/getinsumo', 
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
      dataType: 'json'
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
          url: '<?php echo base_url(MAN); ?>Preventivo/getinsumo',
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
////// HERRAMIENTAS CON SELECT //////


///////////////         HERRAMIENTAS    INSUMOS    CON BUSCAR /////////////////////


////// HERRAMIENTAS //////

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
  //Trae herramientas
  var dataHerramientas = function() {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'dataType': 'json',
      'url': '<?php echo base_url(MAN); ?>Preventivo/getHerramientasB',
    })
    .done( (data) => { tmp = data } );
    // .fail( () => alert("Error al traer Herramientas") );
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
      'url': '<?php echo base_url(MAN); ?>Preventivo/getinsumo',
    })
    .done( (data) => { tmp = data } )
    .fail( () => alert("Error al traer Herramientas") );
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
///////////////       /  HERRAMIENTAS    INSUMOS     CON BUSCAR /////////////////////





////// ADJUNTOS //////

  //abrir modal eliminar adjunto
  $(document).on("click",".eliminaAdjunto",function(){
    $('#modalEliminarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
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
      url: '<?php echo base_url(MAN); ?>Preventivo/eliminarAdjunto',
    }) 
    .done( function(data){     
      //console.table(data); 
      let prevAdjunto = '';
      recargaTablaAdjunto(prevAdjunto);
    })                
    .fail( function(result){                      
      console.error(result);
    }); 
  }

  //abrir modal agregar adjunto
  $(document).on("click",".agregaAdjunto",function(){
    $('#btnAgregarEditar').text("Agregar");
    $('#modalAgregarAdjunto .modal-title').html('<span class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar');

    $('#modalAgregarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
    $('#idAgregaAdjunto').val(idprev);
  });
  //abrir modal editar adjunto
  $(document).on("click",".editaAdjunto",function(){
    $('#btnAgregarEditar').text("Editar");
    $('#modalAgregarAdjunto .modal-title').html('<span class="fa fa-fw fa-pencil text-light-blue"></span> Editar');

    $('#modalAgregarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
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
        url:'<?php echo base_url(MAN); ?>Backlog/agregarAdjunto',
      })
      .done( function(data){     
        //console.info( "adjunto completo : "+data );
        recargaTablaAdjunto( data['back_adjunto'] );
      })                
      .fail( function(result){                      
        console.error(result);
      }); 
    }
  });
  var adj = $('#back_adjunto').val();
  recargaTablaAdjunto(adj);
  function recargaTablaAdjunto(backAdjunto) {
    console.info( "adjunto: "+backAdjunto );
    $('#adjunto').text(backAdjunto);
    $('#adjunto').attr('href', '<?php echo man_asset_url(); ?>assets/filesbacklog/'+backAdjunto);
    if( backAdjunto == null || backAdjunto == '') {
      var accion = '<i class="fa fa-plus-square agregaAdjunto text-light-blue" style="color:#f39c12; cursor:pointer; margin-right:10px" title="Agregar Adjunto"></i>';
    } else {
      var accion = '<i class="fa fa-times-circle eliminaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Eliminar Adjunto"></i>'+'<i class="fa fa-pencil editaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Editar Adjunto"></i>';
    }
    $('#accionAdjunto').html(accion);
  }
////// ADJUNTOS //////

////// GUARDAR //////

  //Guarda Backlog Editado - Chequeado
  function guardar(){
      
    var backid        = $('#id_backlog').val();//
    var tarea         = $('#idtarea').val();//
    var fecha         = $('#fecha').val();//
    var hshombre      = $('#hshombre').val();  
    var duracion      = $('#duracionBack').val(); 
    var id_unidad     = $('#unidad').val();
    var back_canth    = $('#cantOper').val();
    var tareaOpcional = $('#tareaOpcional').val();
    var idTarBonita   = $('#idTarBonita').val();
    var idcompequipo  = $('#idcomponenteequipo').val();
    
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
    
    if((fecha !=='') || (duracion !=='') || (tarea > 0) ){

      WaitingOpen("Guardando");
      $.ajax({
        type: 'POST',
        data: { backid:backid,
                tarea:tarea, 
                fecha:fecha,
                duracion:duracion,
                id_unidad:id_unidad,
                hshombre: hshombre,
                back_canth: back_canth,
                idsherramienta: idsherramienta,
                cantHerram: cantHerram,
                idsinsumo: idsinsumo, 
                cantInsum: cantInsum,
                tareaOpcional: tareaOpcional,
                idTarBonita: idTarBonita,
                idcomponenteequipo: idcompequipo, 
                tipo: 'editNuevo'},

        url: '<?php echo base_url(MAN); ?>Backlog/editar_backlog', 
        success: function(data){
                  WaitingClose();
                  $('#modalSale').modal('hide');
                  if (!data || !data.status) {
                    // asset volvía a la bandeja aunque el guardado hubiera fallado
                    alert((data && data.msj) ? data.msj : 'No se pudo guardar el backlog');
                    return;
                  }
                  Refrescar();                     
                },
        error: function(result){            
                WaitingClose();
                $('#modalSale').modal('hide');
                alert('No se pudo guardar el backlog');
            },
        dataType: 'json'
      });
    }else{
      var hayError = true;
      $('#error').fadeIn('slow');
      return;
    }  
  }

  function Refrescar(){

    $('#content').empty();   
    linkTo("<?php echo base_url(MAN); ?>Tarea/index/<?php echo $permission; ?>");
    WaitingClose();
  }
////// GUARDAR //////


</script> 


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
<!--------------/ MODALES ADJUNTO ------------->