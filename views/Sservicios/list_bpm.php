<!-- <section class="content"> -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title">Solicitudes de Servicios</h4>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalservicio" id="btnAdd">Agregar</button>
                </div>
                <div class="col-md-5">
                    <div id="botonToggleOnOff" style="text-align: center;" class="form-group">
                        <div class="form-check">
                            <label class="checkboxtext">Mostrar solicitudes conformes</label>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="check-conformes" onclick="showSolicitudesConformes()">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="box-body table-scroll table-responsive">
                <table id="servicio" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">Acciones</th>
                            <th>Nro</th>
                            <th>Fecha</th>
                            <th>Fecha Fin</th>
                            <th>T. de ciclo</th>
                            <th>T. Asignación</th>
                            <th>T. Generación</th>
                            <th>Solicitante</th>
                            <th>Equipo</th>
                            <th>Sector</th>
                            <th>Grupo</th>
                            <th>Causa</th>
                            <th>Mantenedor</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($list)){
                        foreach($list as $s){
                            
                            $fecTerminada = $s['fecha_terminada'];

                            echo "<tr id=" . $s["id_solicitud"] . " class='" . $s['id_equipo'] . "' data-json='" . json_encode($rsp) . "'>";

                            echo "<td class='text-center text-light-blue'>";
                            echo '<i class="fa fa-search"  style="cursor: pointer;margin: 3px;" title="Ver solicitud" onclick="mostrarOT(this)"></i>';
                            echo "</td>";
                            echo '<td class="text-center">'.$s['id_solicitud'].'</td>';
                            echo '<td class="text-center">'.$s['f_solicitado'].'</td>';
                            //fecha fin
                            if ( ($fecTerminada == '0000-00-00 00:00:00') || ($fecTerminada == '') ) {
                                echo '<td class="text-center"> S/Fecha</td>';
                            } else {
                                echo '<td class="text-center">'.$fecTerminada.'</td>';
                            }
                            //tiempo de ciclo
                            if(!is_null($s['f_asignacion']) && !is_null($s['f_inicio']) && $s['f_inicio'] != '0000-00-00 00:00:00'){
                                $f_asignacion = new DateTime($s['f_asignacion']);
                                $f_solicitado = new DateTime($s['f_solicitado']);
                                $intervalo = $f_solicitado->diff($f_asignacion);
                                $horas = $intervalo->format('%h');
                                $minutos = $intervalo->format('%i');
                                $minutos = str_pad($minutos, 2, '0', STR_PAD_LEFT);
                                $t_genracion = "$horas:$minutos";                          
                                echo '<td class="text-center">'.$t_genracion.'</td>';
                            }else{
                                echo '<td class="text-center">S/Datos</td>';
                            }
                            //tiempo asignacion
                            if(!is_null($f['f_asignacion']) && !is_null($f['f_inicio']) && $f['f_inicio'] != '0000-00-00 00:00:00'){
                                $f_asignacion = new DateTime($s['f_asignacion']);
                                $f_solicitado = new DateTime($s['f_solicitado']);
                                $intervalo = $f_solicitado->diff($f_asignacion);
                                $horas = $intervalo->format('%h');
                                $minutos = $intervalo->format('%i');
                                $minutos = str_pad($minutos, 2, '0', STR_PAD_LEFT);
                                $t_genracion = "$horas:$minutos";                          
                                echo '<td class="text-center">'.$t_genracion.'</td>';
                            }else{
                                echo '<td class="text-center">S/Datos</td>';
                            }
                            if(!is_null($f['f_asignacion']) && !is_null($f['f_inicio']) && $f['f_inicio'] != '0000-00-00 00:00:00'){
                                $f_asignacion = new DateTime($f['f_asignacion']);
                                $f_solicitado = new DateTime($f['f_solicitado']);
                                $intervalo = $f_solicitado->diff($f_asignacion);
                                $horas = $intervalo->format('%h');
                                $minutos = $intervalo->format('%i');
                                // Rellena los minutos con ceros a la izquierda si es necesario
                                $minutos = str_pad($minutos, 2, '0', STR_PAD_LEFT);
                                $t_genracion = "$horas:$minutos";                          
                                echo '<td class="text-center">'.$t_genracion.'</td>';
                            }else{
                                echo '<td class="text-center">S/Datos</td>';
                            }
                            echo '<td class="text-center">'.$s['solicitante'].'</td>';
                            echo '<td class="text-center">'.$s['equipo'].'</td>';
                            echo '<td class="text-center">'.$s['sector'].'</td>';
                            echo '<td class="text-center">'.$s['grupo'].'</td>';  
                            echo '<td class="text-center">'.$s['causa'].'</td>';
                            echo '<td class="text-center">'.$s['mantenedor'].'</td>';
                            switch ($s['estado']) {
                                case 'S':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Solicitada" class="badge bg-red">Solicitada</span></td>';
                                    break;

                                case 'PL':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Planificada" class="badge bg-yellow">Planificada</span></td>';
                                    break;

                                case 'AS':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Asignada" class="badge bg-purple">Asignada</span></td>';
                                    break;

                                case 'C':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Curso" class="badge bg-green">Curso</span></td>';
                                    break;

                                case 'T':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Terminada" class="badge bg-blue">Terminada</span></td>';
                                    break;

                                case 'CE':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Cerrada" class="badge bg-default">Cerrada</span></td>';
                                    break;

                                case 'CN':
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="Conforme" class="badge bg-black">Conforme</span></td>';
                                    break;

                                default:
                                    echo '<td class="text-center"><span data-toggle="tooltip" title="'.$estado.'" class="btn btn-secondary">'.$estado.'</span></td>';
                                    break;
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div><!--FIN box-body table-scroll table-responsive -->
        </div><!--FIN box-body -->
    </div><!--FIN box-primary -->
<!-- </section> -->
<script>

  // VER OT 
  function mostrarOT(o){

    let idSS = $(o).closest('tr').attr('id');
    //console.log(idSS); 
    
    WaitingOpen('Obteniendo datos de OT...');
    // datos = getDataOtSolServicio(idSS, "Solicitud de Servicio");
    // fillModalViewSolServicio(datos);
    $.ajax({
        async: false,
        data: { idSS: idSS },
        dataType: 'json',
        method: 'POST',
        url: 'index.php/Sservicio/getSS',
      })
      .done( (data) => {
        // console.table(data);
        console.log(data);
        $("#vArea").val(data.AR);
        $("#vProceso").val(data.PR);
        $("#vEquipo").val(data.EQ_SEC[0].equipo);
        $("#vSector").val(data.EQ_SEC[0].sector);
        $("#vFalla").val(data.SS[0].causa);
        $("#vDescripcion").val(data.EQ_SEC[0].descripcion);
        if((data.SS[0].sol_adjunto != "")||(data.SS[0].sol_adjunto != null)){
          $("#adjunto").text("Archivo adjunto");
          $("#adjunto").attr("href", data.SS[0].sol_adjunto);

        }
        
      })
      .fail( () => alert( "Error al traer los datos de la OT." ) )
      .always( () => WaitingClose() );
    $('#verOtSolServicio').modal('show');
    WaitingClose();
  }

  // Trae datos de Solicitud de Servicios con origen Backlog
  function getDataOtSolServicio(idOt, idSolServicio, origen) {
      WaitingOpen('Cargando datos...');
      var datos = null;
      $.ajax({
        async: false,
        data: { idOt:idOt, idSolServicio:idSolServicio },
        dataType: 'json',
        method: 'POST',
        url: 'index.php/Otrabajo/getViewDataSolServicio',
      })
      .done( (data) => {
        console.table(data);
        datos = {
          //Panel datos de OT
          'id_ot'          : data['solicitud'][0]['id_orden'],
          'nro'            : data['solicitud'][0]['nro'],
          'descripcion_ot' : data['solicitud'][0]['descripcionFalla'],
          'grupo'          : data['solicitud'][0]['grupodescrip'],
          'fecha_program'  : data['solicitud'][0]['fecha_program'],
          'fecha_inicio'   : data['solicitud'][0]['fecha_inicio'],
          'fecha_terminada'  : data['solicitud'][0]['fecha_terminada'], 
          'estado'         : data['solicitud'][0]['estado'],
          'sucursal'       : data['solicitud'][0]['descripc'],
          'nombreprov'     : data['solicitud'][0]['provnombre'],
          'origen'         : origen,          
          'asignado'       : data['solicitud'][0]['usrLastName']+' '+data['solicitud'][0]['usrLastName'],
          'estado'         : data['solicitud'][0]['estado'],
          //Panel datos de equipos
          'codigo'         : data['solicitud'][0]['codigo'],
          'marca'          : data['solicitud'][0]['marca'],
          'ubicacion'      : data['solicitud'][0]['ubicacion'],
          'descripcion_eq' : data['solicitud'][0]['descripcionEquipo'],
          'comp_equipo'    : data['solicitud'][0]['compEquipo'],
          'solServicio'   : data['solicitud'][0]['solServicio']
        };
        
        
        var herram = data['herramientas'];
        var insum = data['insumos'];
        var adjunto = data['adjunto'];
         

        $('#tblherrsolicitud tbody tr').remove();
        for (var i = 0; i < herram.length; i++) {
          var tr = "<tr id='"+herram[i]['herrId']+"'>"+          
          "<td>"+herram[i]['herrcodigo']+"</td>"+
          "<td>"+herram[i]['herrmarca']+"</td>"+
          "<td>"+herram[i]['herrdescrip']+"</td>"+
          "<td>"+herram[i]['cantidad']+"</td>"+                   
          "</tr>";
          $('#tblherrsolicitud tbody').append(tr);
        }
        $('#tblinsSolicitud tbody tr').remove();
        for (var i = 0; i < insum.length; i++){                                             
          var tr = "<tr id='"+insum[i]['artId']+"'>"+
         
          "<td>"+insum[i]['artBarCode']+"</td>"+
          "<td>"+insum[i]['artDescription']+"</td>"+
          "<td>"+insum[i]['cantidad']+"</td>"+                   
          "</tr>";
          $('#tblinsSolicitud tbody').append(tr);
        }   
        recargaTablaAdjuntoSolic(adjunto);  
      })
      .fail( () => alert( "Error al traer los datos de la OT." ) )
      .always( () => WaitingClose() );
      return datos;
    }

  // Agregar SS Nueva
  $('#btnAdd').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo MAN; ?>Sservicio/nuevaSS/<?php echo $permission; ?>");
    WaitingClose();
  });
</script>

<!-- carga solicitudes inactivas -->
<script>
 $('#activarSol').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    $("#content").load("<?php echo base_url(); ?>index.php/Sservicio/get_SolicTerminada/<?php echo $permission; ?>");
    WaitingClose();
  });
</script>
<!-- / carga solicitudes inactivas -->

<script>
  // Elimina Solicitud - Chequeado
  $('.fa-times-circle').click( function eliminarSolicitud(){
    if (!confirm("Realmente desea eliminar esta Solicitud?")){
      return;
    }else{
        var id_solic = parseInt($(this).parent('td').parent('tr').attr('id'));
        
        $.ajax({
                type: 'POST',
                data: { id_solic: id_solic},
                url: 'index.php/Sservicio/elimSolicitud',
                success: function(result){
                            WaitingClose('Guardado exitosamente...');
                            //var permisos = '<?php //echo $permission; ?>';
                            var permisos = 'Add-Edit-Del-View-Asignar-Finalizar-OP-';
                            cargarView('Sservicio', 'index', permisos) ;                        
                      },
                error: function(result){
                      WaitingClose();
                      alert("Error en guardado...");
                    },
                dataType: 'json'
              });
    }
  });
  // Imprime Solicitud de Servicios - Chequeado
  $(".fa-print").click(function (e) {

    e.preventDefault();
    var idservicio = $(this).closest('tr').attr('id');
    console.log("El id de solicitud de servicio al imprimir es :");
    console.log(idservicio);

    $.ajax({
        type: 'POST',
        data: { idservicio: idservicio},
        url: 'index.php/Sservicio/getsolImp',
        success: function(data){
                    data = JSON.parse(data,true);
                    console.log(data);
                    console.log(data.datos.sec);
                    // console.log(data['f_solicitado']);
                    //alert("entre");
                    var fecha = new Date(data.datos.f_solicitado);
                    var day = fecha.getDate();
                    var month = fecha.getMonth() + 1;
                    var year = fecha.getUTCFullYear();
                    fecha = day + ' - ' + month + ' - ' + year;

                    datos={
                      'idservicio':idservicio,
                      'f_solicitado':data.datos.f_solicitado,
                      'solicitante':data.datos.solicitante,
                      'hora_sug':data.datos.hora_sug,
                      'codigo':data.datos.codigo,
                      //'descripcion':data['datos'][0]['descripcion'],
                      'ubicacion':data.datos.ubicacion,
                      'sector':data.datos.sec,
                      'grupo':data.datos.degr,
                      'causa':data.datos.causa,
                    };


                    var  texto =
                        '<div class="" id="vistaimprimir">'+
                          '<div class="container">'+
                            '<div class="thumbnail">'+

                              '<div class="caption">'+
                                '<div class="row" >'+
                                  '<div class="panel panel-default">'+
                                    '<div class="form-group">'+
                                      '<h3 class="text-center" align="center"></h3>'+
                                    '</div>'+
                                    '<hr/>'+
                                    '<div class="panel-body">'+
                                      '<div class="container">'+
                                        '<div class="thumbnail">'+
                                          '<div class="row">'+
                                            '<div class="col-sm-12 col-md-12">'+
                                              '<table width="100%" style="text-align:justify">'+
                                                '<tr>'+
                                                '<tr>'+
                                                  '<td  colspan="1"  align="left" valign="top">'+
                                                    '<div class="text-left"> <img src="img/logo.jpg" width="280" height="80" />'+
                                                     '</div>'+
                                                    '</td>'+
                                                    '<td>'+     
                                                    '<div class="col-xs-4" align="rigth">Solicitud Nº: '+datos.idservicio+
                                                      
                                                    '</div>'+

                                                    '<div class="col-xs-4">Fecha: '+fecha+
                                        
                                                    '</div>'+
                                                  '</td>'+

                                                '</tr>'+
                                                '<tr>'+
                                                  '<td >'+
                                                  '</td>'+
                                                '<tr>'+
                                                  '<td>'+
                                                  '<td/>'+
                                                  '<td height="4" colspan="4">'+
                                                    '<div class="col-xs-8">'+
                                                    '</h3>'+
                                                    '</div>'+
                                                  '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td height="4" colspan="4">'+
                                                    '<div class="col-md-3 col-md-offset-9">Solicitado :  '+
                                                    '<br>'+
                                                    '<textarea class="form-control" id="solicitante" name="solicitante" style="padding-left:15px"  value='+datos.solicitante+' rows="2" cols="98">'+datos.solicitante+'</textarea>'+
                                                    '</div>'+
                                                      
                                                  '</td'+
                                                '</tr>'+
                                                
                                                '<tr>'+
                                                  '<td>'+
                                                  '<br>'+
                                                 
                                                    '<div class="col-md-3 col-md-offset-9">Equipo: '+
                                                    '<textarea class="form-control" id="equipo" name="equipo" style="padding-left:15px"  value='+datos.codigo+' rows="2" cols="46">'+datos.codigo+'</textarea>'+
                                                    '</div>'+
                                                    '<br>'+
                                                   
                                                    '<div class="col-md-3 col-md-offset-9">Ubicacion: '+
                                                    '<textarea class="form-control" id="ubicacion" name="ubicacion" style="padding-left:15px"  value='+datos.ubicacion+' rows="2" cols="46">'+datos.ubicacion+'</textarea>'+
                                                      
                                                    '</div>'+
                                                  '</td>'+
                                                 // '<br>'+
                                                  '<td>'+
                                                  '<br>'+
                                                  //'<br>'+
                                                                                       
                                                    '<div class="col-md-3 col-md-offset-9">Sector: '+
                                                      '<textarea class="form-control" id="sector" name="sector" style="padding-left:15px"  value='+datos.sector+' rows="2" cols="46">'+datos.sector+'</textarea>'+
                                                      
                                                    '</div>'+
                                                    '<br>'+
                                                    //'<br>'+
                                                    
                                                    
                                                    '<div class="col-md-3 col-md-offset-9">Grupo: '+
                                                    '<textarea class="form-control" id="grupo" name="grupo" style="padding-left:15px"  value='+datos.grupo+' rows="2" cols="46">'+datos.grupo+'</textarea>'+

                                                   '</td>'+
                                                '</tr>'+
                                                '</tr>'+
                                              '</table>'+
                                            '</div>'+
                                          '</div>'+
                                          '<br>'+
                                          '<div class="row">'+
                                            '<div class="col-xs-12">Causa: '+
                                            
                                            '</div>'+
                                            '<br>'+
                                            '<div class="col-xs-12">'+
                                              '<textarea class="form-control" id="descripcion" name="descripcion" style="padding-left:15px"  value='+datos.causa+' rows="4" cols="98">'+datos.causa+'</textarea>'+
                                            '</div>'+ 
                                          '</div>'+
                                          '<br>'+
                                          '<div class="row">'+
                                          '<div class="col-xs-3">'+
                                                                         
                                            
                                          // causa 93 '<div class="col-sm-12 col-md-12">'+
                                            
                                              //'<div class="col-sm-1>'+
                                                '<label for="inputPassword3" >Realizado:</label>'+
                                                
                                                  ' <input type="text" class="form-control" id="inputPassword3" size="32">'+
                                                '</div>'+
                                                '<br>'+
                                              
                                              '<div class="col-xs-3">'+
                                                '<label for="inputPassword3" >Supervisado:</label>'+
                                                
                                                  ' <input type="text" class="form-control" id="inputPassword3" size="30">'+
                                                '</div>'+
                                                '<br>'+
                                              
                                              
                                              '<div class="col-xs-3" align="rigth">'+
                                                '<label for="inputPassword3"  control-label">Fecha Realizado:</label>'+
                                         
                                                  ' <input type="text" class="form-control" id="inputPassword3" size="27">'+
                                                '</div>'+
                                                '<br>'+
                                              
                                              '<div class="col-xs-3">'+
                                              '<label for="inputPassword3" >Conforme servicio:</label>'+
                                              
                                                
                                                  '__________________________________'+
                                                '</div>'+    
                                            
                                          '</div>'+
                                          
                                          '<br>'+
                                          '<br>'+
                                          //
                                          '<div class="row">'+
                                            '<div class="col-xs-10 col-xs-offset-1" style="text-align: center">'+
                                           
                                              '<table  class="table table-bordered table-hover" style="text-align: center" >'+ //class="table table-bordered"
                                             
                                                '<tr align="center" bottom="middle>'+
                                                  '<td align="center" colspan="1" >'+
                                                    '<div class="text-center">'+
                                                    ' <img src="img/logo.jpg" width="280" height="80" align="right"/>'+
                                                    '</div>'+
                                                  '</td>'+
                                                  //'<br>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td><h3> Vale de Materiales:'+'</h3>'+
                                                  '</td>'+
                                                '</tr>'+
                                              '</table>'+
                                            '</div>'+
                                          '</div>'+
                                          //'<br>'+
                                          //style="text-align: center"
                                          
                                          '<div class="row">'+
                                            '<div class="col-xs-10 col-xs-offset-1 text-center">'+
                                           
                                              '<table class="table table-bordered"  border="1px solid black"  >'+ //class="table table-bordered"
                                             
                                                '<tr colspan="8">'+
                                                  '<th width="2%">Item  (Tachar sino corresponde)</th>'+
                                                  '<th width="15%">Codigo</th>'+ 
                                                  '<th width="40%">Descripcion</th>'+
                                                  '<th width="5%">Cantidad</th>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td style="text-align: center" >1</td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td style="text-align: center" >2</td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td style="text-align: center" >3</td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td style="text-align: center" >4</td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                  '<td style="text-align: center" >5</td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                  '<td><br></td>'+
                                                '</tr>'+
                                                
                                              '</table>'+ 
                                            '</div>'+
                                          '</div>'+
                                          '<br>'+
                                          '<div class="row">'+
                                            '<div class="col-sm-12 col-md-12">'+
                                              '<div class="col-sm-2-2">Entrega (Firma y aclaracion): '+ 
                                              '<input type="text"  size="30">'+
                                                      
                                              '</div>'+
                                              '<br>'+
                                              '<div class="col-sm-2-2">Recibe (Firma y aclaracion): '+ '<input type="text"  size="30">'+
                                             
                                              '</div>'+
                                            '</div>'+
                                          '</div>'+                                                            

                                        '</div>'+
                                      '</div>'+
                                    '</div>'+
                                   
                                  '</div>'+
                                '</div>'+
                              '</div>'+
                              '<style>'+
                                 '.table, .table>tr, .table>td {border: 2px solid #f4f4f4;} '+
                              '</style>';


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

        error: function(result){
                  console.log(result);
                  console.log("error en la vistaimprimir");
                },
                //dataType: 'json'
    });
  });
  // Levanta imagen de solicitud - Chequeado
  $('.fa-picture-o').click(function(){
    $('#imgSolServ').attr('src',''); 
    $('#resp').remove();    

    var imag = $(this).data('imagen');
    if (imag != 'assets/files/orders/sinImagen.jpg') {
      $('#imgSolServ').attr('src',imag); 
    }else{
      $('.imagen').append('<h5 id="resp">Sin imagen cargada.<h5>');
    }
  });
  // Datepicker  
  $("#vstFecha").datepicker({    
    firstDay: 0      
  }).datepicker("setDate", "+1d"); // agrega la cantidad de dias o meses a partir de hoy

  $("#fecha_conformidad").datepicker({
    dateFormat: 'yy/mm/dd',
    firstDay: 1
  }).datepicker("setDate", new Date());

  // Trae Sectores y autocompleta el campo
  var dataF = function () {
    var tmp = null;
    $.ajax({
      'async': false,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "<?php echo MAN; ?>Sservicio/getSector",
      'success': function (data) {
        tmp = data;
      }
    });
    return tmp;
  }();  
  $(".buscSector").autocomplete({
    source: dataF,
    delay: 100,
    minLength: 1,
    focus: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      // manually update the textbox
      $(this).val(ui.item.label);
    },
    select: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      // manually update the textbox and hidden field
      $(this).val(ui.item.label);
      $("#idSector").val(ui.item.value);
      $("#equipSelec").html("");
      // guardo el id de sector
      var idSect =  $("#idSector").val();
      getEquiSector(idSect);
      //console.log("id sector en autocompletar: ");
      //console.log(ui.item.value);
    },
  }); 
  //  llena select de equipos segun sector
  function getEquiSector(idSect){
    var id =  idSect;
    console.log("id de sector para traer equipos: "+id);
    $.ajax({
      'data' : {id_sector : id },
      'async': true,
      'type': "POST",
      'global': false,
      'dataType': 'json',
      'url': "Sservicio/getEquipSector",
      'success': function (data) {
        console.log("Entro por getEquiSector ok");
        console.table(data);//[0]['id_equipo']);
        // Asigna opciones al select Equipo en modal
        //console.log("length: "+data.length);
        var $select = $("#equipSelec");
        for (var i = 0; data.length; i++) {
          $select.append($('<option />', { value: data[i]['id_equipo'], text: data[i]['descripcion'] }));
        }
      },
      'error' : function(data){
        console.log('Error en getEquiSector');
        console.table(data);
      },
    });
  }
  // trae tareas estandar para llenar select
  // getTareasStandar();
  // function getTareasStandar(){
  //   $.ajax({      
  //     'async': true,
  //     'type': "POST",
  //     'global': false,
  //     'dataType': 'json',
  //     'url': "Sservicio/getTareasStandar",
  //     'success': function (data) {
        
  //       console.table(data);        
  //       var $select = $("#tareaSelec");
  //       for (var i = 0; data.length; i++) {
  //         $select.append($('<option />', { value: data[i]['id_tarea'], text: data[i]['descripcion'] }));
  //       }
  //     },
  //     'error' : function(data){
  //       console.log('Error en Traer tareas...');
  //       console.table(data);
  //     },
  //   });
  // }

// Trae Operarios
var dataO = function () {
  var tmp = null;
  $.ajax({
    'async': false,
    'type': "POST",
    'global': false,
    'dataType': 'json',
    'url': "<?php echo MAN; ?>ordenservicio/getOperario",
    'success': function (data) {
        tmp = data;
    }
  });
  return tmp;
}();
$("#vstsolicita").autocomplete({
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
    $("#id_vstsolicita").val(ui.item.value);                 
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



  // Guardado de datos y validaciones
  $("#btnSave").click(function(){

    WaitingOpen('Generando Solcitud');
  	var hayError = false;
    if($('#nombre').val() == '')
    {
    	hayError = true;
    }
    if($('#equipId').val() == '')
    {
      hayError = true;
    }
    if($('#vstsolicita').val() == '')
    {
      hayError = true;
    }
    if($('#vstNote').val() == '')
    {
      hayError = true;
    }
    if(hayError == true){
    	$('#error').fadeIn('slow');
    	return;
    }

    $('#error').fadeOut('slow');
    $('#modalservicio').modal('hide');

    var permisos = $('#permission').val();

    	$.ajax({
          	type: 'POST',
          	data: {
                    equip: $('#equipSelec').val(),
                    solici: $('#vstsolicita').val(),
                    fecha: $('#vstFecha').val(),
                    hora: $('#vstHora').val(),
                    min: $('#vstMinutos').val(),
                    falla: $('#vstNote').val(),
                    tarea: $('#tareaSelec').val()
                  },
        		url: 'index.php/Sservicio/lanzarProcesoBPM',
        		success: function(data){
                    WaitingClose();
                    console.log(data);
                    if (data.status == true){
                      //alert("Solicitud generada exitosamente");
                     
                      cargarView('Sservicio', 'index', permisos) ;           
                    } else{             
                        alert("Falla: "+data.msj);
                    }                   
        					},
        		error: function(data){
        					  WaitingClose();
        					  alert("Error: "+data.msj);         
        				},
            dataType: 'json'
    		});
  });
  //Guardar conformidad 
  $('.fa-thumbs-up').click(function(){
      var id = $(this).parent('td').parent('tr').attr('id'); 
      $('.clear').val('');
      $('#id_sol').val(id);

  });
  function guardarConf(){
    
    var hayError = false;
    if($('#fecha_conformidad').val() == '')
    {
      hayError = true;
    }
    if($('#id_sol').val() == '')
    {
      hayError = true;
    }    

    if(hayError == true){
      $('#errorconf').fadeIn('slow');
      return;
    }

    $('#errorconf').fadeOut('slow');

    WaitingOpen('Guardando...');
    var data = $('#formConform').serializeArray();
    $.ajax({
            type: 'POST',
            data: data,
            url: 'index.php/Sservicio/confSolicitud',
            success: function(result){
                        WaitingClose('Guardado exitosamente...');
                        var permisos = '<?php echo $permission; ?>';
                        cargarView('Sservicio', 'index', permisos) ;
                        //alert("guardado con exito");
                  },
            error: function(result){
                  WaitingClose();
                  alert("Error en guardado...");
                },
            dataType: 'json'
        });
  }
  // Datatables
  $('#servicio').DataTable({
    <?php echo (!DT_SIZE_ROWS ? '"paging": false,' : null) ?>
    "autoWidth": false,
    "paging": true,
    "aLengthMenu": [ 10, 25, 50, 100 ],
    "columnDefs": [ 
      {
        "targets": [ 0 ], 
        "searchable": false,
      },
      {
        "targets": [ 0 ], 
        "orderable": false,
      },
      { 
        "targets": [ 1, 8 ],
        "type": "num",
      }
    ],
    "order": [[1, "desc"]],
  });
</script>


<!-- Modal Solicitud Nueva-->
<div class="modal" id="modalservicio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="modalAction"> </span> Solicitud Servicios</h4>
      </div>
      <div class="modal-body" id="modalBodyservicio">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable" id="error" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos...
            </div>
          </div>
        </div>
        <style>
          .ui-autocomplete{
              z-index:1050;
          }
        </style>
        <div class="row">
          <div class="col-xs-12 col-md-3">
              <label style="margin-top: 7px;">Sector <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <input type="text" class="form-control buscSector" placeholder="Buscar Sector..." id="buscSector" name="buscSector">
              <input type="text" class="hidden idSector" id="idSector" name="idSector">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Equipo <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <select name="equipSelec" class="form-control equipSelec" id="equipSelec">
                <option value="-1" placeholder="Seleccione..."></option>
              </select>
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Solicitante: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <input placeholder="Buscar Nombre..." class="form-control" rows="3" id="vstsolicita" value="">
              <input type="hidden" class="form-control" rows="3" id="id_vstsolicita" value="">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Fecha Sugerido <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <input class="form-control datepicker" placeholder="dd-mm-aaaa" name="datepicker" id="vstFecha">
          </div>
        </div><br>

        <div class="row">
          <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Horario sugerido <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-5 col-md-4">
              <select class="form-control" id="vstHora" style="width: 100%;">
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
              </select>
          </div>
          <div class="col-xs-2 col-md-1">
            <center>:</center>
          </div>
          <div class="col-xs-5 col-md-4">
              <select class="form-control select_equip" id="vstMinutos" style="width: 100%;">
                <option value="00">00</option>
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="45">45</option>
              </select>
          </div>
        </div><br>

        <div class="row">
          <!-- <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Tarea <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <select name="tareaSelec" class="form-control tareaSelec" id="tareaSelec">
                <option value="-1" placeholder="Seleccione...">Seleccione tarea...</option>
              </select>
          </div> -->
          <div class="col-xs-12 col-md-3">
            <label style="margin-top: 7px;">Falla <strong style="color: #dd4b39">*</strong>: </label>
          </div>
          <div class="col-xs-12 col-md-9">
              <textarea placeholder="Agregar una Nota" class="form-control" rows="3" id="vstNote" value=""></textarea>
          </div>
        </div>
      </div> <!--/ .modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
      </div>
    </div> <!-- / .modal-conten -->
  </div>
</div>

<!-- Modal Conformidad -->
<div class="modal" id="modalConformidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="row">
        <div class="col-xs-12">
          <div class="alert alert-danger alert-dismissable" id="errorconf" style="display: none">
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                Revise que todos los campos esten completos
            </div>
        </div>
      </div>

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Conformidad</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle"> 
        <form id="formConform">
          <div class="row">
            <div class="col-xs-12">
              <label for="">Fecha <!--<strong style="color: #dd4b39">*</strong>:--></label>
              <input type="text" id="fecha_conformidad" name="fecha_conformidad" class="form-control">
              <input type="hidden" id="id_sol" name="id_sol" class="form-control clear">
            </div>
            <div class="col-xs-12">
              <label for="">Observaciones<strong style="color: #dd4b39">*</strong>:</label>
              <textarea class="form-control clear" id="observ_conformidad" name="observ_conformidad" placeholder="Observaciones..."></textarea>
            </div> 
          </div>      
        </form>
      </div> 
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>       
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarConf()">Guardar</button>
      </div>  <!-- /.modal footer -->

    </div>  <!-- /.modal-content --><!-- /.modal-body -->
  </div> <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal Conformidad -->

<!-- Modal Foto-->
<div class="modal" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Imagen de Solicitud de Servicio</h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 imagen">
            <img id="imgSolServ" src="" style="max-width: 300px; float: center;">
          </div>
        </div>        
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
//Oculta/Muestra solicitudes con estado Conforme
function showSolicitudesConformes() {
  showConformes = $('#check-conformes').is(':checked');
  table = $('#servicio').DataTable();

  $.ajax({
    type: 'GET',
    dataType: 'json',
    data: {
        showConformes: showConformes
    },
    url: '<?php echo MAN; ?>Sservicio/getServiciosConformes',
    success: function(data){
      // Limpio tabla
      table.clear();

      // Loopeo, dibujo y agrego las filas de la tabla
      $.each(data, function(index, item) {
        var t_ciclo;
        var t_asignacion;
        var t_generacion;

        if((item.f_asignacion != null && item.f_asignacion != '0000-00-00 00:00:00') && (item.f_inicio != null && item.f_inicio != '0000-00-00 00:00:00')){
          var f_asignacion = new Date(item.f_asignacion);
          var f_inicio = new Date(item.f_inicio);
          var tiempoAsignacion = f_inicio - f_asignacion;
          var horasAsignacion = Math.floor(tiempoAsignacion / 1000 / 60 / 60);
          var minutosAsignacion = Math.floor((tiempoAsignacion / 1000 / 60) % 60);
          minutosAsignacion = String(minutosAsignacion).padStart(2, '0');
          t_ciclo = horasAsignacion + ":" + minutosAsignacion;
        }else{
          t_ciclo = 'S/Datos';
        }

        //Checkeo la nullidad de la fecha de asignacion e inicio
        if((item.f_asignacion != null && item.f_asignacion != '0000-00-00 00:00:00') && (item.f_inicio != null && item.f_inicio != '0000-00-00 00:00:00')){
          //Parseo las fechas como objetos Date
          var f_asignacion = new Date(item.f_asignacion);
          var f_inicio = new Date(item.f_inicio);
          //Obtengo la diferencia de tiempo entre la asignacion y el inicio en milisegundos
          var tiempoAsignacion = f_inicio - f_asignacion;
          // Parseo los milisegundos a horas y minutos de asignacion
          var horasAsignacion = Math.floor(tiempoAsignacion / 1000 / 60 / 60);
          var minutosAsignacion = Math.floor((tiempoAsignacion / 1000 / 60) % 60);
          // Agrego un 0 a los minutos si es necesario
          minutosAsignacion = String(minutosAsignacion).padStart(2, '0');
          //Formateo fecha final
          t_asignacion = horasAsignacion + ":" + minutosAsignacion;
        }else{
          t_asignacion = 'S/Datos';
        }
        //Checkeo la nullidad de la fecha de asignacion y solicitado
        if((item.f_asignacion != null && item.f_asignacion != '0000-00-00 00:00:00') && (item.f_solicitado != null && item.f_solicitado != '0000-00-00 00:00:00')){
          var f_asignacion = new Date(item.f_asignacion);
          var f_solicitado = new Date(item.f_solicitado);
          var tiempoGeneracion = f_asignacion - f_solicitado;
          var horasGeneracion = Math.floor(tiempoGeneracion / 1000 / 60 / 60);
          var minutosGeneracion = Math.floor((tiempoGeneracion / 1000 / 60) % 60);
  
          minutosGeneracion = String(minutosGeneracion).padStart(2, '0');
          
          t_generacion = horasGeneracion + ":" + minutosGeneracion;
        }else{
          t_generacion = 'S/Datos';
        }
        
        switch (item.estado) {
          case 'S':
            estado = "Solicitada";
            color = 'red';
            break;
          case 'PL':
            estado = "Planificada";
            color = 'yellow';
            break;
          case 'AS':
            estado = "Asignada";
            color = 'purple';
            break;
          case 'C':
            estado = "Curso";
            color = 'green';
            break;
          case 'T':
            estado = "Terminada";
            color = 'blue';
            break;
          case 'CE':
            estado = "Cerrada";
            color = 'default';
            break;
          case 'CN':
            estado = "Conforme";
            color = 'black';
            break;
        
          default:
            break;
        }
        fila = "<tr id='"+ item.id_solicitud +"' class='"+ item.id_equipo +"' data-idequipo='"+ item.id_equipo +"'>" +
                '<td><a onclick="mostrarOT(this)" href="#"><i class="fa fa-search text-white" style="cursor: pointer;margin-left:-3px"></i>   Ver</a>' +
                '<td>' + item.id_solicitud + '</td>' +
                '<td>' + item.f_solicitado + '</td>' +
                '<td>' + (item.fecha_terminada != null && item.fecha_terminada != '0000-00-00 00:00:00' ? item.fecha_terminada : 'S/Fecha') + '</td>' +
                '<td>' + t_ciclo + '</td>' +
                '<td>' + t_asignacion + '</td>' +
                '<td>' + t_generacion + '</td>' +
                '<td>' + item.solicitante + '</td>' +
                '<td>' + (item.equipo != null ? item.equipo : '') + '</td>' +
                '<td>' + (item.sector != null ? item.sector : '') + '</td>' +
                '<td>' + (item.grupo != null ? item.grupo : '') + '</td>' +
                '<td>' + (item.causa != null ? item.causa : '') + '</td>' +
                '<td>' + (item.mantenedor != null ? item.mantenedor : '') + '</td>' +
                '<td><span data-toggle="tooltip" title="'+ item.estado +'" class="badge bg-'+ color +' estado">'+ estado +'</span></td>' +
            '</tr>';
        table.row.add($(fila));
      });

      // Redibujo la nueva tabla
      table.draw();
    }
  });
}
</script>