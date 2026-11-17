<?php
/**
 * Acciones comunes de las pantallas de tarea de la Bandeja (MIG-ASSET-3). Portado de assets/props/notificacion_estandar.js de asset:
 * tomar y soltar, volver a la bandeja y cerrar la tarea según su tipo. Se omite el modo sin conexión (service worker) y las
 * funciones que apuntaban a acciones que asset no tiene (terminarTarea, estado, Taller/TareaRealizada).
 * Cada acción responde {status, msj}; si falla se muestra el mensaje.
 */
?>
<script>
(function () {
    var permiso = <?php echo json_encode(isset($permission) ? $permission : ''); ?>;
    var acciones = '<?php echo base_url(MAN); ?>Tarea/';

    // Con la tarea tomada se habilita todo lo que cambia datos; sin tomar, solo se puede leer
    window.evaluarEstado = function () {
        if ($('#asignado').val() != '') { habilitar(); } else { deshabilitar(); }
    };
    window.habilitar = function () {
        $('#btonsoltr, #hecho, #guardarComentario, #comentario, #formulario, #acciones_view, .columheader, .getFormularioTarea, #pedidoInsumos, #decisionSolicitud, #verOrden, .panel_form, .oculto').show();
        $('#btontomar').hide();
    };
    window.deshabilitar = function () {
        $('.oculto').hide();
        $('#btontomar').show();
        $('#btonsoltr, #hecho, #guardarComentario, #comentario, #formulario, #acciones_view, .columheader, .getFormularioTarea, #pedidoInsumos, #decisionSolicitud, #verOrden').hide();
    };

    // Vuelve a la bandeja
    window.cargarVista = function () {
        WaitingOpen();
        linkTo(acciones + 'index/' + permiso);
        WaitingClose();
    };
    $('#cerrar').off('click').on('click', function () { cargarVista(); });

    // Llama a una acción de la tarea; al terminar bien vuelve a la bandeja
    function accion(nombre, datos, mensajeOk, mensajeEspera) {
        WaitingOpen(mensajeEspera || 'Cerrando Tarea');
        $.ajax({ type: 'POST', url: acciones + nombre, data: datos, dataType: 'json' })
            .done(function (r) {
                WaitingClose();
                if (r && r.status) {
                    if (mensajeOk) { alert(mensajeOk); }
                    cargarVista();
                } else {
                    alert((r && r.msj) ? r.msj : 'No se pudo completar la operación');
                }
            })
            .fail(function () {
                WaitingClose();
                alert('No se pudo completar la operación');
            });
    }

    // Toma o suelta la tarea en Bonita
    function asignar(nombre, despues) {
        $.ajax({ type: 'POST', url: acciones + nombre, data: { idTarBonita: $('#idTarBonita').val() }, dataType: 'json' })
            .done(function (r) {
                if (r && r.status) { despues(); } else { alert((r && r.msj) ? r.msj : 'No se pudo completar la operación'); }
            })
            .fail(function () { alert('No se pudo completar la operación'); });
    }
    window.tomarTarea = function () { asignar('tomarTarea', function () { $('#asignado').val('1'); habilitar(); }); };
    window.soltarTarea = function () { asignar('soltarTarea', function () { $('#asignado').val(''); deshabilitar(); }); };

    // Análisis de la solicitud: decide si es urgente
    window.decidirUrgencia = function () {
        var opcion = $('input[name="opcion"]:checked').val();
        accion('decidirUrgencia', { opcion: opcion, idTarBonita: $('#idTarBonita').val() },
            opcion == 'true' ? 'Guardado de Solicitud Urgente Exitoso!' : 'Guardado de Solicitud No Urgente Exitoso', 'Guardando');
    };
    // Verificar informe: conforme o no, con el motivo si no lo está
    window.verificarInforme = function () {
        var opcion = $('input[name="opcion"]:checked').val();
        if (opcion === undefined) { alert('Elegí si el informe está conforme o no'); return; }
        // la justificación solo se guarda cuando el informe no está conforme
        accion('verificarInforme', { opcion: opcion, idTarBonita: $('#idTarBonita').val(), justificacion: $('#justificacion').val() || '' });
    };
    // Prestar conformidad con el trabajo
    window.prestarConformidad = function () {
        if ($('input[name="opcion"]:checked').val() === undefined) { alert('Elegí si prestás conformidad o no'); return; }
        accion('prestarConformidad', { opcion: $('input[name="opcion"]:checked').val(), idTarBonita: $('#idTarBonita').val() }, null, 'Cerrando Tarea');
    };
    // Terminar "Ejecutar OT"
    window.ejecutarOT = function () {
        accion('ejecutarOT', { idTarBonita: $('#idTarBonita').val() });
    };

    evaluarEstado();
})();
</script>
