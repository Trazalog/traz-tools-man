<input type="hidden" id="permission" value="<?php echo $permission ?>">

<style>
.datagrid table {
    border-collapse: collapse;
    text-align: left;
    width: 100%;
}

.datagrid {
    font: normal 12px/150% Arial, Helvetica, sans-serif;
    background: #fff;
    overflow: hidden;
}

.datagrid table td,
.datagrid table th {
    padding: 13px 20px;
}

.datagrid table thead th {
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #3B8BBA), color-stop(1, #45A4DB));
    background: -moz-linear-gradient(center top, #3B8BBA 5%, #45A4DB 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#3B8BBA', endColorstr='#45A4DB');
    background-color: #3B8BBA;
    color: #FAF2F8;
    font-size: 13px;
    font-weight: bold;
    border-left: 1px solid #A3A3A3;
}

.datagrid table thead th:first-child {
    border: none;
}

.datagrid table tbody td {
    color: #002538;
    font-size: 13px;
    border-bottom: 1px solid #E1EEF4;
    font-weight: normal;
}

.datagrid table tbody .alt td {
    background: #EBEBEB;
    color: #00273B;
}

.datagrid table tbody td:first-child {
    border-left: none;
}

.datagrid table tbody tr:last-child td {
    border-bottom: none;
}

ul.dropdown-menu {
    z-index: 10000 !important;
    position: relative !important;
}
</style>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Orden de trabajo</h3>
    </div><!-- /.box-header -->
    <div class="row">
        <div class="col-md-3">
            <?php
                if (strpos($permission,'Add') !== false) {
                    echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px; margin-left: 10px" id="btnAdd">Agregar</button>'; 
                }
            ?>
        </div>
    </div>
    <form id="frm-filtros">
            <input type="hidden" id="permissionFilt" name="permissionFilt" value="<?php echo $permission ?>">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 2%;">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <label>Programada Desde</label>
                    <input type="date" class="form-control" id="fec_desde" name="fec_desde" onchange="habilitaFecHasta()">
                </div>
                <!-- /.form-group -->
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <label>Programada Hasta</label>
                    <input type="date" class="form-control" id="fec_hasta" name="fec_hasta" readonly>
                </div>
            <!-- /.form-group -->
            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <label>Equipo</label>
                    <select id="equipoFilt" name="equipoFilt" class="form-control" style="width: 100%">
                        <option value="" selected disabled> - Seleccionar - </option>
                            <?php 
                            foreach ($equipos as $key => $o) {
                                echo "<option value='".$o['id_equipo']."'>".$o['codigo']."</option>";
                            }
                            ?>
                    </select>
                
            </div>
            <!-- /.form-group -->
            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <label>Estado</label>
                <select id="estadoFilt" name="estadoFilt" class="form-control">
                    <option value="" selected disabled> - Seleccionar - </option>
                    <option value="S">Solicitada</option>
                    <option value="PL">Planificada</option>
                    <option value="AS">Asignada</option>
                    <option value="C">Curso</option>
                    <option value="T">Terminada</option>
                    <option value="CE">Cerrada</option>
                    <option value="CN">Conforme</option>
                </select>
            </div>
            <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2" style="float:right; margin-right: 1%">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</label>
                <button type="button" class="btn btn-success btn-flat col-xs-12 col-sm-6 col-md-6 col-lg-6" onclick="filtrar()">Filtrar</button>
                <button type="button" class="btn btn-danger btn-flat flt-clear col-xs-12 col-sm-6 col-md-6 col-lg-6" onclick="limpiar()">Limpiar</button>
            </div>
            <!-- /.form-group -->
        </div>
        <!-- /.row -->
        <br>
    </form>
    <!-- <br> -->
    <hr>
    <div class="box-body">
        <table id="otrabajo" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>Nº Orden</th>
                    <th>Fecha Program.</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Terminada</th>
                    <th>Detalle</th>
                    <th>Tarea STD</th>
                    <th>Equipo</th>
                    <th>Origen</th>
                    <th>Id Solicitud</th>
                    <th>Asignado</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if( $list!=false && count($list) > 0){
                    foreach($list as $a){
                        $id          = $a['id_orden'];
                        $id_equipo   = $a['id_equipo'];
                        $causa       = $a['descripcion'];
                        $idsolicitud = $a['id_solicitud'];
                        echo '<tr id="'.$id.'" class="'.$id.' ot-row" data-id_equipo="'.$id_equipo.'" data-causa="'.$causa.'" data-idsolicitud="'.$idsolicitud.'">';
                        echo '<td>';
                        $ordenservicioId = $a['ordenservicioId'];                       
                        echo $opciones;
                        if($ordenservicioId != NULL){
                            // Ver informe de servicios generado
                            echo '<li role="presentation" id="cargOrden"><a onclick="ver_informe_servicio(this)" style="color:white;" role="menuitem" tabindex="-1" href="#" ><i class="fa fa-file-text text-white" style="color:white; cursor: pointer;margin-left:-1px"></i>Informe de Servicios</a></li>';
                            echo '</ul><div>';
                        }else{
                            echo '</ul><div>';
                        }
                        echo '</td>';                        
                        echo '<td>'.floatval($a['id_orden']).'</td>';
                        $fecha_program = ($a['fecha_program'] == '0000-00-00 00:00:00') ? "0000-00-00" : date_format(date_create($a['fecha_program']), 'Y-m-d');
                        echo '<td>'.$fecha_program.'</td>';
                        $fecha_inicio = ($a['fecha_inicio'] == '0000-00-00 00:00:00') ? "0000-00-00" : date_format(date_create($a['fecha_inicio']), 'Y-m-d');
                        echo '<td>'.$fecha_inicio.'</td>';
                        $fecha_terminada = ($a['fecha_terminada'] == '0000-00-00 00:00:00') ? "0000-00-00" : date_format(date_create($a['fecha_terminada']), 'Y-m-d');
                        echo '<td>'.$fecha_terminada.'</td>';
                        echo '<td>'.$a['descripcion'].'</td>';
                        echo '<td>'.$a['descripcion'].'</td>';
                        echo '<td>'.$a['codigo'].' </td>';
                        echo '<td>'.$a['tipoDescrip'].'</td>';
                        echo '<td>'.$a['id_solicitud'].'</td>';
                        echo '<td>'.$a['nombre'].'</td>';                        
                        echo '<td>'.$a['nomCli'].'</td>';
                        echo '<td>';  
                        
                        if ($a['estado'] == 'S') {
                            // echo  '<small class="label pull-left bg-red">Solicitada</small>';
                            echo bolita('Solicitada', 'red');
                        }
                        if($a['estado'] == 'PL'){                           
                            //echo '<small class="label pull-left bg-orange">Planificada</small>';
                            echo bolita('Planificada', 'yellow');
                        }
                        if($a['estado'] == 'AS'){
                            // echo '<small class="label pull-left bg-yellow">Asignada</small>';
                            echo bolita('Asignada', 'purple');
                        }
                        if ($a['estado'] == 'C') {
                            //echo '<small class="label pull-left bg-blue">Curso</small>' ;
                            echo bolita('Curso', 'green');
                        }
                        if ($a['estado'] == 'T') {
                            //echo  '<small class="label pull-left bg-navy">Terminada</small>';
                            echo bolita('Terminada', 'blue');
                        }
                        if ($a['estado'] == 'CE') {
                            //echo  '<small class="label pull-left bg-green">Cerrada</small>';
                            echo bolita('Cerrada', 'default');
                        }  
                        if ($a['estado'] == 'CN') {
                            //echo  '<small class="label pull-left bg-black">Conforme</small>';
                            echo bolita('Conforme', 'black');
                        }
                        echo '</td>';
                    }
                }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<script>
// var globales
var iort = "";
var ido = "";
var idp = "";
var idArt = 0;
var acArt = "";
var i = "";
var idord = "";
var idfin = "";
var descrip = "";
var sol_id;

$('.ot-row').on('click', function() {
    sol_id = $(this).data('idsolicitud');
});
// cargo plugin DateTimePicker
$('#fechaEntrega,#fechaInicio, #fecha_inicio1, #fecha_inicio, #fecha_entrega1, #fecha_entregaa').datetimepicker({
    format: 'YYYY-MM-DD H:mm:ss', //format: 'YYYY-MM-DD', // es igaul a campo date
    locale: 'es',
});

function traer_equipo() {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: 'index.php/Otrabajo/getequipo',
        success: function(data) {
            $('#equipo').empty();
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#equipo').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['codigo'];
                var opcion = "<option value='" + data[i]['id_equipo'] + "'>" + nombre + "</option>";
                $('#equipo').append(opcion);
            }
        },
        error: function(result) {
            console.error("Error al traer equipos. Ver console.table");
            console.table(result);
        },
    });
}
// Limpia modales y regresa al listado de OTs - Ok test 
function regresa1() {
    $('#content').empty();
    $('#modalOT').empty();
    $('#modalAsig').empty(); //local index 
    $("#content").load("<?php echo base_url(); ?>index.php/Otrabajo/listOrden/<?php echo $permission; ?>");
    WaitingClose();
}


// boton agregar nueva ot
$("#btnAdd").click(function(e) {
    wo();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Otrabajo/nuevaOT/<?php echo $permission; ?>", () => wc());
});
// boton guardar 
$("#btn_cancGuardado").click(function(e) {
    $('#btn_guardar').prop("disabled", false);
    $('#error').fadeOut('slow');
    $('.text_box').val('');
    $('.select_box').val('-1');
});
// Elimina OT (estado = AN) - Ok
function eliminarpred() {
    var idpre = $(this).parent('td').parent('tr').attr('id');
    console.log("Estoy por la opcion SI a eliminar")
    console.log(gloid);

    $.ajax({
        type: 'POST',
        data: {
            gloid: gloid
        },
        url: 'index.php/Otrabajo/baja_predictivo',
        success: function(data) {
            regresa1();
        },
        error: function(result) {
            console.error("Error al eliminar OT. Ver console.table");
            console.table(result);
        }
    });
}


///// EDICION DE ORDEN DE TRABAJO

	//Trae tareas y permite busqueda en el input
	var dataTarea = function() {
        var tmp = null;
        $.ajax({
            'async': false,
            'type': "POST",
            'dataType': 'json',
            'url': '<?php echo MAN; ?>Preventivo/gettarea',
            success: function(data) {
                tmp = data
            },
            error: function(result) {
                error("Error","Error al traer tareas");
                console.log(result);
            },
        })
        return tmp;
	}();
	$("#tarea").autocomplete({
					source: dataTarea,
					delay: 500,
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
									$('#tareacustom').val(''); // borra la tarea custom
					},
	});
	// limpia un input al seleccionar o llenar otro
	$('#tarea').change(function() {
					$('#tareacustom').val('');
	});
	$('#tareacustom').change(function() {
					$('#tarea').val('');
					$('#id_tarea').val('');
	});

	// Trae datos para llenar el modal Editar OT - Ok
	function editar(o) {
        $('#errorE').hide();
        $('#btnEditar').prop("disabled", false);
        var idp = $(o).closest('tr').attr('id');
        // agrega id de ot par guardar con adjuntos
        $('#idAgregaAdjunto').val(idp);
        //borra la tabla de adjuntos antes de cargar 
        $('#tablaadjunto tbody tr').remove();

        $.ajax({
            data: {
                idp: idp
            },
            dataType: 'json',
            type: 'POST',
            url: '<?php echo MAN; ?>Otrabajo/getpencil',
            success: function(data) {
                //console.table(data);
                var resp = data['datos'];

                datos = {
                    'id_ot': resp[0]['id_orden'], //
                    'nro': resp[0]['nro'],
                    'equipo_descrip': resp[0]['codigo'], //
                    'fecha_ingreso': resp[0]['fecha_ingreso'],
                    'id_equipo': resp[0]['id_equipo'], //
                    'marca': resp[0]['marca'],
                    'ubicacion': resp[0]['ubicacion'],
                    'descripcion': resp[0]['equipodescrip'],
                    'id_tarea': resp[0]['id_tarea'],
                    'fecha_program': resp[0]['fecha_program'], //            
                    'fecha_inicio': resp[0]['fecha_inicio'], //
                    'fecha_terminada': resp[0]['fecha_terminada'], //
                    'idusuario': resp[0]['id_usuario'], //
                    'tareadescrip': resp[0]['tareadescrip'], //,     //
                    'nomCli': resp[0]['nomCli']
                    //'id_sucu'       : resp[0]['id_sucursal'],     //
                    //'sucursal'      : resp[0]['descripc']//,        //
                    //'id_proveedor'  : resp[0]['provid'],          //
                    //'nombreprov'    : resp[0]['provnombre']//,      
                }

                var herram = data['herramientas'];
                var insum = data['insumos'];
                var adjunto = data['adjunto'];
                console.table(adjunto);
                completarEdit(datos, herram, insum, adjunto);
            },
            error: function(result) {
                console.error("Error al Editar OT. Ver console.table");
                console.table(result);
            },
        });
	}

	// completa los datos del modal Editar - Ok
	function completarEdit(datos, herram, insum, adjunto) {

					$('#id_ot').val(datos['id_ot']);
					$('#equipo_descrip').val(datos['equipo_descrip']);
					$('#equipo').val(datos['id_equipo']);
					$('#fecha_ingreso').val(datos['fecha_ingreso']);
					$('#marca').val(datos['marca']);
					$('#ubicacion').val(datos['ubicacion']);
					$('#descripcion').val(datos['descripcion']);
					if (datos['id_tarea'] > '0') {
									$('#id_tarea').val(datos['id_tarea']);
									$('#tarea').val(datos['tareadescrip']);
					} else {
									$('#tareacustom').val(datos['tareadescrip']);
					}

					$('#fechaProgramacion').val(datos['fecha_program']);
					$('#fechaInicio').val(datos['fecha_inicio']);
					$('#fechaTerminada').val(datos['fecha_terminada']);
					$('#NombreCliente').val(datos['nomCli']);
					//$("#suci").val(datos['id_sucu']);
					//$("#prov").val(datos['id_proveedor']); 

					$('#tablaherramienta tbody tr').remove();
					for (var i = 0; i < herram.length; i++) {
									var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
													"<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
													"<td>" + herram[i]['herrcodigo'] + "</td>" +
													"<td>" + herram[i]['herrmarca'] + "</td>" +
													"<td>" + herram[i]['herrdescrip'] + "</td>" +
													"<td>" + herram[i]['cantidad'] + "</td>" +
													"</tr>";
									$('#tablaherramienta tbody').append(tr);
					}

					$('#tablainsumo tbody tr').remove();
					for (var i = 0; i < insum.length; i++) {
									var tr = "<tr id='" + insum[i]['artId'] + "'>" +
													"<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
													"<td>" + insum[i]['artBarCode'] + "</td>" +
													"<td>" + insum[i]['artDescription'] + "</td>" +
													"<td>" + insum[i]['cantidad'] + "</td>" +
													"</tr>";
									$('#tablainsumo tbody').append(tr);
					}

					recargaTablaAdjunto(adjunto);

					$(document).on("click", ".elirow", function() {
									var parent = $(this).closest('tr');
									$(parent).remove();
					});
	}

	// Guarda Edicion de OT - Ok
	function guardareditar() {
					var idp = $('#id_ot').val();
					WaitingOpen('Guardando Edición...');
					$('#errorE').hide();
					$('#btnEditar').prop("disabled", true);
					var hayError = false;

					//var nro           = $('#nroedit').val();
					var fecha_inicio = $('#fechaInicio').val();

					var fechaProgramacion = $('#fechaProgramacion').val();
					var fecha_entrega = $('#fechaEntrega').val();
					var id_sucu = $('#suci').val();
					var id_prov = $('#prov').val();
					var id_equipo = $('#equipo').val();
					var tareacustom = $('#tareacustom').val();
					var tareaestandar = $('#tarea').val();
					var id_tarea = $('#id_tarea').val();
					var descripcion = '';
					if (tareacustom == '') {
									descripcion = $('#tarea').val();
					} else {
									descripcion = $('#tareacustom').val();
									id_tarea = 0;
					}

					var parametros = {
									//'nro'           : nro,                                          
									'fecha_program': fechaProgramacion,
									'fecha_entrega': fecha_entrega,
									'id_tarea': id_tarea,
									'descripcion': descripcion,
									'id_sucursal': id_sucu,
									'id_proveedor': id_prov,
									'id_equipo': id_equipo
					};

					// Arma array de herramientas y cantidades
					var idsherramienta = new Array();
					$("#tablaherramienta tbody tr").each(function(index) {
									var id_her = $(this).attr('id');
									idsherramienta.push(id_her);
					});
					var cantHerram = new Array();
					$("#tablaherramienta tbody tr").each(function(index) {
									var cant_herr = $(this).find("td").eq(4).html();
									cantHerram.push(cant_herr);
					});
					// Arma array de insumos y cantidades
					var idsinsumo = new Array();
					$("#tablainsumo tbody tr").each(function(index) {
									var id_ins = $(this).attr('id');
									idsinsumo.push(id_ins);
					});
					var cantInsum = new Array();
					$("#tablainsumo tbody tr").each(function(index) {
									var cant_insum = $(this).find("td").eq(3).html();
									cantInsum.push(cant_insum);
					});

					$.ajax({
                        type: 'POST',
                        data: {
                            parametros: parametros,
                            idOT: idp,
                            idsherramienta: idsherramienta,
                            cantHerram: cantHerram,
                            idsinsumo: idsinsumo,
                            cantInsum: cantInsum
                        },
                        url: 'index.php/Otrabajo/guardar_editar',
                        success: function(data) {
                            WaitingClose();
                            $('#modaleditar').modal('hide');
                            regresa1();
                        },
                        error: function(result) {
                            WaitingClose();
                            $('#btnEditar').prop("disabled", false);
                            console.error("Error al guardar en modal Editar Ot");
                            console.table(result);
                        }
					});

	}
///// EDICION DE ORDEN DE TRABAJO  

/// EDICION Y AGREGADO DE ADJUNTOS
	//abrir modal eliminar adjunto
	$(document).on("click", ".eliminaAdjunto", function() {
					$('#modalEliminarAdjunto').modal('show');
					var id_adjunto = $(this).parents("tr").attr('id');
					$('#idAdjunto').val(id_adjunto);
					console.log(id_adjunto + 'adjunto');
	});
	//eliminar adjunto
	function eliminarAdjunto() {
					$('#modalEliminarAdjunto').modal('hide');
					var id_adjunto = $('#idAdjunto').val();
					$.ajax({
													data: {
																	id_adjunto: id_adjunto
													},
													dataType: 'json',
													type: 'POST',
													url: 'index.php/Otrabajo/eliminarAdjunto',
									})
									.done(function(data) {
													//console.table(data); 
													let prevAdjunto = '';
													borrarRegistro(id_adjunto);
													//recargaTablaAdjunto(prevAdjunto);
									})
									.error(function(result) {
													console.error(result);
									});
	}
	// //abrir modal agregar adjunto
	$(document).on("click", ".agregaAdjunto", function() {
					$('#btnAgregarEditar').text("Agregar");
					$('#modalAgregarAdjunto .modal-title').html(
									'<span class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar');

					$('#modalAgregarAdjunto').modal('show');
					var idOT = $('#id_ot').val();
					$('#idAgregaAdjunto').val(idOT);
	});
	//abrir modal editar adjunto
	$(document).on("click", ".editaAdjunto", function() {
					$('#btnAgregarEditar').text("Editar");
					$('#modalAgregarAdjunto .modal-title').html(
									'<span class="fa fa-fw fa-pencil text-light-blue"></span> Editar');

					$('#modalAgregarAdjunto').modal('show');
					var idprev = $('#id_Predictivo').val();
					$('#idAgregaAdjunto').val(idprev);
	});
	//eliminar adjunto
	$("#formAgregarAdjunto").submit(function(event) {
					$('#modalAgregarAdjunto').modal('hide');
					WaitingOpen('Guardando Adjunto...');
					event.preventDefault();
					if (document.getElementById("inputPDF").files.length == 0) {
									$('#error').fadeIn('slow');
					} else {
									$('#error').fadeOut('slow');
									var formData = new FormData($("#formAgregarAdjunto")[0]);
									$.ajax({
																	cache: false,
																	contentType: false,
																	data: formData,
																	dataType: 'json',
																	processData: false,
																	type: 'POST',
																	url: 'index.php/Otrabajo/agregarAdjunto',
													})
													.done(function(data) {
																	nuevoAdjunto(data);
																	WaitingClose();
													})
													.error(function(result) {
																	WaitingClose();
																	console.error(result);
													});
					}
	});
	// recarga tablas de adjuntos al iniciar la edicion
	function recargaTablaAdjunto(Adjunto) {

					for (var i = 0; i < Adjunto.length; i++) {

									var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
													"<td ><i class='fa fa-times-circle eliminaAdjunto text-light-blue' style='cursor:pointer; margin-right:10px' title='Eliminar Adjunto'></i></td>'" +
													"<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto</a></td>" +
													"</tr>";
									$('#tablaadjunto tbody').append(tr);
					}

	}
	// agrega nuevo registro en tabla despues de guardarlo
	function nuevoAdjunto(data) {
					var tr = "<tr id='" + data['id'] + "'>" +
									"<td ><i class='fa fa-times-circle eliminaAdjunto text-light-blue' style='cursor:pointer; margin-right:10px' title='Eliminar Adjunto'></i></td>'" +
									"<td><a id='' href='" + data['ot_adjunto'] + "' target='_blank'>Archivo adjunto</a></td>" +
									"</tr>";
					$('#tablaadjunto tbody').append(tr);
	}
	// borra registro en tabla si fue eliminado con exito
	function borrarRegistro(id_adjunto) {
					var tabla = $('#tablaadjunto tbody tr');
					$.each(tabla, function() {
									var idTrow = $(this).attr('id');
									if (idTrow == id_adjunto) {
													$(this).remove();
									}
					});
	}
///  / EDICION Y AGREGADO DE ADJUNTOS

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
            'url': '<?php echo MAN; ?>Preventivo/getHerramientasB',
            success: function(data) {
                tmp = data
            },
            error: function(result) {
                error("Error","Error al traer Herramientas");
                console.log(result);
            },
        })
        return tmp;
	}();

	// data busqueda por codigo de herramientas
	function dataCodigoHerr(request, response) {
					function hasMatch(s) {
									return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
					}
					var i, l, obj, matches = [];

					if (request.term === "") {
									response([]);
									return;
					}

					//ordeno por codigo de herramientas
					dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("codigo"));

					for (i = 0, l = dataHerramientas.length; i < l; i++) {
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
									return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
					}
					var i, l, obj, matches = [];

					if (request.term === "") {
									response([]);
									return;
					}

					//ordeno por marca de herramientas
					dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("marca"));

					for (i = 0, l = dataHerramientas.length; i < l; i++) {
									obj = dataHerramientas[i];
									if (hasMatch(obj.marca)) {
													matches.push(obj);
									}
					}
					response(matches);
	}


	//busqueda por marcas de herramientas
	$("#herramienta").autocomplete({
									source: dataCodigoHerr,
									delay: 500,
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
					.data("ui-autocomplete")._renderItem = function(ul, item) {
									return $("<li>")
													.append("<a>" + item.codigo + "</a>")
													.appendTo(ul);
					};

	//busqueda por marcas de herramientas
	$("#marcaherram").autocomplete({
									source: dataMarcaHerr,
									delay: 500,
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
					.data("ui-autocomplete")._renderItem = function(ul, item) {
									return $("<li>")
													.append("<a>" + item.marca + "</a>")
													.appendTo(ul);
					};

	//busqueda por descripcion de herramientas
	$("#descripcionherram").autocomplete({
					source: dataHerramientas,
					delay: 500,
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
	var nrofila = 0; // hace cada fila unica
	$("#agregarherr").click(function(e) {
					// FALTA HACER VALIDACION
					var id_her = $('#id_herramienta').val();
					var herramienta = $("#herramienta").val();
					var marcaherram = $('#marcaherram').val();
					var descripcionherram = $('#descripcionherram').val();
					var cantidadherram = $('#cantidadherram').val();

					nrofila = nrofila + 1;
					var tr = "<tr id='" + id_her + "' data-nrofila='" + nrofila + "'>" +
									"<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
									"<td class='herr'>" + herramienta + "</td>" +
									"<td class='marca'>" + marcaherram + "</td>" +
									"<td class='descrip'>" + descripcionherram + "</td>" +
									"<td class='cant'>" + cantidadherram + "</td>" +
									// guardo id de herram y cantidades
									"<input type='hidden' name='id_her[" + nrofila + "]' value='" + id_her + "'>" +
									"<input type='hidden' name='cant_herr[" + nrofila + "]' value='" + cantidadherram + "'>" +
									"</tr>";
					if (id_her > 0 && cantidadherram > 0) {
									$('#tablaherramienta tbody').append(tr);
					} else {
									return;
					}

					$(document).on("click", ".elirow", function() {
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
            success: function(data) {
                tmp = data
            },
            error: function(result) {
                error("Error","Error al traer Insumos");
                console.log(result);
            },
        })
        return tmp;
	}();

	// data busqueda por codigo de herramientas
	function dataCodigoInsumo(request, response) {
					function hasMatch(s) {
									return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
					}
					var i, l, obj, matches = [];

					if (request.term === "") {
									response([]);
									return;
					}

					//ordeno por codigo de herramientas
					dataHerramientas = dataHerramientas.sort(ordenaArregloDeObjetosPor("codigo"));

					for (i = 0, l = dataInsumos.length; i < l; i++) {
									obj = dataInsumos[i];
									if (hasMatch(obj.codigo)) {
													matches.push(obj);
									}
					}
					response(matches);
	}


	//busqueda por marcas de herramientas
	$("#insumo").autocomplete({
									source: dataCodigoInsumo,
									delay: 500,
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
					.data("ui-autocomplete")._renderItem = function(ul, item) {
									return $("<li>")
													.append("<a>" + item.codigo + "</a>")
													.appendTo(ul);
					};

	//busqueda por descripcion de herramientas
	$("#descript").autocomplete({
					source: dataInsumos,
					delay: 500,
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
	$("#agregarins").click(function(e) {
					var id_insumo = $('#id_insumo').val();
					var $insumo = $("#insumo").val();
					var descript = $('#descript').val();
					var cant = $('#cant').val();
					console.log("El id  del insumo");
					console.log(id_insumo);
					var hayError = false;
					var tr = "<tr id='" + id_insumo + "'>" +
									"<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
									"<td>" + $insumo + "</td>" +
									"<td>" + descript + "</td>" +
									"<td>" + cant + "</td>" +

									// guardo id de insumos y cantidades
									"<input type='hidden' name='id_insumo[" + nrofilaIns + "]' value='" + id_insumo + "'>" +
									"<input type='hidden' name='cant_insumo[" + nrofilaIns + "]' value='" + cant + "'>" +
									"</tr>";
					nrofilaIns = nrofilaIns + 1;
					if (id_insumo > 0 && cant > 0) {
									$('#tablainsumo tbody').append(tr);
					} else {
									return;
					}

					$(document).on("click", ".elirow", function() {
									var parent = $(this).closest('tr');
									$(parent).remove();
					});

					$('#insumo').val('');
					$('#descript').val('');
					$('#cant').val('');
	});
////// INSUMOS //////


// Lleva a la pantalla Asignar Tareas - Ok (no revisé la asignación!!!)
$(".fa-check-square-o").click(function(e) {
    var id = $(this).parent('td').parent('tr').attr('id');
    console.log("El id de OT es: " + id);
    iort = id;
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Otrabajo/cargartarea/<?php echo $permission; ?>/" +
        iort + "");
    WaitingClose();
});

// Trae los datos a mostrar en el modal Asignar OT - Ok
$(".fa-thumb-tack").click(function(e) {
    // $('#modalAsig').modal('show');
    var id_orden = $(this).parent('td').parent('tr').attr('id');
    console.log("El id de OT: " + id_orden);
    $.ajax({
        type: 'GET',
        data: {
            id_orden: id_orden
        },
        url: 'index.php/Otrabajo/getasigna',
        success: function(data) {
            datos = {
                'id_orden': id_orden,
                'nro': data['datos'][0]['nro'],
                'fecha_inicio': data['datos'][0]['fecha_inicio'],
                'estado': data['datos'][0]['estado'],
                'descripcion': data['datos'][0]['descripcion'],
                'equipo': data['datos'][0]['codigo'],
                'id_usuario': data['datos'][0]['id_usuario'],
                'id_equipo': data['datos'][0]['id_equipo'],
                'equipoDescrip': data['datos'][0]['equipoDescrip'],
            };
            var arre = new Array();
            arre = datos['fecha_inicio'].split(' ');
            //var fe= date_format(date_create(arre[0]), 'd-m-Y');
            $('#id_orden').val(datos['id_orden']);
            $('#nro').val(datos['nro']);
            $('#descripcion').val(datos['descripcion']);
            $('#fecha_inicio').val(arre[0]);
            $('#estado').val(datos['estado']);
            $('#equipo13').val(datos['equipo']);
            $('#equipo13').prop('title', datos['equipoDescrip']);
            $('#equipo13id').val(datos['id_equipo']);
            traer_usuario(datos['id_usuario']);
            // click_pedent();
        },
        error: function(result) {
            console.error("Error al ")
            console.table(result);
        },
        dataType: 'json'
    });
});

// llena select usuario en modal Asignar OT - Ok
function traer_usuario(id_usuario) {
    $("#usuario1").html("");
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: "Otrabajo/getusuario",
        success: function(data) {
            $('#usuario1').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['usrId'] == id_usuario) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['usrName'] + ' ' + data[i]['usrLastName'];
                var opcion = "<option value='" + data[i]['usrId'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#usuario1').append(opcion);
            }
        },
        error: function(data) {
            console.error('Error al traer usuarios en modal Asignar OT');
            console.table(data);
        },
    });
}
// Asigna Responsable a OT
function orden() {
    WaitingOpen();
    var id_orden = $('#id_orden').val();
    var fecha_entrega = $('#fecha_entrega').val();
    var usuario = $('#usuario1').val();
    var cliente = $('#id_cliente').val();
    var task_id = sessionStorage.getItem('task_id');
    var case_id = sessionStorage.getItem('case_id');
    console.log("Guardando>> OT: " + id_orden + " | SOID: " + sol_id + " | USER:" + usuario);
    $.ajax({
        type: 'POST',
        data: {
            id_orden: id_orden,
            fecha_entrega: fecha_entrega,
            usuario: usuario,
            sol_id: sol_id,
            case_id: case_id,
            task_id: task_id
        },
        url: 'index.php/Otrabajo/guardar',
        success: function(data) {
            WaitingClose();
            sessionStorage.clear();
            regresa1();
        },
        error: function(result) {
            WaitingClose();
            console.log("ERROR>> " + result);

        }
    });
}

// llena select clientes en modal Asignar OT -
function traer_clientes(id_cliente) {
    $.ajax({
        type: 'POST',
        data: {},
        url: 'index.php/Otrabajo/traer_cli',
        success: function(data) {
            console.info(data);
            /*var selectAttr = '';
            if(data[i]['cliId'] == id_cliente) { var selectAttr = 'selected'; console.log("sel")}
            var nombre = data[i]['cliLastName']+'. .'+datos['cliName'];
            var opcion = "<option value='"+data[i]['cliId']+"' "+selectAttr+">" +nombre+ "</option>";
            $('#cli').append(opcion);

            /*var opcion  = "<option value='-1'>Seleccione...</option>" ;
            $('#cli').append(opcion);
            for(var i=0; i < data.length ; i++) 
            {    
              var nombre = data[i]['cliLastName']+'. .'+datos['cliName'];
              var opcion = "<option value='"+data[i]['cliId']+"'>" +nombre+ "</option>" ;
              $('#cli').append(opcion);
            }*/
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

$(document).ready(function(event) {

    //cargar pedido
    $(".fa-tags").click(function(e) {

        var id_orden = $(this).parent('td').parent('tr').attr('id');
        ido = id_orden; //aca esta el id de orden de trabajo
        console.log("El id de orden para la carga de pedido es :");
        console.log(ido);
        i = 1;
        var opcion = i;
        $('#num1').append(opcion);
        i = i + 1;
        traer_proveedor();
    });

    //guardar pedido
    $('#btnSave').click(function() {

        if (acArt == 'View') {
            $('#modalOT').modal('hide');
            return;
        }

        var hayError = false;
        if ($('#nro').val() == '') {
            hayError = true;
        }

        if ($('#vfech').val() == '') {
            hayError = true;
        }

        if ($('#vsdetalle').val() == '') {
            hayError = true;
        }

        if ($('#sucid').val() == '') {
            hayError = true;
        }




        $('#error').fadeOut('slow');
        WaitingOpen('Guardando cambios');
        $.ajax({
            type: 'POST',
            data: {
                id: idArt,
                act: acArt,
                nro: $('#nro').val(),
                fech: $('#vfech').val(),
                deta: $('#vsdetalle').val(),
                sucid: $('#sucid').val(),
                cli: $('#cliid').val()

            },
            url: 'index.php/Otrabajo/setotrabajo',
            success: function(result) {
                WaitingClose();
                //$('#modalOT').modal('hide');
                //setTimeout("cargarView('otrabajos', 'index', '"+$('#permission').val()+"');",1000);
                regresa1();
            },
            error: function(result) {
                WaitingClose();
                alert("error");
            },
            dataType: 'json'
        });
    });

    //Eliminar
    $(".fa-times-circle").click(function(e) {

        var ido = $(this).parent('td').parent('tr').attr('id');
        console.log("ESTOY ELIMINANDO , el id de orden es:");
        console.log(ido);
        gloid = ido;

    });
    var origen = "";

    $(".fa-toggle-on").click(function(e) {
        var idord = $(this).parent('td').parent('tr').attr('id');
        console.log(idord);
        idfin = idord;
    });

});

function LoadOT(id_, action) {
    idArt = id_;
    acArt = action;
    LoadIconAction('modalAction', action);
    WaitingOpen('Cargando Orden de trabajo');
    $.ajax({
        type: 'POST',
        data: {
            id: id_,
            act: action
        },
        url: 'index.php/otrabajo/getotrabajo',
        success: function(result) {
            WaitingClose();
            $("#modalBodyOT").html(result.html);
            $('#vfech').datepicker({
                changeMonth: true,
                changeYear: true
            });
            setTimeout("$('#modalOT').modal('show')", 800);

        },
        error: function(result) {
            WaitingClose();
            alert("error");
        },
        dataType: 'json'
    });
}

function traer_clientes(idcliente) {
    $.ajax({
        type: 'POST',
        data: {
            idcliente: idcliente
        },
        url: 'index.php/Otrabajo/getcliente', //index.php/
        async: false,
        success: function(data) {

            $('#cliente option').remove();
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#cliente').append(opcion);
            for (var i = 0; i < data.length; i++) {

                var apellido = data[i]['cliLastName'];
                var opcion = "<option value='" + data[i]['cliId'] + "'>" + apellido + "</option>";
                $('#cliente').append(opcion);
            }
        },
        error: function(result) {

            console.log(result);
        },
        dataType: 'json'
    });
}

function finalOT(id_, action) { //esto es nuevo 
    idot = id_;
    ac = action;
    est = 'T';
    LoadIconAction('modalAction', action);
    WaitingOpen('Finalizando');
    $.ajax({
        type: 'POST',
        data: {
            id: id_,
            act: action,
            estado: est
        },
        url: 'index.php/otrabajo/setfinal',
        success: function(data) {
            WaitingClose();


        },
        error: function(result) {
            WaitingClose();
            alert("error");
        },
        dataType: 'json'
    });
}

function click_pedent() {
    var fechai = $("#fecha_inicio").val(); //optengo el valor del campo fecha 
    $.ajax({
        type: 'GET',
        data: {
            fechai: fechai
        },
        /* destinodo*/
        url: 'index.php/Otrabajo/getpedidos', //index.php/
        success: function(data) {

            console.log(data);
            var direccion = data[0]['destinodireccion'];
            var contacto = data[0]['destinocontacto'];
            $('#domicilio').val(direccion);
            $('#contacto').val(contacto);

        },
        error: function(result) {

            console.log(result);
        },
        dataType: 'json'
    });
}

function guardarpedido() {
    console.log("si guardo pedido");
    var id_orden = $(this).parent('td').parent('tr').attr('id');
    var numero = $('#num1').val();
    var fecha = $('#fecha1').val();
    var fecha_entrega = $('#fecha_entrega2').val();
    var proveedor = $('#proveedor').val();
    var descripcion2 = $('#descripcion2').val();
    var parametros = {

        'id_proveedor': proveedor,
        'nro_trabajo': numero,
        'descripcion': descripcion2,
        'fecha': fecha,
        'fecha_entrega': fecha_entrega,
        'estado': 'P',
        'id_trabajo': ido

    };
    console.log(parametros);
    console.log(ido);
    console.log(numero);
    console.log(fecha);
    console.log(fecha_entrega2);
    console.log(proveedor);
    console.log(descripcion2);
    $.ajax({
        type: 'POST',
        data: {
            data: parametros,
            ido: ido
        },
        url: 'index.php/Otrabajo/agregar_pedido', //index.php/
        success: function(data) {
            console.log("Estoy guardando pedido");
            regresa1();

        },
        error: function(result) {

            console.log(result);

        }
        // dataType: 'json'
    });
}



// INFORME DE SERVICIOS
// Genera Informe de Servicio - Evaluar funcionamoento a futuro no esta andando
function generar_informe_servicio(o) {

    var id_sol = parseInt($(o).closest('tr').attr('id'));
    var id_eq = parseInt($(o).closest('tr').attr('data-id_equipo'));
    var id_solicitud = parseInt($(o).closest('tr').attr('data-idsolicitud'));
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Ordenservicio/cargarOrden/<?php echo $permission; ?>/" +
        id_sol + "/" + id_eq + "/" + id_solicitud + "/");
    WaitingClose();
}
// VER INFORME DE SERVICIOS
function ver_informe_servicio(o) {

    var id_OT = $(o).closest('tr').attr('id');
    var id_eq = $(o).closest('tr').attr('data-id_equipo');
    var id_solicitud = $(o).closest('tr').attr('data-idsolicitud');

    WaitingOpen();
    $('#modalInforme').modal('show');
    $('#modalInformeServicios').empty();
    $("#modalInformeServicios").load("<?php echo base_url(); ?>P+" / "+id_solicitud+" / "");
    WaitingClose();
}

// OT TOTAL, pasa a la partalla de ot terminadas 
function guardartotal() {
    console.log("Estoy finalizando total la ot ");
    console.log(idfin);
    $.ajax({
        type: 'POST',
        data: {
            idfin: idfin
        },
        url: 'index.php/Otrabajo/FinalizaOt', //index.php/
        success: function(data) {
            console.log(data);
            alert("Se Finalizando la ORDEN TRABAJO");
            regresa();
        },

        error: function(result) {
            console.log(result);
        }
        //dataType: 'json'
    });
}
// MOSTRAR NOTA DE PEDIDO
function mostrar_pedido(o) {

    var idorde = $(o).closest('tr').attr('id');
    $.ajax({
        type: 'GET',
        url: "<?php echo base_url('index.php/'.ALM) ?>/new/Pedido_Material/getPedidos/" + idorde,
        success: function(data) {
            $('#modallista .body').html(data);
            $('#modallista .body').find('.btn-primary').remove();
            $('#modallista').modal('show');
        },

        error: function(result) {
            alert('Error');
        }
    });
    //$('#modallista .body').load("<?php #echo base_url('index.php/'.ALM) ?>/new/Pedido_Material/getPedidos/"+idorde);

    // WaitingOpen();
    // $('#box-header').empty();
    // WaitingClose();
    // $('#box-header > btn').remove("<?php #echo base_url(); ?>index.php/<?php #echo ALM ?>/new/Pedido_Material/getPedidos/"+idorde);

    // $('#content').empty();
    // $('#content').load("<?php #echo base_url(); ?>index.php/<?php#echo ALM ?>/new/Pedido_Material/getPedidos/"+idorde);

};

// AGREGAR NOTA DE PEDIDO
//Agrega nota de pedido desde la OT
function nota_pedido(o) {
    var id = $(o).parent('td').parent('tr').attr('id');
    console.log("El id de OT es: " + id);
    iort = id;
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Notapedido/agregarnota/<?php echo $permission; ?>/" + iort);
    WaitingClose();
}

// ASIGNAR OT (RESPONSBLE) 
// carga vista modal ejecutar ot y asignar responsable
function verEjecutarOT(o) {
    var id_orden = $(o).closest('tr').attr('id');
    WaitingOpen();
    $('#contRespyTareas').empty();
    $("#contRespyTareas").load("<?php echo base_url(); ?>index.php/Calendario/verEjecutarOT/" + id_orden + "/");
    $('#modalRespyTareas').modal('show');
    
    WaitingClose();  
}
// ASIGNAR TAREAS
// Lleva a la pantalla Asignar Tareas - Ok (no revisé la asignación!!!)
function agregar_tareas(o) {
    var id = $(o).closest('tr').attr('id');
    console.log("El id de OT es: " + id);
    iort = id;
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Otrabajo/cargartarea/<?php echo $permission; ?>/" + iort +
        "");
    WaitingClose();
};
// VER OT 
function mostrarOT(o) {
    console.log(o);
    let idot = $(o).closest('tr').attr('id');
    wo();
    //buscar datos 
    $.ajax({
        data: {
            idot: idot
        },
        dataType: 'json',
        method: 'POST',
        url: '<?php echo MAN; ?>Otrabajo/getOrigenOt',
        success: (data) => {
            console.table(data);
            traerDatosOt(idot, data.tipo, data.id_solicitud);
        },
        error: () => {
            error("Error","Error al traer los datos de la OT.");
        },
        complete: () => {
            wc();
        }
    });
}
// Elige a que fcion que trae datos de OT llamar, según su origen
function traerDatosOt(idOt, tipo, idSolicitud) {
    console.info('id deot' + idOt + ' - ' + idSolicitud + 'id solic');
    var datos = null;
    switch (tipo) {
        case '1': //Orden de trabajo
            datos = getDataOt(idOt, "orden de Trabajo");
            fillModalView(datos);
            $('#verOt').modal('show');
            WaitingClose();
            break;
        case '2': //Solicitud de servicio
            datos = getDataOtSolServicio(idOt, idSolicitud, "Solicitud de Servicio");
            fillModalViewSolServicio(datos);
            $('#verOtSolServicio').modal('show');
            WaitingClose();
            break;
        case '3': //preventivo
            datos = getDataOtPreventivo(idOt, idSolicitud, "Preventivo");
            fillModalViewPreventivo(datos);
            $('#verOtPreventivo').modal('show');
            WaitingClose();
            break;
        case '4': //Backlog 
            datos = getDataOtBacklog(idOt, idSolicitud, "Backlog");
												console.log('loos datosque busco: ');
												console.table(datos);
            fillModalViewBacklog(datos);
            $('#verOtBacklog').modal('show');
            WaitingClose();
            break;
        case '5': //predictivo  LISTO
            datos = getDataOtPredictivo(idOt, idSolicitud, "Predictivo");
            fillModalViewPredictivo(datos);
            $('#verOtPredictivo').modal('show');
            WaitingClose();
            break;
        case '6': //correctivo programado
            //break;
        default:
            console.error('Tipo de dato desconocido');
            WaitingClose();
            break;
    }
}
// devuelve palabra competa en funcion de estados
function getEstadosVer(letraestado) {
    var estado = "";
    switch (letraestado) {
        case 'S':
            estado = 'Solicitado';
            break;
        case 'PL':
            estado = 'Planificado';
            break;
        case 'AS':
            estado = 'Asignado';
            break;
        case 'C':
            estado = 'Curso';
            break;
        case 'T':
            estado = 'Terminada';
            break;
        default:
            estado = 'Cerrada';
            break;
    }
    return estado;
}

/***** 1 OT *****/ //LISTO Ver con adjunto y sin adjunto
// Trae datos de OT 
function getDataOt(idOt, origen) {
    wo();
    var datos = null;
    $.ajax({
        async: false,
        data: {
            idOt: idOt
        },
        dataType: 'json',
        method: 'POST',
        url: '<?php echo MAN; ?>Otrabajo/getViewDataOt',
        success: (data) => {
            datos = {
                //Panel datos de OT
                'id_ot': data['otrabajo'][0]['id_orden'],
                'nro': data['otrabajo'][0]['nro'],
                'descripcion_ot': data['otrabajo'][0]['descripcionFalla'],
                'fecha_inicio': data['otrabajo'][0]['fecha_inicio'],
                'fecha_entrega': data['otrabajo'][0]['fecha_entrega'],
                'fecha_program': data['otrabajo'][0]['fecha_program'],
                'fecha_terminada': data['otrabajo'][0]['fecha_terminada'],
                'estado': data['otrabajo'][0]['estado'],
                'sucursal': data['otrabajo'][0]['descripc'],
                'nombreprov': data['otrabajo'][0]['provnombre'],
                'origen': origen,

                'asignado': data['otrabajo'][0]['usrLastName'] + ' ' + data['otrabajo'][0]['usrLastName'],
                'estado': data['otrabajo'][0]['estado'],
                //Panel datos de equipos
                'codigo': data['otrabajo'][0]['codigo'],
                'marca': data['otrabajo'][0]['marca'],
                'ubicacion': data['otrabajo'][0]['ubicacion'],
                'descripcion_eq': data['otrabajo'][0]['descripcionEquipo'],
                'nomCli': data['otrabajo'][0]['nomCli'],
                'comp_equipo': data['otrabajo'][0]['compEquipo'],
                //'adjunto'        : adjunto
            };

            var herram = data['herramientas'];
            var insum = data['insumos'];
            var adjunto = data['adjunto'];

            $('#tblherrOT tbody tr').remove();
            for (var i = 0; i < herram.length; i++) {
                var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
                    "<td>" + herram[i]['herrcodigo'] + "</td>" +
                    "<td>" + herram[i]['herrmarca'] + "</td>" +
                    "<td>" + herram[i]['herrdescrip'] + "</td>" +
                    "<td>" + herram[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblherrOT tbody').append(tr);
            }
            $('#tblinsOT tbody tr').remove();
            for (var i = 0; i < insum.length; i++) {
                var tr = "<tr id='" + insum[i]['artId'] + "'>" +

                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                    "<td>" + insum[i]['artDescription'] + "</td>" +
                    "<td>" + insum[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblinsOT tbody').append(tr);
            }
            recargaTablaAdjuntoOT(adjunto);
        },
        error: () => {
            error("Error","Error al traer los datos de la OT.");
        },
        complete: () => wc(),
    });
    return datos;
}
//llena datos del modal preventivo
function fillModalView(datos) {
    //llenar datos de ot
    $('#vNroOt').val(datos['nro']);
    $('#vDescripFalla').val(datos['descripcion_ot']);

    $('#vFechaProgram').val(datos['fecha_program']);
    $('#vFechaCreacion').val(datos['fecha_inicio']);
    $('#vFechaTerminOT').val(datos['fecha_terminada']);

    $('#vSucursal').val(datos['sucursal']);
    $('#vProveedor').val(datos['nombreprov']);
    $('#vIdOt').val(datos['id_ot']);
    $('#vOrigen').val(datos['origen']);

    if (datos['asignado'] != 'null null') {
        $('#vAsignado').val(datos['asignado']);
    } else {
        $('#vAsignado').val('Sin Asignar');
    }



    var estadoOtrab = getEstadosVer(datos['estado']);

    $('#vEstado').val(estadoOtrab);
    //llenar datos de equipo
    $('#vCodigoEquipo').val(datos['codigo']);
    $('#vCliente').val(datos['nomCli']);
    $('#vMarcaEquipo').val(datos['marca']);
    $('#vUbicacionEquipo').val(datos['ubicacion']);
    $('#vDescripcionEquipo').val(datos['descripcion_eq']);
}

// recarga tablas de adjuntos al iniciar la edicion
function recargaTablaAdjuntoOT(Adjunto) {
    $('#TabAdjuntoOT tbody').empty();
    if (Adjunto == 0) {
        $('#tblAdjuntoOT tbody').append("<tr><td colspan='1'>Sin Adjuntos</td></tr>");
    } else {
        for (var i = 0; i < Adjunto.length; i++) {
            var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
                "<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto " + (i + 1) +
                "</a></td>" +
                "</tr>";
            $('#tblAdjuntoOT tbody').append(tr);
        }
    }
}



/***** 2 Solicitud de Servicios *****/
// Trae datos de Solicitud de Servicios con origen Backlog
function getDataOtSolServicio(idOt, idSolServicio, origen) {
    WaitingOpen('Cargando datos...');
    var datos = null;
    $.ajax({
            async: false,
            data: {
                idOt: idOt,
                idSolServicio: idSolServicio
            },
            dataType: 'json',
            method: 'POST',
            url: 'index.php/Otrabajo/getViewDataSolServicio',
        })
        .done((data) => {
            console.table(data);
            datos = {
                //Panel datos de OT
                'id_ot': data['solicitud'][0]['id_orden'],
                'nro': data['solicitud'][0]['nro'],
                'descripcion_ot': data['solicitud'][0]['descripcionFalla'],
                'grupo': data['solicitud'][0]['grupodescrip'],
                'fecha_program': data['solicitud'][0]['fecha_program'],
                'fecha_inicio': data['solicitud'][0]['fecha_inicio'],
                'fecha_terminada': data['solicitud'][0]['fecha_terminada'],
                'estado': data['solicitud'][0]['estado'],
                'sucursal': data['solicitud'][0]['descripc'],
                'nombreprov': data['solicitud'][0]['provnombre'],
                'origen': origen,
                'asignado': data['solicitud'][0]['usrLastName'] + ' ' + data['solicitud'][0]['usrLastName'],
                'estado': data['solicitud'][0]['estado'],
                //Panel datos de equipos
                'codigo': data['solicitud'][0]['codigo'],
                'marca': data['solicitud'][0]['marca'],
                'ubicacion': data['solicitud'][0]['ubicacion'],
                'descripcion_eq': data['solicitud'][0]['descripcionEquipo'],
                'comp_equipo': data['solicitud'][0]['compEquipo'],
                'nomCli': data['solicitud'][0]['nomCli'],
                'solServicio': data['solicitud'][0]['solServicio']
            };


            var herram = data['herramientas'];
            var insum = data['insumos'];
            var adjunto = data['adjunto'];


            $('#tblherrsolicitud tbody tr').remove();
            for (var i = 0; i < herram.length; i++) {
                var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
                    "<td>" + herram[i]['herrcodigo'] + "</td>" +
                    "<td>" + herram[i]['herrmarca'] + "</td>" +
                    "<td>" + herram[i]['herrdescrip'] + "</td>" +
                    "<td>" + herram[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblherrsolicitud tbody').append(tr);
            }
            $('#tblinsSolicitud tbody tr').remove();
            for (var i = 0; i < insum.length; i++) {
                var tr = "<tr id='" + insum[i]['artId'] + "'>" +

                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                    "<td>" + insum[i]['artDescription'] + "</td>" +
                    "<td>" + insum[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblinsSolicitud tbody').append(tr);
            }
            recargaTablaAdjuntoSolic(adjunto);
        })
        .fail(() => alert("Error al traer los datos de la OT."))
        .always(() => WaitingClose());
    return datos;
}

//llena datos del modal preventivo
function fillModalViewSolServicio(datos) {
    //llenar datos de ot
    $('#vNroOtSolServicio').val(datos['nro']);
    $('#vDescripFallaSolServicio').val(datos['descripcion_ot']);
    $('#vFechaCreacionSolServicio').val(datos['fecha_inicio']);
    $('#vFechaTerminaSolServ').val(datos['fecha_terminada']);
    $('#vSucursalSolServicio').val(datos['sucursal']);
    $('#vProveedorSolServicio').val(datos['nombreprov']);
    $('#vIdOtSolServicio').val(datos['id_ot']);
    $('#vOrigenSolServicio').val(datos['origen']);
    $('#vFechaProgramSolServicio').val(datos['fecha_program']);
    if (datos['asignado'] != 'null null') {
        $('#vAsignadoSolServicio').val(datos['asignado']);
    } else {
        $('#vAsignadoSolServicio').val('Sin Asignar');
    }

    var estadoSol = getEstadosVer(datos['estado']);

    $('#vEstadoSolServicio').val(estadoSol);
    //llenar datos de equipo
    $('#vCodigoEquipoSolServicio').val(datos['codigo']);
    $('#NombreClienteServicio').val(datos['nomCli']);
    $('#vMarcaEquipoSolServicio').val(datos['marca']);
    $('#vUbicacionEquipoSolServicio').val(datos['ubicacion']);
    $('#vDescripcionEquipoSolServicio').val(datos['descripcion_eq']);
    //llenar datos de soolicitud de servicios
    $('#vSectorSolServicio').val(datos['solServicio']['sector']);
    $('#vGrupoSolServicio').val(datos['grupo']);
    $('#vSolicitanteSolServicio').val(datos['solServicio']['solicitante']);
    $('#vFechaSugeridaSolServicio').val(datos['solServicio']['fechaSugerida']);
    $('#vHorarioSugeridoSolServicio').val(datos['solServicio']['horarioSugerido']);
    $('#vFallaSolServicio').val(datos['solServicio']['falla']);
}

// recarga tablas de adjuntos al iniciar la edicion
function recargaTablaAdjuntoSolic(Adjunto) {
    $('#tbladjSolicitud tbody tr').remove();
    if (Adjunto == 0) {
        $('#tbladjSolicitud').html('<p>Sin Adjuntos</p>');
    } else {
        for (var i = 0; i < Adjunto.length; i++) {
            var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
                "<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto " + (i + 1) +
                " </a></td>" +
                "</tr>";
            $('#tbladjSolicitud tbody').append(tr);
        }
    }
}

/***** 3 preventivo *****/ //  LISTO falta adj- 
// Trae datos de OT con origen Preventivo
function getDataOtPreventivo(idOt, idPreventivo, origen) {
    //WaitingOpen('Cargando datos...');
    var datos = null;
    $.ajax({
            async: false,
            data: {
                idOt: idOt,
                idPreventivo: idPreventivo
            },
            dataType: 'json',
            method: 'POST',
            url: 'index.php/Otrabajo/getViewDataPreventivo',
        })
        .done((data) => {

            datos = {
                //Panel datos de OT
                'id_ot': data['preventivo'][0]['id_orden'],
                'nro': data['preventivo'][0]['nro'],
                'descripcion_ot': data['preventivo'][0]['descripcionFalla'],
                'fecha_inicio': data['preventivo'][0]['fecha_inicio'],
                'fecha_terminada': data['preventivo'][0]['fecha_terminada'],
                'fecha_program': data['preventivo'][0]['fecha_program'],
                'estado': data['preventivo'][0]['estado'],
                'sucursal': data['preventivo'][0]['descripc'],
                'origen': origen,
                'asignado': data['preventivo'][0]['usrLastName'] + ' ' + data['usrLastName'],
                //Panel datos de equipos
                'codigo': data['preventivo'][0]['codigo'],
                'marca': data['preventivo'][0]['marca'],
                'ubicacion': data['preventivo'][0]['ubicacion'],
                'descripcion_eq': data['preventivo'][0]['descripcionEquipo'],
                'nomCli': data['preventivo'][0]['nomCli'],
                'tarea': data['preventivo'][0]['tarea'],
            };

            var herram = data['herramientas'];
            var insum = data['insumos'];
            var adjunto = data['adjunto'];

            $('#tblherrPrevent tbody tr').remove();
            for (var i = 0; i < herram.length; i++) {
                var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
                    "<td>" + herram[i]['herrcodigo'] + "</td>" +
                    "<td>" + herram[i]['herrmarca'] + "</td>" +
                    "<td>" + herram[i]['herrdescrip'] + "</td>" +
                    "<td>" + herram[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblherrPrevent tbody').append(tr);
            }
            $('#tblinsPrevent tbody tr').remove();
            for (var i = 0; i < insum.length; i++) {
                var tr = "<tr id='" + insum[i]['artId'] + "'>" +

                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                    "<td>" + insum[i]['artDescription'] + "</td>" +
                    "<td>" + insum[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblinsPrevent tbody').append(tr);
            }
            recargaTablaAdjuntoPreven(adjunto);

        })
        .fail(() => alert("Error al traer los datos de la OT."))
        .always(() => WaitingClose());
    return datos;
}
//llena datos del modal preventivo
function fillModalViewPreventivo(datos) {
    //llenar datos de ot
    $('#vNroOtPrev').val(datos['nro']);
    $('#vDescripFallaPrev').val(datos['descripcion_ot']);
    $('#vFechaCreacionPrev').val(datos['fecha_inicio']);
    $('#vFechaTerminadaPrev').val(datos['fecha_terminada']);
    $('#vSucursalPrev').val(datos['sucursal']);
    $('#vProveedorPrev').val(datos['nombreprov']);
    $('#vIdOtPrev').val(datos['id_ot']);
    $('#vOrigenPrev').val(datos['origen']);
    $('#vFechaProgramPrev').val(datos['fecha_program']);
    if (datos['asignado'] != 'null undefined') {
        $('#vAsignadoPrev').val(datos['asignado']);
    } else {
        $('#vAsignadoPrev').val('Sin Asignar');
    }
    var estadoPrevent = getEstadosVer(datos['estado']);
    $('#vEstadoPrev').val(estadoPrevent);
    //llenar datos de equipo
    $('#vCodigoEquipoPrev').val(datos['codigo']);
    $('#NombreClientePrev').val(datos['nomCli']);
    $('#vMarcaEquipoPrev').val(datos['marca']);
    $('#vUbicacionEquipoPrev').val(datos['ubicacion']);
    $('#vDescripcionEquipoPrev').val(datos['descripcion_eq']);
    //llenar campos de tarea
    $('#vTareaPrev').val(datos['tarea']['tareadescrip']);
    $('#vComponentePrev').val(datos['tarea']['descripComponente']);
    $('#vFechaBasePrev').val(datos['tarea']['ultimo']);
    $('#vPeriodoPrev').val(datos['tarea']['perido']);
    $('#vFrecuenciaPrev').val(datos['tarea']['frecuencia']);
    $('#vLecturaBasePrev').val(datos['tarea']['lectura_base']);
    $('#vAlertaPrev').val(datos['tarea']['alerta']);
    $('#vDuraciónPrev').val(datos['tarea']['prev_duracion']);
    $('#vUnidadTiempoPrev').val(datos['tarea']['unidaddescrip']);
    $('#vCantOperariosPrev').val(datos['tarea']['prev_canth']);
    llenarTablaHerramientas(datos['tarea']);
    llenarTablaInsumos(datos['tarea']);
}
//llena tabla herramientas del modal preventivo
function llenarTablaHerramientas(tareas) {
    //console.table(tareas['herramientas'][0]);
    $('#vTablaHerramientas').DataTable().clear().draw();
    for (var i = 0; i < tareas['herramientas'][0].length; i++) {
        //console.info('Herramientas: '+tareas['herramientas'][0][i]);
        $('#vTablaHerramientas').DataTable().row.add([
            tareas['herramientas'][0][i].herrcodigo,
            tareas['herramientas'][0][i].herrmarca,
            tareas['herramientas'][0][i].herrdescrip,
            tareas['herramientas'][0][i].cantidad,
        ]).draw();
    }
}
//llena tabla insumos del modal preventivo
function llenarTablaInsumos(tareas) {
    //console.table(tareas['insumos'][0]);
    $('#vTablaInsumos').DataTable().clear().draw();
    for (var i = 0; i < tareas['insumos'][0].length; i++) {
        $('#vTablaInsumos').DataTable().row.add([
            tareas['insumos'][0][i].artBarCode,
            tareas['insumos'][0][i].artDescription,
            tareas['insumos'][0][i].cantidad,
        ]).draw();
    }
}

// recarga tablas de adjuntos al iniciar la edicion
function recargaTablaAdjuntoPreven(Adjunto) {
    $('#TabAdjuntoPrevent tbody tr').remove();
    if (Adjunto == 0) {
        $('#TabAdjuntoPrevent').html('<p>Sin Adjuntos</p>');
    } else {
        for (var i = 0; i < Adjunto.length; i++) {
            var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
                "<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto " + (i + 1) +
                " </a></td>" +
                "</tr>";
            $('#tblAdjuntoPreven tbody').append(tr);
        }
    }
}




/***** 4 Backlog *****/ //  LISTO falta adj- 
// Trae datos de OT con origen Backlog
function getDataOtBacklog(idOt, idBacklog, origen) {
    WaitingOpen('Cargando datos...');
    var datos = null;
    $.ajax({
            async: false,
            data: {
                idOt: idOt,
                idBacklog: idBacklog
            },
            dataType: 'json',
            method: 'POST',
            url: 'index.php/Otrabajo/getViewDataBacklog',
        })
        .done((data) => {

            datos = {
                //Panel datos de OT
                'id_ot': data['backlog'][0]['id_orden'],
                'nro': data['backlog'][0]['nro'],
                'descripcion_ot': data['backlog'][0]['descripcionFalla'],
                'fecha_inicio': data['backlog'][0]['fecha_inicio'],
                'fecha_entrega': data['backlog'][0]['fecha_entrega'],
                'fecha_program': data['backlog'][0]['fecha_program'],
                'fecha_terminada': data['backlog'][0]['fecha_terminada'],
                'estado': data['backlog'][0]['estado'],
                'sucursal': data['backlog'][0]['descripc'],
                'nombreprov': data['backlog'][0]['provnombre'],
                'origen': origen,

                'asignado': data['backlog'][0]['usrLastName'] + ' ' + data['backlog'][0]['usrLastName'],
                'estado': data['backlog'][0]['estado'],
                //Panel datos de equipos
                'codigo': data['backlog'][0]['codigo'],
                'marca': data['backlog'][0]['marca'],
                'ubicacion': data['backlog'][0]['ubicacion'],
                'descripcion_eq': data['backlog'][0]['descripcionEquipo'],
                'comp_equipo': data['backlog'][0]['compEquipo'],
                'tarea': data['backlog'][0]['tarea']
            };
            console.table(datos);
            var herram = data['herramientas'];
            var insum = data['insumos'];
            var adjunto = data['adjunto'];
            $('#tblherrBack tbody tr').remove();
            for (var i = 0; i < herram.length; i++) {
                var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
                    "<td>" + herram[i]['herrcodigo'] + "</td>" +
                    "<td>" + herram[i]['herrmarca'] + "</td>" +
                    "<td>" + herram[i]['herrdescrip'] + "</td>" +
                    "<td>" + herram[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblherrBack tbody').append(tr);
            }
            $('#tblinsumBack tbody tr').remove();
            for (var i = 0; i < insum.length; i++) {
                var tr = "<tr id='" + insum[i]['artId'] + "'>" +

                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                    "<td>" + insum[i]['artDescription'] + "</td>" +
                    "<td>" + insum[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblinsumBack tbody').append(tr);
            }
            recargaTablaAdjuntoBack(adjunto);

        })

        .fail(() => alert("Error al traer los datos de la OT."))
        .always(() => WaitingClose());
    return datos;
}
//llena datos del modal preventivo
function fillModalViewBacklog(datos) {
    //llenar datos de ot
    $('#vNroOtBack').val(datos['nro']);
    $('#vDescripFallaBack').val(datos['descripcion_ot']);
    $('#vFechaCreacionBack').val(datos['fecha_inicio']);
    $('#vFechaProgramBack').val(datos['fecha_program']);
    $('#vFechaTerminadaBack').val(datos['fecha_terminada']);
    $('#vSucursalBack').val(datos['sucursal']);
    $('#vProveedorBack').val(datos['nombreprov']);
    $('#vIdOtBack').val(datos['id_ot']);
    $('#vOrigenBack').val(datos['origen']);

    if (datos['asignado'] != 'null null') {
        $('#vAsignadoBack').val(datos['asignado']);
    } else {
        $('#vAsignadoBack').val('Sin Asignar');
    }

    var estadoBack = getEstadosVer(datos['estado']);
    $('#vEstadoBack').val(estadoBack);
    //llenar datos de equipo
    $('#vCodigoEquipoBack').val(datos['codigo']);
    $('#vMarcaEquipoBack').val(datos['marca']);
    $('#vUbicacionEquipoBack').val(datos['ubicacion']);
    $('#vDescripcionEquipoBack').val(datos['descripcion_eq']);

    //llenar campos de componente-equipo si existe componente
				if (datos && datos['tarea'] && datos['tarea']['compEquipo'] && datos['tarea']['compEquipo'].codigoComponente) {
						$('#vCodigoCompBack').val( datos['tarea']['compEquipo'].codigoComponente );
						$('#vDescripCompBack').val( datos['tarea']['compEquipo'].descripComponente );
						$('#vSistemaBack').val( datos['tarea']['compEquipo'].descripSistema );
				} else {
						$('#vCodigoCompBack').val('Sin componente');
						$('#vDescripCompBack').val('-');
						$('#vSistemaBack').val('-');
				}

    //console.table(datos['tarea']);
    if (datos['tarea']['id_tarea'] == 0) {
        $('#vTareaBack').val(datos['tarea']['tarea_opcional']);
    } else {
        $('#vTareaBack').val(datos['tarea']['tareadescrip']);
    }

    $('#vFechaBack').val(datos['tarea']['fecha']);
    $('#vDuracionBack').val(datos['tarea']['back_duracion']);
}

// recarga tablas de adjuntos al iniciar la edicion
function recargaTablaAdjuntoBack(Adjunto) {
    $('#TabAdjuntoBack tbody tr').remove();
    if (Adjunto == 0) {
        $('#TabAdjuntoBack').html('<p>Sin Adjuntos</p>');
    } else {
        for (var i = 0; i < Adjunto.length; i++) {

            var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
                "<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto " + (i + 1) +
                "</a></td>" +
                "</tr>";
            $('#tblAdjuntoBack tbody').append(tr);
        }
    }
}

/***** 5 Predictivo *****/ //  LISTO falta adj- 
// Trae datos de OT con origen Predictivo
function getDataOtPredictivo(idOt, idPredictivo, origen) {
    WaitingOpen('Cargando datos...');
    var datos = null;
    $.ajax({
            async: false,
            data: {
                idOt: idOt,
                idPredictivo: idPredictivo
            },
            dataType: 'json',
            method: 'POST',
            url: 'index.php/Otrabajo/getViewDataPredictivo',
        })
        .done((data) => {
            console.table(data);
            datos = {
                //Panel datos de OT
                'id_ot': data['predictivo'][0]['id_orden'],
                'nro': data['predictivo'][0]['nro'],
                'descripcion_ot': data['predictivo'][0]['descripcionFalla'],
                'fecha_inicio': data['predictivo'][0]['fecha_inicio'],
                'fecha_entrega': data['predictivo'][0]['fecha_entrega'],
                'fecha_program': data['predictivo'][0]['fecha_program'],
                'fecha_terminada': data['predictivo'][0]['fecha_terminada'],
                'estado': data['predictivo'][0]['estado'],
                'sucursal': data['predictivo'][0]['descripc'],
                'nombreprov': data['predictivo'][0]['provnombre'],
                'origen': origen,
                'asignado': data['predictivo'][0]['usrLastName'] + ' ' + data['predictivo'][0]['usrLastName'],
                'estado': data['predictivo'][0]['estado'],
                //Panel datos de equipos
                'codigo': data['predictivo'][0]['codigo'],
                'marca': data['predictivo'][0]['marca'],
                'ubicacion': data['predictivo'][0]['ubicacion'],
                'descripcion_eq': data['predictivo'][0]['descripcionEquipo'],
                'tarea': data['predictivo'][0]['tarea']
            };

            var herram = data['herramientas'];
            var insum = data['insumos'];
            var adjunto = data['adjunto'];

            $('#tblherrPred tbody tr').remove();
            for (var i = 0; i < herram.length; i++) {
                var tr = "<tr id='" + herram[i]['herrId'] + "'>" +
                    "<td>" + herram[i]['herrcodigo'] + "</td>" +
                    "<td>" + herram[i]['herrmarca'] + "</td>" +
                    "<td>" + herram[i]['herrdescrip'] + "</td>" +
                    "<td>" + herram[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblherrPred tbody').append(tr);
            }
            $('#tblinsPredictivo tbody tr').remove();
            for (var i = 0; i < insum.length; i++) {
                var tr = "<tr id='" + insum[i]['artId'] + "'>" +

                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                    "<td>" + insum[i]['artDescription'] + "</td>" +
                    "<td>" + insum[i]['cantidad'] + "</td>" +
                    "</tr>";
                $('#tblinsPredictivo tbody').append(tr);
            }
            recargaTablaAdjuntoPred(adjunto);
        })
        .fail(() => {
            alert("Error al traer los datos de la OT.")
        })
        .always(() => WaitingClose());
    return datos;
}
//llena datos del modal preventivo
function fillModalViewPredictivo(datos) {
    //llenar datos de ot
    $('#vNroOtPred').val(datos['nro']);
    $('#vDescripFallaPred').val(datos['descripcion_ot']);
    $('#vFechaCreacionPred').val(datos['fecha_inicio']);
    $('#vFechaEntregaPred').val(datos['fecha_terminada']);
    $('#vSucursalPred').val(datos['sucursal']);
    $('#vProveedorPred').val(datos['nombreprov']);

    $('#vIdOtPr').val(datos['id_ot']);
    $('#vOrigenPred').val(datos['origen']);
    $('#vFechaProgramPred').val(datos['fecha_program']);
    if (datos['asignado'] != 'null null') {
        $('#vAsignadoPred').val(datos['asignado']);
    } else {
        $('#vAsignadoPred').val('Sin Asignar');
    }
    var estadoPred = getEstadosVer(datos['estado']);
    $('#vEstadoPred').val(estadoPred);
    //llenar datos de equipo
    $('#vCodigoEquipoPred').val(datos['codigo']);
    $('#vMarcaEquipoPred').val(datos['marca']);
    $('#vUbicacionEquipoPred').val(datos['ubicacion']);
    $('#vDescripcionEquipoPred').val(datos['descripcion_eq']);
    //llenar campos de tarea
    $('#vTareaPred').val(datos['tarea']['tareadescrip']);
    $('#vFechaPred').val(datos['tarea']['fecha']);
    $('#vPeriodoPred').val(datos['tarea']['periodo']);
    $('#vFrecuenciaPred').val(datos['tarea']['frecuencia']);
    $('#vDuraciónPred').val(datos['tarea']['duracion'] + ' ' + datos['tarea']['unidaddescrip']);
    $('#vCantOperariosPred').val(datos['tarea']['cantOperarios']);
    $('#vCantHsHombrePred').val(datos['tarea']['horash']);

}

// recarga tablas de adjuntos al iniciar la edicion
function recargaTablaAdjuntoPred(Adjunto) {
    $('#TabAdjuntoPred tbody tr').remove();
    if (Adjunto == 0) {
        $('#TabAdjuntoPred').html('<p>Sin Adjuntos</p>');
    } else {
        for (var i = 0; i < Adjunto.length; i++) {
            var tr = "<tr id='" + Adjunto[i]['id'] + "'>" +
                "<td ><i class='fa fa-times-circle eliminaAdjunto text-light-blue' style='cursor:pointer; margin-right:10px' title='Eliminar Adjunto'></i></td>'" +
                "<td><a id='' href='" + Adjunto[i]['ot_adjunto'] + "' target='_blank'>Archivo adjunto</a></td>" +
                "</tr>";
            $('#tblAdjuntoPred tbody').append(tr);
        }
    }
}

// ajusto el ancho de la cabecera de las tablas al cargar el modal
$('#verOtPreventivo').on('shown.bs.modal', function(e) {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
});
//y al mostrar panel de acordeon
$('#collapseHerramientas, #collapseInsumos').on('shown.bs.collapse', function() {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
})

// IMPRIMIR
$(".fa-print").click(function(e) {
    let idot = $(this).parent('td').parent('tr').attr('id');
    wo();
    //buscar datos 
    $.ajax({
        data: {
            idot: idot
        },
        dataType: 'json',
        method: 'POST',
        url: '<?php echo MAN; ?>Otrabajo/getOrigenOt',
        success: function(data) {
            console.table(data);
            traerDatosImprimirOt(idot, data.tipo, data.id_solicitud);
        },
        error: function(result) {
            console.log(result);
            console.log("error en la vista imprimir");
        },
        complete: function() {
            wc();
        }
    });
});

// Elige a que fcion que trae datos de OT llamar, según su origen
function traerDatosImprimirOt(idOt, tipo, idSolicitud) {
    console.info(idOt + ' - ' + idSolicitud);
    var datos = null;
    switch (tipo) {
        case '1': //Orden de trabajo
            datos = getDataOt(idOt, "orden de Trabajo");
            fillPrintView(datos, tipo);
            WaitingClose();
            break;
        case '2': //Solicitud de servicio
            datos = getDataOtSolServicio(idOt, idSolicitud, "Solicitud de Servicio");
            fillPrintView(datos, tipo);
            WaitingClose();
            break;
        case '3': //preventivo
            datos = getDataOtPreventivo(idOt, idSolicitud, "Preventivo");
            fillPrintView(datos, tipo);
            WaitingClose();
            break;
        case '4': //Backlog
            datos = getDataOtBacklog(idOt, idSolicitud, "Backlog");
            fillPrintView(datos, tipo);
            WaitingClose();
            break;
        case '5': //predictivo
            datos = getDataOtPredictivo(idOt, idSolicitud, "Predictivo");
            fillPrintView(datos, tipo);
            WaitingClose();
            break;
        case '6': //correctivo programado
            //break;
        default:
            console.error('Tipo de dato desconocido');
            WaitingClose();
            break;
    }
}

//llena datos del modal preventivo
function fillPrintView(datos, tipo) {
    console.table(datos);
    $.ajax({
        type: 'POST',
        data: {
            datos: datos,
            tipo: tipo
        },
        dataType: 'text',
        url: 'index.php/Otrabajo/printOT',
        success: function(data) {
            texto = data;
            var mywindow = window.open('', 'Imprimir', 'height=700,width=900');
            mywindow.document.write('<html><head><title></title>');
            mywindow.document.write('</head><body onload="window.print();">');
            mywindow.document.write(texto);
            mywindow.document.write('</body></html>');
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10
            return true;
        },
        error: function(result) {
            console.log(result);
            console.log("error en la vista imprimir");
        },
    });
}

// DATATABLE
$('#otrabajo').DataTable({
    <?php echo(!DT_SIZE_ROWS ? '"paging": true,' : null) ?>

    "aLengthMenu" : [10, 25, 50, 100],
    "columnDefs" : [{
            "targets": [0],
            "searchable": false,
        },
        {
            "targets": [0],
            "orderable": false,
        },
        {
            "targets": [1, 8],
            "type": "num",
        }
    ],
    "order": [
        [1, "desc"]
    ],
});
$('#tabladetalle').DataTable({
    "aLengthMenu": [10, 25, 50, 100],
    "columnDefs": [{
            "targets": [0],
            "searchable": false
        },
        {
            "targets": [0],
            "orderable": false
        }
    ],
    "order": [
        [1, "asc"]
    ],
});
$('#vTablaHerramientas').DataTable({
    "aLengthMenu": [10, 25, 50, 100],
    "columnDefs": [{
        "targets": [3],
        "type": "num",
    }],
    "order": [
        [0, "asc"]
    ],
});
$('#vTablaInsumos').DataTable({
    "aLengthMenu": [10, 25, 50, 100],
    "order": [
        [0, "asc"]
    ],
});

/* input con horas minutos y segundos */
$("#fechaProgramacion").datetimepicker({
    format: 'YYYY-MM-DD h:mm:ss',
    locale: 'es',
});
</script>




<!-- Modal Aviso desea eliminar -->
<div class="modal" id="modalaviso">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h5>
            </div>
            <div class="modal-body">
                <h4>¿Desea eliminarl Orden de Trabajo?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="eliminarpred()">Eliminar</button>
            </div>
        </div>
    </div>
</div><!-- /.modal fade -->
<!-- / Modal -->

<!--  MODAL asignar Responsable y tareas   -->
<div class="modal bs-example-modal-lg" id="modalRespyTareas" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Asignar Responsable y Tareas</h4>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-12" id="contRespyTareas">


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  MODAL Informe de Servicios   -->
<div class="modal bs-example-modal-lg" id="modalInforme" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Informe de Servicios</h4>
            </div>

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


<!-- Modal FINALIZAR-->
<div class="modal" id="modalfinalizar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-fw fa fa-toggle-on text-light-blue"></span> Finalización </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <h4>
                    Elija la opción de finalización de orden:
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="guardarparcial()">
                    Parcial</button>
                <button type="button" class="btn btn-primary" id="btnSave" data-dismiss="modal"
                    onclick="guardartotal()">Total</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog modal-lg -->
</div> <!-- /.modal fade -->
<!-- / Modal -->


<!-- Modal editar -->
<div class="modal" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger alert-dismissable" id="errorE" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Revise que todos los campos obligatorios esten seleccionados
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-fw fa-pencil text-light-blue"></span> Editar Orden de Trabajo </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del equipo </h3>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 com-md-3">

                                <input type="hidden" id="id_ot" name="id_ot" class="form-control input-md" disabled />

                                <label for="equipo">Equipo:</label>
                                <input type="text" id="equipo_descrip" name="equipo_descrip"
                                    class="form-control input-md" disabled />
                                <input type="hidden" id="equipo" name="equipo" class="form-control input-md" disabled />
                            </div>
                            <div class="col-xs-12 col-sm-3 com-md-3">

                                <input type="hidden" id="id_ot" name="id_ot" class="form-control input-md" disabled />

                                <label for="cliente">Cliente:</label>
                                <input type="text" id="NombreCliente" name="NombreCliente" class="form-control input-md"
                                    disabled />
                                <input type="hidden" id="cliente" name="cliente" class="form-control input-md"
                                    disabled />
                            </div>
                            <div class="col-xs-12 col-sm-3 com-md-3">
                                <label for="fecha_ingreso">Fecha:</label>
                                <input type="text" id="fecha_ingreso" name="fecha_ingreso" class="form-control input-md"
                                    disabled />
                            </div>
                            <div class="col-xs-12 col-sm-3 com-md-3">
                                <label for="marca">Marca:</label>
                                <input type="text" id="marca" name="marca" class="form-control input-md" disabled />
                            </div>
                            <div class="col-xs-12 col-sm-3 com-md-3">
                                <label for="ubicacion">Ubicacion:</label>
                                <input type="text" id="ubicacion" name="ubicacion" class="form-control input-md"
                                    disabled />
                            </div>
                            <div class="col-xs-12">
                                <label for="descripcion">Descripcion: </label>
                                <textarea class="form-control" id="descripcion" name="descripcion" disabled></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><span class="fa fa-building-o"></span> Programación</h4>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <label for="tarea">Tarea Estandar<strong style="color: #dd4b39">*</strong>:</label>
                                <input type="text" id="tarea" name="tarea" class="form-control"
                                    placeholder="Buscar Tarea...">
                                <input type="hidden" id="id_tarea" name="id_tarea">
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label for="tarea_manual">Tarea Personalizada<strong
                                        style="color: #dd4b39">*</strong>:</label>
                                <input type="text" id="tareacustom" name="tareacustom" class="form-control"
                                    placeholder="Ingresar Tarea...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-4">
                                <label for="fechaInicio">Fecha Programación:</label>
                                <!-- <input type="text" class="datepicker form-control fecha" id="fechaInicio" name="fechaInicio" value="<?php //echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>" size="27"/> -->
                                <input type="text" class="datepicker form-control fecha" id="fechaProgramacion"
                                    name="fechaProgramacion" size="27" />
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <label for="fechaInicio">Fecha Inicio:</label>
                                <!-- <input type="text" class="datepicker form-control fecha" id="fechaInicio" name="fechaInicio" value="<?php //echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>" size="27"/> -->
                                <input type="text" class="datepicker form-control fecha" id="fechaInicio"
                                    name="fechaInicio" size="27" disabled />
                            </div>

                            <div class="col-xs-12 col-sm-4">
                                <label for="fechaEntrega">Fecha Terminada:</label>
                                <input type="text" class="datepicker form-control fecha" id="fechaTerminada"
                                    name="fechaTerminada"
                                    value="<?php //echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>"
                                    size="27" disabled />
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <!-- <label for="suci">Sucursal</label>
                  <select  id="suci" name="suci" class="form-control" /> -->
                                <input type="hidden" id="suci" value="1" name="suci" />
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <!-- <label for="prov">Proveedor</label>
                  <select  id="prov" name="prov" class="form-control" /> -->
                                <input type="hidden" id="prov" value="1" name="prov" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <!--tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#herramin" aria-controls="profile"
                                        role="tab" data-toggle="tab">Herramientas</a></li>
                                <li role="presentation"><a href="#insum" aria-controls="messages" role="tab"
                                        data-toggle="tab">Insumos</a></li>
                                <li role="presentation"><a href="#TabAdjunto" aria-controls="messages" role="tab"
                                        data-toggle="tab">Adjunto</a></li>
                            </ul>
                            <!-- /tabs -->

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="herramin">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="herramienta">Codigo <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="herramienta" name="" class="form-control" />
                                            <input type="hidden" id="id_herramienta" name="id_herramienta">
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="marcaherram">Marca <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="marcaherram" name="" class="form-control" />
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="descripcionherram">Descripcion <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="descripcionherram" name="" class="form-control" />
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="cantidadherram">Cantidad <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="cantidadherram" name="" class="form-control"
                                                placeholder="Ingrese Cantidad" />
                                        </div>
                                        <br>
                                        <div class="col-xs-12">
                                            <label></label>
                                            <br>
                                            <button type="button" class="btn btn-primary" id="agregarherr"><i
                                                    class="fa fa-check">Agregar</i></button>
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
                                                        <th>Descripcion</th>
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
                                            <label for="insumo">Codigo <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="insumo" name="insumo" class="form-control" />
                                            <input type="hidden" id="id_insumo" name="">
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="">Descripcion:</label>
                                            <input type="text" id="descript" name="" class="form-control" />
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4">
                                            <label for="cant">Cantidad <strong
                                                    style="color: #dd4b39">*</strong>:</label>
                                            <input type="text" id="cant" name="" class="form-control"
                                                placeholder="Ingrese Cantidad" />
                                        </div>
                                    </div><!-- /.row -->
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <br>
                                            <button type="button" class="btn btn-primary" id="agregarins"><i
                                                    class="fa fa-check">Agregar</i></button>
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
                                </div>
                                <!--/#insum -->

                                <div role="tabpanel" class="tab-pane" id="TabAdjunto">
                                    <div class="row">

                                        <div class="col-xs-12"><i
                                                class="fa fa-plus-square agregaAdjunto text-light-blue"
                                                style="color:#f39c12; cursor:pointer; margin-right:10px"
                                                title="Agregar Adjunto"></i> Agregar Archivo</div>


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

                                                        </td>
                                                        <td>
                                                            <a id="adjunto" href="" target="_blank"></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                                <!--cierre de TabAdjunto-->

                            </div> <!-- tab-content -->

                        </div><!-- /.nav-tabs-custom -->
                    </div>
                </div>

            </div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEditar" onclick="guardareditar()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div>

    </div> <!-- /.modal-content -->
</div> <!-- /.modal-dialog modal-lg -->
</div>




<!--------------- MODALES ADJUNTO ------------->

<!-- Modal Eliminar Adjunto -->
<div class="modal" id="modalEliminarAdjunto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idAdjunto">
                <h4>¿Desea eliminar Archivo Adjunto?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="eliminarAdjunto();">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar adjunto -->
<div class="modal" id="modalAgregarAdjunto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
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



<!-- Modal agregar -->
<div class="modal" id="modalagregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="fa fa-plus-square text-light-blue"></span> Orden
                    de Trabajo</h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            Revise que todos los campos obligatorios estén completos.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Nro: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <input type="text" class="form-control text_box" id="nro1" name="nro1"
                            placeholder="Ingrese Numero de OT">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Equipo <strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <select class="form-control select_box" id="equipo" name="equipo" value="" style="width: 100%;">

                        </select>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Fecha Inicio<strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <input type="datetime" class="form-control" id="fechaInicio" name="fechaInicio" />
                        <!-- <input type="text" class="form-control" id="fechaInicio" name="fechaInicio" value="<?php //echo date_format(date_create(date("Y-m-d")), 'd-m-Y ') ; ?>"/> -->
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Fecha Entrega<strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <input type="datetime" class="form-control text_box" id="fechaEntrega" name="fechaEntrega"
                            value="" />
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Nota: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <textarea placeholder="Orden de trabajo" class="form-control text_box" rows="10" id="vsdetal"
                            name="vsdetal" value=""></textarea>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Sucursal <strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <select class="form-control select2 select_box" id="suci" name="suci" style="width: 100%;">

                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label style="margin-top: 7px;">Proveedor <strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <select class="form-control select2 select_box" id="prov" name="prov" value=""
                            style="width: 100%;">

                        </select>
                    </div>
                </div>
                <br>
                <div class="modal-footer">
                    <button type="button" id="btn_cancGuardado" class="btn btn-default"
                        data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar"
                        onclick="guardaragregar()">Guardar</button>
                </div> <!-- /.modal footer -->
            </div> <!-- /.modal-body -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog modal-lg -->
</div><!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Aviso desea eliminar -->
<div class="modal" id="modalaviso">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h5>
            </div>
            <div class="modal-body">
                <h4>¿Desea eliminarl Orden de Trabajo?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="eliminarpred()">Eliminar</button>
            </div>
        </div>
    </div>
</div><!-- /.modal fade -->
<!-- / Modal -->



<!-- Modal ASIGNA OT -->
<div id="modalAsig" class="modal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="fa fa-thumb-tack text-light-blue"></span> Asignación Orden de
                    trabajo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="nro">Nro:</label>
                        <input type="text" class="form-control" id="nro" name="nro" disabled>
                    </div>
                    <input type="hidden" id="id_orden" name="id_orden">
                    <div class="col-xs-12">
                        <label for="fecha_inicio">Fecha de inicio:</label>
                        <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" disabled>
                    </div>
                    <div class="col-xs-12">
                        <label for="equipo13">Equipo:</label>
                        <input type="text" id="equipo13" name="equipo13" class="form-control" title="" disabled>
                        <input type="hidden" id="equipo13id" name="equipo13id">
                    </div>
                    <div class="col-xs-12">
                        <label for="descripcion">Descripcion:</label>
                        <textarea class="form-control" rows="6" cols="500" id="descripcion" name="descripcion" value=""
                            disabled></textarea>
                    </div>
                    <div class="col-xs-12">
                        <label for="fecha_entrega">Fecha de entrega:</label>
                        <input type="text" id="fecha_entrega" name="fecha_entrega" class="form-control datepicker" />
                    </div>
                    <div class="col-xs-12">
                        <label for="usuario1">Responsable:</label>
                        <select id="usuario1" name="usuario1" class="form-control"></select>
                        <input type="hidden" id="id_usuario" name="id_usuario">
                    </div>
                </div><!-- /.row-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal"
                    onclick="orden()">Guardar</button>
            </div>
        </div>

    </div>
</div><!-- /.modal fade -->
<!-- / Modal -->


<!-- Modal FINALIZAR-->
<div class="modal" id="modalfinalizar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-fw fa fa-toggle-on text-light-blue"></span> Finalización </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <h4>
                    Elija la opción de finalización de orden:
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="guardarparcial()">
                    Parcial</button>
                <button type="button" class="btn btn-primary" id="btnSave" data-dismiss="modal"
                    onclick="guardartotal()">Total</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog modal-lg -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal mostrar pedido-->
<div class="modal" id="modallista" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg body" role="document">

    </div> <!-- /.modal-dialog modal-lg -->
</div> <!-- /.modal fade -->
<!-- / Modal -->


<!-- Modal Ver Orden de Trabajo LISTO con Adjunto-->
<div class="modal" id="verOt" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Orden de Trabajo</h4>
            </div>
            <div class="modal-body">

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOt"
                                    aria-expanded="true" aria-controls="collapseOt">
                                    Datos de OT
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOt" class="panel-collapse collapse in" role="tabpanel"
                            aria-labelledby="headingOne">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <label for="vIdOt">Nº de OT:</label>
                                        <input type="text" class="form-control " name="vIdOt" id="vIdOt" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-9">
                                        <label for="vDescripFalla">Descripción:</label>
                                        <input type="text" class="form-control vDescripFalla" id="vDescripFalla"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaProgram">Fecha Programación:</label>
                                        <input type="text" class="form-control " name="vFechaProgram" id="vFechaProgram"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaCreacion">Fecha Inicio:</label>
                                        <input type="text" class="form-control " name="vFechaCreacion"
                                            id="vFechaCreacion" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaTerminOT">Fecha Terminada:</label>
                                        <input type="text" class="form-control " name="vFechaTerminOT"
                                            id="vFechaTerminOT" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vEstado">Estado:</label>
                                        <input type="text" class="form-control " name="vEstado" id="vEstado" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vSucursal">Sucursal:</label>
                                        <input type="text" class="form-control " name="vSucursal" id="vSucursal"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vProveedor">Proveedor:</label>
                                        <input type="text" class="form-control " name="vProveedor" id="vProveedor"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vOrigen">Origen:</label>
                                        <input type="text" class="form-control " name="vOrigen" id="vOrigen" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vAsignado">Asignado:</label>
                                        <input type="text" class="form-control " name="vAsignado" id="vAsignado"
                                            disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                    href="#collapseEquipo" aria-expanded="false" aria-controls="collapseEquipo">
                                    Datos de equipo
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEquipo" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingTwo">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCodigoEquipo">Equipo:</label>
                                        <input type="text" class="form-control " name="vCodigoEquipo" id="vCodigoEquipo"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCliente">Cliente:</label>
                                        <input type="text" class="form-control " name="vCliente" id="vCliente" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vMarcaEquipo">Marca:</label>
                                        <input type="text" class="form-control " name="vMarcaEquipo" id="vMarcaEquipo"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vUbicacionEquipo">Ubicación:</label>
                                        <input type="text" class="form-control " name="vUbicacionEquipo"
                                            id="vUbicacionEquipo" disabled>
                                    </div>

                                    <div class="col-xs-12">
                                        <label for="vDescripcionEquipo">Descripción:</label>
                                        <Textarea class="form-control " name="vDescripcionEquipo"
                                            id="vDescripcionEquipo" disabled></Textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- vista herramientas -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreePred">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPred"
                                    href="#collapseTareaPredInsHerrOT" aria-expanded="false"
                                    aria-controls="collapseTareaPredInsHerrOT">
                                    Herramientas - Tareas - Insumos
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaPredInsHerrOT" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreePred">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="nav-tabs-custom">
                                            <!--tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#herrOT"
                                                        aria-controls="profile" role="tab"
                                                        data-toggle="tab">Herramientas</a></li>
                                                <li role="presentation"><a href="#insumOT" aria-controls="messages"
                                                        role="tab" data-toggle="tab">Insumos</a></li>
                                                <li role="presentation"><a href="#TabAdjuntoOT" aria-controls="messages"
                                                        role="tab" data-toggle="tab">Adjunto</a></li>
                                            </ul>
                                            <!-- /tabs -->

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="herrOT">

                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <br>
                                                            <table class="table table-bordered" id="tblherrOT">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Marca</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div> <!-- /.tabpanel #herramin-->

                                                <div role="tabpanel" class="tab-pane" id="insumOT">

                                                    <div class="row">

                                                    </div><!-- /.row -->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tblinsOT">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div>
                                                <!--/#insum -->

                                                <div role="tabpanel" class="tab-pane" id="TabAdjuntoOT">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tblAdjuntoOT">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Archivo</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <a id="adjunto" href="" target="_blank"></a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--cierre de TabAdjunto-->

                                            </div> <!-- tab-content -->

                                        </div><!-- /.nav-tabs-custom -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--  ./vista herramientas -->
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal Ver Orden de Trabajo Solicitud de Servicio-->
<div class="modal" id="verOtSolServicio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Orden de Trabajo</h4>
            </div>
            <div class="modal-body">

                <div class="panel-group" id="accordionSolServicio" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOneSolServicio">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordionSolServicio"
                                    href="#collapseOtSolServicio" aria-expanded="true"
                                    aria-controls="collapseOtSolServicio">
                                    Datos de OT
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOtSolServicio" class="panel-collapse collapse in" role="tabpanel"
                            aria-labelledby="headingOneSolServicio">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <label for="vIdOtSolServicio">Nº de OT:</label>
                                        <input type="text" class="form-control " name="vIdOtSolServicio"
                                            id="vIdOtSolServicio" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-9">
                                        <label for="vDescripFallaSolServicio">Descripción:</label>
                                        <input type="text" class="form-control vDescripFallaSolServicio"
                                            id="vDescripFallaSolServicio" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaProgramSolServicio">Fecha Programación:</label>
                                        <input type="text" class="form-control " name="vFechaProgramSolServicio"
                                            id="vFechaProgramSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaCreacionSolServicio">Fecha Inicio:</label>
                                        <input type="text" class="form-control " name="vFechaCreacionSolServicio"
                                            id="vFechaCreacionSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaTerminaSolServ">Fecha Terminada:</label>
                                        <input type="text" class="form-control " name="vFechaTerminaSolServ"
                                            id="vFechaTerminaSolServ" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vEstadoSolServicio">Estado:</label>
                                        <input type="text" class="form-control " name="vEstadoSolServicio"
                                            id="vEstadoSolServicio" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vSucursalSolServicio">Sucursal:</label>
                                        <input type="text" class="form-control " name="vSucursalSolServicio"
                                            id="vSucursalSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vProveedorSolServicio">Proveedor:</label>
                                        <input type="text" class="form-control " name="vProveedorSolServicio"
                                            id="vProveedorSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vOrigenSolServicio">Origen:</label>
                                        <input type="text" class="form-control " name="vOrigenSolServicio"
                                            id="vOrigenSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vAsignadoSolServicio">Asignado:</label>
                                        <input type="text" class="form-control " name="vAsignadoSolServicio"
                                            id="vAsignadoSolServicio" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwoSolServicio">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse"
                                    data-parent="#accordionSolServicio" href="#collapseEquipoSolServicio"
                                    aria-expanded="false" aria-controls="collapseEquipoSolServicio">
                                    Datos de equipo
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEquipoSolServicio" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingTwoSolServicio">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCodigoEquipoSolServicio">Equipo:</label>
                                        <input type="text" class="form-control " name="vCodigoEquipoSolServicio"
                                            id="vCodigoEquipoSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="nomClienteServicio">Cliente:</label>
                                        <input type="text" class="form-control " name="nomClienteServicio"
                                            id="NombreClienteServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vMarcaEquipoSolServicio">Marca:</label>
                                        <input type="text" class="form-control " name="vMarcaEquipoSolServicio"
                                            id="vMarcaEquipoSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vUbicacionEquipoSolServicio">Ubicación:</label>
                                        <input type="text" class="form-control " name="vUbicacionEquipoSolServicio"
                                            id="vUbicacionEquipoSolServicio" disabled>
                                    </div>

                                    <div class="col-xs-12">
                                        <label for="vDescripcionEquipoSolServicio">Descripción:</label>
                                        <Textarea class="form-control " name="vDescripcionEquipoSolServicio"
                                            id="vDescripcionEquipoSolServicio" disabled></Textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreeSolServicio">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse"
                                    data-parent="#accordionSolServicio" href="#collapseSolServicio"
                                    aria-expanded="false" aria-controls="collapseSolServicio">
                                    Solicitud de Servicio
                                </a>
                            </h4>
                        </div>
                        <div id="collapseSolServicio" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreeSolServicio">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vSectorSolServicio">Sector:</label>
                                        <input type="text" class="form-control " name="vSectorSolServicio"
                                            id="vSectorSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vGrupoSolServicio">Grupo:</label>
                                        <input type="text" class="form-control " name="vGrupoSolServicio"
                                            id="vGrupoSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vSolicitanteSolServicio">Solicitante:</label>
                                        <input type="text" class="form-control " name="vSolicitanteSolServicio"
                                            id="vSolicitanteSolServicio" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vFechaSugeridaSolServicio">Fecha sugerida:</label>
                                        <input type="text" class="form-control " name="vFechaSugeridaSolServicio"
                                            id="vFechaSugeridaSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vHorarioSugeridoSolServicio">Horario sugerido:</label>
                                        <input type="text" class="form-control " name="vHorarioSugeridoSolServicio"
                                            id="vHorarioSugeridoSolServicio" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vFallaSolServicio">Causa:</label>
                                        <input type="text" class="form-control " name="vFallaSolServicio"
                                            id="vFallaSolServicio" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- vista herramientas -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreePred">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionBack"
                                    href="#collapseTareaSolicitudInsHerr" aria-expanded="false"
                                    aria-controls="collapseTareaSolicitudInsHerr">
                                    Herramientas - Tareas - Insumos
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaSolicitudInsHerr" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreePred">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="nav-tabs-custom">
                                            <!--tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#herrSolicitu"
                                                        aria-controls="profile" role="tab"
                                                        data-toggle="tab">Herramientas</a></li>
                                                <li role="presentation"><a href="#insumSolicitu"
                                                        aria-controls="messages" role="tab"
                                                        data-toggle="tab">Insumos</a></li>
                                                <li role="presentation"><a href="#TabAdjuntoSolicitud"
                                                        aria-controls="messages" role="tab"
                                                        data-toggle="tab">Adjunto</a></li>
                                            </ul>
                                            <!-- /tabs -->

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="herrSolicitu">

                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <br>
                                                            <table class="table table-bordered" id="tblherrsolicitud">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Marca</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div> <!-- /.tabpanel #herramin-->

                                                <div role="tabpanel" class="tab-pane" id="insumSolicitu">

                                                    <div class="row">

                                                    </div><!-- /.row -->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tblinsSolicitud">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div>
                                                <!--/#insum -->

                                                <div role="tabpanel" class="tab-pane" id="TabAdjuntoSolicitud">
                                                    <div class="row">

                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tbladjSolicitud">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Archivo/s</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                </div>
                                                <!--cierre de TabAdjunto-->

                                            </div> <!-- tab-content -->

                                        </div><!-- /.nav-tabs-custom -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  ./vista herramientas -->

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal Ver Orden de Trabajo Preventivo LISTO con Adjunto -->
<div class="modal" id="verOtPreventivo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Orden de Trabajo</h4>
            </div>
            <div class="modal-body">

                <div class="panel-group" id="accordionPrev" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOnePrev">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordionPrev"
                                    href="#collapseOtPrev" aria-expanded="true" aria-controls="collapseOtPrev">
                                    Datos de OT
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOtPrev" class="panel-collapse collapse in" role="tabpanel"
                            aria-labelledby="headingOnePrev">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <label for="vIdOtPrev">Nº de OT:</label>
                                        <input type="text" class="form-control " name="vIdOtPrev" id="vIdOtPrev"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-9">
                                        <label for="vDescripFallaPrev">Descripción:</label>
                                        <input type="text" class="form-control vDescripFallaPrev" id="vDescripFallaPrev"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaProgramPrev">Fecha Programación:</label>
                                        <input type="text" class="form-control " name="vFechaProgramPrev"
                                            id="vFechaProgramPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaCreacionPrev">Fecha Inicio:</label>
                                        <input type="text" class="form-control " name="vFechaCreacionPrev"
                                            id="vFechaCreacionPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaTerminadaPrev">Fecha Terminada:</label>
                                        <input type="text" class="form-control " name="vFechaTerminadaPrev"
                                            id="vFechaTerminadaPrev" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vEstadoPrev">Estado:</label>
                                        <input type="text" class="form-control " name="vEstadoPrev" id="vEstadoPrev"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vSucursalPrev">Sucursal:</label>
                                        <input type="text" class="form-control " name="vSucursalPrev" id="vSucursalPrev"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vProveedorPrev">Proveedor:</label>
                                        <input type="text" class="form-control " name="vProveedorPrev"
                                            id="vProveedorPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vOrigenPrev">Origen:</label>
                                        <input type="text" class="form-control " name="vOrigenPrev" id="vOrigenPrev"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vAsignadoPrev">Asignado:</label>
                                        <input type="text" class="form-control " name="vAsignadoPrev" id="vAsignadoPrev"
                                            disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwoPrev">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPrev"
                                    href="#collapseEquipoPrev" aria-expanded="false" aria-controls="collapseEquipoPrev">
                                    Datos de equipo
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEquipoPrev" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingTwoPrev">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCodigoEquipoPrev">Equipo:</label>
                                        <input type="text" class="form-control " name="vCodigoEquipoPrev"
                                            id="vCodigoEquipoPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="ClientePrev">Cliente:</label>
                                        <input type="text" class="form-control " name="ClientePrev"
                                            id="NombreClientePrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vMarcaEquipoPrev">Marca:</label>
                                        <input type="text" class="form-control " name="vMarcaEquipoPrev"
                                            id="vMarcaEquipoPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vUbicacionEquipoPrev">Ubicación:</label>
                                        <input type="text" class="form-control " name="vUbicacionEquipoPrev"
                                            id="vUbicacionEquipoPrev" disabled>
                                    </div>

                                    <div class="col-xs-12">
                                        <label for="vDescripcionEquipoPrev">Descripción:</label>
                                        <Textarea class="form-control " name="vDescripcionEquipoPrev"
                                            id="vDescripcionEquipoPrev" disabled></Textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreePrev">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPrev"
                                    href="#collapseTareaPrev" aria-expanded="false" aria-controls="collapseTareaPrev">
                                    Datos de la Tarea
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaPrev" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreePrev">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vTareaPrev">Tarea:</label>
                                        <input type="text" class="form-control " name="vTareaPrev" id="vTareaPrev"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vComponentePrev">Componente:</label>
                                        <input type="text" class="form-control " name="vComponentePrev"
                                            id="vComponentePrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vPeriodoPrev">Periodo:</label>
                                        <input type="text" class="form-control " name="vPeriodoPrev" id="vPeriodoPrev"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vFrecuenciaPrev">Frecuencia:</label>
                                        <input type="text" class="form-control " name="vFrecuenciaPrev"
                                            id="vFrecuenciaPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vLecturaBasePrev">Lectura Base:</label>
                                        <input type="text" class="form-control " name="vLecturaBasePrev"
                                            id="vLecturaBasePrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vAlertaPrev">Alerta:</label>
                                        <input type="text" class="form-control " name="vAlertaPrev" id="vAlertaPrev"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vDuraciónPrev">Duración:</label>
                                        <input type="text" class="form-control " name="vDuraciónPrev" id="vDuraciónPrev"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vUnidadTiempoPrev">Unidad de tiempo:</label>
                                        <input type="text" class="form-control " name="vUnidadTiempoPrev"
                                            id="vUnidadTiempoPrev" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCantOperariosPrev">Cantidad Operarios:</label>
                                        <input type="text" class="form-control " name="vCantOperariosPrev"
                                            id="vCantOperariosPrev" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- vista herramientas -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreePred">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPrev"
                                    href="#collapseTareaPreventInsHerr" aria-expanded="false"
                                    aria-controls="collapseTareaPreventInsHerr">
                                    Herramientas - Tareas - Insumos
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaPreventInsHerr" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreePred">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="nav-tabs-custom">
                                            <!--tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#herrPrevent"
                                                        aria-controls="profile" role="tab"
                                                        data-toggle="tab">Herramientas</a></li>
                                                <li role="presentation"><a href="#insumPrevent" aria-controls="messages"
                                                        role="tab" data-toggle="tab">Insumos</a></li>
                                                <li role="presentation"><a href="#TabAdjuntoPrevent"
                                                        aria-controls="messages" role="tab"
                                                        data-toggle="tab">Adjunto</a></li>
                                            </ul>
                                            <!-- /tabs -->

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="herrPrevent">

                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <br>
                                                            <table class="table table-bordered" id="tblherrPrevent">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Marca</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div> <!-- /.tabpanel #herramin-->

                                                <div role="tabpanel" class="tab-pane" id="insumPrevent">

                                                    <div class="row">

                                                    </div><!-- /.row -->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tblinsPrevent">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div>
                                                <!--/#insum -->

                                                <div role="tabpanel" class="tab-pane" id="TabAdjuntoPrevent">
                                                    <div class="col-xs-12">
                                                        <table class="table table-bordered" id="tblAdjuntoPreven">
                                                            <thead>
                                                                <tr>
                                                                    <th>Archivo/s</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td id="accionAdjuntoPreven">
                                                                    </td>
                                                                    <td>
                                                                        <a id="adjuntopreven" href=""
                                                                            target="_blank"></a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--cierre de TabAdjunto-->

                                            </div> <!-- tab-content -->

                                        </div><!-- /.nav-tabs-custom -->
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!--  ./vista herramientas -->


                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal Ver Orden de Trabajo Backlog LISTO -->
<div class="modal" id="verOtBacklog" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Orden de Trabajobckl</h4>
            </div>
            <div class="modal-body">

                <div class="panel-group" id="accordionBack" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOneBack">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordionBack"
                                    href="#collapseOtBack" aria-expanded="true" aria-controls="collapseOtBack">
                                    Datos de OT
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOtBack" class="panel-collapse collapse in" role="tabpanel"
                            aria-labelledby="headingOneBack">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <label for="vIdOtBack">Nº de OT:</label>
                                        <input type="text" class="form-control " name="vIdOtBack" id="vIdOtBack"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-9">
                                        <label for="vDescripFallaBack">Descripción:</label>
                                        <input type="text" class="form-control vDescripFallaBack" id="vDescripFallaBack"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaProgramBack">Fecha Programación:</label>
                                        <input type="text" class="form-control " name="vFechaProgramBack"
                                            id="vFechaProgramBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaCreacionBack">Fecha Inicio:</label>
                                        <input type="text" class="form-control " name="vFechaCreacionBack"
                                            id="vFechaCreacionBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaTerminadaBack">Fecha Terminada:</label>
                                        <input type="text" class="form-control " name="vFechaTerminadaBack"
                                            id="vFechaTerminadaBack" disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vEstadoBack">Estado:</label>
                                        <input type="text" class="form-control " name="vEstadoBack" id="vEstadoBack"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vSucursalBack">Sucursal:</label>
                                        <input type="text" class="form-control " name="vSucursalBack" id="vSucursalBack"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vProveedorBack">Proveedor:</label>
                                        <input type="text" class="form-control " name="vProveedorBack"
                                            id="vProveedorBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vOrigenBack">Origen:</label>
                                        <input type="text" class="form-control " name="vOrigenBack" id="vOrigenBack"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vAsignadoBack">Asignado:</label>
                                        <input type="text" class="form-control " name="vAsignadoBack" id="vAsignadoBack"
                                            disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwoBack">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionBack"
                                    href="#collapseEquipoBack" aria-expanded="false" aria-controls="collapseEquipoBack">
                                    Datos de equipo
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEquipoBack" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingTwoBack">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCodigoEquipoBack">Equipo:</label>
                                        <input type="text" class="form-control " name="vCodigoEquipoBack"
                                            id="vCodigoEquipoBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vMarcaEquipoBack">Marca:</label>
                                        <input type="text" class="form-control " name="vMarcaEquipoBack"
                                            id="vMarcaEquipoBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vUbicacionEquipoBack">Ubicación:</label>
                                        <input type="text" class="form-control " name="vUbicacionEquipoBack"
                                            id="vUbicacionEquipoBack" disabled>
                                    </div>

                                    <div class="col-xs-12">
                                        <label for="vDescripcionEquipoBack">Descripción:</label>
                                        <Textarea class="form-control " name="vDescripcionEquipoBack"
                                            id="vDescripcionEquipoBack" disabled></Textarea>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vCodigoCompBack">Código de componente-equipo:</label>
                                        <input type="text" class="form-control " name="vCodigoCompBack"
                                            id="vCodigoCompBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vDescripCompBack">Descripción de componente:</label>
                                        <input type="text" class="form-control " name="vDescripCompBack"
                                            id="vDescripCompBack" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vSistemaBack">Sistema:</label>
                                        <input type="text" class="form-control " name="vSistemaBack" id="vSistemaBack"
                                            disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreeBack">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionBack"
                                    href="#collapseTareaBack" aria-expanded="false" aria-controls="collapseTareaBack">
                                    Datos de la Tarea
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaBack" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreeBack">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vTareaBack">Tarea:</label>
                                        <input type="text" class="form-control " name="vTareaBack" id="vTareaBack"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vFechaBack">Fecha:</label>
                                        <input type="text" class="form-control " name="vFechaBack" id="vFechaBack"
                                            disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="vDuracionBack">Duración:</label>
                                        <input type="text" class="form-control " name="vDuracionBack" id="vDuracionBack"
                                            disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- vista herramientas -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThreePred">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionBack"
                                    href="#collapseTareaBackInsHerr" aria-expanded="false"
                                    aria-controls="collapseTareaBackInsHerr">
                                    Herramientas - Tareas - Insumos
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTareaBackInsHerr" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThreePred">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="nav-tabs-custom">
                                            <!--tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#herrback"
                                                        aria-controls="profile" role="tab"
                                                        data-toggle="tab">Herramientas</a></li>
                                                <li role="presentation"><a href="#insumBack" aria-controls="messages"
                                                        role="tab" data-toggle="tab">Insumos</a></li>
                                                <li role="presentation"><a href="#TabAdjuntoBack"
                                                        aria-controls="messages" role="tab"
                                                        data-toggle="tab">Adjunto</a></li>
                                            </ul>
                                            <!-- /tabs -->

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="herrback">

                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <br>
                                                            <table class="table table-bordered" id="tblherrBack">
                                                                <thead>
                                                                    <tr>
                                                                        <!-- <th>Acciones</th> -->
                                                                        <th>Código</th>
                                                                        <th>Marca</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div> <!-- /.tabpanel #herramin-->

                                                <div role="tabpanel" class="tab-pane" id="insumBack">

                                                    <div class="row">

                                                    </div><!-- /.row -->
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <table class="table table-bordered" id="tblinsumBack">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Cantidad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.row -->
                                                </div>
                                                <!--/#insum -->

                                                <div role="tabpanel" class="tab-pane" id="TabAdjuntoBack">

                                                    <div class="col-xs-12">
                                                        <table class="table table-bordered" id="tblAdjuntoBack">
                                                            <thead>
                                                                <tr>
                                                                    <th>Archivo/s</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <a id="adjuntoBack" href="" target="_blank"></a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                                <!--cierre de TabAdjunto-->

                                            </div> <!-- tab-content -->

                                        </div><!-- /.nav-tabs-custom -->
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!--  ./vista herramientas -->

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal Ver Orden de Trabajo Predictivo  LISTO -->
<div class="modal" id="verOtPredictivo" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
																aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Orden de Trabajo</h4>
						</div>
						<div class="modal-body">

								<div class="panel-group" id="accordionPred" role="tablist" aria-multiselectable="true">
												<div class="panel panel-default">
														<div class="panel-heading" role="tab" id="headingOnePred">
																<h4 class="panel-title">
																				<a role="button" data-toggle="collapse" data-parent="#accordionPred"
																								href="#collapseOtPred" aria-expanded="true" aria-controls="collapseOtPred">
																								Datos de OT
																				</a>
																</h4>
														</div>
														<div id="collapseOtPred" class="panel-collapse collapse in" role="tabpanel"	aria-labelledby="headingOnePred">
																<div class="panel-body">

																	<div class="row">
																			<div class="col-xs-12 col-sm-3">
																							<label for="vIdOtPr">Nº de OT:</label>
																							<input type="text" class="form-control " name="vIdOtPr" id="vIdOtPr" disabled>
																			</div>
																			<!-- <div class="col-xs-12 col-sm-3">
																					<label for="vNroOtPred">Número de OT:</label>
																					<input type="text" class="form-control " name="vNroOtPred" id="vNroOtPred" disabled>
																			</div> -->
																			<div class="col-xs-12 col-sm-9">
																							<label for="vDescripFallaPred">Descripción:</label>
																							<input type="text" class="form-control vDescripFallaPred" id="vDescripFallaPred"
																											disabled>
																			</div>

																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vFechaProgramPred">Fecha Programación:</label>
																							<input type="text" class="form-control " name="vFechaProgramPred"
																											id="vFechaProgramPred" disabled>
																			</div>
																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vFechaCreacionPred">Fecha Inicio:</label>
																							<input type="text" class="form-control " name="vFechaCreacionPred"
																											id="vFechaCreacionPred" disabled>
																			</div>
																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vFechaEntregaPred">Fecha Terminada:</label>
																							<input type="text" class="form-control " name="vFechaEntregaPred"
																											id="vFechaEntregaPred" disabled>
																			</div>

																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vEstadoPred">Estado:</label>
																							<input type="text" class="form-control " name="vEstadoPred" id="vEstadoPred"
																											disabled>
																			</div>

																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vSucursalPred">Sucursal:</label>
																							<input type="text" class="form-control " name="vSucursalPred" id="vSucursalPred"
																											disabled>
																			</div>
																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vProveedorPred">Proveedor:</label>
																							<input type="text" class="form-control " name="vProveedorPred"
																											id="vProveedorPred" disabled>
																			</div>
																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vOrigenPred">Origen:</label>
																							<input type="text" class="form-control " name="vOrigenPred" id="vOrigenPred"
																											disabled>
																			</div>
																			<div class="col-xs-12 col-sm-6 col-md-3">
																							<label for="vAsignadoPred">Asignado:</label>
																							<input type="text" class="form-control " name="vAsignadoPred" id="vAsignadoPred"
																											disabled>
																			</div>
																	</div>

																</div>
														</div>
												</div>
												<div class="panel panel-default">
																<div class="panel-heading" role="tab" id="headingTwoPred">
																		<h4 class="panel-title">
																						<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPred"
																										href="#collapseEquipoPred" aria-expanded="false" aria-controls="collapseEquipoPred">
																										Datos de equipo
																						</a>
																		</h4>
																</div>
																<div id="collapseEquipoPred" class="panel-collapse collapse" role="tabpanel"
																				aria-labelledby="headingTwoPred">
																				<div class="panel-body">

																						<div class="row">
																								<div class="col-xs-12 col-sm-6 col-md-4">
																												<label for="vCodigoEquipoPred">Equipo:</label>
																												<input type="text" class="form-control " name="vCodigoEquipoPred"
																																id="vCodigoEquipoPred" disabled>
																								</div>
																								<div class="col-xs-12 col-sm-6 col-md-4">
																												<label for="vMarcaEquipoPred">Marca:</label>
																												<input type="text" class="form-control " name="vMarcaEquipoPred"
																																id="vMarcaEquipoPred" disabled>
																								</div>
																								<div class="col-xs-12 col-sm-6 col-md-4">
																												<label for="vUbicacionEquipoPred">Ubicación:</label>
																												<input type="text" class="form-control " name="vUbicacionEquipoPred"
																																id="vUbicacionEquipoPred" disabled>
																								</div>

																								<div class="col-xs-12">
																												<label for="vDescripcionEquipoPred">Descripción:</label>
																												<Textarea class="form-control " name="vDescripcionEquipoPred"
																																id="vDescripcionEquipoPred" disabled></Textarea>
																								</div>
																						</div>

																				</div>
																</div>
												</div>
												<div class="panel panel-default">
																<div class="panel-heading" role="tab" id="headingThreePred">
																				<h4 class="panel-title">
																								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPred"
																												href="#collapseTareaPred" aria-expanded="false" aria-controls="collapseTareaPred">
																												Datos de la Tarea
																								</a>
																				</h4>
																</div>
																<div id="collapseTareaPred" class="panel-collapse collapse" role="tabpanel"
																				aria-labelledby="headingThreePred">
																				<div class="panel-body">

																								<div class="row">
																												<div class="col-xs-12">
																																<label for="vTareaPred">Tarea:</label>
																																<input type="text" class="form-control " name="vTareaPred" id="vTareaPred"
																																				disabled>
																												</div>

																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vFechaPred">Fecha:</label>
																																<input type="text" class="form-control " name="vFechaPred" id="vFechaPred"
																																				disabled>
																												</div>
																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vPeriodoPred">Periodo:</label>
																																<input type="text" class="form-control " name="vPeriodoPred" id="vPeriodoPred"
																																				disabled>
																												</div>
																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vFrecuenciaPred">Frecuencia:</label>
																																<input type="text" class="form-control " name="vFrecuenciaPred"
																																				id="vFrecuenciaPred" disabled>
																												</div>

																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vDuraciónPred">Duración:</label>
																																<input type="text" class="form-control " name="vDuraciónPred" id="vDuraciónPred"
																																				disabled>
																												</div>
																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vCantOperariosPred">Cantidad Operarios:</label>
																																<input type="text" class="form-control " name="vCantOperariosPred"
																																				id="vCantOperariosPred" disabled>
																												</div>
																												<div class="col-xs-12 col-sm-6 col-md-4">
																																<label for="vCantHsHombrePred">Cantidad horas hombre:</label>
																																<input type="text" class="form-control " name="vCantHsHombrePred"
																																				id="vCantHsHombrePred" disabled>
																												</div>
																								</div>
																				</div>
																</div>
												</div>
												<!-- vista herramientas -->
												<div class="panel panel-default">
																<div class="panel-heading" role="tab" id="headingThreePred">
																				<h4 class="panel-title">
																								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionPred"
																												href="#collapseTareaPredInsHerr" aria-expanded="false"
																												aria-controls="collapseTareaPredInsHerr">
																												Herramientas - Tareas - Insumos
																								</a>
																				</h4>
																</div>
																<div id="collapseTareaPredInsHerr" class="panel-collapse collapse" role="tabpanel"
																				aria-labelledby="headingThreePred">
																				<div class="panel-body">

																								<div class="row">
																												<div class="col-xs-12">
																																<div class="nav-tabs-custom">
																																				<!--tabs -->
																																				<ul class="nav nav-tabs" role="tablist">
																																								<li role="presentation" class="active"><a href="#tblherrPredictivo"
																																																aria-controls="profile" role="tab"
																																																data-toggle="tab">Herramientas</a></li>
																																								<li role="presentation"><a href="#insumPredic" aria-controls="messages"
																																																role="tab" data-toggle="tab">Insumos</a></li>
																																								<li role="presentation"><a href="#TabAdjuntoPred"
																																																aria-controls="messages" role="tab"
																																																data-toggle="tab">Adjunto</a></li>
																																				</ul>
																																				<!-- /tabs -->


																																				<!-- Tab panes -->
																																				<div class="tab-content">
																																								<div role="tabpanel" class="tab-pane active" id="tblherrPredictivo">

																																												<div class="row">
																																																<div class="col-xs-12">
																																																				<br>
																																																				<table class="table table-bordered" id="tblherrPred">
																																																								<thead>
																																																												<tr>
																																																																<th>Código</th>
																																																																<th>Marca</th>
																																																																<th>Descripcion</th>
																																																																<th>Cantidad</th>
																																																												</tr>
																																																								</thead>
																																																								<tbody></tbody>
																																																				</table>
																																																</div>
																																												</div><!-- /.row -->
																																								</div> <!-- /.tabpanel #herramin-->

																																								<div role="tabpanel" class="tab-pane" id="insumPredic">

																																												<div class="row">

																																												</div><!-- /.row -->
																																												<div class="row">
																																																<div class="col-xs-12">
																																																				<table class="table table-bordered" id="tblinsPredictivo">
																																																								<thead>
																																																												<tr>
																																																																<th>Código</th>
																																																																<th>Descripcion</th>
																																																																<th>Cantidad</th>
																																																												</tr>
																																																								</thead>
																																																								<tbody></tbody>
																																																				</table>
																																																</div>
																																												</div><!-- /.row -->
																																								</div>
																																								<!--/#insum -->

																																								<div role="tabpanel" class="tab-pane" id="TabAdjuntoPred">

																																												<div class="col-xs-12">
																																																<table class="table table-bordered" id="tblAdjuntoPred">
																																																				<thead>
																																																								<tr>
																																																												<th>Archivo/s</th>
																																																								</tr>
																																																				</thead>
																																																				<tbody>
																																																								<tr>
																																																												<td>
																																																																<a id="adjuntopred" href="" target="_blank"></a>
																																																												</td>
																																																								</tr>
																																																				</tbody>
																																																</table>
																																												</div>

																																								</div>
																																								<!--cierre de TabAdjunto-->

																																				</div> <!-- tab-content -->

																																</div><!-- /.nav-tabs-custom -->
																												</div>
																								</div>
																				</div>
																</div>
												</div>
												<!--  ./vista herramientas -->
								</div>

						</div>
						<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						</div>
				</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
function imprimir(e) {
    let idot = $(e).closest('tr').attr('id');
    //console.log("id Orden de trabajo: "+idot);

    WaitingOpen('Obteniendo datos de OT...');
    //buscar datos 
    $.ajax({
            data: {
                idot: idot
            },
            dataType: 'json',
            method: 'POST',
            url: 'index.php/Otrabajo/getOrigenOt',
        })
        .done((data) => {
            console.table(data);
            traerDatosImprimirOt(idot, data.tipo, data.id_solicitud);
        })
        .fail(() => alert("Error al traer los datos de la OT."))
        .always(() => WaitingClose());
};

function limpiar(){
    $("#fec_desde").val('');
    $("#fec_hasta").val('');
    $("#fec_hasta").attr('readonly', 'readonly');
    $("#estadoFilt").val('');
    $("#equipoFilt").val('');
}
//Filtra la tabla y la redibuja
//Cada campo esta validado en caso de vacios o NULL no se muestren en la tabla
function filtrar() {
      var data = new FormData($('#frm-filtros')[0]);
      data = formToObject(data);
      wo();
      var url = "<?php echo MAN; ?>Otrabajo/filtrarListado";
      $.ajax({
        type: 'POST',
        data: data,
        url: url,
        success: function(data) {
            WaitingClose();
            // $("#tbl_recepciones").removeAttr('style');
            var table = $('#otrabajo').DataTable();
            //var table = $('table#otrabajo').DataTable();
            table.rows().remove().draw();
            if (data != null && data != ' null') {
                var resp = JSON.parse(data);
                console.log(resp);

                for(var i=0; i<resp.length; i++){
                    var movimCabecera = resp[i];
                    var row = `<tr id="${resp[i].id_orden}" class="${resp[i].id_orden} ot-row" data-id_equipo="${resp[i].id_equipo}" data-causa="${resp[i].descripcion}" data-idsolicitud="${resp[i].id_solicitud}">`;
                    row += `<td>`;
                    row += `<?php echo $opciones; ?>`;
                    if(resp[i].ordenservicioId !=  null){
                        row += '<li role="presentation" id="cargOrden"><a onclick="ver_informe_servicio(this)" style="color:white;" role="menuitem" tabindex="-1" href="#" ><i class="fa fa-file-text text-white" style="color:white; cursor: pointer;margin-left:-1px"></i>Informe de Servicios</a></li>';
                        row += "</ul><div></td>";
                    }else{
                        row += "</ul><div></td>";
                    } 
                    //N° ORDEN
                    if(resp[i].id_orden){
                        row += `<td>${resp[i].id_orden}</td>`;
                    }else{
                        row += `<td></td>`;
                    }
                    //Fecha Programada
                    if(resp[i].fecha_program != '0000-00-00 00:00:00'){
                        var programada = resp[i].fecha_program.slice(0, 10);
                        Date.prototype.toDateInputValue = (function(){
                            var local = new Date(programada);
                            return local.toJSON().slice(0,10);
                        });
                        fecha = new Date().toDateInputValue();

                        row += `<td>`+ fecha +`</td>`;
                        // row += `<td>${resp[i].fecha_program}</td>`;
                    }else{
                        row += `<td></td>`;
                    }
                    //Fecha Inicio
                    if(resp[i].fecha_inicio != '0000-00-00 00:00:00'){
                        var inicio = resp[i].fecha_inicio.slice(0, 10);
                        Date.prototype.toDateInputValue = (function(){
                            var local = new Date(inicio);
                            return local.toJSON().slice(0,10);
                        });
                        fecha = new Date().toDateInputValue();

                        row += `<td>`+ fecha +`</td>`;
                    }else{ row += `<td></td>`}
                    //Fecha Terminada
                    if(resp[i].fecha_terminada != '0000-00-00 00:00:00'){
                        var terminada = resp[i].fecha_terminada.slice(0, 10);
                        Date.prototype.toDateInputValue = (function(){
                            var local = new Date(terminada);
                            return local.toJSON().slice(0,10);
                        });
                        fecha = new Date().toDateInputValue();

                        row += `<td>`+ fecha +`</td>`;
                    }else{ row += `<td></td>`
                    }
                    //Detalle
                    if(resp[i].descripcion){
                        row += `<td>${resp[i].descripcion}</td>`;
                    }else{ row += `<td></td>`
                    }
                    //Tarea Estandar
                    if(resp[i].tareaSTD){
                        row += `<td>${resp[i].tareaSTD}</td>`;
                    }else{ row += `<td></td>`
                    }
                    //Equipo
                    if(resp[i].codigo){
                        row += `<td>${resp[i].codigo}</td>`;
                    }else{ row += `<td></td>`;
                    }
                    //Origen
                    if(resp[i].tipoDescrip){
                        row += `<td>${resp[i].tipoDescrip}</td>`;
                    }else{ row += `<td></td>`;
                    }
                    //ID Solicitud
                    if(resp[i].id_solicitud){
                        row += `<td>${resp[i].id_solicitud}</td>`;
                    }else{ row += `<td></td>`;
                    }
                    //Asignado
                    if(resp[i].nombre){
                        row += `<td>${resp[i].nombre}</td>`;
                    }else{ row += `<td></td>`;
                    }
                    //Cliente
                    if(resp[i].nomCli){
                        row += `<td>${resp[i].nomCli}</td>`;
                    }else{ row += `<td></td>`;
                    }
                    //formateo de la fecha
                    // if(resp[i].fec_alta){
                    //     var fecha_alta = resp[i].fec_alta.slice(0, 10);
                    //     Date.prototype.toDateInputValue = (function(){
                    //         var local = new Date(fecha_alta);
                    //         return local.toJSON().slice(0,10);
                    //     });
                    //     fecha = new Date().toDateInputValue();

                    //     row += `<td>`+ fecha +`</td>`;
                    // }else{ row += `<td></td>`}
                    //Estado
                    if(resp[i].estado){
                        var span_estado = '';
                        switch (resp[i].estado) {
                            case 'S':
                                span_estado = estado('Solicitada', 'red');
                                break;
                            case 'PL':
                                span_estado = estado('Planificada', 'yellow');
                                break;
                            case 'AS':
                                span_estado = estado('Asignada', 'purple');
                                break;
                            case 'C':
                                span_estado = estado('Curso', 'green');
                                break;
                            case 'T':
                                span_estado = estado('Terminada', 'blue');
                                break;
                            case 'CE':
                                span_estado = estado('Cerrada', 'default');
                                break;
                            case 'CN':
                                span_estado = estado('Conforme', 'black');
                                break;
                            default:
                                break;
                        }
                        // var span_estado = estado(resp[i].estado);
                        row += `<td>`+span_estado+`</td>`;
                    }else{
                        row += `<td></td>`;
                    }
                    row += `</tr>`;
                    table.row.add($(row)).draw();
                    movimDetalle = "";
                }
            }
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        },
        error: function() {
          error("Error",'Ha ocurrido un error');
        },
        complete: function(result) {
          wc();
        }
      });
    }
    function estado($texto,$color,$detalle=null){
        return '<span data-toggle="tooltip" title="'+ $detalle +'" class="badge bg-'+$color+' estado">'+$texto+'</span>';
    }
    function habilitaFecHasta(){
        $('#fec_hasta').attr('readonly', false);
    }
</script>