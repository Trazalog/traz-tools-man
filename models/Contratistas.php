<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Contratistas.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Contratistas.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Contratistas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Contratistas activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Contratistas()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratistas | Listado_Contratistas()');
		return man_lista(wso2(REST_MAN . '/abm/contratistas/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Contratistas($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratistas | Obtener_Contratistas(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/contratista/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Contratistas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratistas | Guardar_Contratistas()');
		$rsp = man_escribir('POST', 'abm/contratista', array(
			'nombre' => (string) (isset($data['nombre']) ? $data['nombre'] : ''),
			'contradireccion' => (string) (isset($data['contradireccion']) ? $data['contradireccion'] : ''),
			'contramail' => (string) (isset($data['contramail']) ? $data['contramail'] : ''),
			'contramail1' => (string) (isset($data['contramail1']) ? $data['contramail1'] : ''),
			'contracelular1' => (string) (isset($data['contracelular1']) ? $data['contracelular1'] : ''),
			'contracelular2' => (string) (isset($data['contracelular2']) ? $data['contracelular2'] : ''),
			'contratelefono' => (string) (isset($data['contratelefono']) ? $data['contratelefono'] : ''),
			'contracontacto' => (string) (isset($data['contracontacto']) ? $data['contracontacto'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Contratistas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratistas | Modificar_Contratistas(' . json_encode($data['id_contratista']) . ')');
		$rsp = man_escribir('PUT', 'abm/contratista', array(
			'id_contratista' => (string) $data['id_contratista'],
			'nombre' => (string) (isset($data['nombre']) ? $data['nombre'] : ''),
			'contradireccion' => (string) (isset($data['contradireccion']) ? $data['contradireccion'] : ''),
			'contramail' => (string) (isset($data['contramail']) ? $data['contramail'] : ''),
			'contramail1' => (string) (isset($data['contramail1']) ? $data['contramail1'] : ''),
			'contracelular1' => (string) (isset($data['contracelular1']) ? $data['contracelular1'] : ''),
			'contracelular2' => (string) (isset($data['contracelular2']) ? $data['contracelular2'] : ''),
			'contratelefono' => (string) (isset($data['contratelefono']) ? $data['contratelefono'] : ''),
			'contracontacto' => (string) (isset($data['contracontacto']) ? $data['contracontacto'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Contratistas($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Contratistas | Eliminar_Contratistas(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/contratista/baja', array(
			'id_contratista'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
