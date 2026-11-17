<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ABM de Sucursales.
 *
 * MIG-ASSET-5a: todos los datos por MANDataService (ADR-014). Se conservan las firmas y el formato de salida del model de asset
 * (application/models/Sucursales.php), para que el controller no cambie. Toda operación filtra por la empresa de la sesión.
 */
class Sucursales extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/**
	 * Sucursales activos de la empresa de la sesión.
	 * @return array|false filas o false si no hay
	 */
	function sucursalesList()
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursales | sucursalesList()');
		$filas = man_lista(wso2(REST_MAN . '/abm/sucursales/empresa/' . man_empresa()));
		return $filas === null ? false : $filas;
	}

	/**
	 * Alta para la empresa de la sesión.
	 * @return bool
	 */
	function agregarSucursal($data)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursales | agregarSucursal()');
		$rsp = man_escribir('POST', 'abm/sucursal', array(
			'descripc' => (string) (isset($data['descripc']) ? $data['descripc'] : ''),
			'dire' => (string) (isset($data['dire']) ? $data['dire'] : ''),
			'zonas' => (string) (isset($data['zonas']) ? $data['zonas'] : ''),
			'telefono' => (string) (isset($data['telefono']) ? $data['telefono'] : ''),
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
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursales | update_editar(' . json_encode($id) . ')');
		$rsp = man_escribir('PUT', 'abm/sucursal', array(
			'id_sucursal' => (string) $id,
			'descripc' => (string) (isset($data['descripc']) ? $data['descripc'] : ''),
			'dire' => (string) (isset($data['dire']) ? $data['dire'] : ''),
			'zonas' => (string) (isset($data['zonas']) ? $data['zonas'] : ''),
			'telefono' => (string) (isset($data['telefono']) ? $data['telefono'] : ''),
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}

	/**
	 * Baja lógica (estado AN) de un registro de la empresa de la sesión.
	 * @return bool
	 */
	function bajaSucursal($id)
	{
		log_message('DEBUG', '#TRAZA | TRAZ-TOOLS-MAN | Sucursales | bajaSucursal(' . json_encode($id) . ')');
		$rsp = man_escribir('PUT', 'abm/sucursal/baja', array(
			'id_sucursal'    => (string) $id,
			'id_empresa' => man_empresa(),
		));
		return (bool) $rsp['status'];
	}
}
