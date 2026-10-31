<?php
/**
 * Pantalla de trabajo de una tarea de Mantenimiento dentro del panel de la bandeja de tools (MIG-ASSET-7).
 * Muestra la pantalla de la bandeja de MAN sin lo que ya pone la plantilla de tools (cabecera, comentarios, línea de tiempo, tomar y soltar,
 * y los botones Cerrar y Hecho), y define cerrarTarea(), que es lo que llama el botón "Hecho" del panel.
 * Datos: $vista (pantalla), $data (datos de la pantalla), $nombre (tarea).
 */
?>
<style>
    .man-panel section.content { padding: 0; min-height: 0; }
    .man-panel .nav-tabs, .man-panel #profile, .man-panel #messages { display: none !important; }
    .man-panel #btontomar, .man-panel #btonsoltr, .man-panel #cerrar, .man-panel #hecho { display: none !important; }
</style>
<div class="man-panel">
    <?php $this->load->view(MAN . $vista, $data); ?>
</div>
<?php $this->load->view(MAN . 'tareas/scripts/panel_std', array('nombre' => $nombre, 'permission' => $data['permission'])); ?>
