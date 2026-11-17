<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administrar Órdenes: órdenes terminadas por entregar o reprogramar.
 *
 * MIG-ASSET-4: portado desde el asset actual. Datos por MANDataService (ADR-014), limitados a la empresa de la sesión (asset armaba el SQL
 * pegando lo que mandaba el navegador y entregaba órdenes de cualquier empresa).
 */
class Envios extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('man');
	}

	/** Órdenes terminadas (T) o terminadas parcialmente (TE) de la empresa. @return array filas */
	function envios_List(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envios | envios_List()");
		$filas = man_lista(wso2(REST_MAN . '/envios/empresa/' . man_empresa()));
		return $filas ? $filas : array();
	}

	/** Entrega una orden terminada. @return bool */
	function entregas($id, $fecha){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envios | entregas($id, $fecha)");
		if (!$this->esDeLaEmpresa($id)) {
			return false;
		}
		$rsp = man_escribir('PUT', 'envio/entrega', array('fecha' => (string) $fecha, 'id_orden' => (string) (int) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** Reprograma una orden terminada parcialmente con la fecha elegida (aaaa-mm-dd). @return bool */
	function ModificarFechas($id, $fecha){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Envios | ModificarFechas($id, $fecha)");
		if (!$this->esDeLaEmpresa($id)) {
			return false;
		}
		$rsp = man_escribir('PUT', 'envio/fecha', array('fecha' => (string) $fecha, 'id_orden' => (string) (int) $id, 'id_empresa' => man_empresa()));
		return (bool) $rsp['status'];
	}

	/** La orden está entre las de la empresa por entregar (la base no informa cuántas filas tocó un cambio) */
	private function esDeLaEmpresa($id){
		foreach ($this->envios_List() as $o) {
			if ((int) $o['id_orden'] === (int) $id) {
				return true;
			}
		}
		return false;
	}
}
