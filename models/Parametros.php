<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Parámetros de equipo (setupparam) y catálogo de parámetros.
 *
 * MIG-ASSET-1: todos los datos por MANDataService (ADR-014). Se conservan firmas y formato de salida
 * del model de asset (objetos para result(), arrays para result_array(), false/0 sin filas). Toda
 * operación queda limitada a equipos de la empresa de la sesión.
 */
class Parametros extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	* Trae equipos activos de la empresa logueada
	* @return array|false listado de equipos (objetos id_equipo, codigo)
	*/
	function getequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | getequipo()");
		return $this->objetos(wso2(REST_MAN . '/parametros/equipos/empresa/' . man_empresa()));
	}

	/**
	* Trae parametros asociados al id de equipo
	* @param integer $id id_equipo
	* @return array|false listado de parametros (objetos)
	*/
	function getparametros($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | getparametros(" . json_encode($id) . ")");
		return $this->objetos(wso2(REST_MAN . '/parametros/equipo/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	* Trae info para editar la asociación de un parámetro a un equipo
	* @return array|int filas (arrays) o 0
	*/
	function editar($id_equipo, $id_param){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | editar($id_equipo, $id_param)");
		$filas = man_lista(wso2(REST_MAN . '/parametros/equipo/' . (int) $id_equipo . '/parametro/' . (int) $id_param
			. '/empresa/' . man_empresa()));
		return $filas ? $filas : 0;
	}

	/**
	* Actualiza máximo y mínimo de la asociación
	* @return bool
	*/
	function update_editar($m, $n, $pa, $equ){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | update_editar()");
		$rsp = man_escribir('PUT', 'parametros/equipo', array(
			'maximo'       => (string) $m,
			'minimo'       => (string) $n,
			'id_equipo'    => (string) $equ,
			'id_parametro' => (string) $pa,
			'id_empresa'   => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	* Elimina la asociación de un parámetro a un equipo
	* @return bool
	*/
	function elimina_param($ide, $idp){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | elimina_param($ide, $idp)");
		$rsp = man_escribir('PUT', 'parametros/equipo/baja', array(
			'id_equipo'    => (string) $ide,
			'id_parametro' => (string) $idp,
			'id_empresa'   => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	* Guarda asociación nueva de parámetro a equipo (solo equipos de la empresa)
	* @param array $data id_equipo, id_parametro, maximo, minimo
	* @return bool
	*/
	function guardar_todo($data){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | guardar_todo(" . json_encode($data) . ")");
		$rsp = man_escribir('POST', 'parametros/equipo', array(
			'id_parametro' => (string) $data['id_parametro'],
			'maximo'       => (string) $data['maximo'],
			'minimo'       => (string) $data['minimo'],
			'id_equipo'    => (string) $data['id_equipo'],
			'id_empresa'   => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	* Guarda parámetro nuevo en el catálogo de la empresa
	* @param array $data paramdescrip
	* @return integer|false id de inserción
	*/
	function guardar($data){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | guardar(" . json_encode($data) . ")");
		$rsp = man_escribir('POST', 'parametro', array(
			'paramdescrip' => (string) $data['paramdescrip'],
			'id_empresa'   => man_empresa(),
		));
		// wso2() reduce {"parametro":{"paramId":"N"}} al valor N.
		return ($rsp['status'] && is_scalar($rsp['data'])) ? (int) $rsp['data'] : false;
	}

	/**
	* Trae parámetros del catálogo de la empresa para asociar
	* @return array|false listado de parámetros (objetos)
	*/
	public function traerparametro(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Parametros | traerparametro()");
		return $this->objetos(wso2(REST_MAN . '/parametros/empresa/' . man_empresa()));
	}

	/**
	 * Filas como objetos (lo que devolvía result()), o false si no hay.
	 */
	private function objetos($rsp){
		$filas = man_lista($rsp);
		if (!$filas) {
			return false;
		}
		return json_decode(json_encode($filas));
	}
}
