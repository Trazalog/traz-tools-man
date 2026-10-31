<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Grupos.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Grupos.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Grupos extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Grupos activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Grupos()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | Listado_Grupos()');
		return man_lista(wso2(REST_MAN . '/abm/grupos/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Grupos($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | Obtener_Grupos(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/grupo/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Grupos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | Guardar_Grupos()');
		$rsp = man_escribir('POST', 'abm/grupo', array(
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'form_id' => (string) (isset($data['form_id']) ? $data['form_id'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Grupos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | Modificar_Grupos(' . json_encode($data['id_grupo']) . ')');
		$rsp = man_escribir('PUT', 'abm/grupo', array(
			'id_grupo' => (string) $data['id_grupo'],
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'form_id' => (string) (isset($data['form_id']) ? $data['form_id'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Grupos($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | Eliminar_Grupos(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/grupo/baja', array(
			'id_grupo'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Formularios de la empresa de tools (frm), para asociar al grupo.
	 * @return array lista de filas con form_id y nombre
	 */
	function formularios()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Grupos | formularios()');
		$rsp = wso2(REST_FRM . '/formularios/' . empresa());
		$filas = man_lista($rsp);
		return $filas === null ? array() : $filas;
	}
}
