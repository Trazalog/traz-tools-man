<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Sistemas.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/SistemasABM.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class SistemasABM extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Sistemas activos de la empresa de la sesión.
	 * @return array|false filas o false si no hay
	 */
	function sistemaList()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemasABM | sistemaList()');
		$filas = man_lista(wso2(REST_MAN . '/abm/sistemas/empresa/' . man_empresa()));
		return $filas === null ? false : $filas;
	}

	/**
	 * Alta para la empresa de la sesión.
	 * @return bool
	 */
	function agregarSistema($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemasABM | agregarSistema()');
		$rsp = man_escribir('POST', 'abm/sistema', array(
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Modifica un registro de la empresa de la sesión; solo se toman las columnas conocidas.
	 * @return bool
	 */
	function update_editar($data, $id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemasABM | update_editar(' . json_encode($id) . ')');
		$rsp = man_escribir('PUT', 'abm/sistema', array(
			'sistemaid' => (string) $id,
			'descripcion' => (string) (isset($data['descripcion']) ? $data['descripcion'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function bajaSistema($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | SistemasABM | bajaSistema(' . json_encode($id) . ')');
		$rsp = man_escribir('PUT', 'abm/sistema/baja', array(
			'sistemaid'    => (string) $id,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
