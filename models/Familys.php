<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Familias.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Familys.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Familys extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Familias activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Familys()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Familys | Listado_Familys()');
		return man_lista(wso2(REST_MAN . '/abm/familys/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Familys($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Familys | Obtener_Familys(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/family/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Familys($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Familys | Guardar_Familys()');
		$rsp = man_escribir('POST', 'abm/family', array(
			'famName' => (string) (isset($data['famName']) ? $data['famName'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Familys($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Familys | Modificar_Familys(' . json_encode($data['famId']) . ')');
		$rsp = man_escribir('PUT', 'abm/family', array(
			'famId' => (string) $data['famId'],
			'famName' => (string) (isset($data['famName']) ? $data['famName'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Familys($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Familys | Eliminar_Familys(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/family/baja', array(
			'famId'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
