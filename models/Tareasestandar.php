<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Tareas estándar.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Tareasestandar.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Tareasestandar extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Tareas estándar activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Tareas()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareasestandar | Listado_Tareas()');
		return man_lista(wso2(REST_MAN . '/abm/tareas/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Tareas($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareasestandar | Obtener_Tareas(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/tarea/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Tareas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareasestandar | Guardar_Tareas()');
		$rsp = man_escribir('POST', 'abm/tarea', array(
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Tareas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareasestandar | Modificar_Tareas(' . json_encode($data['id_tarea']) . ')');
		$rsp = man_escribir('PUT', 'abm/tarea', array(
			'id_tarea' => (string) $data['id_tarea'],
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Tareas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Tareasestandar | Eliminar_Tareas(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/tarea/baja', array(
			'id_tarea'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
