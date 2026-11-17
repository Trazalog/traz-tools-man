<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parametrizar predictivo: parámetros por equipo (máximo/mínimo) — MIG-ASSET-1.
 *
 * Portado desde asset sin las acciones que ninguna vista usa y que llamaban a métodos inexistentes
 * del model (baja_parametro, bajaparametro, geteditar, agregar_componente, agregarcomponente).
 */
class Parametro extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Parametros');
		$this->load->helper('man');
	}

	/**
	* Carga vista principal de parametrizar predictivo
	* @param string $permission acciones habilitadas ("Add-Edit-Del"); sin parámetro, solo lectura (E3)
	*/
	public function index($permission = ''){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | index($permission)");
		if (man_empresa() === null) {
			echo '<div class="alert alert-danger">La empresa no está vinculada con Mantenimiento. Contactá al administrador.</div>';
			return;
		}
		$data['permission'] = $permission;
		$this->load->view('parametro/list', $data);
	}

	/**
	* Trae equipos por empresa logueada
	* @return json listado de equipos, o "nada"
	*/
	public function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | getequipo()");
		$equipo = $this->Parametros->getequipo();
		if ($equipo) {
			echo json_encode(array_values($equipo));
		}
		else echo "nada";
	}

	/**
	* Trae parametros asociados al id de equipo
	* @return json listado de parametros, o "nada"
	*/
	public function getparametros(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | getparametros()");
		$id         = $this->input->post('id_equipo');
		$parametros = $this->Parametros->getparametros($id);
		if ($parametros) {
			echo json_encode(array_values($parametros));
		}
		else echo "nada";
	}

	/**
	* Trae la asociación a editar. La vista manda la fila (parent) y el array de ids de parámetro (comp).
	*/
	public function editar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | editar()");
		$id  = $this->input->get('e');      // id_equipo
		$pp  = $this->input->get('parent'); // número de fila
		$cop = $this->input->get('comp');   // ids de parámetro
		$result = false;
		if ($pp !== null && is_array($cop) && !empty($cop[$pp])) {
			$result = $this->Parametros->editar($id, $cop[$pp]);
		}
		if ($result) {
			$arre['datos'] = $result;
			echo json_encode($arre);
		}
		else echo "nada";
	}

	/**
	* Guarda la asociación de parámetros editada y devuelve la lista actualizada del equipo
	*/
	public function guardarmodif(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | guardarmodif()");
		$datos = $this->input->post('datos');
		if ($datos) {
			$equ    = $datos['id_equipo'];
			$result = $this->Parametros->update_editar($datos['maximo'], $datos['minimo'], $datos['id_parametro'], $equ);
			$parametros = $result ? $this->Parametros->getparametros($equ) : false;
			echo json_encode($parametros);
		}
	}

	/**
	* Elimina la asociación y devuelve la lista actualizada del equipo
	*/
	public function eliminar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | eliminar()");
		$id_equipoElim    = $this->input->post('id_equipoElim');
		$id_parametroElim = $this->input->post('id_parametroElim');
		$response   = $this->Parametros->elimina_param($id_equipoElim, $id_parametroElim);
		$parametros = $response ? $this->Parametros->getparametros($id_equipoElim) : false;
		echo json_encode($parametros);
	}

	/**
	* Guarda asociación nueva de parámetros y devuelve la lista actualizada del equipo
	*/
	public function guardar_todo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | guardar_todo()");
		$datos     = $this->input->post('data');
		$id_equipo = $datos['id_equipo'];
		$response  = $this->Parametros->guardar_todo($datos);
		$parametros = $response ? $this->Parametros->getparametros($id_equipo) : false;
		echo json_encode($parametros);
	}

	/**
	* Guarda parametro nuevo
	* @return json id de inserción
	*/
	public function guardar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | guardar()");
		$datos = $this->input->post('data');
		$id_insercion = $this->Parametros->guardar($datos);
		echo json_encode($id_insercion);
	}

	/**
	* Trae parametros para asociar
	* @return json listado de parametros, o "nada"
	*/
	public function traerparametro(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametro | traerparametro()");
		$parametros = $this->Parametros->traerparametro();
		if ($parametros) {
			echo json_encode(array_values($parametros));
		}
		else echo "nada";
	}
}
