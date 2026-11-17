<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Clientes.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Clientes.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Clientes extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Clientes activos de la empresa de la sesión.
	 * @return array|null filas o null si no hay
	 */
	function Listado_Clientes()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Clientes | Listado_Clientes()');
		return man_lista(wso2(REST_MAN . '/abm/clientes/empresa/' . man_empresa()));
	}

	/**
	 * Devuelve el registro pedido, si es de la empresa de la sesión.
	 * @return array|null lista con el registro, o null
	 */
	function Obtener_Clientes($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Clientes | Obtener_Clientes(' . json_encode($id) . ')');
		return man_lista(wso2(REST_MAN . '/abm/cliente/' . (int) $id . '/empresa/' . man_empresa()));
	}

	/**
	 * Alta para la empresa de la sesión (la empresa que llega en $data se ignora).
	 * @return bool
	 */
	function Guardar_Clientes($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Clientes | Guardar_Clientes()');
		$rsp = man_escribir('POST', 'abm/cliente', array(
			'cliName' => (string) (isset($data['cliName']) ? $data['cliName'] : ''),
			'cliLastName' => (string) (isset($data['cliLastName']) ? $data['cliLastName'] : ''),
			'cliDni' => (string) (isset($data['cliDni']) ? $data['cliDni'] : ''),
			'cliAddress' => (string) (isset($data['cliAddress']) ? $data['cliAddress'] : ''),
			'cliPhone' => (string) (isset($data['cliPhone']) ? $data['cliPhone'] : ''),
			'cliEmail' => (string) (isset($data['cliEmail']) ? $data['cliEmail'] : ''),
			'cliRazonSocial' => (string) (isset($data['cliRazonSocial']) ? $data['cliRazonSocial'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Modificar_Clientes($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Clientes | Modificar_Clientes(' . json_encode($data['cliId']) . ')');
		$rsp = man_escribir('PUT', 'abm/cliente', array(
			'cliId' => (string) $data['cliId'],
			'cliName' => (string) (isset($data['cliName']) ? $data['cliName'] : ''),
			'cliDni' => (string) (isset($data['cliDni']) ? $data['cliDni'] : ''),
			'cliAddress' => (string) (isset($data['cliAddress']) ? $data['cliAddress'] : ''),
			'cliPhone' => (string) (isset($data['cliPhone']) ? $data['cliPhone'] : ''),
			'cliEmail' => (string) (isset($data['cliEmail']) ? $data['cliEmail'] : ''),
			'cliRazonSocial' => (string) (isset($data['cliRazonSocial']) ? $data['cliRazonSocial'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function Eliminar_Clientes($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Clientes | Eliminar_Clientes(' . json_encode($data) . ')');
		$rsp = man_escribir('PUT', 'abm/cliente/baja', array(
			'cliId'    => (string) $data,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
