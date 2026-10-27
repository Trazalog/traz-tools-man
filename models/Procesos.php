<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Procesos de la empresa (ABM).
 *
 * MIG-ASSET-1: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de
 * salida del model de asset (application/models/Procesos.php), para que el controller no cambie.
 */
class Procesos extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Procesos activas de la empresa de la sesión.
	 * @return array|null filas (id_proceso, descripcion, id_empresa, estado) o null si no hay
	 */
	function Listado_procesos()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Procesos | Listado_procesos()');
		return man_lista(wso2(REST_MAN . '/procesos/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el proceso pedido, si es de la empresa de la sesión.
	 * @param integer $id id_proceso
	 * @return array|null lista con el proceso, o null
	 */
	function Obtener_procesos($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Procesos | Obtener_procesos(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/proceso/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta de un proceso para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_procesos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Procesos | Guardar_procesos()');
		$rsp = man_escribir('POST', 'proceso', array(
			'descripcion' => (string) $data['descripcion'],
			'id_empresa'  => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica la descripción de un proceso de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_procesos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Procesos | Modificar_procesos(' . json_encode($data['id_proceso']) . ')');
		$rsp = man_escribir('PUT', 'proceso', array(
			'id_proceso'     => (string) $data['id_proceso'],
			'descripcion' => (string) $data['descripcion'],
			'id_empresa'  => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un proceso de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_procesos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Procesos | Eliminar_procesos(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'proceso/baja', array(
			'id_proceso'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
