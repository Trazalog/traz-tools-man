<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Criticidad.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Criticidades.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Criticidades extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Criticidad activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_criticidades()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidades | Listado_criticidades()');
		return man_lista(wso2(REST_MAN . '/abm/criticidades/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_criticidades($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidades | Obtener_criticidades(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/criticidad/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_criticidades($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidades | Guardar_criticidades()');
		$rsp = man_escribir('POST', 'abm/criticidad', array(
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_criticidades($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidades | Modificar_criticidades(' . json_encode($data['id_criti']) . ')');
		$rsp = man_escribir('PUT', 'abm/criticidad', array(
			'id_criti' => (string) $data['id_criti'],
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_criticidades($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Criticidades | Eliminar_criticidades(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/criticidad/baja', array(
			'id_criti'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
