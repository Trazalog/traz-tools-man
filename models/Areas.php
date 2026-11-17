<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Áreas de la empresa (ABM).
 *
 * MIG-ASSET-1: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de
 * salida del model de asset (application/models/Areas.php), para que el controller no cambie.
 */
class Areas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Áreas activas de la empresa de la sesión.
	 * @return array|null filas (id_area, descripcion, id_empresa, estado) o null si no hay
	 */
	function Listado_areas()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Areas | Listado_areas()');
		return man_lista(wso2(REST_MAN . '/areas/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el área pedida, si es de la empresa de la sesión.
	 * @param integer $id id_area
	 * @return array|null lista con el área, o null
	 */
	function Obtener_areas($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Areas | Obtener_areas(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/area/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta de un área para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_areas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Areas | Guardar_areas()');
		$rsp = man_escribir('POST', 'area', array(
			'descripcion' => (string) $data['descripcion'],
			'id_empresa'  => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica la descripción de un área de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_areas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Areas | Modificar_areas(' . json_encode($data['id_area']) . ')');
		$rsp = man_escribir('PUT', 'area', array(
			'id_area'     => (string) $data['id_area'],
			'descripcion' => (string) $data['descripcion'],
			'id_empresa'  => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un área de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_areas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Areas | Eliminar_areas(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'area/baja', array(
			'id_area'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
