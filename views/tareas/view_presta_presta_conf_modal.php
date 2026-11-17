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
      </div><!-- end .panel-body -->
    </div><!--  / <div class="panel panel-default"> -->   

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
                    <th>Pedido</th>
                    <th>Entregado</th>
                    <th>Pendiente</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                   foreach($insumos as $ins){
                      $entregado = isset($ins['entregado']) ? $ins['entregado'] : '';
                      $pendiente = isset($ins['pendiente']) ? $ins['pendiente'] : '';
                      echo '<tr>';
                      echo '<td>'.$ins['pema_id'].'</td>';
                      echo '<td>'.$ins['fecha'].'</td>';
                      //echo '<td>'.$ins['fechahorainicio'].'</td>';
                      echo '<td>'.$ins['barcode'].'</td>';
                      echo '<td>'.$ins['descripcion'].'</td>';
                      echo '<td>'.$ins['cantidad'].'</td>';
                      echo '<td>'.$entregado.'</td>';
                      echo '<td>'.$pendiente.'</td>';
                      echo '</tr>';
                    }
                  ?>
                </tbody>
              </table>
              </div>
            </div>
          </div>
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
          </div>
          <!-- Panel imagenes de evidencia  -->
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingEvidencias">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseEvidencias" aria-expanded="false" aria-controls="collapseEvidencias">
                  Evidencias
                </a>
              </h4>
            </div>
            <div id="collapseEvidencias" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEvidencias">
              <div class="panel-body">
                <div class="row">
                  <?php foreach ($evidencias as $ev): ?>
                    <?php
                      $mime = obtenerExtension($ev['nombre']); //obtengo la extension del archivo
                      $mimeType = explode(';', explode(':', $mime)[1])[0]; // Extrae: image/jpeg
                      $base64 = $ev['valor_base64'];
                      $src = $mime . $base64; // src completo para img
                      $nombre = htmlspecialchars($ev['nombre']);
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3" style="margin-bottom: 20px;">
                      <div class="thumbnail text-center">
                        <a href="javascript:void(0);" onclick="mostrarArchivo('<?= $base64 ?>', '<?= $mimeType ?>', '<?= $nombre ?>', 'modalEvidencia')" title="<?= $nombre ?>">
                          <?php if (strpos($mimeType, 'image/') !== false): ?>
                            <img src="<?= $src ?>" class="img-responsive" style="height: 180px; object-fit: cover;" alt="<?= $nombre ?>">
                          <?php elseif ($mimeType === 'application/pdf'): ?>
                            <div class="text-center" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                              <i class="fa fa-file-pdf-o fa-3x"></i>
                            </div>
                            <div class="caption text-center">
                              <p><?= $nombre ?></p>
                            </div>
                          <?php else: ?>
                            <i class="fa fa-download fa-2x"></i>
                            <p>Descargar <?= $nombre ?></p>
                          <?php endif; ?>
                        </a>
                        <div class="caption">
                          <small><?= $nombre ?></small>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div><!-- Fin Panel imagenes de evidencia  -->

          <!-- F4 REQ-ASSET-ALM: Insumos y entregas (lectura) -->
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingInsumosPC">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseInsumosPC" aria-expanded="false">Insumos y Entregas</a>
              </h4>
            </div>
            <div id="collapseInsumosPC" class="panel-collapse collapse" role="tabpanel">
              <div class="panel-body">
                <table class="table table-condensed" id="tablaInsumosPC">
                  <thead><tr><th>C&oacute;digo</th><th>Art&iacute;culo</th><th>Pedido</th><th>Entregado</th><th>Pendiente</th></tr></thead>
                  <tbody></tbody>
                </table>
                <p class="text-muted" id="sinInsumosPC" style="display:none">Esta OT no gener&oacute; pedido de materiales.</p>
                <h5><span class="fa fa-truck"></span> Entregas del Pedido</h5>
                <table class="table table-condensed" id="tablaEntregasPC">
                  <thead><tr><th>N&deg;</th><th>Fecha</th><th>Comprobante</th><th>Solicitante</th><th>Destino</th></tr></thead>
                  <tbody></tbody>
                </table>
                <p class="text-muted" id="sinEntregasPC" style="display:none">Sin entregas registradas.</p>
              </div>
            </div>
          </div>

          <!-- F5 REQ-ASSET-ALM: Herramientas usadas (lectura, con tiempo de uso) -->
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingHerrPC">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseHerrPC" aria-expanded="false">Herramientas Usadas</a>
              </h4>
            </div>
            <div id="collapseHerrPC" class="panel-collapse collapse" role="tabpanel">
              <div class="panel-body">
                <table class="table table-condensed" id="tablaHerrUsadasPC">
                  <thead><tr><th>Us&oacute;</th><th>C&oacute;digo</th><th>Herramienta</th><th>Marca</th><th>Vale</th><th>Origen</th><th>Uso</th><th>Devoluci&oacute;n</th></tr></thead>
                  <tbody></tbody>
                </table>
                <p class="text-muted" id="sinHerrUsadasPC" style="display:none">No se asociaron herramientas usadas a esta OT.</p>
              </div>
            </div>
          </div>

        </div> <!-- / .panel-group -->
  </form>      

  <div class="pull-right">
    <!-- <button type="button" class="botones btn btn-primary" onclick="enviarOrden()">Guardar</button>  -->
    <button type="button" id="cerrar" class="btn btn-primary" onclick="cerrarModal()">Cerrar</button>
  </div>      

<!-- Modal para evidencias -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" role="dialog" aria-labelledby="modalEvidenciaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEvidenciaLabel">Evidencia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 0;">
        <div id="contenedorEvidencia" style="width: 100%; max-height: 80vh; overflow: auto;"></div>
      </div>
    </div>
  </div>
</div><!-- FIN Modal para evidencias -->

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

<script>
// F4/F5 REQ-ASSET-ALM: insumos+entregas y herramientas usadas en Presta Conformidad (lectura).
(function(){
  var oid = $('#id_ot').val();
  $.ajax({ type:'POST', global:false, dataType:'json', url:'<?php echo base_url(MAN); ?>Ordenservicio/getInsumosPorOT', data:{ id_ot:oid },
    success:function(d){ var tb=$('#tablaInsumosPC tbody'); tb.empty(); d=d||[];
      for(var i=0;i<d.length;i++){ var x=d[i]; tb.append('<tr><td>'+(x.codigo||'')+'</td><td>'+(x.articulo||x.descripcion||'')+'</td><td>'+(x.pedido||0)+'</td><td>'+(x.entregado||0)+'</td><td>'+(x.pendiente||0)+'</td></tr>'); }
      $('#sinInsumosPC').toggle(d.length===0); }});
  $.ajax({ type:'POST', global:false, dataType:'json', url:'<?php echo base_url(MAN); ?>Ordenservicio/getEntregasPorOT', data:{ id_ot:oid },
    success:function(peds){ var tb=$('#tablaEntregasPC tbody'); tb.empty(); peds=peds||[]; var n=0;
      for(var i=0;i<peds.length;i++){ var ents=peds[i].entregas||[]; for(var j=0;j<ents.length;j++){ var e=ents[j]; n++; tb.append('<tr><td>N° '+e.enma_id+'</td><td>'+(e.fecha||'')+'</td><td>'+(e.comprobante||'')+'</td><td>'+(e.solicitante||'')+'</td><td>'+(e.destino||'')+'</td></tr>'); } }
      $('#sinEntregasPC').toggle(n===0); }});
  $.ajax({ type:'POST', global:false, dataType:'json', url:'<?php echo base_url(MAN); ?>Ordenservicio/getHerramientasUsadas', data:{ id_ot:oid },
    success:function(d){ var tb=$('#tablaHerrUsadasPC tbody'); tb.empty(); d=d||[];
      for(var i=0;i<d.length;i++){ var h=d[i];
        var dev = h.devolucion ? '<span class="label label-success">'+h.devolucion+'</span>' : '<span class="label label-warning">sin devolver</span>';
        var org = '<span class="label label-default">'+h.origen.toLowerCase()+'</span>';
        tb.append('<tr><td><input type="checkbox" '+(h.activa?'checked':'')+' disabled></td><td>'+(h.codigo||'')+'</td><td>'+(h.descripcion||'')+'</td><td>'+(h.marca||'')+'</td><td>N\u00b0 '+h.sapa_id+'</td><td>'+org+'</td><td>'+h.dias+(h.dias==1?' d\u00eda':' d\u00edas')+'</td><td>'+dev+'</td></tr>'); }
      $('#sinHerrUsadasPC').toggle(d.length===0); }});
})();
</script>
