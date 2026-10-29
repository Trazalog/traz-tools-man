<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Lecturas de parámetros de equipo (parametroequipo).
 *
 * MIG-ASSET-3: todos los datos por MANDataService (ADR-014), limitados a la empresa de la sesión. Asset no
 * filtraba por empresa las lecturas de un día, el alta ni la baja (T35). Se conservan firmas y formato de
 * salida del model de asset, salvo lectura_List, que la vista no usa y listaba las lecturas de todas las
 * empresas: no se porta.
 */
class Lecturas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	* Equipos activos de la empresa con parámetros asociados, para el autocompletar
	* @return array filas (label, id)
	*/
	function getEquipo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lecturas | getEquipo()");
		$out = array();
		$filas = man_lista(wso2(REST_MAN . '/lecturas-param/equipos/empresa/' . man_empresa()));
		foreach ($filas ? $filas : array() as $f) {
			$out[] = array('label' => $f['label'], 'id' => $f['id']);
		}
		return $out;
	}

	/**
	* Parámetros asociados al equipo
	* @return array filas (param, id)
	*/
	function getParametrosAsoc($equipoId){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lecturas | getParametrosAsoc(" . json_encode($equipoId) . ")");
		$out = array();
		$filas = man_lista(wso2(REST_MAN . '/lecturas-param/equipo/' . (int) $equipoId . '/parametros/empresa/' . man_empresa()));
		foreach ($filas ? $filas : array() as $f) {
			$out[] = array('param' => $f['param'], 'id' => $f['id']);
		}
		return $out;
	}

	/**
	* Guarda la lectura de un parámetro (solo sobre equipos de la empresa)
	* @param array $datas2 paramId, id_equipo, valor, fechahora
	* @return bool
	*/
	function guardar_lectura($datas2){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lecturas | guardar_lectura(" . json_encode($datas2) . ")");
		$rsp = man_escribir('POST', 'lecturas-param', array(
			'paramId'    => (string) $datas2['paramId'],
			'valor'      => (string) $datas2['valor'],
			'fechahora'  => (string) $datas2['fechahora'],
			'id_equipo'  => (string) $datas2['id_equipo'],
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	* Lecturas del equipo en el día de la fecha dada
	* @return array filas (id, valor, paramdescrip)
	*/
	function getLecturasCargadas($fechaformateada, $idequipo){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lecturas | getLecturasCargadas($fechaformateada, $idequipo)");
		$fecha = date("Y-m-d", strtotime($fechaformateada));
		$filas = man_lista(wso2(REST_MAN . '/lecturas-param/equipo/' . (int) $idequipo . '/fecha/' . $fecha . '/empresa/' . man_empresa()));
		return $filas ? $filas : array();
	}

	/**
	* Borra una lectura de la empresa
	* @return bool
	*/
	function deleteLectura($id){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lecturas | deleteLectura(" . json_encode($id) . ")");
		$rsp = man_escribir('POST', 'lecturas-param/baja', array('id' => (string) (int) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}
}
