<?php
/**
 * Acciones de las pantallas de tarea cuando se muestran en el panel de la bandeja de tools (MIG-ASSET-7). Misma lógica que
 * tareas/scripts/tarea_std.php (bandeja de MAN), con dos diferencias: tomar y soltar los hace la plantilla de tools, y al terminar no se
 * navega a otra pantalla sino que se cierra el panel y se actualiza la lista.
 * Datos: $nombre (tarea), $permission.
 */
?>
<script>
(function () {
    var acciones = '<?php echo base_url(MAN); ?>Tarea/';
    var nombre = <?php echo json_encode($nombre); ?>;
    var enviado = false;
    function ok(msj) { if (typeof hecho === 'function') { hecho('Perfecto!', msj); } else { alert(msj); } }
    function mal(msj) { if (typeof error === 'function') { error('Atención', msj); } else { alert(msj); } }

    // Al terminar bien: se cierra el panel y se actualiza la lista de tareas
    window.cargarVista = function () {
        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tareas') && $('#tareas').DataTable().settings()[0].ajax) {
            // Bandeja con tabla de servidor (Proceso2): alcanza con recargar los datos
            $('#tareas').DataTable().ajax.reload(null, false);
            if (typeof closeView === 'function') { closeView(); }
        } else {
            // Bandeja "Mis Tareas" (tabla fija): se vuelve a cargar completa
            linkTo('<?php echo BPM; ?>Proceso');
        }
    };

    // Llama a una acción de la tarea; si sale bien cierra el panel
    function accion(ruta, datos, mensajeOk, mensajeEspera) {
        if (enviado) { return; }
        enviado = true;
        WaitingOpen(mensajeEspera || 'Cerrando Tarea');
        $.ajax({ type: 'POST', url: acciones + ruta, data: datos, dataType: 'json' })
            .done(function (r) {
                WaitingClose();
                if (r && r.status) {
                    if (mensajeOk) { ok(mensajeOk); }
                    cargarVista();
                } else {
                    enviado = false;
                    mal((r && r.msj) ? r.msj : 'No se pudo completar la operación');
                }
            })
            .fail(function () { enviado = false; WaitingClose(); mal('No se pudo completar la operación'); });
    }
    var tarea = function () { return $('#idTarBonita').val() || $('#taskId').val(); };

    window.decidirUrgencia = function () {
        var opcion = $('input[name="opcion"]:checked').val();
        accion('decidirUrgencia', { opcion: opcion, idTarBonita: tarea() },
            opcion == 'true' ? 'Guardado de Solicitud Urgente Exitoso!' : 'Guardado de Solicitud No Urgente Exitoso', 'Guardando');
    };
    window.verificarInforme = function () {
        var opcion = $('input[name="opcion"]:checked').val();
        if (opcion === undefined) { mal('Elegí si el informe está conforme o no'); return; }
        accion('verificarInforme', { opcion: opcion, idTarBonita: tarea(), justificacion: $('#justificacion').val() || '' });
    };
    window.prestarConformidad = function () {
        if ($('input[name="opcion"]:checked').val() === undefined) { mal('Elegí si prestás conformidad o no'); return; }
        accion('prestarConformidad', { opcion: $('input[name="opcion"]:checked').val(), idTarBonita: tarea() }, null, 'Cerrando Tarea');
    };
    window.ejecutarOT = function () { accion('ejecutarOT', { idTarBonita: tarea() }); };

    // Con la tarea tomada se muestran los controles que solo sirven para trabajar (en la bandeja de MAN los mostraba habilitar()); la
    // plantilla de tools solo quita el velo, así que se mira el velo cada medio segundo mientras el panel siga abierto
    var controles = '#formulario, #acciones_view, .columheader, .getFormularioTarea, #pedidoInsumos, #decisionSolicitud, #verOrden, .panel_form, .oculto';
    function estadoControles() {
        if ($('#view').length === 0) { clearInterval(window.__manPanelTimer); return; }
        if ($('#view > .overlay').length === 0) { $(controles).show(); } else { $(controles).hide(); }
    }
    clearInterval(window.__manPanelTimer);
    estadoControles();
    window.__manPanelTimer = setInterval(estadoControles, 500);

    // Las pantallas que al terminar volvían a la bandeja de MAN (Editar Backlog) ahora cierran el panel
    window.Refrescar = function () { WaitingClose(); cargarVista(); };

    // Lo que hace el botón "Hecho" del panel, según la tarea. Si la pantalla no tiene la función, avisa
    function llamar(fn) {
        if (typeof window[fn] === 'function') { window[fn](); } else { mal('Esta tarea se completa con los botones de su pantalla.'); }
    }
    window.cerrarTarea = function () {
        switch (nombre) {
            case 'Analisis de Solicitud de Servicio': decidirUrgencia(); break;
            case 'Verifica Informe de Servicio': verificarInforme(); break;
            case 'Presta conformidad': prestarConformidad(); break;
            case 'Ejecutar OT': ejecutarOT(); break;
            case 'Planificar Solicitud': case 'Planificar Backlog': llamar('verOT'); break;                  // va al Plan de Mantenimiento
            case 'Confecciona informe servicio': llamar('generar_informe_servicio'); break;                    // abre el informe
            case 'Editar Backlog': llamar('guardar'); break;                                                   // guarda y reenvía
            case 'Esperando cambio estado "a Ejecutar"': case 'Esperando cambio estado "a Ejecutar" 2': llamar('redirect_calendario'); break;
            default:
                if (nombre.indexOf('Asignar') === 0) { llamar('asignarTareas'); } else { mal('Esta tarea se completa con los botones de su pantalla.'); }
        }
    };
})();
</script>
