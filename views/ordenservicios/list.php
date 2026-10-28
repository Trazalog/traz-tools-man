<input type="hidden" id="permission" value="<?php echo $permission; ?>">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Informe de Servicio</h3> <!-- EX Orden de Servicios-->
        </div><!-- /.box-header -->

        <div class="box-body">
          <div class="row" style="margin-bottom: -20px;">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <label for="filtro_estado" style="margin-bottom: 0;">Estado</label>
                <select id="filtro_estado" class="form-control" style="width: 200px;">
                  <option value="">Todos</option>
                  <option value="Cerrada">Cerrado</option>
                  <option value="Curso">Curso</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-weight: normal;">
                  <input type="checkbox" id="check_todos"> Seleccionar todos
                </label>
                <button style="margin-left: 10px;" class="btn btn-success" id="btn_verificar">Verificar</button>
              </div>
            </div>
          </div>
          <table id="tblorden" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Acciones</th>
                <th style="width: 100px;">Nº Informe</th>
                <th style="width: 100px;">Nº OT</th>
                <th>Descripción OT</th>
                <th style="min-width: 250px;">Tarea</th>
                <th>Equipo</th>
                <th>Fecha</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Recursos Humanos</th>
                <th class="hidden">id equipo</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($list) > 0) {
                foreach ($list as $a) {
                  $id = $a['id_orden'];
                  echo '<tr id="' . $id . '" data-json=\'' . htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') . '\'>';
                  echo '<td class="icono">';
                  echo '<input type="checkbox" class="check_fila" style="margin-right: 10px;">';
                  echo '<i class="fa fa-sticky-note-o text-light-blue" style="cursor: pointer;" title="Ver Informe"></i>';
                  // echo '<i class="text-light-blue fa fa-fw '.($a['estado'] == 'C' ? 'fa fa-toggle-on' : 'fa fa-toggle-off').'" title="'.($a['estado'] == 'C' ? 'Finalizar Informe' : 'Finalizado').'" style="cursor: pointer; margin-left: 15px;"></i>';
                  echo '</td>';
                  echo '<td>' . $a['id_orden'] . '</td>';
                  echo '<td>' . $a['id_ot'] . '</td>';
                  echo '<td>' . $a['descripcion_ot'] . '</td>';
                  echo '<td>' . $a['tarea'] . '</td>';
                  echo '<td>' . $a['equipo'] . '</td>';
                  echo '<td>' . $a['fecha'] . '</td>';
                  echo '<td>' . $a['fechahorainicio'] . '</td>';
                  echo '<td>' . $a['fechahorafin'] . '</td>';
                  echo '<td>' . (isset($a['recursos_humanos']) ? $a['recursos_humanos'] : '') . '</td>';
                  echo '<td class="hidden">' . $a['id_equipo'] . '</td>';
                  //echo '<td>'.($a['estado'] == 'C' ? '<small class="label pull-left bg-green">Curso</small>' :($a['estado'] == 'T' ? '<small class="label pull-left bg-blue">Terminado</small>' : '<small class="label pull-left bg-red">Solicitado</small>')).'</td>';
              
                  echo '<td>';

                  if ($a['estado'] == 'S') {
                    echo '<small class="label pull-left bg-red">Solicitada</small>';
                  }
                  if ($a['estado'] == 'PL') {
                    echo '<small class="label pull-left bg-orange">Planificada</small>';
                  }
                  if ($a['estado'] == 'AS') {
                    echo '<small class="label pull-left bg-yellow">Asignada</small>';
                  }
                  if ($a['estado'] == 'C') {
                    echo '<small class="label pull-left  bg-blue">Curso</small>';
                  }
                  if ($a['estado'] == 'T') {
                    echo '<small class="label pull-left bg-navy">Terminada</small>';
                  }
                  if ($a['estado'] == 'CE') {
                    echo '<small class="label pull-left bg-green">Cerrada</small>';
                  }

                  echo '</td>';

                  echo '</tr>';
                }
              }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->






<!--  MODAL INFORME DE SERVICIO  -->
<div class="modal fade bs-example-modal-lg" id="modalInforme" tabindex="-1" role="dialog"
  aria-labelledby="myLargeModalLabel">
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













<script>

  function ver_informe_servicio(id_ot, id_eq) {

    WaitingOpen();
    $('#modalInforme').modal('show');
    $('#modalInformeServicios').empty();
    // $("#modalInformeServicios").load("<?php echo base_url(MAN); ?>Ordenservicio/verInforme/"+id_ot+"/"+id_eq+"/"+id_solicitud+"/");
    $("#modalInformeServicios").load("<?php echo base_url(MAN); ?>Ordenservicio/verInforme/" + id_ot + "/" + id_eq + "/");
    WaitingClose();
  }






  // Resetea Nº de orden al recargar la pagina -->
  $('#cargOrden').click(function cargarVista() {
    WaitingOpen();
    $('#content').empty();
    linkTo("<?php echo base_url(MAN); ?>Ordenservicio/getOrdenInactiva/<?php echo $permission; ?>");
    WaitingClose();
  });


  /////// Carga la tabla del Modal y valida que  no se duplique 
  var $flag = 0;
  $(".fa-sticky-note-o").click(function () {

    var id_ot = $(this).parents("tr").find("td").eq(2).html();
    // asset toma la columna 6 (Fecha) como equipo y la ventana queda sin datos del equipo (T33): la 10 es el id oculto
    var id_eq = $(this).parents("tr").find("td").eq(10).html();
    ver_informe_servicio(id_ot, id_eq);

  });

  // muestra el encabezado de la Orden de servicio en Modal
  function mostrarOrd(row) {
    $("#modOrden tr").remove();
    $("#modOrden tbody").append(row);
  }

  // trae lecturas segun id de orden y arma tabla en modal 
  function getLecturaOrden(id_ord) {
    var dataF = function () {
      var tmp = null;
      $.ajax({
        'data': { id_orden: id_ord },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "Ordenservicio/getLecturaOrden",
        'success': function (data) {
          tmp = data;
          //console.table(data);
        }
      });
      return tmp;
    }();
    // Asigna opciones al select #tareas  
    tabla = $('#modLectura').DataTable();
    tabla.clear().draw();
    $.each(dataF, function (i, val) {
      $('#modLectura').DataTable().row.add([
        val.horometroinicio,
        val.horometrofin,
        val.fechahorainicio,
        val.fechahorafin
      ]).draw();
    });
  }

  // trae tareas segun id de orden y arma tabla en modal 
  function getTarOrden(id_ord) {
    var dataF = function () {
      var tmp = null;
      $.ajax({
        'data': { id_orden: id_ord },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "Ordenservicio/getTareaOrden",
        'success': function (data) {
          tmp = data;
          //console.table(data);
        }
      });
      return tmp;
    }();
    // Asigna opciones al select #tareas  
    tabla = $('#modTarea').DataTable();
    tabla.clear().draw();
    $.each(dataF, function (i, val) {
      $('#modTarea').DataTable().row.add([
        val.id_tarea
      ]).draw();
    });
  }

  // trae herramientas segun id de orden y arma tabla en modal 
  function getHerrramOrden(id_ord) {
    var dataF = function () {
      var tmp = null;
      $.ajax({
        'data': { id_orden: id_ord },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "Ordenservicio/getHerramOrden",
        'success': function (data) {
          tmp = data;
          //console.table(data);
        }
      });
      return tmp;
    }();
    tabla = $('#modHerram').DataTable();
    tabla.clear().draw();
    $.each(dataF, function (i, val) {
      $('#modHerram').DataTable().row.add([
        val.herrdescrip,
        val.herrmarca,
        val.herrcodigo
      ]).draw();
    });
  }

  // trae Insumos segun id de orden y arma tabla en modal 
  function getInsumOrd(id_ot) {
    var dataF = function () {
      var tmp = null;
      $.ajax({
        'data': { id_ot: id_ot },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "Ordenservicio/getInsumosPorOT",
        'success': function (data) {
          tmp = data;
          console.table(data);
        }
      });
      return tmp;
    }();
    tabla = $('#modInsum').DataTable();
    tabla.clear().draw();
    $.each(dataF, function (i, val) {

      $('#modInsum').DataTable().row.add([
        val.nroOT,
        val.fecha,
        val.nombre,
        val.codigo,
        val.descripcion,
        val.cantidad
      ]).draw();
    });
  }

  // trae RRHH segun id de orden y arma tabla en modal 
  function getRecOrden(id_ord) {
    console.log('recursossss: ');
    var dataO = function () {
      var tmp = null;
      $.ajax({
        'data': { id_orden: id_ord },
        'async': false,
        'type': "POST",
        'global': false,
        'dataType': 'json',
        'url': "Ordenservicio/getOperarioOrden",
        'success': function (data) {
          tmp = data;
          console.table(data);
        },
      });
      return tmp;
    }();
    tabla = $('#modRecurso').DataTable();
    tabla.clear().draw();
    $.each(dataO, function (i, val) {
      $('#modRecurso').DataTable().row.add([
        val.usrLastName,
        val.usrName
      ]).draw();
    });
  }

  // Cambia el estado de Orden servicio y de solicitud de servicio
  $(".fa-toggle-on").click(function () {

    var id_orden = $(this).parent('td').parent('tr').attr('id'); // guarda el id de orden en var global id_orden
    $.ajax({
      type: 'POST',
      data: { id_orden: id_orden },
      url: '<?php echo base_url(MAN); ?>Ordenservicio/setEstado',
      success: function (data) {
        linkTo('<?php echo base_url(MAN); ?>Ordenservicio/index/' + $('#permission').val());
      },
      error: function (result) {
        alert("Error en cambio de estado");
      },
      dataType: 'json'
    });
  });

  // Cambia el estado de solicitud de servicio 
  $(".fa-thumbs-up").click(function () {

    var id_solServ = $(this).parent('td').parent('tr').attr('id'); // guarda el id de orden en var global id_solServ
    $.ajax({
      type: 'POST',
      data: { id_solServ: id_solServ },
      url: '<?php echo base_url(MAN); ?>Ordenservicio/setEstado',
      success: function (data) {
        linkTo('<?php echo base_url(MAN); ?>Ordenservicio/index/' + $('#permission').val());
      },
      error: function (result) {
        alert("Error en cambio de estado");
      },
      dataType: 'json'
    });
  });

  //cierro todos los collapse
  $('#modalOrder').on('shown.bs.modal', function () {
    $('.collapse-group').find('.collapse').collapse('hide');
  });
  //cierro collapse al abrir otro
  $('.collapse-group').on('show.bs.collapse', '.collapse', function () {
    $('.collapse-group').find('.collapse.in').collapse('hide');
  });

  // ajusto ancho de columnas
  $('#collapseZero, #collapseOne, #collapseTwo, #collapseThree, #collapseFour').on('shown.bs.collapse', function () {
    $($.fn.dataTable.tables(true)).DataTable()
      .columns.adjust();
  });

  $('#tblorden').DataTable({
    <?php echo (!DT_SIZE_ROWS ? '"paging": false,' : null) ?>
  "aLengthMenu": [10, 25, 50, 100],
    "columnDefs": [{
      "targets": [0],
      "searchable": false
    },
    {
      "targets": [0],
      "orderable": false
    }],
    "order": [[1, "desc"]],
    "ordering": true,
    "language": {
      "decimal": "",
      "emptyTable": "No hay datos disponibles",
      "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
      "infoEmpty": "Mostrando 0 a 0 de 0 registros",
      "infoFiltered": "(filtrado de _MAX_ registros totales)",
      "infoPostFix": "",
      "thousands": ",",
      "lengthMenu": "Mostrar _MENU_ registros",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "search": "Buscar:",
      "zeroRecords": "No se encontraron registros coincidentes",
      "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
      },
      "aria": {
        "sortAscending": ": activar para ordenar columna ascendente",
        "sortDescending": ": activar para ordenar columna descendente"
      }
    }
  });

  $('#modLectura, #modTarea, #modHerram, #modInsum, #modRecurso').DataTable({
    "aLengthMenu": [10, 25, 50, 100],
    "order": [[0, "asc"]],
  });

  $('#filtro_estado').on('change', function () {
    var val = $(this).val();
    $('#tblorden').DataTable().column(11).search(val ? '^' + val + '$' : '', true, false).draw();
  });

  // Seleccionar todos
  $('#check_todos').on('change', function () {
    var isChecked = $(this).is(':checked');
    $('.check_fila').prop('checked', isChecked);
  });

  // Al cambiar un check individual, desmarcar "seleccionar todos" si no están todos marcados
  $(document).on('change', '.check_fila', function () {
    var allChecked = $('.check_fila:checked').length === $('.check_fila').length;
    $('#check_todos').prop('checked', allChecked);
  });

  $('#btn_verificar').on('click', function () {
    var selected = [];
    $('.check_fila:checked').each(function () {
      selected.push($(this).closest('tr').data('json'));
    });

    if (selected.length === 0) {
      Swal.fire({
        type: 'info',
        title: 'Atención',
        text: 'Por favor, seleccione al menos un informe.'
      });
      return;
    }

    Swal.fire({
      title: '¿Estas seguro?',
      text: "¿Deseas verificar " + selected.length + " informes?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.value) {
        WaitingOpen();
        $.ajax({
          type: 'POST',
          data: {
            selected: selected
          },
          url: '<?php echo base_url(MAN); ?>Ordenservicio/cierreMasivoVerificaInforme',
          success: function (data) {
            WaitingClose();
            Swal.fire(
              'Verificado!',
              'Los informes han sido marcados para verificación.',
              'success'
            ).then(() => {
              linkTo('<?php echo base_url(MAN); ?>Ordenservicio/index/' + $('#permission').val());
            });
          },
          error: function (result) {
            WaitingClose();
            Swal.fire('Error', 'No se pudieron verificar los informes', 'error');
          },
          dataType: 'json'
        });
      }
    });
  });
</script>