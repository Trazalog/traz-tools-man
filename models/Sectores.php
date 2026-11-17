<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Sectores.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Sectores.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Sectores extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Sectores activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Sectores()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sectores | Listado_Sectores()');
		return man_lista(wso2(REST_MAN . '/abm/sectores/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Sectores($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sectores | Obtener_Sectores(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/sector/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Sectores($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sectores | Guardar_Sectores()');
		$rsp = man_escribir('POST', 'abm/sector', array(
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Sectores($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sectores | Modificar_Sectores(' . json_encode($data['id_sector']) . ')');
		$rsp = man_escribir('PUT', 'abm/sector', array(
			'id_sector' => (string) $data['id_sector'],
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Sectores($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sectores | Eliminar_Sectores(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/sector/baja', array(
			'id_sector'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
