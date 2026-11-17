<input type="hidden" id="permission" value="<?php echo $permission;?>">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title">Solicitudes Terminadas</h3>
                    <?php                    
                       echo '<button class="btn btn-block btn-success" style="width: 100px; margin-top: 10px;" id="solic_list">Ver Activas</button>';
                    ?>
                </div><!-- /.box-header -->

                <div class="box-body">
                    <table id="servicio" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Acciones</th>
                                <th>Nro</th>
                                <th>fecha</th>
                                <th>Solicitante</th>
                                <th>Equipo</th>
                                <th>Sector</th>
                                <th>Grupo</th>
                                <th>Ubicacion</th>
                                <th>Causa</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($list) > 0) {
                                foreach($list as $f)
                                {
                                   
                                    $id_sol = $f['id_solicitud'];                                    
                                    $id_eq = $f['id_equipo'];

                                    echo '<tr id="'.$id_sol.'" class="'.$id_eq.'">';
                                    echo '<td>';

                                    // if (strpos($permission,'Edit') !== false) {

                                    //     echo '<i class="fa fa-fw fa-pencil" style="color: #f39c12; cursor: pointer; margin-left: 15px;" title="Editar" onclick="Loadservicio('.$f['id_solicitud'].',\'Edit\')"></i>';

                                    // }

                                    // echo '<i class="fa fa-fw fa-print" style="color: #A4A4A4; cursor: pointer; margin-left: 15px;" title="Imprimir"  ></i> ';
                                    // echo '<i class="fa fa-picture-o" style="color: #A4A4A4; cursor: pointer; margin-left: 15px;" title="Imagen" onclick="Loadimag('.$f['id_solicitud'].',\'View\')"  ></i> ';

                                    // echo '<i class="fa fa-sticky-note-o" id="cargOrden" style="color: #A4A4A4; cursor: pointer; margin-left: 15px;" title="Orden de Servicios" ></i>';

                                    echo '<i class="fa fa-fw '.($a['estado'] == 'C' ? 'fa fa-toggle-on' : 'fa fa-toggle-off').' title="Terminar" style="color: #006400; cursor: pointer; margin-left: 15px;"></i>';
                                    

                                    // if (strpos($permission,'Del') !== false) {

                                    //     echo '<i class="fa fa-fw fa-times-circle" style="color: #dd4b39; cursor: pointer; margin-left: 15px;" title="Eliminar" onclick="Loadservicio('.$f['id_solicitud'].',\'Del\')"></i>';
                                    // }

                                    if (strpos($permission,'View') !== false) {

                                        echo '<i class="fa fa-fw fa-search" style="color: #3c8dbc; cursor: pointer; margin-left: 15px;" title="Consultar" onclick="Loadservicio('.$f['id_solicitud'].',\'View\')"></i>';
                                    }

                                    echo '</td>';
                                    echo '<td style="text-align: left">'.$f['id_solicitud'].'</td>';
                                    echo '<td style="text-align: left">'.$f['f_solicitado'].'</td>';
                                    echo '<td style="text-align: left">'.$f['solicitante'].'</td>';
                                    echo '<td style="text-align: left">'.$f['equipo'].'</td>';
                                    echo '<td style="text-align: left">'.$f['sector'].'</td>';
                                    echo '<td style="text-align: left">'.$f['grupo'].'</td>';
                                    echo '<td style="text-align: left">'.$f['ubicacion'].'</td>';
                                    echo '<td style="text-align: left">'.$f['causa'].'</td>';
                                    
                                    echo '<td style="text-align: center">'.($f['estado'] == 'C' ? '<small class="label pull-left bg-green">Curso</small>' :($f['estado'] == 'T' ? '<small class="label pull-left bg-blue">Terminado</small>' : '<small class="label pull-left bg-red">Solicitado</small>')).'</td>';
                                    echo '</tr>';

                                } //   / foreach
                            }
                            ?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col 8 -->
    </div><!-- /.row -->



<script>
  $(function () {
    
    $('#servicio').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "language": {
              "lengthMenu": "Ver _MENU_ filas por página",
              "zeroRecords": "No hay registros",
              "info": "Mostrando pagina _PAGE_ de _PAGES_",
              "infoEmpty": "No hay registros disponibles",
              "infoFiltered": "(filtrando de un total de _MAX_ registros)",
              "sSearch": "Buscar:  ",
              "oPaginate": {
                  "sNext": "Sig.",
                  "sPrevious": "Ant."
                }
        }
    });
  });
</script>

<!-- carga solicitudes inactivas -->
<script>
 $('#solic_list').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    linkTo("<?php echo base_url(MAN); ?>Sservicio/index/<?php echo $permission; ?>");
    WaitingClose();
  });
</script>
<!-- / carga solicitudes inactivas -->

<!-- Cambia el estado de Orden servicio y de solicitud de servicio  -->
<script>   
  $(".fa-toggle-off").click(function () {  

    var id_solic = $(this).parent('td').parent('tr').attr('id'); // guardad el id de solicitud
    $.ajax({
          type: 'POST',
          data: {id_solicitud: id_solic},
          url: '<?php echo base_url(MAN); ?>Sservicio/activSolicitud', 
          success: function(data){                   
                   setTimeout(function () { linkTo('<?php echo base_url(MAN); ?>Sservicio/index/' + $('#permission').val()); }, 0);
                },            
          error: function(result){
                alert("Error en cambio de estado");
              },
              dataType: 'json'
          });
  });
</script>
<!-- / Cambia el estado de Orden servicio y de solicitud de servicio  -->
