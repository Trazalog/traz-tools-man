<style>
	.frm-save {
		display: none;
	}
</style>
<input type="hidden" id="permission" value="<?php echo $permission;?>">
<!-- <div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error1" style="display: none">
            <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
            Este equipo! Si tiene datos tecnicos cargados
        </div>
    </div>
</div> -->
<div class="box box-primary">
    <div class="box-header with-border">
        <h4 class="box-title">Equipos</h4>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
            <?php
                if (strpos($permission,'Add') !== false) {
                    echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="btnAgre">Agregar</button>';
                }
            ?>
            </div>
        </div>
        <div class="box-body table-scroll table-responsive">
            <table id="sales" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>Equipo</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Proceso</th>
                        <th>Sector</th>
                        <th>Criticidad</th>
                        <th>Cliente</th>
                        <th>Estado</th>                    
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box-body -->
</div><!-- /.box box-primary-->
<div id="form-dinamico" class="frm-open" data-readonly='true' href='#' data-info="38"></div> 

<script>
$(document).ready(function(){
    $(".datepicker").datepicker();

    //Ambas funciones evitan el error de que se quiera acceder a un elemento con aria-hiden=true
    $('#modalasignar').on('show.bs.modal', function () {
        $(this).removeAttr('inert');
    });

    $('#modalasignar').on('hidden.bs.modal', function () {
        $(this).attr('inert', '');
    });
    $('#sales').DataTable({
        'lengthMenu':[[10,25,50,100,],[10,25,50,100]],
        'paging' : true,
        'processing':true,
        'serverSide': true,
        'order': [[1, 'asc']],
        'search': true,
        'ajax':{
            type: 'POST',
            url: '<?php echo MAN; ?>Equipo/paginado'
        },
        'columnDefs':[
            {
                'targets':[0],
                "searchable": false,
                'data':'acciones',
                'render':function(data,type,row){
                    var id = row['id_equipo'];
                    meta_disp = row['meta_disp'];
                    if(meta_disp == null){
                        var meta_disp = 0;
                    }
                    var permission = "<?php echo $permission?>";
                    var r = `<tr id="${id}" data-equipo="${id}" data-meta="${meta_disp}">`;
                    r = r + `<td>`;
                    if (permission.indexOf("Del") !== -1) {
                        r = r + `<i href="#" class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar" onclick="eliminarEquipo(${id})"></i>`;
                    }
                    if (permission.indexOf("Edit") !== -1) {
                        r = r + `<i class="fa fa-fw fa-pencil text-light-blue editEquipo" style="cursor: pointer; margin-left: 15px;" title="Editar" onclick="editarEquipo(${id})"></i>`;
                    }
                    if (permission.indexOf("Del") !== -1) {
                        r = r + `<i class="fa fa-fw fa-user text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Contratista" data-toggle="modal" data-target="#modalasignar" onclick="asignarContratista(${id})"></i>`;
                        //antes estaba el estado R por que ERA REPARACION pero ahora reparacion es RE
                        if( (row['estadoEquipo'] == 'AC') || (row['estadoEquipo'] == 'RE') ){
                        r = r + `<i  href="#"class="fa fa-fw fa-toggle-on text-light-blue " style="cursor: pointer; margin-left: 15px;" title="Inhabilitar" onclick="inhabilitarEquipo(${id})"></i>`;
                        }
                        else {
                        r = r + `<i class="fa fa-fw fa-toggle-off text-light-blue" title="Habilitar" style="cursor: pointer; margin-left: 15px;" onclick="habilitarEquipo(${id})"></i>`;
                        }
                    }
                    if (permission.indexOf("Lectura") !== -1) {
                        if( row['estadoEquipo'] == 'AC' || row['estadoEquipo'] == 'RE' ) {
                            let deeq = row['deeq'];
                            let form_id =  row['form_id'];
                            r = r + `<i class="fa fa-hourglass-half text-light-blue nuevaLectura" style="cursor: pointer; margin-left: 15px;" title="Mantenimiento Autónomo" onclick="mantenimientoAutonomo(${id},'${deeq}' ,'${form_id}')"></i>`;
                        }
                        r = r + `<i class="fa fa-history text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Historial de Lecturas" data-toggle="modal" data-target="#modalhistlect" onclick="historialLectura(${id})"></i>`;
                    }
                    return r = r + `<button class="btn-link" onclick="asignar_meta(${meta_disp},${id})"><i class="fa fa-bar-chart text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Asignar Meta"></i></button>
                    </td>`;
                }
            }
            ,
            {
                'targets':[1],
                'data':'codigo',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['codigo']}</td>`
                }
            },
            {
                'targets':[2],
                'data':'descripcion',
                'render': function(data, type, row){
                    return `<td> ${row['deeq']} </td>`
                }
            },
            {
                'targets':[3],
                'data':'area',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['dear']}</td>`
                }
            },
            {
                'targets':[4],
                'data':'proceso',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['depro']}</td>`
                }
            },
            {
                'targets':[5],
                'data':'sector',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['desec']}</td>`
                }
            },
            {
                'targets':[6],
                'data':'criticidad',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['decri']}</td>`
                }
            },
            {
                'targets':[7],
                'data':'cliente',
                'render': function(data, type, row){
                    return `<td class="maquin">${row['clie']}</td>`
                }
            },
            {
                'targets':[8],
                'data':'estado',
                "searchable": false,
                'render': function(data, type, row){
                    switch (row['estadoEquipo']) {
                        case 'AC':
                            return '<td class="maquin"><span data-toggle="tooltip" title="" class="badge bg-green estado">Activo</span></td></tr>';
                            break;
                        case 'RE':
                            return '<td class="maquin"><span data-toggle="tooltip" title="" class="badge bg-yellow estado">Reparación</span></td></tr>';
                            break;
                        case 'AL':
                            return '<td class="maquin"><span data-toggle="tooltip" title="" class="badge bg-blue estado">Alta</span></td></tr>';
                            break;
                        case 'IN':
                            return '<td class="maquin"><span data-toggle="tooltip" title="" class="badge bg-red estado">Inhabilitado</span></td></tr>';
                            break;
                        default:
                            return '<td class="maquin"><span data-toggle="tooltip" title="" class="badge bg-gray estado">S/E</span></td></tr>';
                            break;
                    }
                }
            }
        ]
    });
});

$('#modalhistlect').on('shown.bs.modal', function(e) {
    // recalcula el ancho de las columnas
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
})


var tr = "";
var isOpenWindow = false;
var idEquipo = "";
var ide = "";
var idglob = "";



// Carga vista para agregar equipo nuevo - Chequeado
edit = 0;
datos = Array()
$('#btnAgre').click(function cargarVista() {
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Equipo/cargarequipo/<?php echo $permission; ?>");
    WaitingClose();
});

// Asigna contratista - Chequeado
function asignarContratista(idEquipo){
    idglob = idEquipo;
    console.log("variable global -> id de equipo: " + idglob);
    $('#tablaempresa tbody').html("");
    tr = null;

    click_co(idEquipo);
    traer_contratista();
    llenaContratistasEquipo(idEquipo);
}

$( document ).ready(function() {
    console.log( "ready!" );
    $(".inhabilitar").click(function(){
    });
});

function inhabilitarEquipo(idEquipo){
    $.ajax({
        type: 'POST',
        data: {
            idequipo: idEquipo
        },
        url: '<?php echo MAN;?>Equipo/cambio_equipo',
        success: function(data) {
            console.log(data);
            //alert("Se cambio el estado del equipo a INACTIVO");
            WaitingClose();
            //regresa();
			reloadTable();
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

function habilitarEquipo(idequipo) {
    console.log("ID equipo en fcion: " + idequipo);
    // Si el estado es Alta (saco lectura de tabla equipo (ultima lectura))
    wo();
    $.ajax({
        async: true,
        data: {
            idequipo: idequipo
        },
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/estado_alta',
        success: function(data) {
            wc();
            hecho("Hecho","Se cambio el estado del equipo a ACTIVO");
			reloadTable(idequipo);
        },
        error: function(result) {
            error();
            console.log(result);
        },
        complete: function() {
            wc();
        }
    });
}

function alta_historial_lectura(parametros) {
    console.log("parametros:");
    console.table(parametros);
    $.ajax({
        data: {
            parametros: parametros
        },
        dataType: 'json',
        type: 'POST',
        url: 'index.php/Equipo/alta_historial_lectura',
        success: function(data) {
            console.table(data);
            //alert("Se agregó historial lecturas");
        },
        error: function(result) {
            console.error("Error al agregar historial lecturas");
            console.table(result);
        },
    });
}

//cambio el estado a activo, sin importar si el anterior es alta, inhabilitado, etc...
function cambiar_estado(idequipo, vuelve = true) {
    $.ajax({
        data: {
            idequipo: idequipo
        },
        dataType: 'json',
        type: 'POST',
        url: 'index.php/Equipo/cambio_estado',
        success: function(data) {
            console.log(data);
            WaitingClose();
            alert("Se cambio el estado del equipo a ACTIVO");
            if (vuelve == true) {
                regresa();
            }
        },
        error: function(result) {
            console.error("Error al cambiar el estado");
            console.table(result);
        },
    });
}

// Impresion - Chequeado
$(".fa-print").click(function(e) {
    e.preventDefault();
    var idequip = $(this).parent('td').parent('tr').attr('id');
    console.log("El id de orden al imprimir es :");
    console.log(idequip);
    // alert(id_orden);
    $.ajax({
        type: 'POST',
        data: {
            idequip: idequip
        },
        dataType: 'json',
        url: 'index.php/Equipo/getsolImp', 
        success: function(data) {
            console.log("Entre a la impresion");
            console.log(data);
            console.log(data.datos.codigo);
            console.log(data.equipos.asegurado);
            console.log(data.orden.nombre);
            var fecha = new Date(data.datos.fechain);
            var day = fecha.getDate();
            var month = fecha.getMonth();
            var year = fecha.getUTCFullYear();
            fecha = day + '-' + month + '-' + year;
            //data.equipos.fecha_vigencia
            //data.equipos.fecha_inicio
            var fechav = new Date(data.equipos.fecha_vigencia);
            var day = fechav.getDate();
            var month = fechav.getMonth();
            var year = fechav.getUTCFullYear();
            fechav = day + '-' + month + '-' + year;
            var fechai = new Date(data.equipos.fecha_inicio);
            var day = fechai.getDate();
            var month = fechai.getMonth();
            var year = fechai.getUTCFullYear();
            fechai = day + '-' + month + '-' + year;
            var trequipos = '';
            for (var i = 0; i < data['orden'].length; i++) {
                var fecha1 = new Date(data['orden'][i]['fecha']);
                var day = fecha1.getDate();
                var month = fecha1.getMonth();
                var year = fecha1.getUTCFullYear();
                fecha1 = day + '-' + month + '-' + year;
                trequipos = trequipos + "<tr>  <td width='10%'>" + fecha1 +
                    "</td> <td width='10%'>" + data['orden'][i]['causa'] +
                    "</td> <td width='10%'>" + data['orden'][i]['causa'] +
                    "</td> <td width='10%'>" + data['orden'][i]['nombre'] +
                    "</td><td width='10%'>" + data['orden'][i]['estado'] + "</td>  </tr>";
            }
            var texto =
                '<div class="" id="vistaimprimir">' +
                '<div class="container">' +
                '<div class="thumbnail">' +

                '<div class="caption">' +
                '<div class="row" >' +
                '<div class="panel panel-default">' +
                '<div class="form-group">' +
                '<h3 class="text-center" align="center"></h3>' +
                '</div>' +
                '<hr/>' +
                '<div class="panel-body">' +
                '<div class="container">' +
                '<div class="thumbnail">' +
                '<div class="row">' +
                '<div class="col-sm-12 col-md-12">' +
                '<table width="100%" style="text-align:justify" >' +
                '<tr>' +
                '<tr>' +
                '<td  colspan="1"  align="left" >' +
                '<div class="text-left"> <img src="img/LOGO.jpg" width="280" height="80" /> </div></td>' +
                '</td>' +
                '<td >' +
                '<div  class="col-md-4 "><h3> FICHA TECNICA DE SERVICIO</h3>' +
                '</div>' +
                '</td>' +
                '</tr>' +
                '</tr>' +
                '</table>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-sm-12 col-md-12">' +
                '<table width="100%" style="text-align:justify" border="1px solid black" >' +
                '<tr>' +
                '<td>Numero de serie</td>' +
                '<td>' + data.datos.numero_serie + '</td>' +
                '<td style="text-align: left"" >Codigo del equipo</td>' +
                '<td>' + data.datos.codigo + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Marca del motor</td>' +
                '<td>' + data.datos.marca + '</td>' +
                '<td align="left" >Estado del equipo</td>' +
                '<td>' + data.datos.estado + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Modelo del motor</td>' +
                '<td>' + data.datos.modelo + '</td>' +
                '<td>Dominio</td>' +
                '<td>' + data.datos.dominio + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Numero de motor</td>' +
                '<td>' + data.datos.numero_motor + '</td>' +
                '<td>Marca de equipo</td>' +
                '<td>' + data.datos.marcaeq + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Año de fabricacion</td>' +
                '<td>' + data.datos.fabricacion + '</td>' +
                '<td>Modelo de equipo</td>' +
                '<td>' + data.datos.modelo + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Baterias</td>' +
                '<td>' + data.datos.bateria + '</td>' +
                '<td>Ubicacion</td>' +
                '<td>' + data.datos.ubicacion + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Peso Operativo</td>' +
                '<td>' + data.datos.ubicacion + '</td>' +
                '<td>Sector</td>' +
                '<td>' + data.datos.sector + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Ingreso a la Reparacion</td>' +
                '<td>' + fecha + '</td>' + //data.datos.fechain
                '<td>Horas del equipo a la fecha</td>' +
                '<td>' + data.datos.hora_lectura + '</td>' +
                '</tr>' +

                '</table>' +
                '</div>' +
                '</div>' +
                '<br>' +
                '<br>' +
                '<div class="row">' +
                '<div class="col-sm-12 col-md-12">' +
                '<table width="100%" style="text-align:justify" border="1px solid black" >' +
                '<tr>' +
                '<td colspan="4" align="center">Datos de Poliza de Seguro</td>' +
                '</tr>' +
                '<tr>' +
                '<td colspan="4" align="left">Seguro Obligatorio Automotor</td>' +
                '</tr>' +
                '<tr>' +
                '<td colspan="4" align="left">Decreto 1716/08 - Reclamo Ley: 26.363</td>' +
                '</tr>' +
                '<tr>' +
                '<td>Asegurado</td>' +
                '<td colspan="4">' + data.equipos.asegurado + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Ref</td>' +
                '<td>' + data.equipos.ref + '</td>' +
                '<td >Poliza</td>' +
                '<td>' + data.equipos.numero_pliza + '</td>' +
                '</tr>' +

                '<tr>' +
                '<td>Vigencia desde</td>' +
                '<td>' + fechav + '</td>' + //data.equipos.fecha_vigencia
                '<td>Hasta</td>' +
                '<td>' + fechai + '</td>' + //data.equipos.fecha_inicio
                '</tr>' +

                '</table>' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-6 col-md-6" border="1" >' +
                '</div>' +

                '<br>' +
                '<br>' +

                //aca va la tabla

                '<div class="row">' +
                '<div class="col-xs-10 col-xs-offset-1 text-center">' +

                '<table class="table table-bordered"  style="text-align:justify" border="1px solid black" >' +
                //class="table table-bordered"
                '<thead>' +
                '<tr colspan="6" height="30">' +
                '<th width="20%">Fecha </th>' +
                '<th width="40%">Descripcion del arreglo</th>' +
                '<th width="25%">Diagnostico realizado por </th>' +
                '<th width="25%">Reparacion realizado por </th>' +
                '<th width="10%">Estado de la reparacion </th>' +
                '</tr>' +
                '</thead>' +

                '<tbody style="text-align:center">' + trequipos +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr>' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr>' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr>' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '<tr colspan="2">' +
                '<td style="text-align: center" ></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '<td><br></td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>' +
                //'<div class="container-fluid">'+

                '</div>' +
                '</div>' +
                '</div>' +


                '</div>' +
                '</div>' +
                '</div>' +
                '<style>' +
                '.table, .table>tr, .table>td  {} ' +
                '</styl>';
            //border:  1px solid black;


            var mywindow = window.open('', 'Imprimir', 'height=700,width=900');
            mywindow.document.write('<html><head><title></title>');
            //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
            //mywindow.document.write('<link rel="stylesheet" href="main.css">
            mywindow.document.write('</head><body onload="window.print();">');
            mywindow.document.write(texto);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10
            //mywindow.print();
            //mywindow.close();
            return true;
        },
        error: function(result) {
            console.log(result);
            console.log("error en la vistaimprimir");
        },
    });
});

    $(".clear").val(""); //llimpia los inputs del modal lectura

/// agrega el estado del boton en modal - Chequeado
function estBoton($estado) {

    var estado = $estado;
    console.log('esBoton Estado: '+estado);
    if (estado == 'RE') { //reparacion -- Dato anterior erroneo Estado == Reparacion = RE
        inhabilitar();
    }
    if (estado == 'AC') { //activo -- Dato anterior erroneo Estado == Activo = AC
        habilitar();
    }
}

/// cambio de estado desde el boton - Chequeado
$(".llave").click(function(e) {
    var estadobton = $(this).attr("class");

    if (estadobton == 'fa fa-fw llave fa-toggle-on') {
        inhabilitar();
        $('#divFalla').css('display', 'block');
    }
    if (estadobton == 'fa fa-fw llave fa-toggle-off') {
        habilitar();
        $('#divFalla').css('display', 'none');
    }
});

// Chequeado
function habilitar() {
    $(".llave").removeClass("fa-toggle-off");
    $(".llave").addClass("fa-toggle-on");
    $("label#botestado").text('Activo');
    $("input#estado").val('AC'); // Estado Activo
}
// Chequeado
function inhabilitar() {
    $(".llave").removeClass("fa-toggle-on");
    $(".llave").addClass("fa-toggle-off");
    $("label#botestado").text('Reparación');
    $("input#estado").val('RE'); // Estado Reparacion
}

// Completa campos y select para Editar equipos - Listo
function completarEdit(datos, edit) {
    console.log("datos que llegaron");
    $('#equipo').val(datos['id_equipo']);
    $('#descripcion').val(datos['descripcion']);
    $('#fecha_ingreso').val(datos['fecha_ingreso']);
    $('#fecha_garantia').val(datos['fecha_garantia']);

    $('#marca1').append($('<option>', {
        text: datos['marca']
    }));

    $('#codigo').val(datos['codigo']);
    $('#ubicacion').val(datos['ubicacion']);
    $('#empresa').val(datos['empresa']);
    $('#id_empresa').val(datos['id_empresa']);

    $('#criticidad').append($('<option>', {
        text: datos['criticidad'],
        value: datos['id_criticidad']
    }));

    $('#etapa').append($('<option>', {
        text: datos['sector'],
        value: datos['id_sector']
    }));

    $('#grupo').append($('<option>', {
        text: datos['grupo'],
        value: datos['id_grupo']
    }));
    $('#estado').val(datos['estado']);

    var fecha = datos['fecha_ultimalectura'];

    $('#fecha_ultimalectura').val(fecha);

    $('#ultima_lectura').val(datos['ultima_lectura']);

    //traer_empresa();
    traer_etapa();
    traer_grupo();
    traer_criticidad();
    traer_marca();
}

// limpia los select modal editar - listo
function limpiarselect() {
    $('#criticidad').html("");
    $('#marca1').html("");
    $('#etapa').html("");
    $('#grupo').html("");
}

// Chequeado
function regresa() {
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Equipo/index/<?php echo $permission; ?>");
    WaitingClose();
}

// Chequeado para impresion
function cerro() {
    isOpenWindow = false;
}

// Guarda edicion de equipo
function guardar() {

    var idEquipo = $('#id_equipo').val();
    var codigo = $('#codigo').val();
    var ubicacion = $('#ubicacion').val();
    var marca = $('#marca option:selected').val();
    var descripcion = $('#descripcion').val();
    var fecha_ingreso = $('#fecha_ingreso').val();
    var fecha_ultimalectura = $('#fecha_ultimalectura').val();
    var ultima_lectura = $('#ultima_lectura').val();
    var fecha_garantia = $('#fecha_garantia').val();
    var estado = $('#estado').val();
    var sector = $('#etapa option:selected').val();
    var criticidad = $('#criticidad option:selected').val();
    var grupo = $('#grupo').val();
    var id_area = $('#area option:selected').val();
    var id_proceso = $('#proceso option:selected').val();
    var id_cliente = $('#cliente option:selected').val();
    var numero_serie = $('#numse').val();
    var descrip_tecnica = $('#destec').val();

    var parametros = {
        'descripcion': descripcion,
        'fecha_ingreso': fecha_ingreso,
        'fecha_garantia': fecha_garantia,
        'marca': marca,
        'codigo': codigo,
        'ubicacion': ubicacion,
        //'id_empresa' : id_empresa,
        'id_sector': sector,
        'id_grupo': grupo,
        'id_area': id_area,
        'id_proceso': id_proceso,
        'id_criticidad': criticidad,
        'id_customer': id_cliente,
        'numero_serie': numero_serie,
        'estado': estado,
        'fecha_ultimalectura': fecha_ultimalectura,
        'ultima_lectura': ultima_lectura,
        'descrip_tecnica': descrip_tecnica,
    };

    console.log("estoy editando");
    console.log(idEquipo);
    console.table(parametros);
    wo();
    $.ajax({
        type: 'POST',
        data: {
            data: parametros,
            idEquipo: idEquipo
        },
        url: '<?php echo MAN; ?>Equipo/editar_equipo',
        success: function(data) {
            wc();
            hecho();
			reloadTable();
        },
        error: function(result) {
            console.log(result);
        }
        //dataType: 'json',
    });
}

// Trae grupo y completa el select grupo - Chequeado
function traer_grupo() {
    $.ajax({
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getgrupo', 
        success: function(data) {

            //var opcion  = "<option value='-1'>Seleccione...</option>" ;
            //$('#grupo').append(opcion);
            for (var i = 0; i < data.length; i++) {

                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_grupo'] + "'>" + nombre + "</option>";
                $('#grupo').append(opcion);
            }
        },
        error: function(result) {

            console.log(result);
        },
        dataType: 'json'
    });
}
// Trae criticidad y completa el select grupo - Chequeado
function traer_criticidad() {
    $.ajax({
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getcriti', 
        success: function(data) {

            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_criti'] + "'>" + nombre + "</option>";
                $('#criticidad').append(opcion);
            }
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

// Trae etapa/sector y llena el select - Chequeado
function traer_etapa() {
    $.ajax({
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getetapa', 
        success: function(data) {
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_sector'] + "'>" + nombre + "</option>";
                $('#etapa').append(opcion);
            }
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

// Llena select en modal editar de marcas
function traer_marca() {
    //$('#marca1').html('');
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Equipo/getmarca', 
        success: function(data) {

            //var opcion  = "<option value='-1'>Seleccione...</option>" ;
            $('#marca1').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['marcadescrip'];
                var opcion = "<option value='" + data[i]['marcaid'] + "'>" + nombre + "</option>";
                $('#marca1').append(opcion);

            }

        },
        error: function(result) {

            console.log(result);
        },
        dataType: 'json'
    });
}

// Trae contratistas y llena selecte en modal editar
function traer_contratista() {
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Equipo/getcontra',
        dataType: 'json',
        success: function(data) {
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#empresae').html('');
            $('#empresae').append(opcion);
            if(data.status){
                for (var i = 0; i < data.contra.length; i++) {
                    var nombre = data.contra[i].nombre;
                    var opcion = "<option value='" + data.contra[i]['id_contratista'] + "'>" + nombre + "</option>";
                    $('#empresae').append(opcion);
                }
            }
        },
        error: function(result) {
            error();
            console.log(result);
        },
    });
}


function llenaContratistasEquipo(id_equipo) {
    wo();
    $.ajax({
        data: {
            id_equipo: id_equipo
        },
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getContratistasEquipo',
        success: function(data) {
            tabla = $('#tablaempresa').DataTable();
            tabla.clear().draw();
            for (var i = 0; i < data.length; i++) {
                //agrego valores a la tabla
                tablaCompleta = tabla.row.add([
                    '<i class="fa fa-fw fa-times-circle text-light-blue elirow" style="cursor: pointer; margin-left: 15px;" onclick="eliminarContratista('+data[i]['id_contratista']+')" data-eqContr="' +
                    data[i]['id_contratista'] + '"></i>',
                    data[i]['codigo'],
                    data[i]['nombre']
                ]);
                tablaCompleta.node().id = data[i]['id_contratista'];
                tablaCompleta.nodes().to$().attr("data-equipo", data[i]['id_codigo']);
                tablaCompleta.nodes().to$().attr("data-contratista", data[i]['id_contratista']);
                tabla.draw();
            }
        },
        error: function(result) {
            error();
            console.log(result);
        },
        complete: () => {
            wc();
        }
    });
}

//agrega contratista
$("#adde").click(function(e) {
    var id_equipo = $('#id_equipoC').val();
    var id_contratista = $('#empresae').val();
    console.log("id_contratista: " + id_contratista);
    console.log("id_equipo: " + id_equipo);

    wo();
    var hayError = false;
    if ($('#empresae').val() == -1) {
        hayError = true;
    }
    if (hayError == true) {
        $('#errorC').fadeIn('slow');
        wc();
        return;
    } else {
        $('#errorC').fadeOut('slow');
        $.ajax({
            data: {
                id_equipo: id_equipo,
                id_contratista: id_contratista
            },
            dataType: 'json',
            type: "POST",
            url: '<?php echo MAN; ?>Equipo/guardarcontra',
            success: (data) => {
                if(data){
                    hecho("Hecho","Contratista guardado con éxito");
                    llenaContratistasEquipo(id_equipo);
                }else{
                    error();
                }
            },
            error: (result) => {
                error();
                console.log("Error: " + result['status']);
                console.log(result);
            },
            complete : () => {
                wc();
            }
        })
    }
});

function eliminarContratista(id_contratista){
    var id_equipo = $('#id_equipoC').val();
    console.log(" Constratista: "+id_contratista+" Equipo: "+id_equipo);
    wo();
    $.ajax({
        data: {
            id_contratista: id_contratista,
            id_equipo: id_equipo,
        },
        dataType: 'json',
        type: "POST",
        url: '<?php echo MAN; ?>Equipo/delContratista',
        success: (data) => {
            llenaContratistasEquipo(id_equipo);
            hecho("Hecho","Contratista eliminado con éxito");
        },
        error: (result) => {
            error();
            console.log("Error: " + result['status']);
            console.log(result);
        },
        complete: () => {
            wc();
        }
    });
}

$(document).on("click", ".btnDel", function() {

    //var id_contratistaquipo = $(this).parent().parent().attr('id');
    var id_contratistaquipo = $('#empresae').val();
    var id_equipo = $('#id_equipoC').val();

    console.log(" Constratista: "+id_contratistaquipo+" Equipo: "+id_equipo)

    WaitingOpen("Agregando contratista a equipo")
    /*
    $.ajax({
            data: {
                id_contratistaquipo: id_contratistaquipo
            },
            dataType: 'json',
            type: "POST",
            url: 'index.php/Equipo/delContra',
        })
        .done(function(data) {
            llenaContratistasEquipo(id_equipo);
        })
        .error(function(result) {
            alert("Error eliminando contratista...");
            console.log("Error: " + result['status']);
            console.table(result);
        })
        .always(function() {
            WaitingClose();
        });
    */
});


function click_co(id_equipo) {
    $.ajax({
        type: 'POST',
        data: {
            id_equipo: id_equipo
        },
        dataType: 'json',
        url: '<?php echo MAN; ?>Equipo/getco',
        success: function(data) {
            //aca trae la marca erronea
            console.table(data);
            var fechai = data[0]['fecha_ingreso'];
            var fechag = data[0]['fecha_garantia'];
            var mar = data[0]['marcadescrip'];
            var ubica = data[0]['ubicacion'];
            var descrip = data[0]['descripcion'];
            var codigoe = data[0]['codigo'];
            $('#codigoe').val(codigoe);
            $('#fecha_ingresoe').val(fechai);
            $('#fecha_garantiae').val(fechag);
            $('#marcae').val(mar);
            $('#descripcione').val(descrip);
            $('#ubicacione').val(ubica);
            $('#id_equipoC').val(id_equipo);
        },
        error: function(result) {
            console.log(result);
        },
    });
}

function historialLectura(idEquipo){
  
    //detectarForm();
    //initForm();
    $("tr.registro").remove();
    $.ajax({
        type: 'POST',
        data: {
            idequipo: idEquipo
        },
        url: 'index.php/Equipo/getHistoriaLect',
        success: function(data) {
            console.table(data);
            llenarModal(data);
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

/// Hitorial de lecturas
// $(".fa-history").click(function(e) {
	//     $("tr.registro").remove();
	//     var $id_equipo = $(this).parent('td').parent('tr').attr('id');
	//     console.log("id de equipo: " + $id_equipo);

	//     $.ajax({
	//         type: 'POST',
	//         data: {
	//             idequipo: $id_equipo
	//         },
	//         url: 'index.php/Equipo/getHistoriaLect',
	//         success: function(data) {
	//             console.table(data);
	//             llenarModal(data);
	//         },
	//         error: function(result) {
	//             console.log(result);
	//         },
	//         dataType: 'json'
	//     });
// });


function recargarTabla() {
    $("tr.registro").remove();
    var $id_equipo = $('#id_Equipo_modal').val();

    $.ajax({
        type: 'POST',
        data: {
            idequipo: $id_equipo
        },
        url: 'index.php/Equipo/getHistoriaLect',
        success: function(data) {
            console.table(data);
            llenarModal(data);
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

// redibuja tabla conservando la paginacion
function reloadTable(){
    let table = $('#sales').DataTable();
        table
        .order( [[ 1, 'asc' ]] )
        .draw( false );
}

/// llena modal historial de lecturas
function llenarModal(data) {
    $('#tblhistorial').DataTable().clear().draw();
    if (data.length == 0) return;
    $('#id_Equipo_modal').val(data[0]['id_equipo']);
    localStorage.setItem('id_equipo', data[0]['id_equipo']);
    // console.table(data);
    if (Array.isArray(data) && data.length) {
        console.log("El equipo SI tiene historial de lecturas");
        $("#codEquipo").text(data[0]['codigo']);
        //borro los datos de la tabla
        $('#tblhistorial').DataTable().clear().draw(); 

        /*  harkodeo muestra formulario de empresas*/
        var formulario = <?php if(EMPRESAS_FORM == empresa()) echo 1; else echo 0; ?>;

        for (var i = 0; i < data.length; i++) {
            var fecha =  data[i]['fecha'].substr(0,10);
            var hora =   data[i]['fecha'].substr(10,17);
            var lectura = parseFloat(data[i]['lectura']);
            
            /* Muestra boton de formulario o no */
            if(formulario){
                var frmOpenLink = '<a class="frm-open" data-readonly="true"  href="#" data-info="'+data[i]['info_id']+'"><i class="fa fa-paperclip formularios"></i></a>';
            }else {
                var frmOpenLink = '';
                }
           // </i><td class="text-center"><a class="frm-open" data-readonly="true"  href="#" data-info="'+data[i]['info_id']+'"><i class="fa fa-paperclip"></i></a></td>'
            $('#tblhistorial').DataTable().row.add([
                '<i class="fa fa-fw fa-pencil text-light-blue editLectura" style="cursor: pointer; margin-left: 15px;" title="Editar lectura" data-idLectura="' +
                data[i]['id_lectura'] + '"></i> '+ frmOpenLink +'  ',
                lectura,
                fecha,
                hora,
                data[i]['operario_nom'],
                data[i]['turno'],
                data[i]['observacion'],
                // estado()
                data[i]['estado']
            ]).draw();
        }
    } else {
        $("#codEquipo").text("Equipo sin historial de lecturas");
        $('#tblhistorial').DataTable().clear().draw();
        console.log("El equipo NO tiene historial de lecturas");
    }
}

    // Manejar clic en elementos con clase .formularios dentro de #form-dinamico
    $(document).on("click", ".formularios", function() {
        //initForm();
        detectarForm(); 
        $("#form-dinamico").show();
   
    });

$(document).on("click", ".editLectura", function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $("#modalEditLecturaObservacion").modal('show');
    var idLectura = $(this).data("idlectura");
    var lectura = $(this).parents("tr").find("td").eq(1).html();
    var observacion = $(this).parents("tr").find("td").eq(6).html();
    // console.log(observacion);
    $('#idLecturaEdit').val(idLectura);
    $('#lecturaEdit').val(lectura);
    $('#observacionEdit').val(observacion);
    $('#errorEditLectura').fadeOut(0);
    $('#errorEditObservacion').fadeOut(0);
    $('#errorEditLectura2').fadeOut(0);
});

function guardarEdit() {
    $('#errorEditLectura').fadeOut('fast');
    $('#errorEditObservacion').fadeOut('fast');
    $('#errorEditLectura2').fadeOut('fast');

    console.log('estoy guardando');
    var id_lectura = $('#idLecturaEdit').val();
    var lectura = $('#lecturaEdit').val();
    var observacion = $('#observacionEdit').val();
    var id_equipo = localStorage.getItem('id_equipo');

    if ((id_lectura == "") || (lectura == "") || (observacion =="")){
        if(lectura == ""){
            $('#errorEditLectura').fadeIn('slow');
        }
        if(observacion == ""){
            $('#errorEditObservacion').fadeIn('slow')
        }
        return;
    } else {

        $.ajax({
            type: "POST",
            url: "index.php/Equipo/setLecturaObservacionEdit",
            data: {
                id_equipo,
                lectura,
                observacion,
                id_lectura
            },
            success: function(data) {
                console.log(data);
                if(!data){
                    $('#errorEditLectura2').fadeIn('slow');
                }else{
																	debugger;
                    console.log("Guardado con exito...");
                    $("#modalEditLecturaObservacion").modal('hide');
                    //recargarTabla();
					reloadTable();
                }
            },
            error: function(result) {
                $("#modalEditLecturaObservacion").modal('hide');
                alert('Ocurrió un error en la Edición...');
                console.log(result);
            },
            dataType: 'json'
        });

        //   $('#modalectura').modal('hide');
        //   $('#errorLectura').hide();
    }
}


// Chequea los campos llenos - Chequeado
function validarCampos() {
    var hayError = "";
    if ($('#lectura').val() == "") {
        hayError = true;
    }else{
         
         if(parseInt($('#lectura').val()) <= parseInt($("#spanNuevaLectura").text())){
             hayError = "lectura";
         }
    }
    if ($('#operario').val() == "") {
        hayError = true;
    }
    if ($('#turno').val() == "") {
        hayError = true;
    }
    if ($('#observacion').val() == "") {
        hayError = true;
    }

    return hayError;
}

// guada lectura nueva de equipo
function guardarlectura() {
    equipo= $('#id_maquina').val();
    var hayError = false;
    hayError = validarCampos();
    if (hayError == true) {
        $('#errorLectura').fadeIn('slow');
        $('#errorLectura2').fadeOut('fast');
    }else if(hayError == "lectura"){
        $('#errorLectura2').fadeIn('slow');
        $('#errorLectura').fadeOut('fast');
    }else {

        var lectura = $("#formlectura").serializeArray();
        console.table(lectura);
        $.ajax({
            type: "POST",
            url: "index.php/Equipo/setLectura",
            data: lectura,
            success: function(data) {               
                guardaForm(equipo);
                console.log("Guardado con exito...");
                
				//reloadTable();
            },
            error: function(result) {
                console.log("Error en guardado de Lectura...");
                console.log(result);
            },
            dataType: 'json'
        });

        $('#modalectura').modal('hide');
        $('#errorLectura').hide();
        reloadTable();
    }
}

async function guardaForm(equipo)
{
     //obtengo el formulario
     idFormDinamico = "#"+$('.frm-new').find('form').attr('id');

    //si tiene id_form guardo el formulario 
    if(idFormDinamico != "#undefined") 
    {
    
        frm_validar(idFormDinamico)
        if(!frm_validar(idFormDinamico)){
            error('Error..','Debes completar los campos obligatorios (*)');
            return;                    
        }
        //var info_id = frmGuardar(idFormDinamico);
        var info_id = await frmGuardarConPromesa($(idFormDinamico));
        guradaInfo_id(info_id, equipo);
    } 
}

function guradaInfo_id(info_id = '', equipo)
{
    console.log(equipo)
    console.log(info_id)
    $.ajax({
            type: "POST",
            url: "index.php/Equipo/guardaInfo_idLectura",
            data: {'info_id':info_id, 'equipo': equipo},
            success: function(data) {

                console.log("Guardado info_id con exito...");
                
				//reloadTable();
            },
            error: function(result) {
                console.log("Error en guardado de Lectura...");
                console.log(result);
            },
            dataType: 'json'
        });
}
/************************************/
/********** ELIMINA EQUIPO **********/
/************************************/
function eliminarEquipo(idEquipo){
    if (!confirm("Realmente desea eliminar este equipo?")) {
        return;
    } else {
        console.log("ID equipo a eliminar: " + idEquipo);
        $.ajax({
            type: 'POST',
            data: {
                idEquipo: idEquipo
            },
            dataType: 'json',
            url: '<?php echo MAN; ?>Equipo/baja_equipo',
            success: function(data) {
                hecho("Éxito","Equipo/sector ANULADO");
                reloadTable();
            },
            error: function(result) {
                error();
                console.log(result);
            },
        });
    }
}
/************************************/
/**********  EDITA EQUIPO  **********/
/************************************/
function editarEquipo(idEquipo){
    console.info('id de equipo a editar: ' + idEquipo);

    $.ajax({
        data: {
            idEquipo: idEquipo
        },
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getEditar',
        success: function(result) {
            console.log(result);
            llenarCampos(result);
        },
        error: function(result) {
            console.log(result);
            error();
        },
    });
}

function llenarCampos(data) {
    //ubicacion
    llenar_area(data[0]['id_area']);
    llenar_proceso(data[0]['id_proceso']);
    llenar_criticidad(data[0]['id_criti']);
    llenar_sector(data[0]['id_sector']);
    llenar_grupo(data[0]['id_grupo']);
    //equipo/sector
    $('#id_equipo').val(data[0]['id_equipo']);
    $('#codigo').val(data[0]['codigo']);
    llenar_marca(data[0]['marcaid']);
    llenar_cliente(data[0]['cliId']);
    $('#descripcion').val(data[0]['deeq']);
    $('#numse').val(data[0]['numero_serie']);
    $('#ubicacion').val(data[0]['ubicacion']);
    $('#fecha_ingreso').val(data[0]['fecha_ingreso']);
    $('#fecha_garantia').val(data[0]['fecha_garantia']);
    $('#fecha_ultimalectura').val(data[0]['fecha_ultimalectura']);
    $('#ultima_lectura').val(data[0]['ultima_lectura']);
    $('#destec').val(data[0]['descrip_tecnica']);
    $('#estado').val(data[0]['estado']);
    //info complementaria
    llenar_adjunto(data[0]['archivo']);
    //abro modal
    $('#modaleditar').modal('show');
    WaitingClose();
}
// Trae area y llena el select Area - Listo
function llenar_area(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getarea',
        success: function(data) {
            //console.table(data);
            $('#area').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['id_area'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_area'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#area').append(opcion);
            }
        },
        error: function(result) {
            console.error(result);
        },
    });
}
// Trae area y llena el select Proceso - Listo
function llenar_proceso(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getproceso',
        success: function(data) {
            //console.log("proceso: "+id);
            //console.table(data);
            $('#proceso').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['id_proceso'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_proceso'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#proceso').append(opcion);
            }
        },
        error: function(result) {
            console.error(result);
        },
    });
}
// Trae area y llena el select Criticidad - Listo
function llenar_criticidad(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getcriti',
        success: function(data) {
            //console.log("criticidad: "+id);
            //console.table(data);
            $('#criticidad').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['id_criti'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_criti'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#criticidad').append(opcion);
            }
        },
        error: function(result) {
            console.error(result);
        },
    });
}
// Trae area y llena el select Sector - Listo
function llenar_sector(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getetapa', 
        success: function(data) {
            //console.log("sector/etapa: "+id);
            //console.table(data);
            $('#etapa').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['id_sector'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_sector'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#etapa').append(opcion);
            }
        },
        error: function(result) {
            error();
            console.log(result);
        },
    });
}
// Trae area y llena el select Grupo - Listo
function llenar_grupo(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getgrupo', 
        success: function(data) {
            //console.table(data);
            $('#grupo').text("");
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#grupo').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['id_grupo'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['descripcion'];
                var opcion = "<option value='" + data[i]['id_grupo'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#grupo').append(opcion);
            }
        },
        error: function(result) {
            error();
            console.log(result);
        },
    });
}
// Trae area y llena el select Grupo - Listo
function llenar_marca(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getmarca',
        success: function(data) {
            //console.log("marca: "+id);
            //console.table(data);
            $('#marca').text("");
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['marcaid'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['marcadescrip'];
                var opcion = "<option value='" + data[i]['marcaid'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#marca').append(opcion);
            }
        },
        error: function(result) {
            console.table(result);
        },
    });
}
// Trae area y llena el select Grupo - Listo
function llenar_cliente(id) {
    $.ajax({
        data: {},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo MAN; ?>Equipo/getcliente', 
        success: function(data) {
            //console.log("cliente: "+id);
            //console.table(data);
            $('#cliente').text("");
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#cliente').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var selectAttr = '';
                if (data[i]['cliId'] == id) {
                    var selectAttr = 'selected';
                }
                var nombre = data[i]['cliRazonSocial'];
                var opcion = "<option value='" + data[i]['cliId'] + "' " + selectAttr + ">" + nombre +
                    "</option>";
                $('#cliente').append(opcion);
            }
        },
        error: function(result) {
            console.table(result);
        },
    });
}
//llena los datos de archivo adjunto
function llenar_adjunto(adjunto) {
    var accion = '';
    accion = '<i class="fa fa-plus-square agregaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Agregar Adjunto"></i>'+'<br>';
    //console.info( "adjunto: "+adjunto );
    if (adjunto){
        for (let i = 0; i < adjunto.length; i++) {
            accion += 
                '<a href="assets/filesequipos/'+ adjunto[i].adjunto +'" target="_blank">'+ adjunto[i].adjunto +'</a>' +
                '<i class="fa fa-times-circle text-light-blue" style="cursor:pointer; margin-right:10px" title="Eliminar Adjunto"  onclick=eliminaAdjunto('+adjunto[i].id_adjunto+')></i>' +
                '<i class="fa fa-pencil text-light-blue" style="cursor:pointer; margin-right:10px" title="Editar Adjunto" onclick=editaAdjunto('+adjunto[i].id_adjunto+')></i>';
            }
    }
    $('#accionAdjunto').html(accion);
}

// Agrega las areas nuevas - Listo
function guardararea() {

    var descripcion = $('#nomarea').val();
    var parametros = {
        'descripcion': descripcion
    };
    console.log(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            type: "POST",
            url: "<?php echo MAN; ?>Equipo/agregar_area",
            dataType: 'json',
            data: {
                parametros: parametros
            },
            success: function(data) {
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
            error: function(result) {
                error();
                console.log(result);
            },
            complete: () =>{
                wc();
            }
        });
    } else {
        error("Error","Por favor complete la descripción del área, es un campo obligatorio");
    }
}

// Agrega las procesos nuevos - Listo
function guardarproceso() {

    var descripcion = $('#nomproceso').val();
    var parametros = {
        'descripcion': descripcion
    };
    console.log(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "<?php echo MAN; ?>Equipo/agregar_proceso",
            data: {
                parametros: parametros
            },
            success: function(data) {
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
            error: function(result) {
                console.log("entro por el error");
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error("Error","Por favor complete la descripción del proceso, es un campo obligatorio");
    }
}

// Agrega criticidad nueva - Listo
function guardarcri() {
    var descripcion = $('#de').val();
    var parametros = {
        'descripcion': descripcion
    };
    console.log(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            type: "POST",
            url: "<?php echo MAN; ?>Equipo/agregar_criti",
            dataType: 'json',
            data: {
                parametros: parametros
            },
            success: function(data) {
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
            error: function(result) {
                console.log("entro por el error");
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error("Error","Por favor complete criticidad, es un campo obligatorio");
    }
}

// Agrega las grupos nuevos - Listo
function guardargrupo() {
    var descripcion = $('#nomgrupo').val();
    var parametros = {
        'descripcion': descripcion,
        'estado': 'AC',
    };
    console.table(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            type: "POST",
            url: "<?php echo MAN; ?>Equipo/agregar_grupo",
            dataType: 'json',
            data: {
                parametros: parametros
            },
            success: function(data) {
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
            error: function(result) {
                console.log("entro por el error");
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error("Error","Por favor complete la descripción del grupo, es un campo obligatorio");
    }
}

// Agrega sector/etapa nuevos - Listo
function guardaretapa() {
    var descripcion = $('#nometapa').val();
    var parametros = {
        'descripcion': descripcion,
        'estado': 'AC',
    };
    console.table(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            type: "POST",
            url: "<?php echo MAN; ?>Equipo/agregar_etapa",
            dataType: 'json',
            data: {
                parametros: parametros
            },
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
            error: function(result) {
                console.log("entro por el error");
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error("Error","Por favor complete la descripción de la etapa, es un campo obligatorio");
    }
}

// Agrega sector/etapa nuevos
function guardarCliente() {
    var cliName = $('#cliName').val();
    var cliLastName = $('#cliLastName').val();
    var cliDni = $('#cliDni').val();
    var cliAddress = $('#cliAddress').val();
    var cliPhone = $('#cliPhone').val();
    var cliEmail = $('#cliEmail').val();
    var cliRazonSocial = $('#cliRazonSocial').val();

    var parametros = {
        'cliName': cliName,
        'cliLastName': cliLastName,
        'cliDni': cliDni,
        'cliAddress': cliAddress,
        'cliPhone': cliPhone,
        'cliEmail': cliEmail,
        'cliRazonSocial': cliRazonSocial,
        'estado': 'AC',
    };
    console.table(parametros);
    var hayError = false;

    if (parametros != 0) {
        wo();
        $.ajax({
            data: {
                parametros: parametros
            },
            dataType: 'json',
            type: "POST",
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
            error: function(result) {
                error();
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error('Error','Complete por favor los campos obligatorios.');
    }
}


// Agrega las grupos nuevos - Listo
function guardarmarca() {
    var descripcion = $('#nombreMarca').val();
    var parametros = {
        'marcadescrip': descripcion,
        'estado': 'AC',
    };
    console.table(parametros);
    var hayError = false;
    if (parametros != 0) {
        wo();
        $.ajax({
            data: {
                parametros: parametros
            },
            dataType: 'json',
            type: 'POST',
            url: "<?php echo MAN; ?>Equipo/agregar_marca", 
            success: function(data) {
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
            error: function(result) {
                error();
                console.log(result);
            },
            complete: function(){
                wc();
            }
        });
    } else {
        error("Error","Por favor complete la descripcion de Marca, es un campo obligatorio");
    }
}

//abrir modal eliminar adjunto
/* $(document).on("click", ".eliminaAdjunto", function() {
    $('#modalEliminarAdjunto').modal('show');
    var idEquipo = $('#id_equipo').val();
    $('#idAdjunto').val(idEquipo);
}); */

//eliminar adjunto
function eliminaAdjunto(idAdjunto) {
    $('#modalEliminarAdjunto').modal('show');
    var idEquipo = $('#id_equipo').val();
    $('#idEliminaEquipo').val(idEquipo);
    $('#idEliminaAdjunto').val(idAdjunto);
}

//Eliminar adjunto
$("#formEliminarAdjunto").submit(function(event) {
    $('#modalEliminarAdjunto').modal('hide');
    event.preventDefault();
    var idEquipo = $('#idEliminaEquipo').val();
    var idAdjunto = $('#idEliminaAdjunto').val();

        $.ajax({
                dataType: 'json',    
                type: 'POST',
                url: 'index.php/Equipo/eliminaAdjunto',
                data: { 
                    idEquipo:idEquipo,
                    idAdjunto:idAdjunto
                    }
            })
            .success(function(data) {
                debugger;
                llenar_adjunto(data);
            })
            .error(function(result) {
                console.error(result);
            });
});

//abrir modal agregar adjunto
 $(document).on("click", ".agregaAdjunto", function() {
    $('#modalAgregarAdjunto').modal('show');
    var idEquipo = $('#id_equipo').val();
    $('#idAgregaAdjunto').val(idEquipo);
});

//abrir modal editar adjunto
function editaAdjunto(idAdjunto) {
    $('#modalEditarAdjunto').modal('show');
    var idEquipo = $('#id_equipo').val();
    $('#idEquipo').val(idEquipo);
    $('#id_adjunto').val(idAdjunto);
};

//agregar adjunto
$("#formAgregarAdjunto").submit(function(event) {
    $('#modalAgregarAdjunto').modal('hide');
   debugger
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
                url: 'index.php/Equipo/agregarAdjunto',
            })
            .done(function(data) {
                llenar_adjunto(data);
            })
            .error(function(result) {
                console.error(result);
            });
    }
});
/*
$('#modaleditar').on('hidden.bs.modal', function(e) {
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Equipo/index/<?php echo $permission; ?>");
})*/

//editar adjunto
$("#formEditarAdjunto").submit(function(event) {
    $('#modalEditarAdjunto').modal('hide');
    debugger
    event.preventDefault();
    if (document.getElementById("inputEditarPDF").files.length == 0) {
        $('#error').fadeIn('slow');
    } else {
        $('#error').fadeOut('slow');
        var formData = new FormData($("#formEditarAdjunto")[0]);

        $.ajax({
                cache: false,
                contentType: false,
                data: formData,
                dataType: 'json',
                processData: false,
                type: 'POST',
                url: 'index.php/Equipo/EditarAdjunto',
            })
            .done(function(data) {
                debugger;
                llenar_adjunto(data);
            })
            .error(function(result) {
                console.error(result);
            });
    }
});
// Datatable - Chequeado
//------------------------------------------------------

$('#tblhistorial').DataTable({
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

$('#tablaempresa').DataTable({
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

</script>




<!-- Modal CONTRATISTA -->
<div id="modalasignar" class="modal" role="dialog" inert>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Asignación de contratista a equipo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title fa fa-cogs"> Datos del Equipo</h4>
                            </div><!-- /.panel-heading -->

                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <label for="codigoe">Codigo:</label>
                                        <input id="codigoe" name="codigoe" class="form-control" disabled>
                                        <input type="hidden" id="id_equipoC" name="id_equipoC">
                                    </div>

                                    <div class="col-xs-12 col-md-6">
                                        <label for="ubicacione">Ubicacion:</label>
                                        <input type="text" id="ubicacione" name="ubicacione" class="form-control"
                                            disabled>
                                    </div>

                                    <div class="col-xs-12 col-md-6">
                                        <label for="marcae">Marca:</label>
                                        <input type="text" id="marcae" name="marcae" class="form-control" disabled>
                                    </div>

                                    <div class="col-xs-12 col-md-6">
                                        <label for="fecha_ingresoe">Fecha de Ingreso:</label>
                                        <input type="date" id="fecha_ingresoe" name="fecha_ingresoe"
                                            class="form-control input-md" disabled>
                                    </div>

                                    <div class="col-xs-12 col-md-6">
                                        <label for="fecha_garantiae">Fecha de Garantia:</label>
                                        <input type="date" id="fecha_garantiae" name="fecha_garantiae"
                                            class="form-control input-md" disabled>
                                    </div>

                                    <div class="col-xs-12">
                                        <label for="">Descripcion: </label>
                                        <textarea class="form-control" id="descripcione" name="descripcione"
                                            disabled></textarea>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title fa fa-file-text-o"> Contratista</h4>
                            </div><!-- /.panel-heading -->

                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="alert alert-danger alert-dismissable" id="errorC"
                                            style="display: none">
                                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                            Revise que todos los campos esten completos
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select id="empresae" name="empresae" class="form-control" />
                                        <input type="hidden" id="id_contratista" name="id_contratista">
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <button type="button" class="btn btn-primary" id="adde">Agregar</button>
                                    </div>
                                </div><br>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-bordered" id="tablaempresa">
                                            <thead>
                                                <tr>
                                                    <th>Acción</th>
                                                    <th>Equipo</th>
                                                    <th>Contratistas Asignados</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerro()">Cancelar</button>
                <!--<button type="button" class="btn btn-primary" id="reset" data-dismiss="modal" onclick="guardarsi()">Guardar</button>-->
            </div>
        </div>
    </div>
</div>
<!-- / Modal CONTRATISTA -->

<!-- Modal EDITAR -->
<div id="modaleditar" class="modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="fa fa-fw fa-pencil text-light-blue"></span> Editar Equipo/Sector
                </h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2 class="panel-title"><span class="fa fa-globe"></span> Ubicación del Equipo / Sector
                                </h2>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <input type="hidden" id="unin" name="unin" class="form-control"
                                        value="<?php echo $empresa ?>">

                                    <div class="col-md-6 col-sm-12">
                                        <!-- FIRST COLUMN -->
                                        <div class="row">
                                            <div class="col-xs-8"><label>Área<strong
                                                        style="color: #dd4b39">*</strong>:</label>
                                                <input type="hidden" id="id_area" name="id_area">
                                                <select id="area" name="area" class="form-control"
                                                    value="<?php echo $area ?>"></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addarea"
                                                    data-toggle="modal" data-target="#modalarea"><i class="fa fa-plus">
                                                        Agregar</i></button>
                                            </div>

                                            <div class="col-xs-8"><label>Proceso<strong
                                                        style="color: #dd4b39">*</strong>:</label>
                                                <input type="hidden" id="id_proceso" name="id_proceso">
                                                <select id="proceso" name="prid_procesooceso" class="form-control"
                                                    value=""></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addproceso"
                                                    data-toggle="modal" data-target="#modalproceso"><i
                                                        class="fa fa-plus"> Agregar</i></button>
                                            </div>

                                            <div class="col-xs-8"><label>Criticidad<strong
                                                        style="color: #dd4b39">*</strong>:</label>
                                                <select id="criticidad" name="criticidad" class="form-control"></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addcriti"
                                                    data-toggle="modal" data-target="#modalcrit"><i class="fa fa-plus">
                                                        Agregar</i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <!-- FIRST COLUMN -->
                                        <div class="row">
                                            <div class="col-xs-8"><label>Sector/Etapa<strong
                                                        style="color: #dd4b39">*</strong>:</label>
                                                <select id="etapa" name="etapa" class="form-control" value=""></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addetapa"
                                                    data-toggle="modal" data-target="#modaletapa"><i class="fa fa-plus">
                                                        Agregar</i></button>
                                            </div>

                                            <div class="col-xs-8"><label>Grupo:</label>
                                                <select id="grupo" name="grupo" class="form-control"></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addgrupo"
                                                    data-toggle="modal" data-target="#modalgrupo"><i class="fa fa-plus">
                                                        Agregar</i></button>
                                            </div>

                                            <div class="col-xs-8"><label>Cliente:</label>
                                                <select id="cliente" name="cliente" class="form-control"></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <label>&emsp;</label><br>
                                                <button type="button" class="btn btn-primary" id="addcliente"
                                                    data-toggle="modal" data-target="#modalCliente"><i
                                                        class="fa fa-plus"> Agregar</i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <!-- FIRST COLUMN -->
                                        <div class="row">

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
                                        <label>Código</label> <strong style="color: #dd4b39">*</strong>:
                                        <input type="text" id="codigo" name="codigo" class="form-control"
                                            placeholder="Ingrese Código de Equipo">
                                        <input type="hidden" id="id_equipo" name="id_equipo">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Marca</label> <strong style="color: #dd4b39">*</strong>:
                                        <select id="marca" name="marca" class="form-control" value=""></select>
                                    </div>
                                    <div class="col-xs-4">
                                        <label>&emsp;</label><br>
                                        <button type="button" class="btn btn-primary" id="addcriti" data-toggle="modal"
                                            data-target="#modalMarca"><i class="fa fa-plus"> Agregar</i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Descripción</label> <strong style="color: #dd4b39">*</strong>:
                                        <textarea class="form-control" id="descripcion" name="descripcion"
                                            placeholder="Ingrese breve Descripción (Tamaño Máx 255 caracteres) ..."
                                            cols="20" rows="3"></textarea>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Número de serie:</label>
                                        <input type="text" id="numse" name="numse" class="form-control input-md"
                                            placeholder="Ingrese Número de serie">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Ubicación (Georeferencial)</label>:
                                        <input type="text" id="ubicacion" name="ubicacion" class="form-control"
                                            placeholder="Ingrese Ubicación">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Fecha de Ingreso:</label>
                                        <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                                            class="form-control input-md">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Fecha de Garantía:</label>
                                        <input type="date" id="fecha_garantia" name="fecha_garantia"
                                            class="form-control input-md">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Fecha de Lectura Inicial:</label>
                                        <input type="datetime" id="fecha_ultimalectura" name="fecha_ultima"
                                            class="form-control input-md" disabled="">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Lectura Inicial:</label>
                                        <input type="text" id="ultima_lectura" name="ultima_lectura"
                                            class="form-control input-md" placeholder="Ingrese Ultima Lectura" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label>Archivo Adjunto:</label>
                                        <table class="table table-bordered" id="tablaadjunto">
                                            <tbody>
                                                <tr>
                                                    <td id="accionAdjunto">
                                                        <!-- -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12">
                                        <label>Descripción Técnica:</label>
                                        <textarea class="form-control" id="destec" name="destec"
                                            placeholder="Ingrese Descripción Técnica..."></textarea>
                                        <input type="hidden" id="estado" name="estado" class="form-control input-md">
                                    </div>
                                </div>
                            </div><!-- /.panel-body-->
                        </div><!-- /.panel -->

                    </div><!-- /.col-xs-12 -->
                </div><!-- /.row -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal"  onclick="guardar()">Guardar</button>
                </div>

            </div><!-- /.modal-body -->


        </div>
    </div>
</div>
<!-- / Modal EDITAR -->

<!-- Modal LECTURA -->
<div class="modal" id="modalectura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Lectura Equipo</h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="alert alert-danger alert-dismissable" id="errorLectura" style="display: none">
                    <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
                    Complete todos los datos obligatorios.
                </div>
                <div class="alert alert-danger alert-dismissable" id="errorLectura2" style="display: none">
                    <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
                    Valor de lectura incorrecto.
                </div>
                <form id="formlectura">

                    <div class="form-group">
                        <i href="#" class="fa fa-fw llave" style="cursor:pointer; color:#3c8dbc" title=""></i>
                        <label class="radio-inline" id="botestado"></label>
                        <input type="hidden" name="estado" id="estado">
                    </div>
                    <div class="form-group">
                        <label for="maquina">Equipo </label> <!-- <strong style="color: #dd4b39">*</strong>: -->
                        <input type="text" id="maquina" class="form-control clear" disabled>
                        <!-- id_equipo = id_maquina -->
                        <input type="text" id="id_maquina" name="id_equipo" class="form-control hidden clear">
                    </div>
                    <div class="form-group">
                        <label for="">Lectura <strong style="color: #dd4b39">*</strong>:</label>
                        <input type="text" id="lectura" name="lectura" class="form-control clear" placeholder="Inserte Cantidad" onkeypress="return validaNum(event)">
                        <span>Ingrese valor mayor a: </span><span id="spanNuevaLectura"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Operario <strong style="color: #dd4b39">*</strong>:</label>
                        <input type="text" id="operario" name="operario" class="form-control clear" placeholder="Inserte Operario">
                    </div>
                    <div class="form-group">
                        <label for="">Turno <strong style="color: #dd4b39">*</strong>:</label>
                        <input type="text" id="turno" name="turno" class="form-control clear" placeholder="Inserte Turno">
                    </div>
                    <div class="form-group">
                        <label for="observacion">Observaciones <strong style="color: #dd4b39">*</strong>:</label>
                        <textarea class="form-control clear" id="observacion" name="observacion" placeholder="Observaciones..."></textarea>
                    </div>
                    <input type="hidden" name="form_id" id="form_id">

                    <!-- si la empresa quiere que se largue la solicitud cuando se pone en RE el equipo -->
                    <?php if(EMPRESAS_FORM == empresa()) 
                
                        echo '<div class="form-group" id="divFalla" style="display: none;">
                                <label for="falla">Falla <strong style="color: #dd4b39">*</strong>:</label>
                                <textarea class="form-control clear" id="falla" name="falla" placeholder="Inserte falla"></textarea>
                            </div> '
                
                    ?>
                    
                </form>
                 <!-- si la empresa tiene formularios -->
                <?php if(EMPRESAS_FORM == empresa()) 
                
                    echo '<div class="frm-new" id="formulario"></div> '
                
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarlectura()">Guardar</button>
            </div> <!-- /.modal footer -->

        </div> <!-- /.modal-content -->
        <!-- /.modal-body -->
    </div> <!-- /.modal-dialog modal-lg -->
</div> <!-- /.modal fade -->
<!-- / Modal LECTURA -->

<!-- Modal Historial de Lecturas -->
<div class="modal" id="modalhistlect" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Historial de Lecturas</h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body">
                <input type="hidden" id="id_Equipo_modal">
                <label>Equipo: <span id="codEquipo"></span></label>
                <table id="tblhistorial" class="table table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th>Edición/Formulario</th>
                            <th>Lectura</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Operario</th>
                            <th>Turno</th>
                            <th>Observación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- -->
                    </tbody>
                </table>
            </div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->

    </div> <!-- /.modal-dialog modal-lg -->
</div> <!-- /.modal fade -->
<!-- / Modal Historial de Lecturas -->

<!-- Modal Edicion de Lecturas -->
<div class="modal" id="modalEditLecturaObservacion">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Editar Lectura</h4>
            </div>
            <form id="formEditarLectura">
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissable" id="errorEditLectura" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Por favor ingrese una nueva lectura antes de guardar...
                    </div>
                    <div class="alert alert-danger alert-dismissable" id="errorEditLectura2" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Ingrese un valor de lectura correcto...
                    </div>
                    <div class="alert alert-danger alert-dismissable" id="errorEditObservacion" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Por favor ingrese una nueva observacion antes de guardar...
                    </div>
                    <form class="form-horizontal">
                        <div class="form-group col-sm-12">
                            <label for="lecturaEdit" class="col-sm-2 control-label">Nueva Lectura</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="lecturaEdit"
                                    placeholder="Ingrese nueva lectura...">
                                <input type="hidden" class="form-control" id="idLecturaEdit">
                                <span>*ingrese un valor de lectura entre el anterior y el siguente valor (si existen)</span>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="observacionEdit" class="col-sm-2 control-label">Nueva observacion</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="observacionEdit"
                                    placeholder="Ingrese nueva observacion...">
                                <input type="hidden" class="form-control" id="idObservacionEdit">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btnAgregarEditar"
                        onclick="guardarEdit()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Modal Edicion de Lecturas -->

<!-- Modal criticidad-->
<div class="modal" id="modalcrit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Sector </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Criticidad <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="de" name="de" placeholder="Ingrese criticidad" class="form-control" />
                    </div>
                </div>
            </div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardarcri()">Guardar</button>
            </div> <!-- /.modal footer -->

        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal area-->
<div class="modal" id="modalarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Área </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Nombre de Área: <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="nomarea" name="nomarea" placeholder="Ingrese Nombre o Descripción"
                            class="form-control input-md" size="30" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardararea()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Proceso-->
<div class="modal" id="modalproceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Proceso </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Nombre de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="nomproceso" name="nomproceso" placeholder="Ingrese Nombre o Descripción"
                            class="form-control input-md" size="30" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardarproceso()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Etapa-->
<div class="modal" id="modaletapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Sector/Etapa de Proceso </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Nombre de Sector/Etapa de Proceso: <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="nometapa" name="nometapa" placeholder="Ingrese Nombre o Descripcion"
                            class="form-control input-md" size="30" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardaretapa()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Grupo-->
<div class="modal" id="modalgrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Grupo </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Nombre de Grupo: <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="nomgrupo" name="nomgrupo" placeholder="Ingrese Nombre o Descripción"
                            class="form-control input-md" size="30" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardargrupo()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Marca-->
<div class="modal" id="modalMarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="modalAction"
                        class="fa fa-plus-square text-light-blue"></span> Agregar Marca </h4>
            </div> <!-- /.modal-header  -->

            <div class="modal-body" id="modalBodyArticle">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Nombre de Marca: <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" id="nombreMarca" name="nombreMarca"
                            placeholder="Ingrese Nombre o Descripción" class="form-control input-md" size="30" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="guardarmarca()">Guardar</button>
            </div> <!-- /.modal footer -->
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
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
                        <input type="text" class="form-control" id="cliName">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Apellido <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" class="form-control" id="cliLastName">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Dni <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" class="form-control" id="cliDni">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Direccion <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" class="form-control" id="cliAddress">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Telefono <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" class="form-control" id="cliPhone">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Email <strong style="color: #dd4b39">*</strong>: </label>
                    </div>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" id="cliEmail">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-xs-12">
                        <label style="margin-top: 7px;">Razon Social <strong style="color: #dd4b39">*</strong>: </label>
                        <input type="text" class="form-control" id="cliRazonSocial">
                    </div>
                </div><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal -->

<!-- Modal Eliminar Adjunto -->
<div class="modal" id="modalEliminarAdjunto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar</h4>
            </div>
            <form id="formEliminarAdjunto">
                <div class="modal-body">
                    <input type="hidden" id="idEliminaEquipo" name="idEliminaEquipo">
                    <input type="hidden" id="idEliminaAdjunto" name="idEliminaAdjunto">

                    <h4>¿Desea eliminar Archivo Adjunto?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Eliminar</button>
                </div>
            </form>

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

            <form id="formAgregarAdjunto" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Seleccione un Archivo Adjunto
                    </div>
                    <input type="hidden" id="idAgregaAdjunto" name="idAgregaAdjunto">
                    <input id="inputPDF" name="inputPDF[]" type="file" class="form-control input-md" multiple>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btnAgregarEditar">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Editar adjunto -->
<div class="modal" id="modalEditarAdjunto">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span class="fa fa-fw fa-plus-square text-light-blue"></span> Editar</h4>
            </div>

            <form id="formEditarAdjunto" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        Seleccione un Archivo Adjunto
                    </div>
                    <input type="hidden" id="idEquipo" name="idEquipo">
                    <input type="hidden" id="id_adjunto" name="id_adjunto">
                    <input id="inputEditarPDF" name="inputEditarPDF" type="file" class="form-control input-md">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btnAgregarEditar">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal area-->
<div class="modal" id="modalFormulario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span class="fa fa-fw fa-plus-square text-light-blue"></span> Editar</h4>
            </div>
            <div class="modal-body">
    
             <?php
                /* detectarForm();
                initForm(); */
                echo "<a class='frm-open' id='form' data-readonly='true' href='#' data-info='38'><i class='fa fa-paperclip'></i></a>";
             ?>
            </div>

    
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->
<!-- / Modal -->

<?php
 $modal = new StdClass();
 $modal->id = 'asignar_meta';
 $modal->titulo = 'Equipos | Asignar Meta';
 $modal->icono = 'fa fa-bar-chart';
 $modal->body = "<div class='form-group'>
                    <input class='form-control' type='hidden' id='eq_modal_meta' name='eq_modal_meta'>
                    <input class='form-control' type='number' min='1' max='100' name='meta_modal_eq' id='meta_modal_eq'>
                    <a href='#' class='flt-clear pull-right text-red'>
                    <small><i class='fa fa-times'></i> Borrar Meta</small>
                    </a>
                </div>";
 $modal->accion = 'Guardar';
 $modal->tam ='sm';
 echo modal($modal);
?>

<script>
var equipo = null;
//ASIGNAR META
function asignar_meta(meta,eq){
    wo();
    //Datos de la metas
    $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: '<?php echo MAN;?>Equipo/getMeta',
        data: {eq},
        success: function(rsp) {
            wc();
            console.log(rsp);
            $('#eq_modal_meta').val(eq);
            $('#meta_modal_eq').val(rsp);            
            $('#asignar_meta').modal('show');
        },
        error: function(rsp) {
            alert('Error: ' + rsp.msj);
            console.log(rsp.msj);
        }
    });
};

function guardarMeta(){

    console.log('Meta Ready!');

    $('#asignar_meta').modal('show');
    $('#content').empty();            
    $("#content").load("<?php echo base_url(); ?>index.php/Equipo/index/<?php echo $permission; ?>");
}

$('#asignar_meta .btn-accion').click(function(event) {
    
    var eq = $('#eq_modal_meta').val();
    var meta = $('#meta_modal_eq').val();
    debugger;
    if (meta == null || meta == '') {
        error("Error",'No se ingreso ningún valor.');
        return;
    }
    if (meta <= 0 || meta > 100) {
        error("Error",'Solo valores del 1 al 100');
        return;
    }

    if(eq == null || eq == ''){
      error("Error",'Equipo Inválido');
      return;
    }
    wo();
    $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: '<?php echo MAN; ?>Equipo/asignarMeta',
        data: {eq, meta},
        success: function(rsp) {
            hecho();
            /*equipo.dataset.meta = meta;*/
            /*guardarMeta();*/
            $('#asignar_meta').modal('hide');
            //alert('Meta asignada correctamente.');
        },
        error: function(rsp) {
            error();
            console.log(rsp);
        },
        complete: function(rsp) {
            wc();
        }
    });

});

/// FUNCION QUE VALIDA QUE EL CAMPO SEA SOLO NUMEROS
function validaNum(e) {
        e = (e) ? e : window.event;
        var code = (e.which) ? e.which : e.keyCode;
        if ( (code > 31 && code < 48) || code > 57) {
            return false;
        }
        return true;
    }



function mantenimientoAutonomo(id,deeq, form_id){
   

    $(".clear").val(""); //llimpia los inputs del modal lectura
    $("#spanNuevaLectura").text("");
    $('#errorLectura').fadeOut(0);
    $('#errorLectura2').fadeOut(0);
    $("#modalectura").modal('show');
    $('#id_maquina').val(id);
    $('#maquina').val(deeq);
    $('#form_id').val(form_id)
    $('#formulario').attr('data-form', form_id);
    detectarFormV2();
    initForm();
    //debugger;
    $.ajax({
        data: {
            idEquipo: id
        },
        dataType: 'json',
        type: 'POST',
        url: 'index.php/Equipo/getEqPorId',
        success: function(data) {
            console.table(data);
            $("#spanNuevaLectura").text(data[0].lectura);
            estBoton(data[0].estado); //agrega boton de estados
												//reloadTable();
        },
        error: function(result) {
            console.log(result);
        },
    });
}
</script>