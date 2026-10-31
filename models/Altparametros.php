<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Parámetros.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Altparametros.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Altparametros extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Parámetros activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Altparametros()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametros | Listado_Altparametros()');
		return man_lista(wso2(REST_MAN . '/abm/altparametros/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Altparametros($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametros | Obtener_Altparametros(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/altparametro/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Altparametros($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametros | Guardar_Altparametros()');
		$rsp = man_escribir('POST', 'abm/altparametro', array(
			'paramdescrip' => (string) (isset($data['paramdescrip']) ? $data['paramdescrip'] : ''),
			'min' => (string) (isset($data['min']) ? $data['min'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Altparametros($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametros | Modificar_Altparametros(' . json_encode($data['paramId']) . ')');
		$rsp = man_escribir('PUT', 'abm/altparametro', array(
			'paramId' => (string) $data['paramId'],
			'paramdescrip' => (string) (isset($data['paramdescrip']) ? $data['paramdescrip'] : ''),
			'min' => (string) (isset($data['min']) ? $data['min'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Altparametros($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Altparametros | Eliminar_Altparametros(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/altparametro/baja', array(
			'paramId'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
