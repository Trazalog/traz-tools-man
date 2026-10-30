<input type="hidden" id="permission" value="<?php echo $permission;?>">
<?php //dump();?>
<section class="content">
	<?php echo $cabecera; ?>
	<input type="hidden" class="form-control " id="id_OT" value="<?php echo $id_OT ?>">
	<input type="hidden" class="form-control " id="id_SS" value="<?php echo $id_SS ?>">
	<input type="hidden" class="form-control " id="idTarBonita" value="<?php echo $idTarBonita ?>">
	<input type="hidden" class="form-control " id="id_EQ" value="<?php echo $id_EQ ?>">

	<div class="row">
		<div class="col-xs-12">
			<div class="box">

				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 col-md-12">
							<div role="tabpanel" class="tab-pane">
								<div class="form-group">

									<!-- Nav tabs -->
									<ul class="nav nav-tabs" role="tablist">
										<li role="presentation" class="active"><a href="#home" aria-controls="home"
												role="tab" data-toggle="tab">Tareas</a></li>
										<li role="presentation"><a href="#profile" aria-controls="profile" role="tab"
												data-toggle="tab">Comentarios</a></li>
										<li
											<?php echo ($device == 'android' ? 'class= "hidden"' :'class= ""') ?>role="presentation">
											<a href="#messages" aria-controls="messages" role="tab"
												data-toggle="tab">Vista
												Global
											</a></li>
									</ul>

									<!-- Tab panes -->
									<div class="tab-content">

										<div role="tabpanel" class="tab-pane active" id="home">
											<!-- <h4 class="panel-heading">Tarea</h4> -->

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

												<input type="text" class="form-control hidden" id="asignado"
													value="<?php echo $TareaBPM["assigned_id"] ?>">
												<form>
													<div class="panel panel-default">
														<!-- <h4 class="panel-heading">INFORMACION:</h4> -->
														<div class="panel-heading">INFORMACION:</div>

														<div class="form-group">
															<div class="col-sm-6 col-md-6">
																<label for="tarea">Tarea</label>
																<input type="text" class="form-control" id="tarea"
																	value="<?php echo $TareaBPM['displayName'] ?>"
																	disabled><!-- id de listarea -->
																<input type="text" class="hidden" id="tbl_listarea"
																	value="<?php echo $datos[0]['id_listarea'] ?>">
																<input type="text" class="hidden" id="idform"
																	value="<?php echo $idForm ?>">
																<!-- id de task en bonita -->
																<input type="text" class="hidden" id="idTarBonita"
																	value="<?php echo $TareaBPM['id'] ?>">
																<input type="text" class="hidden" id="esTareaStd"
																	value="<?php echo $infoTarea['visible'] ?>">
																<input type="text" class="hidden" id="case_id"
																	value="<?php echo $TareaBPM['caseId'] ?>">
															</div>
														</div>


														<div class="form-group">
															<div class="col-sm-6 col-md-6">
																<label for="fecha">Fecha de Creación</label>
																<input type="text" class="form-control" id="fecha"
																	placeholder=""
																	value="<?php echo $TareaBPM['last_update_date'] ?>"
																	disabled>
															</div>
														</div><br>


														<div class="form-group ">
															<div class="col-sm-6 col-md-6 ">
																<label for="ot ">Orden de Trabajo:</label>
																<input type="text " class="form-control " id="ot"
																	placeholder=" "
																	value="<?php echo $datos[0][ 'id_orden'] ?>"
																	disabled>
															</div>
														</div><br>

														<div class="form-group">
															<div class="col-sm-6 col-md-6">
																<label for="duracion_std">Duracion Estandar
																	(minutos):</label>
																<input type="text" class="form-control"
																	id="duracion_std" placeholder=""
																	value="<?php echo $datos[0]['duracion_std']  ?>"
																	disabled>
															</div></br>
														</div>

														<br>

														<div class="form-group">
															<div class="col-sm-12 col-md-12">
																<label for="detalle">Detalle</label>
																<textarea class="form-control" id="detalle" rows="3"
																	disabled><?php echo $TareaBPM['displayDescription']?></textarea>
															</div>
														</div></br> </br> </br> </br> </br>


														<div class="form-group">
															<div class="col-sm-12 col-md-12">
																<!-- Modal formulario tarea -->
																<?php if($idForm != 0){echo '<button type="button" id="formulario" class="btn btn-primary" data-toggle="modal"data-target=".bs-example-modal-lg" onclick="getformulario()">Completar Formulario </button>';}?>
															</div>
														</div>

												</form>

											</div>
										</div>
										<!-- prioridad de la SServicios -->
										<div class="form-group" id="decisionSolicitud">
											<div class="radioBtn col-sm-12 col-md-12">
												<center>
													<label class="control-label">¿El Informe de Servicios es correcto?
													</label>
													</br>
													<div class="col-md-12">
														<label class="radio-inline" for="radios-0">
															<input type="radio" name="opcion" id="radios-0" value="true"
																checked="checked"> Si
														</label>
														<label class="radio-inline" for="radios-1">
															<input type="radio" name="opcion" id="radios-1"
																value="false"> No
														</label>
													</div>
													</br></br>
													<div class="form-group" id="justgroup" hidden>
															<div class="col-sm-12 col-md-12">
																<label for="justificacion">Justificacion</label>
																<textarea class="form-control" id="justificacion" rows="3" name="justificacion"></textarea>
															</div>
													</div>
												</center>
											</div>
										</div>
									</div>

									<div role="tabpanel" class="tab-pane" id="profile">
										<div class="panel-body">
											<!-- COMENTARIOS -->
											<?php echo $comentarios ?>
										</div>
									</div>

									<div role="tabpanel"
										<?php echo ($device == 'android' ? 'class= "hidden"' :'class= "tab-pane"') ?>
										id="messages">
										<!-- <div role="tabpanel" class="tab-pane" id="messages" > -->

										<div class="panel-body">
											<?php echo $lineaTiempo; ?>
										</div>

									</div>

								</div>
							</div>
						</div>
					</div>

				</div><!-- /.row -->

				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="hecho" onclick="ver_informe_servicio()"> Ver Inf. Servicio</button>
					<!-- <button type="button" id="cerrar" class="btn btn-primary" onclick="cargarVista()">Cerrar</button> -->
					<button type="button" id="cerrar" class="btn btn-primary" onclick="cargarVistaTarea()">Cerrar</button>
					<button type="button" class="btn btn-success" id="hecho" onclick="verificarInforme()">Hecho</button>
				</div> <!-- /.modal footer -->

			</div><!-- /.box body -->
		</div> <!-- /.box  -->
	</div><!-- /.col -->
	</div><!-- /.row -->
</section><!-- /.content -->


<script>

	$('#cerrar').click(function cargarVistaTarea() {
		WaitingOpen();
		$('#content').empty();
		linkTo("<?php echo base_url(MAN); ?>Tarea/index/<?php echo $permission; ?>");
		WaitingClose();
	});

	function ver_informe_servicio (o){ 
	
		var id_OT =	$('#id_OT').val();	
		var id_eq  = $('#id_EQ').val();	
		var id_solicitud = $('#id_SS').val();
		var idTarBonita = $('#idTarBonita').val();   
		 
		WaitingOpen();
		$('#modalInforme').modal('show');
		$('#modalInformeServicios').empty();
		$("#modalInformeServicios").load("<?php echo base_url(MAN); ?>Ordenservicio/verInforme/"+id_OT+"/"+id_eq+"/"+id_solicitud+"/"+idTarBonita+"/");
		WaitingClose();
	}


	$("#radios-1").click(function() {
  		$("#justgroup").show();
	});
	$("#radios-0").click(function() {
  		$("#justgroup").hide();
	});
//	Scripts para acordeon del modal
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

//	./ Scripts para acordeon del modal






</script>



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

