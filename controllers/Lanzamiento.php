<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lanzamiento automático de las OTs preventivas y predictivas programadas sin lanzar — MIG-8.
 *
 * Solo se ejecuta desde la línea de comandos (cron del sistema operativo, una vez por día). Por cada empresa con órdenes sin lanzar
 * calcula el margen de días (man_dias_lanzamiento) y le pide a toolsMANAPI (POST /tools/man/plan/lanzar-pendientes) que lance las que
 * caen dentro del margen o ya pasaron. La API es la que lanza el caso de Bonita y guarda el caso en la orden; acá solo se recorre.
 * Una orden que falla queda sin lanzar y se reintenta en la corrida siguiente.
 *
 * Uso: php index.php traz-tools-man/Lanzamiento/ejecutar   (ver doc/infra/man-lanzamiento-cron.md)
 */
class Lanzamiento extends CI_Controller {

	/** Tope de pasadas por empresa (la API lanza hasta 200 órdenes por pedido) */
	const PASADAS = 5;

	function __construct(){
		parent::__construct();
		if (!is_cli()) {
			show_404();
		}
		$this->load->helper('man');
	}

	/**
	* Recorre las empresas con órdenes sin lanzar y lanza las que corresponde
	*/
	public function ejecutar(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Lanzamiento | ejecutar()");
		$filas = man_lista(wso2(REST_MAN . '/plan/empresas-sin-lanzar'));
		if (!$filas) {
			echo "Sin ordenes por lanzar\n";
			return;
		}
		$lanzadas = 0;
		foreach ($filas as $f) {
			$idEmpresa = (string) $f['id_empresa'];
			$rsp = $this->rest->callAPI('GET', REST_CORE . '/empresa/porAssetId/' . $idEmpresa);
			$aux = $rsp['status'] ? json_decode($rsp['data'], true) : null;
			if (empty($aux['empresa']['empr_id'])) {
				log_message('ERROR', "#TRAZA | TRAZ-TOOLS-MAN | Lanzamiento | empresa de asset $idEmpresa sin vínculo con tools");
				echo "Empresa $idEmpresa: sin vinculo con tools, se omite\n";
				continue;
			}
			$emprId = (string) $aux['empresa']['empr_id'];
			$dias = man_dias_lanzamiento($emprId);
			$previas = array();
			for ($i = 0; $i < self::PASADAS; $i++) {
				$r = $this->lanzarPendientes($idEmpresa, $emprId, $dias);
				if ($r === null) {
					echo "Empresa $idEmpresa: la API no respondio\n";
					break;
				}
				$ok = 0;
				$mal = array();
				foreach ($r as $o) {
					if (!empty($o['status'])) {
						$ok++;
					} else {
						$mal[] = isset($o['id_orden']) ? $o['id_orden'] : '?';
						log_message('ERROR', "#TRAZA | TRAZ-TOOLS-MAN | Lanzamiento | orden " . (isset($o['id_orden']) ? $o['id_orden'] : '?')
							. " empresa $idEmpresa >> " . (isset($o['msj']) ? $o['msj'] : 'sin detalle'));
					}
				}
				$lanzadas += $ok;
				echo "Empresa $idEmpresa (margen $dias dias): $ok lanzadas, " . count($mal) . " fallidas" . ($mal ? ' (' . implode(',', $mal) . ')' : '') . "\n";
				// Otra pasada solo si el pedido llegó al tope y se pudo lanzar algo
				if (count($r) < 200 || $ok === 0) {
					break;
				}
			}
		}
		echo "Total: $lanzadas lanzadas\n";
		log_message('INFO', "#TRAZA | TRAZ-TOOLS-MAN | Lanzamiento | ejecutar() >> $lanzadas lanzadas");
	}

	/** Pide a la API lanzar las pendientes de la empresa. @return array|null una entrada por orden, o null si la API no contestó */
	private function lanzarPendientes($idEmpresa, $emprId, $dias){
		$rsp = $this->rest->callAPI('POST', HOST . '/tools/man/plan/lanzar-pendientes', array(
			'id_empresa' => $idEmpresa,
			'emprId'     => $emprId,
			'session'    => '',
			'dias'       => (string) $dias,
		));
		$aux = json_decode($rsp['data'], true);
		if (!$rsp['status'] || empty($aux['status']) || !isset($aux['resultado']) || !is_array($aux['resultado'])) {
			log_message('ERROR', "#TRAZA | TRAZ-TOOLS-MAN | Lanzamiento | lanzar-pendientes empresa $idEmpresa >> " . json_encode($rsp['data']));
			return null;
		}
		return $aux['resultado'];
	}
}
