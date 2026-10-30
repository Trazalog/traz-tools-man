<style>
.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}  
.switch input {display:none;}  
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}  
.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}  
input:checked + .slider {
    background-color: #2196F3;
}  
input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}  
input:checked + .slider:before {
    -webkit-transform: translateX(19px);
    -ms-transform: translateX(19px);
    transform: translateX(19px);
}  
/* Rounded sliders */
  .slider.round {
    border-radius: 34px;
}  
.slider.round:before {
    border-radius: 50%;
}
.oculto {
  display: none;
}
</style>
<input type="hidden" id="permission" value="<?php echo $permission;?>"> 
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <div class="col-xs-12">
            <h3 class="box-title">Solicitudes de Servicios</h3>
          </div>
          <?php
           if (strpos($permission,'Add') !== false) {
              echo '<div class="col-xs-4"><button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalservicio" id="btnAdd">Agregar</button></div>';
           }          
          ?>
          <div class="col-xs-8" style="text-align: center;">
            <div class="form-check">
              <label style="margin-right: 10px;">Mostrar solicitudes conformes</label>
            </div>
              <label class="switch">
                <input type="checkbox" id="check-conformes" >
                <span class="slider round"></span>
              </label>
          </div><!-- /.col-xs-12 -->
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="servicio" class="table table-striped table-hover" style="width:100%;table-layout: fixed;">
            <thead>
              <tr>
                <th width="2%">Acciones</th>
                <th>Nro</th>
                <th>Nro de OT</th>
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
             
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col 8 -->
  </div><!-- /.row -->


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

<!-- Modal para eliminar solicitud -->
<div class="modal fade" id="modalVerOT" tabindex="-1" role="dialog">
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
                                        <input type="text" class="form-control" name="vIdOt" id="vIdOt" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-9">
                                        <label for="vDescripFalla">Descripción:</label>
                                        <input type="text" class="form-control vDescripFalla" id="vDescripFalla" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaProgram">Fecha Programación:</label>
                                        <input type="text" class="form-control" name="vFechaProgram" id="vFechaProgram" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaCreacion">Fecha Inicio:</label>
                                        <input type="text" class="form-control" name="vFechaCreacion" id="vFechaCreacion" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vFechaTerminOT">Fecha Terminada:</label>
                                        <input type="text" class="form-control" name="vFechaTerminOT" id="vFechaTerminOT" disabled>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3">
                                        <label for="vEstado">Estado:</label>
                                        <input type="text" class="form-control" name="vEstado" id="vEstado" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar solicitud -->
<div class="modal fade" id="modalEliminarSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalEliminarSolicitudLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalEliminarSolicitudLabel">Eliminar Solicitud</h4>
      </div>
      <div class="modal-body">
        <form id="formEliminarSolicitud">
          <input type="hidden" id="idSolicitud" name="id_solicitud">
          <input type="hidden" id="case_id" name="case_id">
          <div class="form-group">
            <label for="motivoEliminacion">Motivo de eliminación:</label>
            <textarea class="form-control" id="motivoEliminacion" name="motivoEliminacion" rows="3" required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Orden de Trabajo Solicitud de Servicio-->
<div class="modal" id="verOtSolServicio" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Solicitud de servicio</h4>
      </div>
      <div class="modal-body">
        
        <div class="panel-group" id="accordionSolServicio" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOneSolServicio">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordionSolServicio" href="#collapseOtSolServicio" aria-expanded="true" aria-controls="collapseOtSolServicio">
                  Datos
                </a>
              </h4>
            </div>
            <div id="collapseOtSolServicio" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOneSolServicio">
              <div class="panel-body">

                <div class="row">
                  <!-- <div class="col-xs-12 col-sm-6">
                    <label for="vIdOtSolServicio">Nº de SS:</label>
                    <input type="text" class="form-control " name="vIdOtSolServicio" id="vIdOtSolServicio" disabled>
                  </div> -->
                 
                  <div class="col-xs-12 col-sm-6">
                    <label for="vSector">Sector:</label>
                    <input type="text" class="form-control vSector" id="vSector" disabled>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <label for="vEquipo">Equipo:</label>
                    <input type="text" class="form-control " name="vEquipo" id="vEquipo" disabled>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <label for="vArea">Area:</label>
                    <input type="text" class="form-control " name="vArea" id="vArea" disabled>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <label for="vProceso">Proceso:</label>
                    <input type="text" class="form-control " name="vProceso" id="vProceso" disabled>
                  </div>
                  <div class="col-xs-12">
                    <label for="vDescripcion">Descripción:</label>
                    <Textarea class="form-control " name="vDescripcion" id="vDescripcion" disabled></Textarea>
                  </div>
                  
                  <div class="col-xs-12">
                    <label for="vFalla">Falla:</label>
                    <Textarea class="form-control " name="vFalla" id="vFalla" disabled></Textarea>
                  </div>

                  <!-- Nuevos campos para motivo, ID usuario y fecha de eliminación -->
                  <div class="col-xs-12" style="margin-top: 30px;">
                    <label for="motivo_eliminada" class="oculto">Motivo de Eliminación:</label>
                    <textarea class="form-control" id="motivo_eliminada" name="motivo_eliminada" class="oculto" disabled></textarea>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <label for="fecha_eliminada" class="oculto">Fecha de Eliminación:</label>
                    <input type="date" class="form-control oculto" id="fecha_eliminada" name="fecha_eliminada" disabled>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <label for="hora_eliminada" class="oculto">Hora de Eliminación:</label>
                    <input type="time" class="form-control oculto" id="hora_eliminada" name="hora_eliminada" disabled>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <label for="nombre_usuario" class="oculto">Usuario que Eliminó:</label>
                    <input type="text" class="form-control oculto" id="nombre_usuario" name="nombre_usuario" disabled>
                  </div>
                  <!-- Fin de nuevos campos -->  
                </div>

              </div>
            </div>
          </div> 
          <br>
          <div class="panel-group" id="panelAdj" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOneSolServicio">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#panelAdj" href="#collapseadj" aria-expanded="false" aria-controls="collapseadj">
                  Adjunto
                </a>
              </h4>
            </div>
            <div id="collapseadj" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOneSolServicio">
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12">
                    <table class="table table-bordered" id="tbladjSolicitud"> 
                      <thead>
                        <tr>                                       
                          <th>Archivo/s</th>
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
                  </div><!-- ./col-xs-12 -->
                </div><!-- ./row -->
                <div class="row">
                  <div class="col-md-12 col-xs-12 col-lg-12">
                    <div id="adjuntosGallery">
                    </div>
                  </div>
                </div><!-- /.row -->
              </div><!-- ./panel-body -->
            </div>
          </div> 

          <!-- vista herramientas -->
          <div class="panel panel-default" hidden>
            <div class="panel-heading" role="tab" id="headingThreePred">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordionBack" href="#collapseTareaSolicitudInsHerr" aria-expanded="false" aria-controls="collapseTareaSolicitudInsHerr">
                  Herramientas - Tareas - Insumos
                </a>
              </h4>
            </div>
            <div id="collapseTareaSolicitudInsHerr" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThreePred">
              <div class="panel-body">
                
                  <div class="row">
                      <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                          <!--tabs -->
                          <ul class="nav nav-tabs" role="tablist">                
                            <li role="presentation" class="active"><a href="#herrSolicitu" aria-controls="profile" role="tab" data-toggle="tab">Herramientas</a></li>
                            <li role="presentation"><a href="#insumSolicitu" aria-controls="messages" role="tab" data-toggle="tab">Insumos</a></li>
                            <li role="presentation"><a href="#TabAdjuntoSolicitud" aria-controls="messages" role="tab" data-toggle="tab">Adjunto</a></li>                        
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
                            </div><!--/#insum -->
        
                            <div role="tabpanel" class="tab-pane" id="TabAdjuntoSolicitud">
                              <div class="row" >  
                              
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
                            </div><!--cierre de TabAdjunto--> 
                            
                          </div>  <!-- tab-content -->
                          
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
  //calculo diferencia entre horas
  function calculateTimeDiff(start, end) {
      if (!start || !end || start === '0000-00-00 00:00:00' || end === '0000-00-00 00:00:00') {
          return 'S/Datos';
      } else {
          let startDate = new Date(start);
          let endDate = new Date(end);
          let diff = endDate - startDate;
          let hours = Math.floor(diff / 3600000);
          let minutes = Math.floor((diff % 3600000) / 60000);
          return `${hours}:${minutes.toString().padStart(2, '0')}`;
      }
  }

  function bolita(text, color) {
      return `<small class="label pull-left bg-${color}">${text}</small>`;
  }  
       
  $(document).ready(function() {

    var table = $('#servicio').DataTable({
        'ordering': true,
        'lengthMenu': [[10, 25, 50, 100], [10, 25, 50, 100]],
        'paging': true,
        'processing': true,
        'serverSide': true,
        'order': [[1, 'desc']],
        'ajax': {
            'url': '<?php echo base_url(MAN); ?>Sservicio/paginado',
            'type': 'POST',
            'data': function(d) {
                d.showConformes = $('#check-conformes').is(':checked'); // Agrega el parámetro showConformes
            }
        },
        'columns': [
            {
                "data": null,
                "render": function(data, type, row) {
                    var r = `<a onclick='mostrarOT(${JSON.stringify(row)})' href="#" title="Consultar"><i class="fa fa-search text-white" style="cursor: pointer;margin-left:-3px"></i></a>`;
                    r = r + `<td>`;
                    if (row.estado === 'S') {
                      var r = r + `<a onclick='mostrarModalEliminacion(${JSON.stringify(row)})' href="#" title="Eliminar"><i class="fa fa-trash text-white" style="cursor: pointer; margin-left: 15px;"></i></a>`;
                    }
                    if (row.estado === 'AS') {
                      var r = r + `<a onclick='mostrarModalEditarResponsable(${JSON.stringify(row)})' href="#" title="Editar Asignado"><i class="fa fa-user text-white" style="cursor: pointer; margin-left: 15px;"></i></a>`;
                    }
                    return r = r + `</td>`;
                }
            },
            { "data": "id_solicitud" },
            {
                "data": "id_orden",
                "render": function(data, type, row) {
                    if (data) {
                        return `<button class="btn btn-warning" onclick='abrirModal(${JSON.stringify(row)})'>${data}</button>`;
                    } else {
                        return '';
                    }
                }
            },
            { "data": "f_solicitado" },
            {
                "data": "fecha_terminada",
                "render": function(data, type, row) {
                    return (data == '0000-00-00 00:00:00' || data == '') ? 'S/Fecha' : data;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return calculateTimeDiff(row.f_solicitado, row.f_asignacion);
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return calculateTimeDiff(row.f_asignacion, row.f_inicio);
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return calculateTimeDiff(row.f_solicitado, row.f_asignacion);
                }
            },
            { "data": "solicitante" },
            { "data": "equipo" },
            { "data": "sector" },
            { "data": "grupo" },
            { "data": "causa" },
            { "data": "mantenedor" },
            {
                "data": "estado",
                "render": function(data, type, row) {
                    let colors = {
                        'EL': 'light-blue',
                        'S': 'red',
                        'PL': 'yellow',
                        'AS': 'purple',
                        'C': 'green',
                        'T': 'blue',
                        'CE': 'gray',
                        'CN': 'black'
                    };
                    let labels = {
                        'EL': 'Eliminada',
                        'S': 'Solicitada',
                        'PL': 'Planificada',
                        'AS': 'Asignada',
                        'C': 'Curso',
                        'T': 'Terminada',
                        'CE': 'Cerrada',
                        'CN': 'Conforme'
                    };
                    return bolita(labels[data], colors[data]);
                }
            }
        ]
    });

    $('#check-conformes').on('change', function() {
        table.ajax.reload();
    });
});

function abrirModal(rowData) {
    // Función para formatear fechas
    function formatDate(datetime) {
        if (!datetime || datetime === '0000-00-00 00:00:00') {
            return 'Sin fecha';
        }
        const date = new Date(datetime);
        const options = {
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit'
        };
        return date.toLocaleDateString('es-AR', options);
    }

    // Rellenar el modal con los datos formateados
    $('#vIdOt').val(rowData.id_orden || '');
    $('#vDescripFalla').val(rowData.descripcion || 'Sin descripción');
    $('#vFechaProgram').val(formatDate(rowData.fecha_program));
    $('#vFechaCreacion').val(formatDate(rowData.f_inicio));
    $('#vFechaTerminOT').val(formatDate(rowData.fecha_terminada));
    
    // Mapeo de estados
    let estadoLabels = {
        'EL': 'Eliminada',
        'S': 'Solicitada',
        'PL': 'Planificada',
        'AS': 'Asignada',
        'C': 'Curso',
        'T': 'Terminada',
        'CE': 'Cerrada',
        'CN': 'Conforme'
    };
    $('#vEstado').val(estadoLabels[rowData.estado] || rowData.estado || '');

    // Mostrar el modal
    $('#modalVerOT').modal('show');
}

/*function mostrarModalEliminacion(row) {
  console.log(row);
  Swal.fire({
      title: 'Atención!',
      text: 'Solo los usuarios solicitantes pueden eliminar solicitudes!',
      confirmButtonText: 'Aceptar',
    }).then((result) => {
      if (result.value) {
        console.log('Confirmado');
        var validacionSolicitante = validaUserSolicitante();
        console.log(validacionSolicitante);
        document.getElementById('idSolicitud').value = row.id_solicitud;
        $('#modalEliminarSolicitud').modal('show');
      }
    });
} */

async function mostrarModalEliminacion(row) {
  console.log(row);
  Swal.fire({
    title: 'Atención!',
    text: 'Solo los usuarios solicitantes pueden eliminar solicitudes!',
    confirmButtonText: 'Aceptar',
  }).then(async (result) => {
    if (result.value) {
      console.log('Confirmado');
      
      try {
        var validacionSolicitante = await validaUserSolicitante();
        console.log(validacionSolicitante);
        if (validacionSolicitante.trim() == "true") { // Aquí puedes verificar si el usuario es solicitante
          document.getElementById('idSolicitud').value = row.id_solicitud;
          document.getElementById('case_id').value = row.case_id;
          $('#modalEliminarSolicitud').modal('show');
        } else {
          Swal.fire('Error', 'No tienes permisos para eliminar esta solicitud.', 'error');
        }
      } catch (error) {
        Swal.fire('Error', 'Ocurrió un error al validar el usuario.', 'error');
      }
    }
  });
}

/* function validaUserSolicitante()
{
  $.ajax({
    url: '<?php echo base_url(MAN); ?>Sservicio/validaUsuarioSolicitante', 
    method: 'POST',
    data: {},
    success: function(response) {
      return response;
    },
    error: function(error) {
      WaitingClose();
      alert('Error al eliminar la solicitud.');
    }
  });
}
 */
function validaUserSolicitante() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: '<?php echo base_url(MAN); ?>Sservicio/validaUsuario',
      method: 'POST',
      data: {filtro1: 'Solicitante',},
      success: function(response) {
        resolve(response);
      },
      error: function(error) {
        reject(error);
      }
    });
  });
}

function confirmarEliminacion() {
  WaitingOpen('Eliminando Solcitud');
  var motivo = document.getElementById('motivoEliminacion').value;
  var idSolicitud = document.getElementById('idSolicitud').value;
  var case_id = document.getElementById('case_id').value;

  if (motivo.trim() === "") {
    alert("Por favor, ingrese un motivo para la eliminación.");
    return;
  }
  
  $.ajax({
    url: '<?php echo base_url(MAN); ?>Sservicio/eliminar_solicitud', 
    method: 'POST',
    data: {
      id_solicitud: idSolicitud,
      motivo: motivo,
      case_id : case_id
    },
    success: function(response) {
      WaitingClose('Eliminado con éxito...');
      var permisos = '<?php echo $permission; ?>';
      linkTo('<?php echo base_url(MAN); ?>Sservicio/index/' + permisos) ; 
    },
    error: function(error) {
      WaitingClose();
      alert('Error al eliminar la solicitud.');
    }
  });

  $('#modalEliminarSolicitud').modal('hide');
}

var baseUrl = "<?php echo base_url(); ?>";
  // VER OT 
  function mostrarOT(o){
    
    //let idSS = $(o).closest('tr').attr('id');
    //console.log(idSS); 
    let idSS = o.id_solicitud;
    
    WaitingOpen('Obteniendo datos de OT...');
    // datos = getDataOtSolServicio(idSS, "Solicitud de Servicio");
    // fillModalViewSolServicio(datos);
    $.ajax({
        async: false,
        data: { idSS: idSS },
        dataType: 'json',
        method: 'POST',
        url: '<?php echo base_url(MAN); ?>Sservicio/getSS',
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
        // Validación del estado y manipulación de campos
        if (data.SS[0].estado === "EL") {
            $("#motivo_eliminada").val(data.SS[0].motivo_eliminada).removeClass('oculto');
            $("#nombre_usuario").val(data.SS[0].nombre_usuario).removeClass('oculto');
            // Extracción de la parte de la fecha y la hora del campo datetime
            let [fechaEliminada, horaEliminada] = data.SS[0].fecha_eliminada.split(' ');
            $("#fecha_eliminada").val(fechaEliminada).removeClass('oculto');
            $("#hora_eliminada").val(horaEliminada).removeClass('oculto');
            
            // Mostrar las etiquetas también
            $("label[for='motivo_eliminada']").removeClass('oculto');
            $("label[for='fecha_eliminada']").removeClass('oculto');
            $("label[for='hora_eliminada']").removeClass('oculto');
            $("label[for='nombre_usuario']").removeClass('oculto');
        } else {
            $("#motivo_eliminada").addClass('oculto');
            $("#nombre_usuario").addClass('oculto');
            $("#fecha_eliminada").addClass('oculto');
            $("#hora_eliminada").addClass('oculto');
            
            // Ocultar las etiquetas también
            $("label[for='motivo_eliminada']").addClass('oculto');
            $("label[for='fecha_eliminada']").addClass('oculto');
            $("label[for='hora_eliminada']").addClass('oculto');
            $("label[for='nombre_usuario']").addClass('oculto');
        }
        if(data.adjuntos != null){
          $contenedor = $('#adjuntosGallery');
          $contenedor.empty();
          data.adjuntos.forEach((adjunto) => {
            $contenedor.append(`
              <div class="col-md-3 col-xs-6">
                <a href="${baseUrl + adjunto.url + adjunto.nombre}" target="_blank">
                  <img src="${baseUrl + adjunto.url + adjunto.nombre}" class="img-responsive" style="width: 100%; height: 100px;">
                </a>
              </div>
            `);
          });

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
        url: '<?php echo base_url(MAN); ?>Otrabajo/getViewDataSolServicio',
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
    linkTo("<?php echo base_url(MAN); ?>Sservicio/nuevaSS/<?php echo $permission; ?>");
    WaitingClose();
  });
</script>

<!-- carga solicitudes inactivas -->
<script>
 $('#activarSol').click( function cargarVista(){
    WaitingOpen();
    $('#content').empty();
    linkTo("<?php echo base_url(MAN); ?>Sservicio/get_SolicTerminada/<?php echo $permission; ?>");
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
                url: '<?php echo base_url(MAN); ?>Sservicio/elimSolicitud',
                success: function(result){
                            WaitingClose('Guardado exitosamente...');
                            //var permisos = '<?php //echo $permission; ?>';
                            var permisos = 'Add-Edit-Del-View-Asignar-Finalizar-OP-';
                            linkTo('<?php echo base_url(MAN); ?>Sservicio/index/' + permisos) ;                        
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
        url: '<?php echo base_url(MAN); ?>Sservicio/getsolImp',
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
    if (imag != '<?php echo man_asset_url(); ?>assets/files/orders/sinImagen.jpg') {
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
      'url': "Sservicio/getSector",
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
      'url': "ordenservicio/getOperario",
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
        		url: '<?php echo base_url(MAN); ?>Sservicio/lanzarProcesoBPM',
        		success: function(data){
                    WaitingClose();
                    console.log(data);
                    if (data.status == true){
                      //alert("Solicitud generada exitosamente");
                     
                      linkTo('<?php echo base_url(MAN); ?>Sservicio/index/' + permisos) ;           
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
            url: '<?php echo base_url(MAN); ?>Sservicio/confSolicitud',
            success: function(result){
                        WaitingClose('Guardado exitosamente...');
                        var permisos = '<?php echo $permission; ?>';
                        linkTo('<?php echo base_url(MAN); ?>Sservicio/index/' + permisos) ;
                        //alert("guardado con exito");
                  },
            error: function(result){
                  WaitingClose();
                  alert("Error en guardado...");
                },
            dataType: 'json'
        });
  }

  //cartel de aviso modificacion responsable
  async function mostrarModalEditarResponsable(row) {
    console.log(row);
  Swal.fire({
    title: 'Atención!',
    text: 'Solo los usuarios supervisores pueden editar solicitudes!',
    confirmButtonText: 'Aceptar',
  }).then(async (result) => {
    if (result.value) {
      console.log('Confirmado');
      
      try {
        var validacionUser = await validaUserAsignacion();
        console.log(validacionUser);

        if (validacionUser.trim() == "true") { 
          var id_orden = row.id_orden;
          WaitingOpen();
          $('#contRespyTareas').empty();
          $("#contRespyTareas").load("<?php echo base_url(MAN); ?>Calendario/verEjecutarOT/" + id_orden + "/" + true);
          $('#modalRespyTareas').modal('show'); 
          WaitingClose();  

        } else {
          Swal.fire('Error', 'No tienes permisos para editar esta solicitud.', 'error');
        }
      } catch (error) {
        Swal.fire('Error', 'Ocurrió un error al validar el usuario.', 'error');
      }
    }
  });
}


  function validaUserAsignacion() {
       return new Promise((resolve, reject) => {
      $.ajax({
        url: '<?php echo base_url(MAN); ?>Sservicio/validaUsuario',
        method: 'POST',
        data: {filtro1: 'Supervisor de Taller', filtro2: 'Administrador'},
        success: function(response) {
          resolve(response);
        },
        error: function(error) {
          reject(error);
        }
      });
    }); 
  }
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

<!-- Modal Aviso eliminar -->
<div class="modal" id="modalEliminarSolServicio2" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><span class="fa fa-fw fa-times-circle text-light-blue"></span>Eliminar</h4>
      </div>
      <div class="modal-body">
        <div class="col-xs-12">
            <input type="hidden" id="e_idSolicitud" name="e_idSolicitud">
            <label for="e_motivo">Motivo: </label>
            <textarea class="form-control" id="e_motivo" name="e_motivo"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="#">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- / Modal -->



<script>
// $(document).ready(function(){
//   //Oculto solicitudes conformes
//   showSolicitudesConformes();
// });
//Oculta/Muestra solicitudes con estado Conforme
/*function showSolicitudesConformes() {
  showConformes = $('#check-conformes').is(':checked');
  table = $('#servicio').DataTable();

  $.ajax({
    type: 'GET',
    dataType: 'json',
    data: {
        showConformes: showConformes
    },
    url: '<?php echo base_url(MAN); ?>Sservicio/getServiciosConformes',
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
}*/

</script>