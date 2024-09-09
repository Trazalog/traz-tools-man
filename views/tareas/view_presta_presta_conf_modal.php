<div class="modal-header">    
    <!-- <h4 class="modal-title">Informe de Servicios</h4> -->
</div>
<form class="form-horizontal" role="form" id="form_order" action="" accept-charset="utf-8">
    <input class="form-control" type="hidden" name="idTarBonita" id="idTarBonita" value="<?php echo $idTarBonita;?>" disabled/> 

    <!--  ORDEN SERVICIO  -->
    <div class="row">               
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
                    <input type="text" name="nomEquipo" class="form-control nomEquipo form_equipos" id="nomEquipo" value="<?php echo $equipos["nomb_equipo"];?>" disabled>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="descEquipo">Descripción</label>
                    <input type="text" name="descEquipo" class="form-control descEquipo form_equipos" id="descEquipo" value="<?php echo $equipos["desc_equipo"];?>" disabled>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" class="form-control estado form_equipos" id="estado" value="<?php echo $equipos["estado"];?>" disabled>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="sector">Sector</label>
                    <input type="text" name="sector" class="form-control sector form_equipos" id="sector" value="<?php echo $equipos["sector"];?>" disabled>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="grupo">Grupo</label>
                    <input type="text" name="grupo" class="form-control grupo form_equipos" id="grupo" value="<?php echo $equipos["grupo_desc"];?>" disabled>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <label for="ubicacion">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control ubicacion form_equipos" id="ubicacion" value="<?php echo $equipos["ubicacion"];?>" disabled>
                </div>
            </div>
        </div><!-- /.panel-body -->
    </div><!-- /.panel panel-default -->

    <!-- TABS -->
    <div class="panel-group collapse-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
            <a class="tarea"role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseZero" aria-expanded="false" aria-controls="collapseZero">
                Lecturas
            </a>
            </h4>
        </div>
        <div id="collapseZero" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
            <table id="modLectura" class="table table-bordered table-hover">
                <thead>
                <tr>                            
                    <th>Horómetro inicio</th>
                    <th>Horómetro fin</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>                        
                </tr>
                </thead>
                <tbody>
                <?php                  
                foreach($lecturas as $lect){
                    echo '<tr>';
                    echo '<td>'.$lect['horometroinicio'].'</td>';
                    echo '<td>'.$lect['horometrofin'].'</td>';
                    echo '<td>'.$lect['fechahorainicio'].'</td>';
                    echo '<td>'.$lect['fechahorafin'].'</td>';
                    echo '</tr>';
                }     
                ?>
                </tbody>
            </table> 
            </div>
        </div>
        </div>
        <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
            <a class="tarea"role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                Tareas
            </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
            <table id="modLectura" class="table table-bordered table-hover">
                <thead>
                <tr>                            
                    <th>Fecha inicio tarea</th>
                    <th>Fecha fin tarea</th>
                    <th>Duracion total</th>                           
                </tr>
                </thead>
                <tbody>
                <?php                  
                foreach($lecturasOT as $lect){
                    echo '<tr>';
                    echo '<td>'.$lect['fecha_inicio'].'</td>';
                    echo '<td>'.$lect['fecha_terminada'].'</td>';
                    $then = new DateTime($lect['fecha_inicio']);

                    $now = new DateTime($lect['fecha_terminada']);

                    $sinceThen = $then->diff($now);

                    echo '<td>Horas: '.$sinceThen->h.' / Minutos: '.$sinceThen->i.' / Segundos: '.$sinceThen->s.'</td>';
                    echo '</tr>';
                }     
                ?>
                </tbody>
            </table>
            <table id="modTarea" class="table table-bordered table-hover">
                <thead>
                <tr>                            
                    <th>Listado de tareas</th>
                </tr>
                </thead>
                <tbody>
                <?php                  
                foreach($tareas as $tar){
                    echo '<tr>';
                    echo '<td>'.$tar["id_tarea"].'</td>';                    
                    echo '</tr>';
                }     
                ?>
                </tbody>
            </table> 
            </div>
        </div>
        </div>
        <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
            <h4 class="panel-title">
            <a class="herramientas collapsed" id="herramientas" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Orden de Herramientas
            </a>
            </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="panel-body">
            <table id="modHerram" class="table table-bordered table-hover">
                <thead>
                <tr>                            
                    <th>Herramientas</th>             
                    <th>Marca</th>
                    <th>Código</th>     
                </tr>
                </thead>
                <tbody>
                <?php                  
                foreach($herramientas as $herr){
                    echo '<tr>';
                    echo '<td>'.$herr['herrdescrip'].'</td>';
                    echo '<td>'.$herr['herrmarca'].'</td>';
                    echo '<td>'.$herr['herrcodigo'].'</td>';                    
                    echo '</tr>';
                }                
                ?> 
                </tbody>
            </table>
            </div>
        </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                <a class=" insumos collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Orden de Insumos
                </a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                    <table id="modInsum" class="table table-bordered table-hover">
                        <thead>
                            <tr>                            
                            <th>Nº O.Insumo</th>
                            <th>Fecha</th>
                            <!-- <th>Solicitante</th> -->
                            <th>Código</th>  
                            <th>Descripción</th>           
                            <th>Cantidad</th> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php                  
                            foreach($insumos as $ins){
                                echo '<tr>';
                                echo '<td>'.$ins['pema_id'].'</td>';
                                echo '<td>'.$ins['fecha'].'</td>';
                                //echo '<td>'.$ins['fechahorainicio'].'</td>';
                                echo '<td>'.$ins['barcode'].'</td>';
                                echo '<td>'.$ins['descripcion'].'</td>';
                                echo '<td>'.$ins['cantidad'].'</td>';
                                echo '</tr>';
                            }     
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- / .panel panel-default -->
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingFour">
                <h4 class="panel-title">
                <a class=" insumos collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Recursos Humanos
                </a>
                </h4>
            </div>
            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                <div class="panel-body">

                <h5 style="font-weight: bold;">Responsable Asignado: <spam style="font-weight: lighter;"><?php echo $nom_responsable; ?></spam></h5>     

                <table id="modRecurso" class="table table-bordered table-hover">
                    <thead>
                        <tr>                            
                        <th>Apellido</th> 
                        <th>Nombre</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?php                                 
                        foreach($rrhh as $rh){
                            echo '<tr>';
                            echo '<td>'.$rh['usrLastName'].'</td>';
                            echo '<td>'.$rh['usrName'].'</td>';                      
                            echo '</tr>';
                        }     
                        ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div><!-- / .panel panel-default -->
    </div> <!-- / .panel-group -->
</form>
<div class="pull-right">
    <!-- <button type="button" class="botones btn btn-primary" onclick="enviarOrden()">Guardar</button>  -->
    <button type="button" id="cerrar" class="btn btn-primary" onclick="cerrarModal()">Cerrar</button>
</div>
<script>
    function cerrarModal(){
        $('#modalInforme').modal('hide');
    }  

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
        
        $('#tablalistareas, #tablalistherram').DataTable({
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
  
</script>
