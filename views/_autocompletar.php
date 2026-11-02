<?php
/**
 * Arreglo de las listas de autocompletar (jQuery UI) para las pantallas de MAN. Se incluye al principio de cada pantalla que las usa.
 *  - La versión de jQuery UI que carga tools calcula mal la posición con el jQuery nuevo (falla al medir la ventana) y la lista
 *    se quedaba en la esquina de la pantalla: se le da `document.body` como referencia.
 *  - La lista tiene que quedar por encima de las ventanas (modal = 1050).
 * Se ejecuta antes de crear cada autocompletar, así que los toma como valor por defecto.
 */
?>
<style>.ui-autocomplete { z-index: 2000 !important; }</style>
<script>
(function () {
    if ($.ui && $.ui.autocomplete) {
        $.ui.autocomplete.prototype.options.position = { my: 'left top', at: 'left bottom', collision: 'none', within: document.body };
    }
})();
</script>
