<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Componentes: catálogo (ABM) y asignación de componentes a equipos.
 *
 * MIG-ASSET-1: todos los datos por MANDataService (ADR-014). Se conservan firmas y formato de salida
 * del model de asset (result() → objetos, result_array() → arrays, false sin filas). Toda operación
 * queda limitada a la empresa de la sesión.
 */
class Componentes extends CI_Model{

	/** @var int id del último componente creado (lo que en asset se leía con insert_id()). */
	public $ultimo_id = 0;

	function __construct(){
		parent::__construct();
		$this->load->helper('man');
	}

	/** Catálogo de componentes activos con su marca. @return array|false */
	function listadoABM(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | listadoABM()");
		return $this->arrays(wso2(REST_MAN . '/componentes/abm/empresa/' . man_empresa()));
	}

	/** Componentes asignados a equipos de la empresa. @return array|false */
	function componentes_List(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | componentes_List()");
		return $this->arrays(wso2(REST_MAN . '/componentes/equipos/empresa/' . man_empresa()));
	}

	/** Equipos (no anulados) de la empresa, todas las columnas. @return array|false */
	function traerequipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | traerequipo()");
		return $this->arrays(wso2(REST_MAN . '/componentes/equipos-empresa/' . man_empresa()));
	}

	/**
	 * Descripción de un equipo de la empresa.
	 * En asset este método siempre fallaba (get_where con un escalar y $row['descripcion'] sobre un objeto);
	 * acá devuelve lo que su controller espera (decisión E12).
	 * @return array|null array('descripcion' => ...)
	 */
	function getequipo($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getequipo(" . json_encode($id) . ")");
		$filas = man_lista(wso2(REST_MAN . '/componentes/equipo/' . (int) $id . '/descripcion/empresa/' . man_empresa()));
		return $filas ? array('descripcion' => $filas[0]['descripcion']) : null;
	}

	/** Marcas no anuladas de la empresa. @return array|false objetos */
	function getmarca(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getmarca()");
		return $this->objetos(wso2(REST_MAN . '/componentes/marcas/empresa/' . man_empresa()));
	}

	/** Componentes para autocompletar (label/value). @return array */
	function getcomponente(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getcomponente()");
		$filas = man_lista(wso2(REST_MAN . '/componentes/autocompletar/empresa/' . man_empresa()));
		return $filas ? $filas : array();
	}

	/** Sistemas activos de la empresa. @return array|false objetos */
	function getsistema(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getsistema()");
		return $this->objetos(wso2(REST_MAN . '/componentes/sistemas/empresa/' . man_empresa()));
	}

	/**
	 * Alta de componente en el catálogo de la empresa. El id creado queda en $this->ultimo_id.
	 * @return bool
	 */
	function agregar_componente($insert){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | agregar_componente(" . json_encode($insert) . ")");
		$rsp = man_escribir('POST', 'componente', array(
			'descripcion' => (string) $insert['descripcion'],
			'informacion' => (string) $insert['informacion'],
			'marcaid'     => (string) $insert['marcaid'],
			'estado'      => isset($insert['estado']) ? (string) $insert['estado'] : '',
			'id_empresa'  => man_empresa(),
		));
		$ok = $rsp['status'] && is_scalar($rsp['data']);
		$this->ultimo_id = $ok ? (int) $rsp['data'] : 0;
		return $ok;
	}

	/** Asocia un componente a un equipo de la empresa. @return bool */
	function insert_componente($data2){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | insert_componente(" . json_encode($data2) . ")");
		$rsp = man_escribir('POST', 'componente/equipo', array(
			'id_componente' => (string) $data2['id_componente'],
			'codigo'        => (string) $data2['codigo'],
			'sistemaid'     => (string) $data2['sistemaid'],
			'id_equipo'     => (string) $data2['id_equipo'],
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Componentes activos asociados a un equipo de la empresa. @return array|false objetos */
	function getcompo($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getcompo(" . json_encode($id) . ")");
		return $this->objetos(wso2(REST_MAN . '/componentes/equipo/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/** Guarda el nombre del archivo del componente. @return bool */
	function updatecomp($ultimoId, $update){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | updatecomp($ultimoId)");
		$rsp = man_escribir('PUT', 'componente/pdf', array(
			'pdf'           => (string) $update['pdf'],
			'id_componente' => (string) $ultimoId,
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Baja de la asociación equipo/componente. @return bool */
	function delete_asociacion($idequip, $idcomp){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | delete_asociacion($idequip, " . json_encode($idcomp) . ")");
		$rsp = man_escribir('PUT', 'componente/equipo/baja', array(
			'id_componente' => (string) $idcomp,
			'id_equipo'     => (string) $idequip,
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Datos para el modal de editar una asociación. @return array|false objetos */
	function getEditar($idCompEq){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | getEditar($idCompEq)");
		return $this->objetos(wso2(REST_MAN . '/componente/equipo/' . (int) $idCompEq . '/empresa/' . man_empresa()));
	}

	/** Modifica una asociación (equipo, componente y código). @return bool */
	function updateEditar($data, $id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | updateEditar(" . json_encode($data) . ", $id)");
		$rsp = man_escribir('PUT', 'componente/equipo', array(
			'id_equipo'          => (string) $data['id_equipo'],
			'id_empresa'         => man_empresa(),
			'id_componente'      => (string) $data['id_componente'],
			'codigo'             => (string) $data['codigo'],
			'idcomponenteequipo' => (string) $id,
		));
		return (bool) $rsp['status'];
	}

	/** Baja lógica de un componente del catálogo. @return bool */
	function bajaComponente($idcomp){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | bajaComponente($idcomp)");
		$rsp = man_escribir('PUT', 'componente/baja', array(
			'id_componente' => (string) $idcomp,
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Modifica un componente del catálogo (pdf solo si viene). @return bool */
	function editarComponente($datos, $idComponente){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Componentes | editarComponente($idComponente)");
		$rsp = man_escribir('PUT', 'componente', array(
			'descripcion'   => (string) $datos['descripcion'],
			'informacion'   => (string) $datos['informacion'],
			'marcaid'       => (string) $datos['marcaid'],
			'pdf'           => isset($datos['pdf']) ? (string) $datos['pdf'] : '',
			'id_componente' => (string) $idComponente,
			'id_empresa'    => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/** Filas como arrays (result_array()), o false si no hay. */
	private function arrays($rsp){
		$filas = man_lista($rsp);
		return $filas ? $filas : false;
	}

	/** Filas como objetos (result()), o false si no hay. */
	private function objetos($rsp){
		$filas = man_lista($rsp);
		return $filas ? json_decode(json_encode($filas)) : false;
	}
}
