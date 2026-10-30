<?php
/**
 * Filtro del calendario (Equipos, Areas, Grupos, Sectores y Origenes). Filtra en el navegador los eventos ya cargados.
 * Dato: $filtro, objeto con una lista de objetos "descripcion" por campo.
 */
$this->load->helper('man_tarea');
?>
<div id="filtros" class="row">
    <?php foreach ($filtro as $key => $o) { ?>
    <div class="col-md-12">
        <div class="form-group">
            <label for="flt-<?php echo man_esc($key); ?>"><?php echo man_esc(ucfirst($key)); ?>:</label>
            <input list="lst-<?php echo man_esc($key); ?>" id="flt-<?php echo man_esc($key); ?>" onchange="filtroSelect()"
                class="form-control" placeholder="- Seleccionar -" autocomplete="off">
            <datalist id="lst-<?php echo man_esc($key); ?>">
                <?php foreach ($o as $opt) { echo '<option value="' . man_esc($opt->descripcion) . '">'; } ?>
            </datalist>
            <a href="#" class="flt-clear pull-right text-red"><small><i class="fa fa-times"></i> Limpiar </small></a>
        </div>
    </div>
    <?php } ?>
</div>
