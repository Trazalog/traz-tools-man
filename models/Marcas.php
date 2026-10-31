<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Marcas.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Marcas.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Marcas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Marcas activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Marcas()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marcas | Listado_Marcas()');
		return man_lista(wso2(REST_MAN . '/abm/marcas/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Marcas($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marcas | Obtener_Marcas(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/marca/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Marcas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marcas | Guardar_Marcas()');
		$rsp = man_escribir('POST', 'abm/marca', array(
			'marcadescrip' => (string) (isset($data['marcadescrip']) ? $data['marcadescrip'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Marcas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marcas | Modificar_Marcas(' . json_encode($data['marcaid']) . ')');
		$rsp = man_escribir('PUT', 'abm/marca', array(
			'marcaid' => (string) $data['marcaid'],
			'marcadescrip' => (string) (isset($data['marcadescrip']) ? $data['marcadescrip'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Marcas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Marcas | Eliminar_Marcas(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/marca/baja', array(
			'marcaid'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
