<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="componente-man">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Componentes</h3>
          <?php
          if (strpos($permission,'Add') !== false) {
            echo '<button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" data-toggle="modal" data-target="#modalAddComp">Agregar</button>';
          }
          ?>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table id="tbl-componentes" class="table table-bordered table-hover">
            <thead>
              <tr>                
                <th>Acciones</th>
                <th>Marca</th> 
                <th>Descripción</th>   
                <th>Información</th>
                <th>Adjunto</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($list as $a)
              {
                //dump($a);
                $idc     = 0;
                $idcom   = $a['id_componente'];
                $idmarca = $a['marcaid'];
                echo '<tr data-idcomponente="'.$idcom.'" data-idmarca="'.$idmarca.'">';
                echo '<td>';
                if (strpos($permission,'Edit') !== false) {
                  echo '<a href="#" class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Editar componente" data-toggle="modal" data-target="#modalEditar"></a>';
                }
                if (strpos($permission,'Del') !== false) {
                  echo '<a href="#" class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 15px;" title="Eliminar componente" data-toggle="modal" data-target="#modaleliminar"></a>';
                }
                echo '</td>';
                echo '<td>'.$a['marcadescrip'].'</td>';
                echo '<td>'.$a['descripcion'].'</td>';
                echo '<td>'.$a['informacion'].'</td>';
                if($a['pdf'] != NULL){
                  $url = man_asset_url().'assets/files/equipos/'.$a['pdf']; 
                  echo '<td><a href="'.$url.'" target="_blank">Adjunto</td>';
                }else{
                  echo '<td>Sin Adjuntos</td>';
                }
               
                //echo '<td><a href="'.$url.'" target="_blank">'.basename($a['pdf']).'</td>';
                $idc++;
              }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.componente-man -->

<!-- Modal Agregar componente-->
<div class="modal" id="modalAddComp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="fa fa-plus-square text-light-blue"></span> Agregar Componente</h4>
       </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <div class="alert alert-danger alert-dismissable" id="errorComponentes" style="display: none">
          <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
          Complete todos los datos obligatorios.
        </div>
        <form id="formComponentes" enctype="multipart/form-data">
          <div class="row" >
            <div class="alert alert-danger alert-dismissable" id="error1" style="display: none">
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              Revise que todos los campos esten completos...                  
            </div>
            <div class="col-xs-12 col-sm-6">
              <label>Marca <strong style="color: #dd4b39">*</strong>: </label>
              <select class="form-control input-md" id="ma" name="ma" />
            </div>
            <div class="col-xs-12 col-sm-6">
              <label>Descripción <strong style="color: #dd4b39">*</strong>: </label>
              <input type="text"   class="form-control input-md" id="descrip1"  name="descrip1" placeholder="Ingrese Descripcion" />
            </div>
            <div class="col-xs-12"><label>Información:</label>
              <textarea class="form-control" id="info" name="info" placeholder="Ingrese Informacion Adicional"></textarea>
            </div>
            <div class="col-xs-12">
              <label><span class="fa fa-file-pdf-o"></span> Adjuntar</label>
              <input id="inputPDF" name="inputPDF" type="file"  class="form-control input-md">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="guardarComponente">Guardar</button>
        </div>  <!-- /.modal footer -->
        </form>
      </div>  <!-- /.modal-body -->
    </div> <!-- /.modal-content -->

  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal eliminar-->
<div class="modal" id="modaleliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"  id="myModalLabel"><span id="modalAction" class="fa fa-fw fa-times-circle text-light-blue"></span> Eliminar Componente</h4>
      </div> <!-- /.modal-header  -->

      <div class="modal-body" id="modalBodyArticle">
        <p>¿Realmente desea Eliminar Componente? </p>
      </div>  <!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSave" data-dismiss="modal" onclick="eliminarComponente()" >Eliminar</button>
      </div>  <!-- /.modal footer -->
      
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->

<!-- Modal Editar componente-->
<div class="modal" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="fa fa-plus-square text-light-blue"></span> Agregar Componente</h4>
      </div> <!-- /.modal-header  -->

      <form id="formComponentesE" enctype="multipart/form-data">
        <div class="modal-body" id="modalBodyArticle">
          <div class="alert alert-danger alert-dismissable" id="errorComponentesE" style="display: none">
            <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
            Complete todos los datos obligatorios.
          </div>
          <input type="hidden" name="idComponenteE" id="idComponenteE">
          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <label>Marca <strong style="color: #dd4b39">*</strong>: </label>
              <select class="form-control input-md" id="marcaE" name="marcaE" />
            </div>
            <div class="col-xs-12 col-sm-6">
              <label>Descripción <strong style="color: #dd4b39">*</strong>: </label>
              <input type="text"   class="form-control input-md" id="descripcionE"  name="descripcionE" placeholder="Ingrese Descripcion" />
            </div>
            <div class="col-xs-12"><label>Información:</label>
              <textarea class="form-control" id="informacionE" name="informacionE" placeholder="Ingrese Informacion Adicional"></textarea>
            </div>
          </div>
          <div class="row"><hr>
            <div class="col-xs-12">
              <label><span class="fa fa-file-pdf-o"></span> Archivo adjunto: <span id="pdfanterior"></span></label>
              <input id="pdfE" name="pdfE" type="file"  class="form-control input-md">
            </div>
          </div>
        </div>  <!-- /.modal-body -->

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="editarComponente">Editar</button>
        </div>  <!-- /.modal footer -->
      </form>
    
    </div> <!-- /.modal-content -->
  </div>  <!-- /.modal-dialog modal-lg -->
</div>  <!-- /.modal fade -->
<!-- / Modal -->
<script>
var idComponente = "";

// Trae marcas para modal agregar componente
traer_marca();
function traer_marca(){ // Ok
  $.ajax({
    data: { },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo base_url(MAN); ?>Componente/getmarca', 
    success: function(data){
      var opcion = "<option value='-1'>Seleccione...</option>" ; 
      $('#ma').append(opcion); 
      for(var i=0; i < data.length ; i++) 
      {    
        var nombre = data[i]['marcadescrip'];
        var opcion = "<option value='"+data[i]['marcaid']+"'>" +nombre+ "</option>" ; 
        $('#ma').append(opcion); 
      }
    },
    error: function(result){
      console.error("Error al traer marcas");
      console.table(result);
    },
  });
}



// Guarda un componente nuevo
$('#guardarComponente').click(function(e) { //
  e.preventDefault();
  WaitingOpen("Guardando Componente");
  
  var descripcion = $('#descrip1').val();
  var informacion = $('#informacion').val();
  var marcaid     = $('#ma').val();
  var pdf         = $('#input-4').val();
  var parametros  = {
    'descripcion' : descripcion,
    'informacion' : informacion,
    'marcaid'     : marcaid,
    'pdf'         : pdf,
  };                                              
  //console.log("marcaid"+marcaid);
  var hayError = false; 
  $('#errorComponentes').hide();
  if ( marcaid < 0 ) {
    hayError = true;
    console.log("entro x marcaid");
  }
  if( descripcion == "" ) {
    hayError = true;
    console.log("entro x descrip");
  }

  if(hayError == true){
    $('#errorComponentes').fadeIn('slow');
  }
  else{
    var formData = new FormData(document.getElementById("formComponentes"));
    $.ajax({
      cache: false,
      contentType: false,
      data: formData,
      dataType: "html",
      processData: false,
      type: "POST",
      url: "<?php echo base_url(MAN); ?>Componente/agregarComponente", 
      success: function(data){
        $("modalAddComp").modal("hide");
        $(".modal-backdrop.in").hide();
        regresa();
      },
      error: function(result){
          console.error("Error al crear componente");
          console.table(result);
      },
    });
  }
});



// Saca el id de componente a eliminar
$(".fa-times-circle").click(function (e) { // Ok
    idComponente = $(this).parent('td').parent('tr').data('idcomponente');
    console.log("El id de componente a eliminar es: "+idComponente);
});
// Elimina componente
function eliminarComponente() { // Ok
  $.ajax({
    type: 'POST',
    data: { idComponente:idComponente },
    url: '<?php echo base_url(MAN); ?>Componente/bajaComponente',
    success: function(data){
      regresa();
    },
    error: function(result){
      console.error("Error al eliminar componente");
      console.table(result);
    }
  });
} 



// Llena campos del modal editar
$(".fa-pencil").click(function(e){
  var idComponente = $(this).parent('td').parent('tr').data('idcomponente');
  var idmarca      = $(this).parent('td').parent('tr').data('idmarca');
  var descripcion  = $(this).parent('td').next().next().html();
  var informacion  = $(this).parent('td').next().next().next().html();
  var archivoPDF   = $(this).parent('td').next().next().next().next().children().attr("href");
  $("#idComponenteE").val(idComponente);
  llena_marca(idmarca);
  $("#descripcionE").val(descripcion);
  $("#informacionE").val(informacion);
  //var pdfanterior = "<a href='"+archivoPDF+"' target='_blank'>"+archivoPDF.split('/').pop()+"</a>";
  var pdfanterior = "<a href='"+archivoPDF+"' target='_blank'>"+archivoPDF+"</a>";
  $("#pdfanterior").html(pdfanterior);
});

// Llena select marcas del modal Editar
function llena_marca(idMarca){ // Ok
  $.ajax({
    data: { },
    dataType: 'json',
    type: 'POST',
    url: '<?php echo base_url(MAN); ?>Componente/getmarca', 
    success: function(data){
      var opcion = "<option value='-1'>Seleccione...</option>" ; 
      $('#marcaE').empty(); 
      for(var i=0; i < data.length ; i++) 
      {
        var selected = (idMarca == data[i]['marcaid']) ? 'selected' : ''; 
        var nombre = data[i]['marcadescrip'];
        var opcion = "<option value='"+data[i]['marcaid']+"' "+selected+">" +nombre+ "</option>" ; 
        $('#marcaE').append(opcion); 
      }
    },
    error: function(result){
      console.error("Error al traer marcas");
      console.table(result);
    },
  });
}



$("#editarComponente").click(function(e){
  e.preventDefault();

  WaitingOpen("Guardando Componente");
  idComponente = $("#idComponenteE").val();
  idmarca      = $("#marcaE").val();
  descripcion  = $("#descripcionE").val();
  informacion  = $("#informacionE").val();
  pdf          = $("#pdfE").val();

  var hayError = false; 
  $('#errorComponentesE').hide();
  if ( idmarca < 0 ) {
    hayError = true;
    console.log("entro x marcaid");
  }
  if( descripcion == "" ) {
    hayError = true;
    console.log("entro x descrip");
  }

  if(hayError == true){
    $('#errorComponentesE').fadeIn('slow');
  }
  else{
    //Preparo los datos para enviarlos al controlador
    var formData = new FormData(document.getElementById("formComponentesE"));
    /*formData.append('id_componente', idComponente);
    formData.append('descripcion', descripcion);
    formData.append('informacion', informacion);
    formData.append('marcaid', idmarca); */

    //ajax
    $.ajax({
      cache: false,
      contentType: false,
      data: formData,
      dataType: "html",
      processData: false,
      type: "POST",
      url: "<?php echo base_url(MAN); ?>Componente/editarComponente",
      success: function(data){
        console.debug(data);
        $("modalEditar").modal("hide");
        $(".modal-backdrop.in").hide();
        regresa();
        WaitingClose();
      },
      error: function(result){
        console.error("Error al editar componente");
        console.table(result);
        WaitingClose();
      },
    });
  }
});



// recarga la vista del listado de componentes
function regresa(){ // Ok
  linkTo("<?php echo base_url(MAN); ?>Componente/index/<?php echo $permission; ?>");
  WaitingClose();
}


$('#tbl-componentes').DataTable({
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
</script>