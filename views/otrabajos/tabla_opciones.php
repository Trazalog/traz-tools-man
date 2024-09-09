
<?php
    echo '<div class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
			<i class="fa fa-ellipsis-h text-light-blue opcion" style="cursor: pointer;"></i></a>
			<ul class="dropdown-menu" style="[5:51, 28/3/2019] Mi Princesa: background: -moz-linear-gradient(45deg, rgba(60,148,201,1) 0%, rgba(70,170,232,1) 100%); /* ff3.6+ */
			background: -webkit-gradient(linear, left bottom, right top, color-stop(0%, rgba(60,148,201,1)), color-stop(100%, rgba(70,170,232,1))); /* safari4+,chrome */
			background: -webkit-linear-gradient(45deg, rgba(60,148,201,1) 0%, rgba(70,170,232,1) 100%); /* safari5.1+,chrome10+ */
			background: -o-linear-gradient(45deg, rgba(60,148,201,1) 0%, rgba(70,170,232,1) 100%); /* opera 11.10+ */
			background: -ms-linear-gradient(45deg, rgba(60,148,201,1) 0%, rgba(70,170,232,1) 100%); /* ie10+ */
			background: linear-gradient(45deg, rgba(60,148,201,1) 0%, rgba(70,170,232,1) 100%); /* w3c */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#46aae8", endColorstr="#3c94c9",GradientType=1 );">';

    if (strpos($permission,'Pedidos') !== false) { 

        //echo '<li role="presentation"><a onclick="nota_pedido(this)" style="color:white;" role="menuitem" tabindex="-1" href="#"><i class="fa fa-cart-plus text-white" style="color:white; cursor: pointer;margin-left:-3px"></i>Agregar Nota de Pedido</a></li>';
   
    }

    if (strpos($permission,'Asignar') !== false) {		

			echo '<li role="presentation"><a onclick="verEjecutarOT(this)" style="color:white;" role="menuitem" tabindex="-1" href="#"><i class="fa fa-user text-white" style="color:white; cursor: pointer;" ></i>Asignar Resp y Tareas</a></li>';
               
    }

    if (strpos($permission,'Edit') !== false) {

			// if( ($a['estado'] == 'S') || ($a['estado'] == 'PL') ){
				echo '<li role="presentation"><a onclick="editar(this)" style="color:white;" role="menuitem" tabindex="-1" href="#" data-toggle="modal" data-target="#modaleditar"><i class="fa fa-pencil text-white" style="color:white; cursor: pointer;"></i>Editar</a></li>';
			// }	
    }

    if (strpos($permission,'Del') !== false) {

			//echo '<li role="presentation"><a style="color:white;" role="menuitem" tabindex="-1" href="#" data-toggle="modal" data-target="#modalaviso"><i class="fa fa-times-circle text-white" style="color:white; cursor: pointer;"></i>Eliminar</a></li>';
   
    }

    //// GENERA INFORME DE SERVICIOS
    
    if (strpos($permission,'Del') !== false) {

       //iba a funcion generar informe de servicios
        //echo '<li role="presentation" id="cargOrden"><a onclick="generar_informe_servicio(this)" style="color:white;" role="menuitem" tabindex="-1" href="#" ><i class="fa fa-file-text text-white" style="color:white; cursor: pointer;margin-left:-1px"></i>Informe de Servicios</a></li>';
    }   
  
    if (strpos($permission,'Pedidos') !== false) {

        echo '<li role="presentation"><a onclick="mostrar_pedido(this)"style="color:white;" role="menuitem" tabindex="-1" href="#"><i class="fa fa-truck text-white" style="color:white; cursor: pointer;margin-left:-3px"></i>Pedido de Materiales</a></li>';    
        
    }


    echo '<li role="presentation"><a onclick="mostrarOT(this)"style="color:white;" role="menuitem" tabindex="-1" href="#"><i class="fa fa-search text-white" style="color:white; cursor: pointer;margin-left:-3px"></i>Ver OT</a></li>';
   
    echo '<li role="presentation"><a onclick="imprimir(this)"style="color:white;" role="menuitem" tabindex="-1" href="#"><i class="fa fa-print text-white" style="color:white; cursor: pointer;margin-left:-3px"></i>Imprimir</a></li>';

    // echo '</ul><div>';