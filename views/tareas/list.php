<style>
    .badge-orange {
        background-color: orange;
        color: white;
    }

    .my-processing-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.7);
        /* Fondo blanco semitransparente */
        z-index: 9999;
    }

    /* Contenedor del mensaje */
    .my-processing-content {
        background-color: #3c8dbc;
        /* Color de fondo azul  */
        color: white;
        padding: 20px 30px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        font-size: 16px;
        line-height: 1.6;
    }

    /* Estilo del spinner  */
    .my-processing-content i {
        margin-bottom: 10px;
        display: block;
        font-size: 24px;
    }
</style>

<input type="hidden" id="permission" value="<?php echo $permission; ?>">
<input type="hidden" id="tiempoRecarga" value="<?php echo $tiempoRecarga; ?>">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h1 class="box-title">Mis Tareas</h1>
                    <a class="btn btn-primary" style="margin-left: 5%" title="Actualizar" id="btnActualizar"><i
                            class="glyphicon glyphicon-refresh"></i></a>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="datagrid">
                        <table id="bandeja" class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <?php
                                    echo '<th width="10%">Tip. Tarea</td>';

                                    echo '<th width="7%"' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . ' >Estado</th>';

                                    echo '<th ' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . ' >Asignado</th>';

                                    echo '<th ' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . ' >Fecha Asignación</th>';

                                    /*echo '<th '.($device == 'android' ? 'class= "hidden"' :'class= ""').' >Hora Asignación</td>';*/

                                    echo '<th ' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . ' >Equipo</td>';

                                    echo '<th ' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . '  >Sector</td>';

                                    echo '<th ' . ($device == 'android' ? 'class= "hidden"' : 'class= ""') . ' >Cliente</td>';

                                    echo '<th>Tarea</th>';

                                    echo '<th class="' . ($device == 'android' ? 'hidden' : null) . '">Descripción</th>';

                                    echo '<th width="7%">Id S.S</td>';

                                    echo '<th width="7%">Id OT</td>';

                                    echo '<th width="10%">Id Pedido</td>';

                                    // echo '<th '.($device == 'android' ? 'class= "hidden"' :'class= ""').' >Prioridad</td>';
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Redefinicion body de tabla en script de datatable abajo -->

                            </tbody>
                        </table>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->

<div class="modal fade" id="finalizar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="glyphicon glyphicon-check btncolor " style="color: #6aa61b"> </span> Finalización </h4>
            </div>
            <center>
                <div>Debe completar el formulario asociado a esta tarea para terminarla</div>
            </center>

        </div>
    </div>
</div>

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

    //variable de control para saber si ya estoy updateando la tabla(evita multiples llamados al controlador cuando ya hay uno en curso) 
    var isUpdating;
    //variable de control para la recarga cuando cambio de pagina o busco
    var cambioPagina;

    /*-----------inicio datatable--------*/
    function initDataTable() {
        //debugger;
        myTable = $('#bandeja').DataTable({
            'initComplete': function (settings, json) {
                //console.log('tabla completada');

                //bandera para activar la actualizacion de la tabla
                isUpdating = false;
                /* funcion que crea intervalo de actualizar tabla */
                startUpdateInterval();

            },
            'ordering': true,
            'searchDelay': 3000,
            'lengthMenu': [[10, 25, 50, 100,], [10, 25, 50, 100]],
            'paging': true,
            'pagingType': 'simple',
            'processing': true,
            'language': {
                'processing': '<div class="my-processing-overlay"><div class="my-processing-content"><i class="fa fa-spinner fa-spin fa-2x"></i><div>Procesando...<br>Por favor, espere</div></div></div>'
            },
            'serverSide': true,
            'order': [[9, 'desc']],
            'search': true,
            'ajax': {
                type: 'POST',
                url: '<?php echo base_url(MAN); ?>Tarea/paginado'
            },
            'columnDefs': [
                {
                    //Agregado para que funcione cabecera de imprimir,descargar excel o pdf.   
                    "defaultContent": "-",
                    "targets": "_all",
                },
                {
                    'targets': [0],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda nomTarea text-center";
                        $(td).attr('class', clase);
                    },
                    //visible: false,
                    'data': 'Tipo',
                    'render': function (data, type, row) {

                        var color = '';
                        var valor = '';
                        var detalle = '';
                        switch (row['tip_ta']) {
                            case '1':
                                color = '#3c8dbc'; //Orden Trabajo (celeste)
                                valor = 'OT';
                                detalle = 'Orden Trabajo';
                                break;
                            case '2':
                                color = '#f56954'; //Correctivo (rojo)
                                valor = 'CR';
                                detalle = 'Correctivo';
                                break;
                            case '3':
                                color = '#39CCCC'; //Preventivo (turquesa)
                                valor = 'PV';
                                detalle = 'Preventivo';
                                break;
                            case '4':
                                color = '#ff851b'; //Backlog (naranja)
                                valor = 'BK';
                                detalle = 'Backlog';
                                break;
                            case '5':
                                color = '#00a65a'; //Predictivo (verde)
                                valor = 'PD';
                                detalle = 'Predictivo';
                                break;
                            case '6':
                                color = '#D81B60'; //Correctivo Programado (fucsia)
                                valor = 'UR';
                                detalle = 'Urgente';
                                break;
                        };
                        //$('td', data).css('background-color', color);                
                        //$(row).find('td:eq(1)').css('background-color', color);                 
                        return `<td>${(row['tip_ta']) ? bolitaEstado(valor, color, detalle) : ' '}</td></tr>`;
                    }
                },
                {
                    'targets': [1],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var asig = row['assigned_id'];
                        if (asig != "") {
                            clase = (device == 'android' ? "celda nomTarea hidden" : "celda nomTarea text-center");

                        }
                        else {
                            clase = (device == 'android' ? "celda nomTarea hidden" : "celda nomTarea text-center");
                        }
                        $(td).attr('class', clase);
                    },
                    'data': 'Estado',
                    'render': function (data, type, row) {
                        /*$('.dataTables_filter input').keyup(function() {
                            msjBusqueda(function(){
                        }, 1000 );
                        });*/
                        //WaitingClose(); 
                        var id = row['id'];
                        var asig = row['assigned_id'];
                        var processId = row['processId'];
                        var device = "<?php echo $device ?>";
                        r = '<tr>';
                        if (asig != "") {
                            r = `<td> <i class="fa fa-user" style="color: #5c99bc ; cursor: pointer;"" title="Asignado" data-toggle="modal" data-target="#modalSale"></i></td>`
                        }
                        else {
                            r = `<td> <i class="fa fa-user" style="color: #d6d9db ; cursor: pointer;"" title="Sin Asignar" data-toggle="modal" data-target="#modalSale"></i></td>`
                        }
                        return r;
                    }
                },
                {
                    'targets': [2],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = (device == 'android' ? "celda nomTarea tddate" : "celda nomTarea tddate");
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Asignado',
                    'render': function (data, type, row) {
                        return `<td>${row['usr_asignado']}</td>`;
                    }
                },
                {
                    'targets': [3],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = (device == 'android' ? "celda nomTarea tddate" : "celda nomTarea tddate");
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Fecha Asignación',
                    'render': function (data, type, row) {
                        return `<td>${row['assigned_date']}</td>`;
                    }
                },
                {
                    'targets': [4],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = (device == 'android' ? "celda nomTarea tddate" : "celda nomTarea tddate");
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Equipo',
                    'render': function (data, type, row) {
                        return `<td>${(row['equipoDesc']) ? row['equipoDesc'] : ' '}</td>`;
                    }
                },
                {
                    'targets': [5],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = (device == 'android' ? "celda nomTarea tddate" : "celda nomTarea tddate");
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Sector',
                    'render': function (data, type, row) {
                        device = "<?php echo $device ?>";
                        return `<td>${(row['sectorDesc']) ? row['sectorDesc'] : ' '}</td>`;
                    }
                },
                {
                    'targets': [6],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = (device == 'android' ? "celda nomTarea tddate" : "celda nomTarea tddate");
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Cliente',
                    'render': function (data, type, row) {
                        return `<td>${(row['nomCli']) ? row['nomCli'] : ' '}</td>`;
                    }
                },
                {
                    'targets': [7],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda nomTarea tddate";
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Tarea',
                    'render': function (data, type, row) {
                        return `<td>${row['displayName']}</td>`;
                    }
                },
                {
                    'targets': [8],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda tareaDesc " + (device == 'android' ? 'hidden' : null);
                        $(td).attr('class', clase);
                        $(td).attr('style', 'text-align: left');
                    },
                    'data': 'Descripcion',
                    'render': function (data, type, row) {
                        var device = "<?php echo $device ?>";
                        return `<td>${(row['displayDescription']) ? row['displayDescription'] : ' '}</td>`;
                    }
                },
                {
                    'targets': [9],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda nomTarea text-center";
                        $(td).attr('class', clase);
                    },
                    'data': 'Id S.S',
                    'render': function (data, type, row) {
                        return `<td>${(row['ss']) ? bolita(row['ss'], 'blue') : ' '}</td>`;
                    }
                },
                {
                    'targets': [10],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda nomTarea text-center";
                        $(td).attr('class', clase);
                    },
                    'data': 'Id OT',
                    'render': function (data, type, row) {
                        return `<td>${(row['ot']) ? bolitaButton(row['ot'], 'orange', row['detalle']) : ' '}</td>`;
                    }
                },
                {
                    'targets': [11],
                    'createdCell': function (td, cellData, rowData, row, col) {
                        var device = "<?php echo $device ?>";
                        var clase = "celda nomTarea text-center";
                        $(td).attr('class', clase);
                    },
                    'data': 'Id Pedido',
                    'render': function (data, type, row) {
                        return `<td>${(row['pema_id']) ? bolita(row['pema_id'], 'green') : ' '}</td></tr>`;
                    }
                },
            ],
            //agregado de data-json al tr de la tabla
            createdRow: function (row, data, dataIndex) {
                var id = data['id'];
                var processId = data['processId'];
                var proc = tagProceso(processId);
                $(row).attr('id', id);
                $(row).attr('class', id);
                $(row).attr('style', 'cursor:pointer');
                $(row).attr('tags', (proc) ? proc : '');
                $(row).attr('onclick', 'detalleTarea(this)');


            }
        });

    }
    /*-----------fin datatable--------*/


    /* -----------actualiza la data de las tareas -------- */
    async function actualizaData() {
        isUpdating = true;
        let dataUpdate = new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                data: {},
                cache: false,
                url: '<?php echo base_url(MAN); ?>Tarea/actualizaTareas',
                success: function (data) {
                    resolve("Se updateo data correctamente");
                },
                error: function (data) {
                    reject("Error al updatear datos");
                }
            });
        });

        return await dataUpdate;
    }


    /* -----------actualiza cada cierto tiempo la tabla -------- */
    function startUpdateInterval() {
        intervalID = setInterval(function () {
            //console.log('creando intervalo');
            //debugger;
            if (!isUpdating) {
                actualizaData()
                    .then((result) => {
                        console.log(result);

                        if (myTable && myTable.settings()[0].oInit.initComplete) {
                            //si cambie de pagina o estoy buscando no borro la tabla
                            if (!cambioPagina) {
                                myTable.clear();
                                myTable.destroy();
                                clearInterval(intervalID); //se recargaba infinitamente en test 
                                initDataTable();
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
            }
            else {
                //elimino intervalo para no generar reiteradas llamadas
                clearInterval(intervalID);
                //console.log('intervalo delete');
            }
        }, $('#tiempoRecarga').val());

    }

    $(document).ready(function () {
        isUpdating = false;
        cambioPagina = false;

        /* inicia datatable */
        initDataTable();

        /* control de cambio de pagina en tabla*/
        myTable.on('page.dt', function () {
            //console.log('El usuario cambió de página');
            cambioPagina = true;
            clearInterval(intervalID);
        });

        /* control de busqueda en tabla */
        $('body').on('click', '#bandeja_filter input', function () {
            //console.log('El usuario hizo clic en el input de búsqueda');
            cambioPagina = true;
            clearInterval(intervalID);
        });

    });

    // Asignar la función btnActualizar al evento click del botón, reinicia la recarga
    $('#btnActualizar').on('click', function () {
        btnActualizar();
    });

    //actualiza la tabla de tareas y rearma el datatable
    function btnActualizar() {
        clearInterval(intervalID);
        cambioPagina = false;
        WaitingOpen();
        $.ajax({
            type: 'GET',
            data: {},
            url: '<?php echo base_url(MAN); ?>Tarea/actualizaTareas',
            success: function (data) {
                myTable.clear();
                myTable.destroy();
                WaitingClose();
                initDataTable();
            },
            error: function (result) {
                console.log(result);
            },
        });

    }


    var idfin = "";
    var id_tarea = "";
    var nomTarea = "";
    var tareaDesc = "";
    var fechaCreacion = "";

    actualizar_terminadas();

    function actualizar_terminadas() {
        var aux = sessionStorage.getItem("tareas_cerradas");
        if (aux == null || aux.length == 0) return;
        var aux = aux.split("-");
        for (var i = 0; i < aux.length - 1; i++) {
            $("#" + aux[i]).remove();
        }
    }


    //Tomo valor de la celda y carga detalle de la tarea
    function detalleTarea(e) {
        //quedaba pegado tooltip
        $('.ui-tooltip').css('display', 'none');
        isUpdating = true;

        var id = $(e).attr('id');
        WaitingOpen();
        if (!$(e).attr('tags').includes('#pedidoMaterial')) {
            linkTo("<?php echo base_url(MAN); ?>Tarea/detaTarea/<?php echo $permission; ?>/" + id);
        } else {
            linkTo('<?php echo base_url(ALM); ?>Proceso/detalleTarea/' + id);
        }
        WaitingClose();

    };


    // Carga para cargar notif estandar
    function verTarea(idTarBonita) {

        WaitingOpen();
        linkTo("<?php echo base_url(MAN); ?>Tarea/detaTarea/<?php echo $permission; ?>/" + idTarBonita);
        WaitingClose();

    }


    /////////// TERMINAR TAREA   ///////

    // boton terminar tarea
    $('.btnFin').click(function () {
        var idTarBonita = $(this).parents('tr').find('td').eq(8).html();
    });
    //PROBANDO TRAER COORDENADAS GPS
    $("#pruebagps").click(function (e) {
        var xlat = null;
        var xlon = null;
        // if (!window.mobileAndTabletcheck()) {
        if (true) {
            if (obtenerPosicion()) {
                console.log('LAT: ' + lat + ' - LON: ' + lon + ' - ACC: ' + ac);
                xlat = lat;
                xlon = lon;
            }
            else {
                alert('GPS | No se pudo Obtener Ubicación, Por favor Activar el GPS del Dispositivo.');
                return;
            }
        } else {
            console.log('GPS | No Mobile');
        }
        console.table("latitud y long antes de llamar a ajax");
        console.table(xlat);
        console.table(xlon);
    });
    //FIN PREUBA TRAER CORDENADAS GPS

    // Recargar vista
    function recargar() {
        WaitingOpen();
        $(".content").load("<?php echo base_url(MAN); ?>Tarea/index/<?php echo $permission; ?>/");
        WaitingClose();
    }

    if (!conexion()) offline();
    else {
        sessionStorage.removeItem('tareas_cerradas');
    }

    function offline() {
        console.log('Pagina Offline | Cargando Contenido...');

        //Checks Subtareas
        var aux = JSON.parse(sessionStorage.getItem('tareas_cerradas'));
        if (aux != null) {
            Object.keys(aux).forEach(function (key) {
                $('#' + key).remove();
            });
        }
    }
    //DataTable('#bandeja',true, false);
    function bolita(texto, color, detalle = null) {
        return `<span data-toggle='tooltip' title='${detalle}' class='badge bg-${color} estado'>${texto}</span>`;
    }

    function bolitaButton(texto, color, detalle = null) {
        return `
        <span data-toggle="tooltip" title="${texto}" class="badge badge-${color} estado" onclick="verOt(event, ${texto})" style="cursor: pointer;">
            ${texto}
        </span>`;
    }

    function bolitaEstado(texto, color, detalle = null) {
        return `<span data-toggle='tooltip' title='${detalle}' style='font-weight: bold; color: #FFFFFF; display: inline-block;  height: 10%;  width: 30%;  background-color: ${color};  border-radius: 45px;'>${texto}</span>`;
    }

    function tagProceso($id) {

        if ($id == '<?php echo BPM_PROCESS_ID_PEDIDOS_NORMALES ?>') return '#pedidoMaterial';

        if ($id == '<?php echo BPM_PROCESS_ID_PEDIDOS_EXTRAORDINARIOS ?>') return '#pedidoMaterial#extraordinario';

    }

    function msjBusqueda() {
        /*   $('#waitingText').css('color','black');*/
        //$('.overlay>.fa').css('top','20%');
        //$('.overlay').css('background','rgb(247 247 247 / 40%)');  
        /* SIM  WaitingOpen('El volumen de datos es muy grande, espere mientras se procesa la búsqueda. Gracias por su paciencia'); */
        WaitingOpen('Cargando...');

    }

    function verOt(event, idOt) {
        event.stopPropagation(); // Detén la propagación del evento a los elementos padres (como la fila)
        event.preventDefault();  // Previene la acción predeterminada del evento (como la navegación)
        WaitingOpen('Cargando datos...');
        //busca datos de ot
        getDataOt(idOt);

        // Abrir el modal
        $('#verOt').modal('show');
        WaitingClose();
    }

    // Trae datos de OT 
    function getDataOt(idOt) {

        var datos = null;
        $.ajax({
            async: false,
            data: {
                idOt: idOt
            },
            dataType: 'json',
            method: 'POST',
            url: '<?php echo base_url(MAN); ?>Otrabajo/getViewDataOt',
        })
            .done((data) => {
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

                    'asignado': data['otrabajo'][0]['usrLastName'] + ' ' + data['otrabajo'][0]['usrLastName'],
                    'estado': data['otrabajo'][0]['estado'],
                    //Panel datos de equipos
                    'codigo': data['otrabajo'][0]['codigo'],
                    'marca': data['otrabajo'][0]['marca'],
                    'ubicacion': data['otrabajo'][0]['ubicacion'],
                    'descripcion_eq': data['otrabajo'][0]['descripcionEquipo'],
                    'nomCli': data['otrabajo'][0]['nomCli'],
                    'comp_equipo': data['otrabajo'][0]['compEquipo'],
                };
                $('#vNroOt').val(datos['nro']);
                $('#vDescripFalla').val(datos['descripcion_ot']);

                $('#vFechaProgram').val(datos['fecha_program']);
                $('#vFechaCreacion').val(datos['fecha_inicio']);
                $('#vFechaTerminOT').val(datos['fecha_terminada']);

                $('#vSucursal').val(datos['sucursal']);
                $('#vProveedor').val(datos['nombreprov']);
                $('#vIdOt').val(datos['id_ot']);
                $('#vOrigen').val('Orden de Trabajo');

                if (datos['asignado'] != 'null null') {
                    $('#vAsignado').val(datos['asignado']);
                } else {
                    $('#vAsignado').val('Sin Asignar');
                }

                var estadoPred = getEstadosVer(datos['estado']);
                $('#vEstado').val(estadoPred);
                // datos de equipo
                $('#vCodigoEquipo').val(datos['codigo']);
                $('#vMarcaEquipo').val(datos['marca']);
                $('#vUbicacionEquipo').val(datos['ubicacion']);
                $('#vDescripcionEquipo').val(datos['descripcion_eq']);

                //datos de herramientas
                var herram = data['herramientas'];
                var insum = data['insumos'];
                var adjunto = data['adjunto']
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
                    var tr = "<tr id='" + insum[i]['artId'] + "'>"
                    "<td>" + insum[i]['artBarCode'] + "</td>" +
                        "<td>" + insum[i]['artDescription'] + "</td>" +
                        "<td>" + insum[i]['cantidad'] + "</td>" +
                        "</tr>";
                    $('#tblinsOT tbody').append(tr);
                }
                recargaTablaAdjuntoOT(adjunto);
            })
            .fail(() => alert("Error al traer los datos de la OT."));
        return datos;
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


    // recarga tablas de adjuntos al iniciar la edicion
    function recargaTablaAdjuntoOT(Adjunto) {
        $('#TabAdjuntoOT tbody tr').remove();
        if (Adjunto == 0) {
            $('#TabAdjuntoOT').html('<p>Sin Adjuntos</p>');
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

</script>