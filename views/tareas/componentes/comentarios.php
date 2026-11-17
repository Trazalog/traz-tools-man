<?php
/**
 * Comentarios del caso de una tarea (Bonita). Portado de asset: el contenido se escapa (asset lo mostraba tal cual) y, al agregar un
 * comentario, se vuelve a pedir la lista en vez de armar la fila en el navegador.
 * Variables: case_id, idTarBonita, comentarios (filas de Bonita con el usuario).
 */
?>
<input class="hidden" id="case_id" value="<?php echo man_esc($case_id); ?>">
<div class="panel panel-primary">
    <div class="panel-heading">Comentarios</div>
    <div class="panel-body" style="max-height: 500px;overflow-y: scroll;" id="panelComentarios">
        <ul id="listaComentarios">
            <?php foreach ($comentarios as $f) {
                if (isset($f['userId']['userName']) && strcmp($f['userId']['userName'], 'System') != 0) { ?>
                    <hr/>
                    <li>
                        <h4><?php echo man_esc($f['userId']['firstname'] . ' ' . $f['userId']['lastname']); ?>
                            <small style="float: right"><?php echo man_esc(date('H:i  d/m/Y', strtotime($f['postDate']))); ?></small></h4>
                        <p><?php echo nl2br(man_esc($f['content'])); ?></p>
                    </li>
                <?php }
            } ?>
        </ul>
    </div>
</div>
<textarea id="comentario" class="form-control" placeholder="Nuevo Comentario..."></textarea>
<br />
<button class="btn btn-primary" id="guardarComentario" onclick="guardarComentario()">Agregar</button>

<script>
function guardarComentario() {
    var comentario = $.trim($('#comentario').val());
    if (comentario === '') { return; }
    $('#guardarComentario').prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url(MAN); ?>Tarea/GuardarComentario',
        data: { idTarBonita: <?php echo (int) $idTarBonita; ?>, content: comentario },
        dataType: 'json'
    }).done(function (r) {
        if (r && r.status) {
            $('#comentario').val('');
            // vuelve a pedir la lista para ver el comentario con el nombre y la hora que le puso Bonita
            $('#panelComentarios').load('<?php echo base_url(MAN); ?>Tarea/ObtenerComentariosBPM/<?php echo (int) $idTarBonita; ?> #listaComentarios');
        } else {
            alert((r && r.msj) ? r.msj : 'No se pudo guardar el comentario');
        }
    }).fail(function () {
        alert('No se pudo guardar el comentario');
    }).always(function () {
        $('#guardarComentario').prop('disabled', false);
    });
}
</script>
