<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Backlog</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                <?php
                    if (strpos($permission,'Add') !== false) {
                        echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;"id="btnAgre">Agregar</button>';
                    }
                ?>
            </div>
        </div>
        <table id="sales" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Nº Backlog</th>
                    <th>Equipo</th>
                    <th>Componente</th>
                    <th>Sistema</th>
                    <th>Tarea</th>
                    <th>Fecha</th>
                    <th>Duración</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php      
                //dump($list, 'lista backlog');
                foreach($list['data'] as $a){
                    //if ($a['estado'] == 'C') {
                    $id  = $a['backId'];
                    $ide = $a['id_equipo'];
                    echo '<tr id="'.$id.'" class="'.$ide.'">';
                        echo '<td>';                     
                        if (strpos($permission,'Add') !== false) {
                            echo '<i class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar" data-toggle="modal" data-target="#modalaviso"></i>';
                            
                            if( ($a['estado'] == 'S') || ($a['estado'] == 'C') ){
                            echo '<i class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar" ></i>';
                            }   
                            if ($a['back_adjunto']) {
                            echo '<a href="'.base_url().'assets/filesbacklog/'.$a['back_adjunto'].'" target="_blank"><i class="fa fa-file-pdf-o text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Ver Archivo"></i></a>';
                            }                          
                        }                     
                        echo '</td>';
                        echo '<td>'.$a['backId'].'</td>';
                        echo '<td>'.$a['codigo'].'</td>';
                        echo '<td>'.$a['componente'].'</td>';
                        echo '<td>'.$a['sistema'].'</td>';
                        
                        if ($a["id_tarea"] <= 0) {
                        echo '<td>'.$a["tarea_opcional"].'</td>';
                        } else {
                        echo '<td>'.$a['de1'].'</td>';
                        }                     
                        echo '<td data-sort="'.strtotime($a['fecha']).'">'.date_format(date_create($a['fecha']), 'd-m-Y').'</td>';
                        //echo '<td>'.$a['horash'].'</td>'; 
                        switch ($a['id_unidad']) {
                        case '2':
                        echo '<td>'.$a['back_duracion'].' horas</td>';
                            break;
                        case '3':
                        echo '<td>'.$a['back_duracion'].' dias</td>';
                            break;                                              
                        default:
                        echo '<td>'.$a['back_duracion'].' min</td>';
                            break;
                        }    
                        
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
                    echo '</tr>';
                    //}                    
                }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<script>
var gloid = "";

$(document).ready(function(event) {
    edit = 0;
    datos = Array();
    $('#btnAgre').click(function cargarVista() {
        wo();
        $('#content').empty();
        $("#content").load(
            "<?php echo MAN; ?>Backlog/cargarback/<?php echo $permission; ?>", function() {
                wc();
            });
    });

  //Eliminar backlog - Chequeado
    $(".fa-times-circle").click(function(e) {
        var idpre = $(this).parent('td').parent('tr').attr('id');
        console.log("ESTOY ELIMINANDO , el id de back es:");
        console.log(idpre);
        gloid = idpre;
    }); 
    //Editar - Busca los datos de backlog por id - Chequeado
    $(".fa-pencil").click(function(e) {

        $('#limpiar').val(''); // limpia los inputs del modal editar 
        $('#modalSale').modal('show'); // levanta el modal 

        var idpred = $(this).parent('td').parent('tr').attr('id'); // id de backlog
        var ide = $(this).parent('td').parent('tr').attr('class'); // ide de equipo
        console.log("Id de Backlog: " + idpred);
        $('#id_backlog').val(idpred);
        datos = parseInt(ide); //parsea a int para sacar el resto de las clases.    

        $.ajax({
            type: 'GET',
            data: {
                idpred: idpred,
                datos: datos
            },
            url: 'index.php/Backlog/geteditar',
            success: function(data) {
                console.table(data);
                datos = {
                    'codigo': data['equipo'][0]['codigo'],
                    'marca': data['equipo'][0]['marca'],
                    'descripcion': data['equipo'][0]['des'],
                    'fecha_ingreso': data['equipo'][0]['fecha_ingreso'],

                    'desta': data['equipo'][0]['de1'],
                    'hora': data['equipo'][0]['horash'],
                    'ubicacion': data['equipo'][0]['ubicacion'],
                    'sistema': data['equipo'][0]['sistema'],
                    'componente': data['equipo'][0]['componente'],
                    'codcompeq': data['equipo'][0]['codcompeq'],

                    'backId': idpred,
                    'idtarea': data['datos'][0]['id_tarea'],
                    'tarea': data['datos'][0]['tareadescrip'],
                    'fecha': data['datos'][0]['fecha'],
                    'duracion': data['datos'][0]['back_duracion'],
                    'unidtiempo': data['datos'][0]['id_unidad'],
                    'operarios': data['datos'][0]['back_canth'],
                    'hh': data['datos'][0]['horash'],
                    'back_adjunto': data['datos'][0]['back_adjunto'],
                    'tarea_opcional': data['datos'][0]['tarea_opcional']
                };

                console.table(datos);


                var herram = data['herramientas'];
                var insum = data['insumos'];
                completarEdit(datos, herram, insum);
            },
            error: function(result) {
                console.log(result);
            },
            dataType: 'json'
        });
    });

    $('#sales').DataTable({
        <?php echo (!DT_SIZE_ROWS ? '"paging": false,' : null) ?>

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
            [6, "asc"]
        ]
    });

});

//Eliminar - Chequeado     
function eliminarpred() {
   
    var idpre = $(this).parent('td').parent('tr').attr('id');
    $.ajax({
        type: 'POST',
        data: {
            gloid: gloid
        },
        url: 'index.php/Backlog/baja_backlog_estado_Borrado',
        success: function(data) {

            Refrescar1();
        },
        error: function(result) {
            alert('Error en la operación eliminar...');
            console.log(result);
        },
        dataType: 'json'
    });
}

// Completa los campos con datos de backlpog - Chequeado
function completarEdit(datos, herram, insum) {

    // var fecha= new Date(datos['fecha']);
    // var day = getFormattedPartTime(fecha.getDate() + 1);
    // var month = getFormattedPartTime(fecha.getMonth() + 1);
    // var year = fecha.getUTCFullYear();
    // var fechater = day + '-' + (month) + '-' + year ;

    var fecha = datos['fecha'];

    $("#equipo").val(datos['codigo']);
    $("#id_backlog").val(datos['backId']);
    $('#fecha_ingreso').val(datos['fecha_ingreso']);
    $('#marca').val(datos['marca']);
    $('#ubicacion').val(datos['ubicacion']);
    $('#descripcion').val(datos['descripcion']);

    $('#tarea').val(datos['tarea']);
    $('#idtarea').val(datos['idtarea']);

    $('#tareaOpcional').val(datos['tarea_opcional']);


    $('#fecha').val(fecha);
    $('#periodo').val(datos['periodo']);
    $('#horash').val(datos['duracion']);
    $('#sistema_componente').val(datos['sistema']);
    $('#descrip_componente').val(datos['componente']);
    $('#codigo_componente').val(datos['codcompeq']);

    $('#cantidad').val(datos['cantidad']);
    $('#duracion').val(datos['duracion']);
    $('#unidad').val(datos['unidtiempo']);
    $('#cantOper').val(datos['operarios']);
    $('#hshombre').val(datos['hh']);



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

    recargaTablaAdjunto(datos['back_adjunto']);
    $(document).on("click", ".elirow", function() {
        var parent = $(this).closest('tr');
        $(parent).remove();
    });

}

function getFormattedPartTime(partTime) {

    if (partTime < 10)
        return "0" + partTime;
    return partTime;
}

// Guarda Backlog Editado - Chequeado
function guardar() {

    var backid = $('#id_backlog').val(); //
    var tarea = $('#idtarea').val(); //
    var fecha = $('#fecha').val(); //
    var hshombre = $('#hshombre').val();
    var duracion = $('#duracion').val();
    var id_unidad = $('#unidad').val();
    var back_canth = $('#cantOper').val();
    var tareaOpcional = $('#tareaOpcional').val();

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

    if ((fecha !== '') || (duracion !== '') || (tarea > 0)) {

        WaitingOpen("Guardando");
        $.ajax({
            type: 'POST',
            data: {
                backid: backid,
                tarea: tarea,
                fecha: fecha,
                duracion: duracion,
                id_unidad: id_unidad,
                hshombre: hshombre,
                back_canth: back_canth,
                idsherramienta: idsherramienta,
                cantHerram: cantHerram,
                idsinsumo: idsinsumo,
                cantInsum: cantInsum,
                tareaOpcional: tareaOpcional,
                tipo: 'edit'
            },
            url: 'index.php/Backlog/editar_backlog',
            success: function(data) {
                WaitingClose();
                $('#modalSale').modal('hide');
                if (data['status']) {
                    Refrescar();
                } else {
                    alert(data['msj']);
                }
            },
            error: function(result) {
                WaitingClose();
                $('#modalSale').modal('hide');
                console.log(result);
                console.log("Entre por el error");
            },
            dataType: 'json'
        });
    } else {
        var hayError = true;
        $('#error').fadeIn('slow');
        return;
    }
}

// trae equipo para llenar select en edicion
traer_equipo();

function traer_equipo() {
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Backlog/getequipo',
        success: function(data) {
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['codigo'];
                var opcion = "<option value='" + data[i]['id_equipo'] + "'>" + nombre + "</option>";
                $('#equipo').append(opcion);
            }
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
}

// Trae tareas par  autocompletar, limpia uno u otra tarea 

// limpia un input al seleccionar o llenar otro
$('#tarea').change(function() {
    $('#tareaOpcional').val('');
});
$('#tareaOpcional').change(function() {
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
            'url': '<?php echo MAN;?>Preventivo/gettarea',
            success: function(data) {
                tmp = data;
            },
            error: function(result) {
                error('Error','Error al traer tareas');
                console.log(result);
            }
        });
    return tmp;
}();
$("#tarea").autocomplete({
    source: dataTarea,
    delay: 500,
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
// Trae unidades de tiempo - Chequeado
$(function() {
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Preventivo/getUnidTiempo',
        success: function(data) {
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#unidad').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['unidaddescrip'];
                var opcion = "<option value='" + data[i]['id_unidad'] + "'>" + nombre + "</option>";
                $('#unidad').append(opcion);
            }
        },
        error: function(result) {
            error('Error','Error al traer unidades de tiempo');
            console.log(result);
        },
        dataType: 'json'
    });
});

// Calcula horas hombre por tiempo y unidades - Chequeado
function calcularHsHombre() {

    var entrada = $('#duracion').val();
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

    hsHombre = parseFloat(hs * operarios);
    $('#hshombre').val(hsHombre);

}

// Calcula hs hombre si están los 3 parametros y cambia alguno de ellos
$('#duracion, #unidad, #cantOper').change(function() {
    if ($('#duracion').val() != "" && $('#unidad').val() != "-1" && $('#cantOper').val() != "")
        calcularHsHombre();
    //calcDuracionBack(); 
});

//Trae herramientas
$(function() {
    $('#herramienta').html("");
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Preventivo/getherramienta',
        success: function(data) {
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#herramienta').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['herrcodigo'];
                var opcion = "<option value='" + data[i]['herrId'] + "'>" + nombre + "</option>";
                $('#herramienta').append(opcion);
            }
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
});
$("#herramienta").change(function() {
    var id_herramienta = $(this).val();
    console.log("El id de la herramienta que seleccione es:");
    console.log(id_herramienta);
    codhermglo = id_herramienta;
    $.ajax({
        type: 'POST',
        data: {
            id_herramienta: id_herramienta
        },
        url: 'index.php/Preventivo/getdatos', //index.php/
        success: function(data) {

            console.log(data);
            var marca = data[0]['herrmarca'];
            $('#marcaherram').val(marca);
            var des = data[0]['herrdescrip'];
            $('#descripcionherram').val(des);
            var codigo = data[0]['herrcodigo'];
        },

        error: function(result) {

            console.log(result);
        },
        dataType: 'json'
    });
});
var cod = "";
$("#agregarherr").click(function(e) {

    var id_herramienta = $("#herramienta").val(codhermglo);
    var id_her = codhermglo;
    var id_herramienta1 = $("#herramienta").val();
    console.log("herramienta de prueba :" + id_herramienta1);

    var $herramienta = $("select#herramienta option:selected").html();
    var marcaherram = $('#marcaherram').val();
    var descripcionherram = $('#descripcionherram').val();
    var cantidadherram = $('#cantidadherram').val();

    var tr = "<tr id='" + id_her + "'>" +
        "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
        "<td>" + $herramienta + "</td>" +
        "<td>" + marcaherram + "</td>" +
        "<td>" + descripcionherram + "</td>" +
        "<td>" + cantidadherram + "</td>" +
        "</tr>";
    console.log(tr);
    $('#tablaherramienta tbody').append(tr);

    $(document).on("click", ".elirow", function() {
        var parent = $(this).closest('tr');
        $(parent).remove();
    });

    $('#herramienta').val('');
    $('#marcaherram').val('');
    $('#descripcionherram').val('');
    $('#cantidadherram').val('');
});


// trae insumos
$(function() {
    $('#insumo').html("");
    $.ajax({
        type: 'POST',
        data: {},
        url: '<?php echo MAN; ?>Preventivo/getinsumo',
        success: function(data) {
            var opcion = "<option value='-1'>Seleccione...</option>";
            $('#insumo').append(opcion);
            for (var i = 0; i < data.length; i++) {
                var nombre = data[i]['codigo'];
                var opcion = "<option value='" + data[i]['value'] + "'>" + nombre + "</option>";
                $('#insumo').append(opcion);
            }
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
});

$("#insumo").change(function() {

    var id_insumo = $(this).val();
    codinsumolo = id_insumo;
    console.log("El id de insumo que seleccione es:");
    console.log(id_insumo);
    console.log(codinsumolo);
    $.ajax({
        type: 'POST',
        data: {
            id_insumo: id_insumo
        },
        url: 'index.php/Preventivo/getinsumo', //index.php/
        success: function(data) {
            console.log(data);
            var d = data[0]['label'];
            $('#descript').val(d);
            var insumo = data[0]['value'];
        },
        error: function(result) {
            console.log(result);
        },
        dataType: 'json'
    });
});
$("#agregarins").click(function(e) {

    var id_in = $('#insumo').val();
    //alert(id_in);
    var $insumo = $("select#insumo option:selected").html();
    var descript = $('#descript').val();
    var cant = $('#cant').val();
    var tr = "<tr id='" + id_in + "'>" +
        "<td ><i class='fa fa-ban elirow' style='color: #f39c12'; cursor: 'pointer'></i></td>" +
        "<td>" + $insumo + "</td>" +
        "<td>" + descript + "</td>" +
        "<td>" + cant + "</td>" +
        "</tr>";
    $('#tablainsumo tbody').append(tr);
    $(document).on("click", ".elirow", function() {
        var parent = $(this).closest('tr');
        $(parent).remove();
    });
    $('#insumo').val('');
    $('#descript').val('');
    $('#cant').val('');
});



function Refrescar() {

    $('#content').empty();
    $('#modalSale').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Backlog/index/<?php echo $permission; ?>");
    WaitingClose();
    WaitingClose();
}

function Refrescar1() {

    $('#content').empty();
    // $('#modalSale').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Backlog/index/<?php echo $permission; ?>");
    // WaitingClose();
    WaitingClose();
}

//$("#fecha").datepicker(); 

// cargo plugin DateTimePicker
$('#fecha').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    locale: 'es',
});


//////////////////////////////////////////////////////////////////////////////////


//abrir modal eliminar adjunto
$(document).on("click", ".eliminaAdjunto", function() {
    $('#modalEliminarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
    $('#idAdjunto').val(idprev);
});
//eliminar adjunto
function eliminarAdjunto() {
    $('#modalEliminarAdjunto').modal('hide');
    var idprev = $('#idAdjunto').val();
    $.ajax({
            data: {
                idprev: idprev
            },
            dataType: 'json',
            type: 'POST',
            url: 'index.php/Preventivo/eliminarAdjunto',
        })
        .done(function(data) {
            //console.table(data); 
            let prevAdjunto = '';
            recargaTablaAdjunto(prevAdjunto);
        })
        .error(function(result) {
            console.error(result);
        });
}

//abrir modal agregar adjunto
$(document).on("click", ".agregaAdjunto", function() {
    $('#btnAgregarEditar').text("Agregar");
    $('#modalAgregarAdjunto .modal-title').html(
        '<span class="fa fa-fw fa-plus-square text-light-blue"></span> Agregar');

    $('#modalAgregarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
    $('#idAgregaAdjunto').val(idprev);
});
//abrir modal editar adjunto
$(document).on("click", ".editaAdjunto", function() {
    $('#btnAgregarEditar').text("Editar");
    $('#modalAgregarAdjunto .modal-title').html(
        '<span class="fa fa-fw fa-pencil text-light-blue"></span> Editar');

    $('#modalAgregarAdjunto').modal('show');
    var idprev = $('#id_backlog').val();
    $('#idAgregaAdjunto').val(idprev);
});
//eliminar adjunto
$("#formAgregarAdjunto").submit(function(event) {
    $('#modalAgregarAdjunto').modal('hide');

    event.preventDefault();
    if (document.getElementById("inputPDF").files.length == 0) {
        $('#error').fadeIn('slow');
    } else {
        $('#error').fadeOut('slow');
        var formData = new FormData($("#formAgregarAdjunto")[0]);
        //debugger
        $.ajax({
                cache: false,
                contentType: false,
                data: formData,
                dataType: 'json',
                processData: false,
                type: 'POST',
                url: 'index.php/Backlog/agregarAdjunto',
            })
            .done(function(data) {
                console.info("adjunto completo : " + data);
                recargaTablaAdjunto(data['back_adjunto']);
            })
            .error(function(result) {
                console.error(result);
            });
    }
});

function recargaTablaAdjunto(backAdjunto) {
    console.info("adjunto: " + backAdjunto);
    $('#adjunto').text(backAdjunto);
    $('#adjunto').attr('href', 'assets/filesbacklog/' + backAdjunto);
    if (backAdjunto == null || backAdjunto == '') {
        var accion =
            '<i class="fa fa-plus-square agregaAdjunto text-light-blue" style="color:#f39c12; cursor:pointer; margin-right:10px" title="Agregar Adjunto"></i>';
    } else {
        var accion =
            '<i class="fa fa-times-circle eliminaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Eliminar Adjunto"></i>' +
            '<i class="fa fa-pencil editaAdjunto text-light-blue" style="cursor:pointer; margin-right:10px" title="Editar Adjunto"></i>';
    }
    $('#accionAdjunto').html(accion);
}
</script>
<!-- Modal Precaucion Eliminar -->
<div class="modal" id="modalaviso">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="fa fa-fw fa-times-circle" style="color:#A4A4A4"></span>
                        Eliminar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <center>
                        <h4>
                            <p>¿ DESEA ELIMINAR BACKLOG ?</p>
                        </h4>
                    </center>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"
                            onclick="eliminarpred()">SI</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
                    </center>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Editar -->
<div class="modal fade" id="modalSale" tabindex="2000" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="fa fa-fw fa-pencil"></span> Backlog</h4>
            </div>

            <div class="modal-body" id="modalBodySale">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><span class="fa fa-cogs"></span> Datos del Equipo</h3>

                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <label for="equipo">Equipos <strong style="color: #dd4b39">*</strong></label>
                                <!-- <select id="equipo" name="equipo" value="" class="form-control" ></select> -->
                                <input id="equipo" name="equipo" value="" class="form-control limpiar" disabled>
                                <input type="hidden" id="id_backlog" name="id_backlog">
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <label for="fecha_ingreso">Fecha:</label>
                                <input type="text" id="fecha_ingreso" name="fecha_ingreso"
                                    class="form-control limpiar input-md" disabled />
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <label for="marca">Marca:</label>
                                <input type="text" id="marca" name="marca" class="form-control limpiar input-md"
                                    disabled />
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <label for="ubicacion">Ubicacion:</label>
                                <input type="text" id="ubicacion" name="ubicacion" class="form-control limpiar input-md"
                                    disabled />
                            </div>

                            <div class="col-xs-12">
                                <label for="descripcion">Descripcion: </label>
                                <textarea class="form-control limpiar" id="descripcion" name="descripcion"
                                    disabled></textarea>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="codigo_componente">Código de componente-equipo :</label>
                                <input type="text" id="codigo_componente" name="codigo_componente"
                                    class="form-control input-md" placeholder="Ingrese código de componente" disabled />
                                <input type="hidden" id="idcomponenteequipo" name="idcomponenteequipo" value="" />
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="descrip_componente">Descripción de componente:</label>
                                <input type="text" id="descrip_componente" name="descrip_componente"
                                    class="form-control input-md" disabled />
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="sistema_componente">Sistema:</label>
                                <input type="text" id="sistema_componente" name="sistema_componente"
                                    class="form-control input-md" disabled />
                            </div>

                        </div><!-- /.row -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Programación</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <label for="tarea">Tarea Estandar<strong style="color: #dd4b39">*</strong>:</label>
                                <input type="text" class="form-control limpiar" id="tarea" name="tarea"
                                    placeholder="Busque Tarea..." />
                                <input type="hidden" class="form-control limpiar" id="idtarea" name="idtarea" />
                                <!-- <select id="tarea" name="tarea" value="" class="form-control limpiar" > -->
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <label for="tarea">Tarea Personalizada<strong style="color: #dd4b39">*</strong>:</label>
                                <input type="text" class="form-control" id="tareaOpcional" name="tareaOpcional" value=""
                                    placeholder="Ingrese Tarea..." />
                                <!-- <select id="tarea" id="tareaOpcional" name="tareaOpcional" placeholder="Ingrese Tarea..." value="" class="form-control limpiar" >
                </select>                               -->
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="vfecha">Fecha Creación:</label>
                                <!-- <input type="text" class="datepicker  form-control limpiar fecha" id="fecha" name="vfecha" value="<?php ///echo date_format(date_create(date("Y-m-d H:i:s")), 'd-m-Y H:i:s') ; ?>" size="27"/>  -->
                                <input type="text" class="form-control limpiar fecha" id="fecha" name="vfecha"
                                    size="27" />
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="duracion">Duración Estandar <strong
                                        style="color: #dd4b39">*</strong>:</label>
                                <input type="text" class="form-control" id="duracion" name="duracion" />
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="unidad">U. de tiempo <strong style="color: #dd4b39">*</strong></label>
                                <select id="unidad" name="unidad" class="form-control" />
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="cantOper">Cant. Operarios <strong style="color: #dd4b39">*</strong>:</label>
                                <input type="text" class="form-control" id="cantOper" name="cantOper" />
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <label for="">Horas Hombre <strong style="color: #dd4b39">*</strong>:</label>
                                <input type="text" class="form-control" name="hshombre" id="hshombre" disabled>
                            </div>


                        </div><!-- /.row -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->

                <!-- Tab nav -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#herramin" aria-controls="profile" role="tab"
                                data-toggle="tab">Herramientas</a></li>
                        <li role="presentation"><a href="#insum" aria-controls="messages" role="tab"
                                data-toggle="tab">Insumos</a></li>
                        <li role="presentation"><a href="#TabAdjunto" aria-controls="home" role="tab"
                                data-toggle="tab">Adjunto</a></li>
                    </ul>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="herramin">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="herramienta">Codigo <strong
                                                style="color: #dd4b39">*</strong>:</label>
                                        <select id="herramienta" name="herramienta" class="form-control input-md"
                                            value=""></select>
                                        <input type="hidden" id="id_herramienta" name="id_herramienta">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="marcaherram">Marca:</label>
                                        <input type="text" id="marcaherram" name="marcaherram"
                                            class="form-control input-md" />
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="descripcionherram">Descripcion:</label>
                                        <input type="text" id="descripcionherram" name="descripcionherram"
                                            class="form-control input-md" />
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="cantidadherram">Cantidad <strong
                                                style="color: #dd4b39">*</strong>:</label>
                                        <input type="text" id="cantidadherram" name="cantidadherram"
                                            class="form-control input-md" placeholder="Ingrese Cantidad" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <br>
                                        <button type="button" class="btn btn-primary" id="agregarherr"><i
                                                class="fa fa-check">Agregar</i></button>
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
                                        <select id="insumo" name="insumo" class="form-control input-md"
                                            value=""></select>
                                        <!-- <input type="hidden" id="id_insumo" name="id_insumo"> -->
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="descript">Descripcion:</label>
                                        <input type="text" id="descript" name="descript"
                                            class="form-control input-md" />
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <label for="cant">Cantidad:</label>
                                        <input type="text" id="cant" name="cant" class="form-control input-md"
                                            placeholder="Ingrese Cantidad" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <br>
                                        <button type="button" class="btn btn-primary" id="agregarins"><i
                                                class="fa fa-check">Agregar</i></button>
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
                    </div>
                    <!--cierre div insum-->

                    <div role="tabpanel" class="tab-pane" id="TabAdjunto">
                        <div class="row">
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

                    </div>
                    <!--cierre de TabAdjunto-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="reset" data-dismiss="modal"
                            onclick="guardar()">Guardar</button>
                    </div>

                </div>
            </div>
        </div>
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
    <!--------------/ MODALES ADJUNTO ------------->