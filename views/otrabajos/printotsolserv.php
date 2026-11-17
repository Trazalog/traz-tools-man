<div class="row">
    <div class="col-xs-12">
        <h1>Orden de trabajo</h1>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <h3>Datos de OT</h3>
        Id de Orden de trabajo: <?php echo $id_ot; ?><br>
        Nro de Orden de trabajo: <?php echo $nro; ?><br>
        Descripción: <?php echo $descripcion_ot; ?><br>
        Fecha Inicio: <?php echo $fecha_inicio; ?><br>
        Fecha de Entrega: <?php echo $fecha_entrega; ?><br>
        Fecha Programada: <?php echo $fecha_program; ?><br>
        Estado: <?php echo $estado; ?><br>
        Sucursal: <?php echo $sucursal; ?><br>
        Proveedor: <?php echo $nombreprov; ?><br>
        Origen: <?php echo $origen; ?><br>
        Asignado: <?php echo $asignado; ?><br>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <h3>Datos de Equipo</h3>
        Equipo: <?php echo $codigo; ?><br>
        Descripción: <?php echo $descripcion_eq; ?><br>
        Marca: <?php echo $marca; ?><br>
        Ubicación: <?php echo $ubicacion; ?><br>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <h3>Datos de Solicitud de Servicio</h3>
        Sector: <?php print_r($solServicio['sector']); ?><br>
        Grupo: <?php print_r($solServicio['grupo']); ?><br>
        Solicitante: <?php print_r($solServicio['solicitante']); ?><br>
        Fecha Sugerida: <?php print_r($solServicio['fechaSugerida']); ?><br>
        Horario Sugerido: <?php print_r($solServicio['horarioSugerido']); ?><br>
        Causa: <?php print_r($solServicio['falla']); ?><br>
    </div>
</div> 
<?php if ($muestraFirma): ?>

<div class="row">
    <div class="col-xs-12">
        <br>
        Fecha y hora inicio: <?php print_r($fecha_inicio); ?><br>
        Fecha y hora fin: <?php print_r($fecha_terminada); ?><br>
    </div>
</div> 


<div class="row" style="display: flex; justify-content: center; align-items: center; margin-top: 150px; text-align: center;">
    
    <div style="margin-right: 50px;">
        ___________________________<br>
        <strong>Firma y Aclaración del Supervisor</strong>
    </div>
    <div style="margin-left: 50px;">
        ___________________________<br>
        <strong>Firma y Aclaración del Mantenedor</strong>
    </div>
</div>
<?php endif; ?>


<div class="row" style="margin-top: 20px;">
    <div class="col-xs-12">
        <h3>Sector ordenado</h3>
        <label style="margin-right: 20px;">
            <span style="display:inline-block; width:14px; height:14px; border:1px solid #000; margin-right:5px; vertical-align:middle;"></span> Sí
        </label>
        <label>
            <span style="display:inline-block; width:14px; height:14px; border:1px solid #000; margin-right:5px; vertical-align:middle;"></span> No
        </label>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-xs-12">
        <h3>Observaciones</h3>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 12px; height: 20px;"></div>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 12px; height: 20px;"></div>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 12px; height: 20px;"></div>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 12px; height: 20px;"></div>
    </div>
</div>