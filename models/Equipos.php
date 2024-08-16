<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Equipos extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
    }

    public function equipos_List(){
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; // guarda usuario logueado

        $this->assetDB->select('equipos.id_equipo,
        equipos.codigo,
        equipos.descripcion AS deeq,
        equipos.estado AS estadoEquipo,
        equipos.meta_disponibilidad as meta_disp,
        unidad_industrial.id_unidad,
        unidad_industrial.descripcion AS deun,
        area.id_area,
        area.descripcion AS dear,
        empresas.id_empresa,
        empresas.descripcion AS deem,
        sector.id_sector,
        sector.descripcion AS desec,
        criticidad.id_criti,
        criticidad.descripcion AS decri,
        admcustomers.cliId,
        admcustomers.cliRazonSocial AS clie,
        grupo.form_id,
        grupo.id_grupo,
        proceso.id_proceso,
        proceso.descripcion AS depro');
        $this->assetDB->from('equipos');
        $this->assetDB->join('sector', 'sector.id_sector=equipos.id_sector');
        $this->assetDB->join('empresas', 'empresas.id_empresa=equipos.id_empresa');
        $this->assetDB->join('unidad_industrial', 'unidad_industrial.id_unidad=equipos.id_unidad');
        $this->assetDB->join('criticidad', 'criticidad.id_criti=equipos.id_criticidad');
        $this->assetDB->join('area', 'area.id_area=equipos.id_area');
        $this->assetDB->join('grupo', 'grupo.id_grupo=equipos.id_grupo');
        $this->assetDB->join('proceso', 'proceso.id_proceso=equipos.id_proceso');
        $this->assetDB->join('admcustomers', 'admcustomers.cliId=equipos.id_customer');
        $this->assetDB->where('equipos.estado !=', 'AN');
        $this->assetDB->where('equipos.id_empresa', $empId);
        $this->assetDB->order_by('equipos.id_equipo', 'ASC');
        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            $equipos = $query->result_array();
            foreach ($equipos as &$valor) {
                if (($valor['estadoEquipo'] == 'AC') || ($valor['estadoEquipo'] == 'RE')) {
                    $idEquipo = $valor['id_equipo'];
                    $this->assetDB->select('*');
                    $this->assetDB->from('historial_lecturas');
                    $this->assetDB->where('id_equipo', $valor['id_equipo']);
                    $this->assetDB->order_by('fecha', 'DESC');
                    $this->assetDB->limit(1);

                    $query2 = $this->assetDB->get();
                    $estado = $query2->result_array();
                    $valor['estado'] = $estado[0]['estado'];
                } else {
                    $valor['estado'] = $valor['estadoEquipo'];
                }
            }
            return $equipos;
        } else {
            return [];
        }
    }
    /**
	* Da de baja equipos (AN)
	* @param integer $idEquipo
	* @return bool booleano de exito/fracaso de operación
	*/
    public function baja_equipos($data, $idequipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | baja_equipos(data: ".json_encode($data)." | ID: $idequipo)");
        $empId = empresa();
        $this->assetDB->where('equipos.id_empresa', $empId);
        $this->assetDB->where('id_equipo', $idequipo);
        $query = $this->assetDB->update('equipos', $data);

        return $query;
    }
    /**
	* Devuelve listado de areas cargadas por empresa
	* @param 
	* @return array listado de areas por empresa
	*/
    public function getareas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getareas()");
        
        $empresaId = empresa();
        $query = $this->assetDB->get_where('area', array('id_empresa' => $empresaId, 'estado' => 'AC'));
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getequipofichas($id)
    {

        $this->assetDB->select('ficha_equipo.*');
        $this->assetDB->from('ficha_equipo');
        $this->assetDB->join('equipos', 'equipos.id_equipo = ficha_equipo.id_equipo');
        $this->assetDB->where('equipos.id_equipo', $id);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getsector()
    {
        $userdata = $this->session->userdata('user_data');
        $empresaId = $userdata[0]['id_empresa'];
        $query = $this->assetDB->get_where('sector', array('id_empresa' => $empresaId));
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    /**
	* Trae criticidad
	* @param 
	* @return array listado de criticidad
	*/
    public function getcriti(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getcriti()");
        $empId = empresa();

        $this->assetDB->select('criticidad.*');
        $this->assetDB->from('criticidad');
        $this->assetDB->where('criticidad.id_empresa', $empId);
        $this->assetDB->where('criticidad.estado', 'AC');
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    /**
	* Trae grupo por empresa
	* @param 
	* @return array listado de grupos
	*/
    public function getgrupos(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getgrupos()");
        $empId = empresa();

        $this->assetDB->select('grupo.*');
        $this->assetDB->from('grupo');
        $this->assetDB->where('grupo.id_empresa', $empId);
        $this->assetDB->where('grupo.estado', 'AC');
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    /**
	* Trae procesos
	* @param 
	* @return array listado de procesos
	*/
    public function getprocesos(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getprocesos()");        
        $empId = empresa();

        $this->assetDB->select('proceso.*');
        $this->assetDB->from('proceso');
        $this->assetDB->where('proceso.id_empresa', $empId);
        $this->assetDB->where('proceso.estado', 'AC');
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    // Trae empresa logueada (Listo)
    public function getempresa()
    {

        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];
        $this->assetDB->select('empresas.id_empresa,
							empresas.descripcion');
        $this->assetDB->from('empresas');
        $this->assetDB->where('empresas.id_empresa', $empId);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    /**
	* Trae etapa/sector
	* @param 
	* @return array listado de etapa/sector
	*/
    public function getetapas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getprocesos()");
        $empId = empresa();

        $this->assetDB->select('sector.*');
        $this->assetDB->from('sector');
        $this->assetDB->where('sector.id_empresa', $empId);
        $this->assetDB->where('sector.estado', 'AC');
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    /**
	* Busca contratistas segun empresa logueada
	* @param 
	* @return array listado de contratistas
	*/
    public function getcontra(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getcontra()");
        $empId = empresa();

        $this->assetDB->select('contratistas.*');
        $this->assetDB->from('contratistas');
        $this->assetDB->where('contratistas.id_empresa', $empId);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }

    }
    /**
	* Trae todas las marcas
	* @param 
	* @return array listado de las marcas
	*/
    public function getmarcas(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getmarcas()");
        $empId = empresa();

        $this->assetDB->select('marcasequipos.*');
        $this->assetDB->from('marcasequipos');
        $this->assetDB->where('marcasequipos.id_empresa', $empId);
        $this->assetDB->where('marcasequipos.estado', 'AC');
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }

    }
    /**
	* Trae clientes
	* @param 
	* @return array listado de las clientes
	*/
    public function getclientes(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getclientes()");
        $empId = empresa();

        $this->assetDB->select('admcustomers.*');
        $this->assetDB->from('admcustomers');
        $this->assetDB->where('admcustomers.estado !=', 'AN');
        $this->assetDB->where('admcustomers.id_empresa', $empId);
        $query = $this->assetDB->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }

    }

    // Trae unidades industriales - Listo
    public function getunidads()
    {

        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->select('unidad_industrial.*');
        $this->assetDB->from('unidad_industrial');
        $this->assetDB->where('unidad_industrial.id_empresa', $empId);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    // Agrega unidad industrial y devuelve id de insercion - Listo
    public function agregar_unidades($data)
    {

        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];
        $data['id_empresa'] = $empId;

        $this->assetDB->insert('unidad_industrial', $data);
        $query = $this->assetDB->insert_id();
        return $query;
    }
    /**
	* Da de alta un area
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_area($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_area()");

        $data['id_empresa'] = empresa();
        $data['estado'] = 'AC';
        $this->assetDB->insert("area", $data);
        $query = $this->assetDB->insert_id();
        return $query;
    }
    /**
	* Da de alta un proceso
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_proceso($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_proceso()");

        $data['id_empresa'] = empresa();
        $data['estado'] = 'AC';
        $this->assetDB->insert("proceso", $data);
        $query = $this->assetDB->insert_id();
        return $query;
    }
   	/**
	* Da de alta una etapa
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_etapa($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_etapa()");
        
        $data['id_empresa'] = empresa();
        $data['estado'] = 'AC';
        $this->assetDB->insert("sector", $data);
        $query = $this->assetDB->insert_id();
        return $query;
    }
    /**
	* Da de alta una nueva criticidad
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_criti($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_criti()");
        
        $data['id_empresa'] = empresa();
        $data['estado'] = 'AC';
        $query = $this->assetDB->insert("criticidad", $data);
        return $query;
    }
    /**
	* Da de alta un grupo
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_grupos($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_grupos()");
        
        $data['id_empresa'] = empresa();
        $data['estado'] = 'AC';
        $query = $this->assetDB->insert("grupo", $data);
        return $query;
    }
    /**
	* Da de alta una marca
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_marcas($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_marcas()");

        $data['id_empresa'] = empresa();
        $query = $this->assetDB->insert("marcasequipos", $data);
        return $query;
    }
    /**
	* Da de alta un cliente
	* @param 
	* @return integer ID de insercion
	*/
    public function agregar_clientes($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | agregar_clientes()");

        $data['id_empresa'] = empresa();
        $query = $this->assetDB->insert("admcustomers", $data);
        return $query;
    }

    public function agregar_sector($data)
    {

        $query = $this->assetDB->insert("sector", $data);
        return $query;
    }

    public function agregar_componente($data)
    {

        $query = $this->assetDB->insert("componentes", $data);
        return $query;

    }

    public function agregar_seguros($data)
    {

        $query = $this->assetDB->insert("seguro", $data);
        return $query;

    }
    /**
	* Comprueba unicidad de codigo de equipo
	* @param integer $codigo
	* @return bool si es unico el codigo de equipo
	*/
    function validaUnicidadCodigo($codigo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | validaUnicidadCodigo(".$codigo.")");
        $sql = "select * from equipos where codigo = '".$codigo."'";
        $query = $this->assetDB->query($sql);

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
	* Agrega equipo nuevo
	* @param array $data formulario de alta de equipo
	* @return array respuesta de insercion
	*/
    // Agrega equipo nuevo
    public function insert_equipo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | insert_equipo(".json_encode($data).")");
        $query['result'] = $this->assetDB->insert("equipos", $data);
        $query['id_equipo'] = $this->assetDB->insert_id();
        return $query;
    }

    // Agrega info adicional de equipo nuevo - Listo
    public function insert_equipinfo($data)
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];
        $data['id_empresa'] = $empId;
        $query = $this->assetDB->insert("informacionequipo", $data);
        return $query;
    }

    public function update_equipo2($estado, $idequi)
    {

        $this->assetDB->where('id_equipo', $idequi);
        $query = $this->assetDB->update("equipos", $estado);
        return $query;
    }

    // Cambia estado del equipo a inhabilitado desde la lista
    public function update_cambio($data, $idequipo)
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->where('id_equipo', $idequipo);
        $this->assetDB->where('id_empresa', $empId);
        $query = $this->assetDB->update("equipos", $data);

        if ($query) {
            $userdata = $this->session->userdata('user_data');
            $data = array(
                'id_equipo' => $idequipo,
                'lectura' => 0,
                'usrId' => $userdata[0]['usrId'],
                'fecha' => date('Y-m-d H:i:s'),
                'estado' => 'IN',
            );
            $this->assetDB->insert('historial_lecturas', $data);
        }

        return $query;
    }

    public function update_estado($idequipo)
    {
        $this->assetDB->select('estado');
        $this->assetDB->order_by('fecha', 'asc');
        $this->assetDB->where('id_equipo', $idequipo);
        $ultimoEstado = $this->assetDB->get('historial_lecturas')->last_row()->estado;

        $this->assetDB->where('id_equipo', $idequipo);
        $this->assetDB->set('estado', $ultimoEstado);
        return $this->assetDB->update("equipos");
    }

    public function update_e($estado, $idequi)
    {

        $this->assetDB->where('id_equipo', $idequi);
        $query = $this->assetDB->update("equipos", $estado);
        return $query;
    }

    // Devuelve datos para editar equipos - Listo
    public function getpencil($id)
    {

        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->select('equipos.*,
	    					empresas.descripcion AS deemp,
	    					empresas.id_empresa,
	    					sector.id_sector,
	    					sector.descripcion AS desect,
	    					grupo.id_grupo,
	    					grupo.descripcion AS degrupo,
	    					criticidad.id_criti,
	    					criticidad.descripcion AS decriti,
	    					');
        $this->assetDB->from('equipos');
        $this->assetDB->join('empresas', 'empresas.id_empresa = equipos.id_empresa');
        $this->assetDB->join('sector', 'sector.id_sector = equipos.id_sector');
        $this->assetDB->join('criticidad', 'criticidad.id_criti = equipos.id_criticidad');
        $this->assetDB->join('grupo', 'grupo.id_grupo = equipos.id_grupo');
        //$this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
        $this->assetDB->where('equipos.id_equipo', $id);
        $this->assetDB->where('equipos.id_empresa', $empId);

        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    public function getdatosfichas($id)
    {

        $this->assetDB->select('equipos.id_equipo,
	    					equipos.descripcion,
	    					equipos.fecha_ingreso,
	    					equipos.fecha_garantia,
	    					equipos.marca,
	    					equipos.ubicacion,
	    					equipos.codigo,
	    					equipos.estado,
	    					equipos.fecha_ultimalectura,
	    					equipos.ultima_lectura,
	    					sector.id_sector,
	    					sector.descripcion AS desect');
        $this->assetDB->from('equipos');
        $this->assetDB->join('sector', 'sector.id_sector = equipos.id_sector');
        $this->assetDB->where('equipos.id_equipo', $id);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    //aparentemente no se usa
    public function contratista($id)
    {
        $this->assetDB->select('equipos.*');
        $this->assetDB->from('equipos');
        $this->assetDB->where('equipos.id_equipo', $id);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    public function getcodigo($data = null)
    {

        $query = $this->assetDB->get_where('equipos');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function Buscar($a)
    {

        $sql = "SELECT id_equipo
	    	  FROM equipos
	    	 WHERE codigo=$a
	    	  ";

        $query = $this->assetDB->query($sql);

        foreach ($query->result_array() as $row) {

            $data = $row['id_equipo'];

            return $data;
        }
    }
    /**
	* Guarda los datos editados de un equipo
	* @param array $data enviado a traves de formulario en pantalla; integer
	* @param integer $id ID del equipo en tabla
	* @return array respuesta de la operacion 
	*/
    public function update_editar($data, $id){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | update_editar(data: ".json_encode($data)." | id: $id)");
        $this->assetDB->where('id_equipo', $id);
        $query = $this->assetDB->update("equipos", $data);
        return $query;
    }
    /**
	* 
	* @param $id_equipo
	* @return array listado de 
	*/
    public function getco($data = null){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getco()");
        if ($data == null) {
            return false;
        } else {
            $ide = $data['id_equipo'];
            $this->assetDB->select('*');
            $this->assetDB->from('equipos A');
            $this->assetDB->join('marcasequipos B', 'A.marca = B.marcaid');
            $this->assetDB->where('id_equipo', $ide);
            $query = $this->assetDB->get();
            if ($query->num_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
        }
    }

    public function getinfo($data = null)
    {

        if ($data == null) {
            return false;
        } else {

            $ide1 = $data['idequipo'];

            $query = $this->assetDB->get_where('equipos', array('id_equipo' => $ide1));
            if ($query->num_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }

        }
    }
    /**
	* Guarda contratista asignado a equipo por empresa
	* @param 
	* @return bool booleano de exito/fracaso de operación
	*/
    public function insert_contratista($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | insert_contratista(".json_encode($data).")");
        $query = $this->assetDB->insert("contratistaquipo", $data);
        return $query;
    }
    /**
	* Elimina la relacion contratista asignado a equipo por empresa
	* @param 
	* @return bool booleano de exito/fracaso de operación
	*/
    public function delContratista($id_contratista,$id_equipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | delContratista(ID Contratista: $id_contratista, ID Equipo: $id_equipo)");
        $this->assetDB->where('id_contratista', $id_contratista);
        $this->assetDB->where('id_equipo', $id_equipo);
        $query = $this->assetDB->delete('contratistaquipo');
        return $query;
    }

    public function insert_componentes($data)
    {
        $query = $this->assetDB->insert("componentes", $data);
        return 1;

    }

    public function insert_componenteequip($data)
    {
        $query = $this->assetDB->insert("componenteequipo", $data);
        //$query= $this->assetDB->query($sql);
        return 1;

    }

    public function agregar_ficha($data)
    {
        $query = $this->assetDB->insert("ficha_equipo", $data);
        return 1;

    }

    public function update_ficha($data, $id)
    {
        $this->assetDB->where('id_equipo', $id);
        $query = $this->assetDB->update("ficha_equipo", $data);
        return $query;
    }

    public function getsolImps($id)
    {

        $sql = "SELECT equipos.ubicacion, equipos.marca, equipos.codigo, equipos.estado, equipos.id_sector, ficha_equipo.marca AS marcaeq, ficha_equipo.modelo, ficha_equipo.numero_motor, ficha_equipo.numero_serie, ficha_equipo.fecha_ingreso as fechain, ficha_equipo.dominio, ficha_equipo.fabricacion, ficha_equipo.peso, ficha_equipo.bateria, ficha_equipo.hora_lectura, sector.descripcion AS sector
     		FROM equipos
     		JOIN ficha_equipo ON ficha_equipo.id_equipo=equipos.id_equipo
     		JOIN sector ON  sector.id_sector=equipos.id_sector



                  WHERE equipos.id_equipo=$id
              ";

        $query = $this->assetDB->query($sql);
        foreach ($query->result_array() as $row) {

            $data['codigo'] = $row['codigo'];
            $data['estado'] = $row['estado'];
            $data['ubicacion'] = $row['ubicacion'];
            $data['marca'] = $row['marca'];
            $data['marcaeq'] = $row['marcaeq'];
            $data['modelo'] = $row['modelo'];
            $data['numero_motor'] = $row['numero_motor'];
            $data['numero_serie'] = $row['numero_serie'];
            $data['fechain'] = $row['fechain'];
            $data['dominio'] = $row['dominio'];
            $data['fabricacion'] = $row['fabricacion'];
            $data['peso'] = $row['peso'];
            $data['bateria'] = $row['bateria'];
            $data['hora_lectura'] = $row['hora_lectura'];
            $data['sector'] = $row['sector'];

            return $data;
        }

    }

    public function getequiposseguro($id)
    {

        $sql = "SELECT *
     		FROM seguro
     		JOIN equipos ON equipos.id_equipo=seguro.id_equipo



                  WHERE equipos.id_equipo=$id
              ";

        $query = $this->assetDB->query($sql);
        foreach ($query->result_array() as $row) {

            $data['asegurado'] = $row['asegurado'];
            $data['ref'] = $row['ref'];
            $data['numero_pliza'] = $row['numero_pliza'];
            $data['fecha_inicio'] = $row['fecha_inicio'];
            $data['fecha_vigencia'] = $row['fecha_vigencia'];
            $data['cobertura'] = $row['cobertura'];

            return $data;
        }

    }

    public function getequiposorden($id)
    {

        $sql = "SELECT orden_servicio.fecha, orden_servicio.id_contratista, orden_servicio.id_equipo, orden_servicio.id_solicitudreparacion, orden_servicio.estado, solicitud_reparacion.causa, contratistas.nombre
     		FROM orden_servicio
     		JOIN solicitud_reparacion ON solicitud_reparacion.id_solicitud=orden_servicio.id_solicitudreparacion
     		JOIN equipos ON equipos.id_equipo= orden_servicio.id_equipo
     		JOIN contratistas ON contratistas.id_contratista= orden_servicio.id_contratista
     		WHERE orden_servicio.id_equipo=$id
              ";

        /*$query= $this->assetDB->query($sql);
        foreach ($query->result_array() as $row){

        $data['fecha'] = $row['fecha'];
        $data['causa'] = $row['causa'];
        $data['nombre'] = $row['nombre'];
        $data['estado'] = $row['estado'];

        }

        return $data; */

        $query = $this->assetDB->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }

    }

    /// Guarda lectura Hugo
    public function setLecturas($data)
    {
        $userdata = $this->session->userdata('user_data');
        $empId    = $userdata[0]['id_empresa'];
        $this->assetDB->where('id_equipo', $data["id_equipo"]);
        $estadoActual = $this->assetDB->get('equipos')->row('estado');

        $this->assetDB->trans_start();
        $id_equipo = $data["id_equipo"];
        $lectura = $data["lectura"];
        $observacion = $data["observacion"];
        $operario = $data['operario'];
        $turno = $data['turno'];
        $estado = $data['estado'];
        $userdata = $this->session->userdata('user_data');
        $usrId = $userdata[0]['usrId'];

        //---------------------------------------------------------
        //Me traigo la ultima lectura antes de colocar la nueva
        $ultima_lectura = "SELECT equipos.ultima_lectura 
                            FROM equipos
                            WHERE equipos.id_equipo = $id_equipo";
        $ultima_lectura = $this->assetDB->query($ultima_lectura);
        $ultima_lectura = $ultima_lectura->result()[0]->ultima_lectura;
        //---------------------------------------------------------
        $resultado = $lectura - $ultima_lectura;

        $preventivo ="SELECT preventivo.prevId, preventivo.estadoprev, preventivo.critico1, preventivo.lectura_base
        FROM preventivo
        WHERE preventivo.id_empresa = $empId AND preventivo.id_equipo = $id_equipo AND preventivo.estadoprev = 'M' OR preventivo.estadoprev = 'C' OR preventivo.estadoprev = 'PL'";
        $preventivo = $this->assetDB->query($preventivo);
        $preventivoEstado = $preventivo->result()[0]->estadoprev; 
        if($preventivoEstado == 'C'){
            //como es creado ('C') hay q sumarle la lectura base
            //a la diferencia (guardada en $resultado) antes de preguntar
            //si es mayor a critico1 (la frecuencia)
            $resultado = + $preventivo->result()[0]->lectura_base;
        }
        $datos = array(
            'id_equipo' => $id_equipo,
            'lectura' => $lectura,
            'fecha' => date('Y-m-d H:i:s'),
            'usrId' => $usrId,
            'observacion' => $observacion,
            'operario_nom' => $operario,
            'turno' => $turno,
            'estado' => $estado
            // 'obs' => ($estadoActual == $estado),
        );
        //guarda la lectura
        //dump_exit($datos);
        $this->assetDB->where('id_equipo', $id_equipo);
        $this->assetDB->insert('historial_lecturas', $datos);

        // actualiza el estado en equipos (R reparacion y O a AC)
        if ($estado == "RE") {
            $estado_eq = array('estado' => "RE");
        }
        if ($estado == "AC") {
            $estado_eq = array('estado' => "AC");
        }
        $this->assetDB->where('equipos.id_equipo', $id_equipo);
        $this->assetDB->update('equipos', $estado_eq);
        $this->assetDB->trans_complete();

        if ($this->assetDB->trans_status() === true) {
            //preguntamos si la cantidad de horas ingresadas en la nueva lectura
            //supera la frecuencia
            $preventivoCritico1 = $preventivo->result()[0]->critico1;
            if($resultado >= $preventivoCritico1){
                $idPreventivo = $preventivo->result()[0]->prevId;
                //realizo la modificacion del preventivo
                $sql = "UPDATE preventivo 
                        SET preventivo.estadoprev = 'M'
                        WHERE preventivo.id_empresa = $empId AND preventivo.prevId = $idPreventivo";
                $this->assetDB->query($sql);
                return true;
            }else return false;
        }else return false;
    }

    function anteriorHistorialLectura($idLectura = false, $id_equipo = false){

        if($idLectura && id_equipo){
            
    
            return $lect;
        }else return false;
    }

    public function posteriorHistorialLectura($idLectura, $id_equipo){
        

        return $lect;
    }

    // Guarda edicion de lectura si el valor a cambiar es mayor a una lectura anterior y menor a una lectura posterior
    public function setLecturaObservacionEdit($data)
    {
        $idLectura = $data["id_lectura"];
        $lectura = $data["lectura"];
        $observacion = $data["observacion"];
        $id_equipo = $data["id_equipo"];
        $bandAnt = false;//bandera de elemento anterior
        $bandPos = false;//bandera de elemento posterior

        //verifico si se encuentra un valor de lectura anterior al que quiero editar
        $sql = "SELECT max(historial_lecturas.id_lectura) as idLectura
            FROM historial_lecturas
            WHERE historial_lecturas.id_lectura < '" . $idLectura . "'
            AND historial_lecturas.id_equipo = '" . $id_equipo . "'
            ";
    
            $idLect = $this->assetDB->query($sql);
            $idLect = $idLect->result()[0]->idLectura;
            if ($idLect != null) {

                $bandAnt = true;

                $sql = "SELECT historial_lecturas.lectura
                FROM historial_lecturas
                WHERE historial_lecturas.id_lectura = '" . $idLect . "'
                ";
        
                $anterior = $this->assetDB->query($sql)->result()[0]->lectura;
            } else {
                $bandAnt = false;
            }

        //verifico si se encuentra un valor de lectura posterior al que quiero editar
        $sql = "SELECT min(historial_lecturas.id_lectura) as idLectura
        FROM historial_lecturas
        WHERE historial_lecturas.id_lectura > '" . $idLectura . "'
        AND historial_lecturas.id_equipo = '" . $id_equipo . "'
        ";

        $idLect = $this->assetDB->query($sql);
        $idLect = $idLect->result()[0]->idLectura;
        if ($idLect != null){

            $sql = "SELECT historial_lecturas.lectura
            FROM historial_lecturas
            WHERE historial_lecturas.id_lectura = '" . $idLect . "'
            ";
    
            $posterior = $this->assetDB->query($sql)->result()[0]->lectura;

            $bandPos = true;
        } else {
            $bandPos = false;
        }

        //segun se encuentre un valor de lectura anterior y posterior se verifica que el nuevo valor de lectura se encuentre entre el valor anterior y el posterior si existen
        if($bandAnt && $bandPos){
            if(($lectura < $posterior) && ($lectura > $anterior)){
                $this->assetDB->where('historial_lecturas.id_lectura', $idLectura);
                $query = $this->assetDB->update("historial_lecturas", array('lectura' => $lectura, 'observacion' => $observacion));
                return $query;
            }else{
                return false;
            }
        }else if(!$bandAnt && !$bandPos){
            $this->assetDB->where('historial_lecturas.id_lectura', $idLectura);
            $query = $this->assetDB->update("historial_lecturas", array('lectura' => $lectura, 'observacion' => $observacion));
            return $query;
        }else if(!$bandAnt && $bandPos){
            if($lectura < $posterior){
                $this->assetDB->where('historial_lecturas.id_lectura', $idLectura);
                $query = $this->assetDB->update("historial_lecturas", array('lectura' => $lectura, 'observacion' => $observacion));
                return $query;
            }else{
                return false;
            }
        }else{
            if($lectura > $anterior){
                $this->assetDB->where('historial_lecturas.id_lectura', $idLectura);
                $query = $this->assetDB->update("historial_lecturas", array('lectura' => $lectura, 'observacion' => $observacion));
                return $query;
            }else{
                return false;
            }
        }
        // $this->assetDB->where('historial_lecturas.id_lectura', $idLectura);
        // $query = $this->assetDB->update("historial_lecturas", array('lectura' => $lectura, 'observacion' => $observacion));
    }

    /// Trae lecturas de equipo por id de equipo
    public function getHistoriaLecturas($data)
    {
        $id = $data['idequipo'];
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; // guarda empresa logueadda

        $this->assetDB->select('historial_lecturas.*');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->join('equipos', 'equipos.id_equipo = historial_lecturas.id_equipo');
        $this->assetDB->where('historial_lecturas.id_equipo', $id);
        $this->assetDB->where('equipos.id_empresa', $empId);

        $query = $this->assetDB->get();

        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            return [];
        }
    }

    public function getEqPorIds($ideq)
    {

        $this->assetDB->select('equipos.id_equipo,
							equipos.codigo,
                            equipos.id_grupo,
							historial_lecturas.lectura,
							historial_lecturas.fecha,
							historial_lecturas.estado'
                        );
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->join('equipos', 'equipos.id_equipo = historial_lecturas.id_equipo');
        $this->assetDB->where('historial_lecturas.id_equipo', $ideq);
        $this->assetDB->order_by('id_lectura', 'DESC');
        //
        $this->assetDB->limit(1);

        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    /**
     * Devuleva la cantidad de equipos operativos.
     *
     * @return Void|Array     Cantidad de equipos operativos (estado activo o reparacion).
     */
    public function kpiSacarEquiposOperativos()
    {
        $userdata = $this->session->userdata('user_data');
        $empresaId = $userdata[0]['id_empresa'];
        $sql = "SELECT equipos.id_equipo, equipos.estado
        FROM equipos
        JOIN sector ON sector.id_sector=equipos.id_sector
        JOIN empresas ON empresas.id_empresa=equipos.id_empresa
        JOIN unidad_industrial ON unidad_industrial.id_unidad=equipos.id_unidad
        JOIN criticidad ON criticidad.id_criti=equipos.id_criticidad
        JOIN area ON area.id_area=equipos.id_area
        JOIN proceso ON proceso.id_proceso=equipos.id_proceso
        WHERE equipos.estado != 'AN'
        AND equipos.id_empresa = '" . $empresaId . "'
        ";
        //GROUP BY equipos.estado
        /*$sql = "SELECT COUNT(equipos.estado) AS cantEstadoActivo, equipos.estado
        FROM equipos
        WHERE equipos.id_empresa = '".$empresaId."'
        AND (equipos.estado = 'AC' OR equipos.estado = 'RE')
        GROUP BY equipos.estado
        ";*/
        $query = $this->assetDB->query($sql);

        if ($query->num_rows() != 0) {
            $equipos = $query->result_array();
            foreach ($equipos as &$valor) {
                if (($valor['estado'] == 'AC') || ($valor['estado'] == 'RE')) {
                    $idEquipo = $valor['id_equipo'];
                    $this->assetDB->select('estado');
                    $this->assetDB->from('historial_lecturas');
                    $this->assetDB->where('id_equipo', $idEquipo);
                    $this->assetDB->order_by('id_equipo', 'DESC');
                    $this->assetDB->limit(1);

                    $query2 = $this->assetDB->get();
                    $estado = $query2->result_array();
                    $valor['estado'] = $estado[0]['estado'];
                    $valor['cantEstadoActivo'] = 0;
                } else {
                    $valor['estado'] = $valor['estado'];
                    $valor['cantEstadoActivo'] = 0;
                }
            }
            return $equipos;
        }

    }

    /**
     * Calcula la disponibilidad de los ultimos 12meses de los equipos.
     *
     * @param   Int     $idEquipo   id del equipo a evaluar | todos los equipos.
     *
     * @return  Array|Void  Disponibilidad de equipos x mes de los ultimos 12meses.
     */
    public function kpiCalcularDisponibilidad($idEquipo, $fechaInicio = false, $fechaFin = false)
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; // guarda usuario logueado

        $disponibilidad = array();

        $this->assetDB->select('equipos.id_equipo, meta_disponibilidad as meta_dsp');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->join('equipos', 'equipos.id_equipo = historial_lecturas.id_equipo');
        $this->assetDB->join('grupo', 'grupo.id_grupo=equipos.id_grupo');
        $this->assetDB->join('sector', 'sector.id_sector=equipos.id_sector');
        $this->assetDB->join('empresas', 'empresas.id_empresa=equipos.id_empresa');
        $this->assetDB->join('unidad_industrial', 'unidad_industrial.id_unidad=equipos.id_unidad');
        $this->assetDB->join('criticidad', 'criticidad.id_criti=equipos.id_criticidad');
        $this->assetDB->join('area', 'area.id_area=equipos.id_area');
        $this->assetDB->join('proceso', 'proceso.id_proceso=equipos.id_proceso');
        $this->assetDB->join('admcustomers', 'admcustomers.cliId=equipos.id_customer');
        $this->assetDB->where('equipos.estado !=', 'AN');
        $this->assetDB->where('equipos.id_empresa', $empId);

        if ($idEquipo != 'all') {
            $this->assetDB->where('equipos.id_equipo', $idEquipo);
        }

        $this->assetDB->order_by('equipos.id_equipo', 'ASC');
        $this->assetDB->group_by('equipos.id_equipo');

        //$aux = $this->assetDB->get_compiled_select();
        $query = $this->assetDB->get();
        $equipos = $query->result_array();

        $nroEquipos = sizeof($equipos) == 0 ? 1 : sizeof($equipos);
        //dump($equipos, 'equipos');
        $metaPromedio = 0;
        //para cada equipo con lecturas calculo la disponibilidad
        for ($k = 0; $k < sizeof($equipos); $k++) {
            $metaPromedio = $metaPromedio + $equipos[$k]["meta_dsp"];
            //para cada mes
            for ($mes = 1; $mes <= 12; $mes++) {
                $mes2 = date('m') - 11 + $mes - 1;
                $fecha = date('Y-m', mktime(0, 0, 0, $mes2, 1, date('Y')));
                $inicio = date_create($fecha);

                $inicio->modify('first day of this month');
                $fin = date_create($fecha);
                $fin->modify('last day of this month');
                $fechasInicio[$mes - 1] = $inicio->format('Y-m-d 00:00:00');
                $fechasFin[$mes - 1] = $fin->format('Y-m-d 23:59:59');
                //dump($fechasInicio[$mes-1]);
                //dump($fechasFin[$mes-1]);
                //dump( $equipos[$k]["id_equipo"], 'id-equipo');

                //traigo historial de lecturas entre las fechas inicio y fin de mes
                $historialLecturasMes[$k] = $this->getHistorialLecturasMes($equipos[$k]["id_equipo"], $fechasInicio[$mes - 1], $fechasFin[$mes - 1]);
                //dump($historialLecturasMes, 'historial del mes '.$mes.' - equipo '.$equipos[$k]["id_equipo"]);
                $estadoaux = $historialLecturasMes[$k][0]['estado'];
                //$arrayIntervalos = array();
                $arrayIntervalos[$k][$mes - 1][0] = $historialLecturasMes[$k][0];

                //obtengo arreglo con los intervalos entre cambios de estado
                $i = 1;
                $nroLecturasMes = sizeof($historialLecturasMes[$k]);
                for ($j = 0; $j < $nroLecturasMes; $j++) {
                    if ($historialLecturasMes[$k][$j]['estado'] != $estadoaux) {
                        $arrayIntervalos[$k][$mes - 1][$i] = $historialLecturasMes[$k][$j];
                        $i++;
                        $estadoaux = $historialLecturasMes[$k][$j]['estado'];
                    }
                }
                //revisar siguiente linea
                $arrayIntervalos[$k][$mes - 1][$i] = $historialLecturasMes[$k][$nroLecturasMes - 1];
            }
            //dump($arrayIntervalos[$k], 'arrayIntervalos'.$mes.' - equipo'.$equipos[$k]["id_equipo"]);

            //Cambio la fecha de finalizacion del intervalo del mes actual
            $finDeMes = new DateTime(date('Y-m'));
            $finDeMes->modify('last day of this month')->setTime(23, 59, 59);
            $finDeMesString = $finDeMes->format('Y-m-d H:i:s');
            $ultimaFecha = date('Y-m-d H:i:s');
            //dump($finDeMesString);
            //dump($arrayIntervalos[$k][11][$nroLecturasMes-1]['fecha']);
            //dump($ultimaFecha);
            $sizeIntervalos = sizeof($arrayIntervalos[$k][11]);
            //dump($sizeIntervalos, 'tamaño intervalos');
            $arrayIntervalos[$k][11][$sizeIntervalos - 1]['fecha'] = $ultimaFecha;
            //dump($arrayIntervalos[$k], 'arrayIntervalos '.$mes.' - equipo'.$equipos[$k]["id_equipo"]);

            //calculo tiempo operativo y disponibilidad
            for ($mes = 1; $mes <= 12; $mes++) {
                $tiempoOperativo[$k][$mes - 1] = 0;
                $nroLecturasMes = sizeof($arrayIntervalos[$k][$mes - 1]);

                for ($j = 0; $j < $nroLecturasMes; $j++) {
                    if ($arrayIntervalos[$k][$mes - 1][$j]['estado'] == 'AC') {
                        if ((isset($arrayIntervalos[$k][$mes - 1][$j - 1])) and ($arrayIntervalos[$k][$mes - 1][$j - 1]['estado'] == 'AC')) {
                            $fin = $arrayIntervalos[$k][$mes - 1][$j]['fecha'];
                            $tiempoOperativo[$k][$mes - 1] = $tiempoOperativo[$k][$mes - 1] + (int) $this->diferencia($inicio, $fin);
                            $inicio = $arrayIntervalos[$k][$mes - 1][$j]['fecha'];
                        } else {
                            $inicio = $arrayIntervalos[$k][$mes - 1][$j]['fecha'];
                        }
                    } else {
                        if ((isset($arrayIntervalos[$k][$mes - 1][$j - 1])) and ($arrayIntervalos[$k][$mes - 1][$j - 1]['estado'] == 'AC')) {
                            $fin = $arrayIntervalos[$k][$mes - 1][$j]['fecha'];
                            $tiempoOperativo[$k][$mes - 1] = $tiempoOperativo[$k][$mes - 1] + (int) $this->diferencia($inicio, $fin);
                        }
                    }
                }

                // Tiempo operativo en meses futuros = 0
                // (para perdiodos de tiempo parametrizados)
                /*$diferenciaFecha = strtotime($finDeMesString) < strtotime($arrayIntervalos[$k][$mes-1][$nroLecturasMes-1]['fecha']);
                if( ($nroLecturasMes == '2') AND ($diferenciaFecha) )
                {
                $tiempoOperativo[$k][$mes-1] = 0;
                }*/

                //saco la fecha inicio y fecha fin de mes
                $mesActualInt = date('m') - 11 + $mes - 1; //date( 'Y-'.$mes );
                $mesActual = date('Y-m', mktime(0, 0, 0, $mesActualInt, 1, date('Y')));

                $fechaInicio = new DateTime(date('Y-m', mktime(0, 0, 0, $mesActualInt, 1, date('Y'))));
                $fechaInicioString = $fechaInicio->format('Y-m-d H:i:s');

                $fechaFin = new DateTime(date('Y-m', mktime(0, 0, 0, $mesActualInt, 1, date('Y'))));
                $fechaFin->modify('last day of this month')->setTime(23, 59, 59);
                $fechaFinString = $fechaFin->format('Y-m-d H:i:s');

                $cantSegundosPorMes = $this->diferencia($fechaInicioString, $fechaFinString);
                //dump($cantSegundosPorMes);
                $disponibilidadMes[$k][$mes] = number_format($tiempoOperativo[$k][$mes - 1] * 100 / $cantSegundosPorMes, 2, '.', '');
                $yearMonth[$mes] = $mesActual;

                //dump($disponibilidadMes[$k]);
            }

            //calculo disponibilidad de mes actual
            //dump($arrayIntervalos[$k][11][0]['fecha']);
            //dump($arrayIntervalos[$k][11][$nroLecturasMes-1]['fecha']);
            if ($ultimaFecha == $arrayIntervalos[$k][11][$nroLecturasMes - 1]['fecha']) {
                $cantSegundosPorMes = $this->diferencia($arrayIntervalos[$k][11][0]['fecha'], $arrayIntervalos[$k][11][$nroLecturasMes - 1]['fecha']);
                $disponibilidadMes[$k][12] = number_format($tiempoOperativo[$k][11] * 100 / $cantSegundosPorMes, 2, '.', '');
            }
            //dump($cantSegundosPorMes);
            //dump($disponibilidadMes[$k], 'Disponibilidad equipo '.$equipos[$k]["id_equipo"]);

        }

        //disponibilidad de todos los equipos
        $dispon = array();
        foreach ($disponibilidadMes as $x => $disponiEquipos) {
            foreach ($disponiEquipos as $id => $value) {
                if (array_key_exists($id, $dispon)) {
                    $dispon[$id] += $value;
                    //$dispon[$id] = $dispon[$id] / 2;
                } else {
                    $dispon[$id] = $value;
                }
            }
        }
        for ($x = 1; $x <= 12; $x++) {
            $dispon[$x] = $dispon[$x] / $nroEquipos;
        }
        //dump($dispon, 'TO');

        //preparo info para mandar
        $i = 0;
        foreach ($dispon as $value) {
            $disp[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($yearMonth as $value) {
            $mesAnio[$i] = $value;
            $i++;
        }

        $disponibilidad['promedioMetas'] = $metaPromedio / $nroEquipos;
        $disponibilidad["porcentajeHorasOperativas"] = $disp;
        $disponibilidad["tiempo"] = $mesAnio;
        //dump_exit($disponibilidad);
        return $disponibilidad;
    }

    /**
     * Calcula el estado inicial del primer periodo.
     *
     * @param   String  $fechaInicio    Fecha de inicio.
     *
     * @return  String  Estado del último historial de lectura antes de la fecha inicial.
     */
    public function getEstadoAnterior($fechaInicio, $idEquipo) // Ok

    {
        $this->assetDB->select('estado');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->where('id_equipo', $idEquipo);
        $this->assetDB->where('fecha <=', $fechaInicio);
        $this->assetDB->order_by('fecha', 'DESC');
        $this->assetDB->limit(1);
        $query = $this->assetDB->get();
        $lecturaAnterior = $query->result_array();
        return $lecturaAnterior[0]['estado'];
    }

    /**
     * Saca diferencia entre dos fechas.
     *
     * @param   Datetime    $fecha_inicio   Fecha de inicio.
     * @param   Datetime    $fecha_fin      Fecha final de la comparacion.
     *
     * @return  Datetime    Diferencia de fechas.
     */
    public function diferencia($fecha_inicio, $fecha_fin, $formato = "s") // Ok

    {
        $datetime1 = new DateTime($fecha_inicio);
        $datetime2 = new DateTime($fecha_fin);
        $diferencia = $datetime1->diff($datetime2, false);
        switch ($formato) {
            case "y": //años
                $total = $diferencia->y + $diferencia->m / 12 + $diferencia->d / 365.25;
                break;
            case "m": //meses
                $total = $diferencia->y * 12 + $diferencia->m + $diferencia->d / 30 + $diferencia->h / 24;
                break;
            case "d": //dias
                $total = $diferencia->y * 365.25 + $diferencia->m * 30 + $diferencia->d + $diferencia->h / 24 + $diferencia->i / 60;
                break;
            case "h": //horas
                $total = ($diferencia->y * 365.25 + $diferencia->m * 30 + $diferencia->d) * 24 + $diferencia->h + $diferencia->i / 60;
                break;
            case "i": //minutos
                $total = (($diferencia->y * 365.25 + $diferencia->m * 30 + $diferencia->d) * 24 + $diferencia->h) * 60 + $diferencia->i + $diferencia->s / 60;
                break;
            case "s": //segundos
                $total = ((($diferencia->y * 365.25 + $diferencia->m * 30 + $diferencia->d) * 24 + $diferencia->h) * 60 + $diferencia->i) * 60 + $diferencia->s;
                break;
        }
        //dump_exit($total);
        return $total;
    }

    public function getHistorialLecturasMes($idEquipo, $fechaInicio, $fechaFin)
    {
        //saco las lecturas del mes
        $this->assetDB->select('id_lectura, id_equipo, lectura, fecha, estado');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->where('id_equipo', $idEquipo);
        $this->assetDB->where('fecha >=', $fechaInicio);
        $this->assetDB->where('fecha <=', $fechaFin);
        $this->assetDB->order_by("fecha", "asc");

//        $aux = $this->assetDB->get_compiled_select();

        $query = $this->assetDB->get();
        $historialLecturas = $query->result_array();
        //dump($historialLecturas, 'Historial de lecturas del mes');

        //agrego lectura el primer dia del mes a las 00:00hs
        $estadoInicial = $this->getEstadoAnterior($fechaInicio, $idEquipo);
        //dump($estadoInicial, 'Estado inicial');
        $lecturaInicio[0] = array(
            'id_lectura' => '0',
            'id_equipo' => '0',
            'lectura' => '0',
            'fecha' => $fechaInicio,
            'estado' => $estadoInicial,
        );
        $historialLecturas = array_merge($lecturaInicio, $historialLecturas);

        //agrego lectura al final del mes a las 23:59
        $ultimaLectura = end($historialLecturas);
        $ultimoEstado = $ultimaLectura['estado'];
        //dump($ultimoEstado, 'Último estado');
        $lecturaFin[0] = array(
            'id_lectura' => '0',
            'id_equipo' => '0',
            'lectura' => '0',
            'fecha' => $fechaFin,
            'estado' => $ultimoEstado,
        );
        $historialLecturas = array_merge($historialLecturas, $lecturaFin);
        //dump($historialLecturas, 'Historial de lecturas del mes');
        return $historialLecturas;
    }

    /**
     * Trae info de equipo a editar
     *
     * @param   Int     Id de equipo a editar
     *
     * @return  Array  Información del equipo.
     */
    public function getEquipoId($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getEquipoId(". $idEquipo .")");
        $empId = empresa();

        $this->assetDB->select('equipos.id_equipo,
                    equipos.descripcion AS deeq,
                    equipos.fecha_ingreso,
                    equipos.fecha_garantia,
                    equipos.codigo,
                    equipos.ubicacion,
                    equipos.fecha_ultimalectura,
                    equipos.ultima_lectura,
                    equipos.descrip_tecnica,
                    equipos.numero_serie,
                    equipos.estado,
                    unidad_industrial.id_unidad,
                    unidad_industrial.descripcion AS deun,
                    area.id_area,
                    area.descripcion AS dear,
                    empresas.id_empresa,
                    empresas.descripcion AS deem,
                    sector.id_sector,
                    sector.descripcion AS desec,
                    criticidad.id_criti,
                    criticidad.descripcion AS decri,
                    proceso.id_proceso,
                    proceso.descripcion AS depro,
                    marcasequipos.marcaid,
                    marcasequipos.marcadescrip'
        );
        $this->assetDB->from('equipos');
        $this->assetDB->join('sector', 'sector.id_sector=equipos.id_sector');
        $this->assetDB->join('empresas', 'empresas.id_empresa=equipos.id_empresa');
        $this->assetDB->join('unidad_industrial', 'unidad_industrial.id_unidad=equipos.id_unidad');
        $this->assetDB->join('criticidad', 'criticidad.id_criti=equipos.id_criticidad');
        $this->assetDB->join('area', 'area.id_area=equipos.id_area');
        $this->assetDB->join('proceso', 'proceso.id_proceso=equipos.id_proceso');
        $this->assetDB->join('marcasequipos', 'marcasequipos.marcaid = equipos.marca');
        $this->assetDB->where('equipos.id_empresa', $empId);
        $this->assetDB->where('equipos.estado !=', 'AN');
        /*// esta validacion hay que hacerla al traer grupos sectores y equipos
        $this->assetDB->where('sector.estado !=', 'AN');
        $this->assetDB->where('marcasequipos.estado !=', 'AN');*/
        $this->assetDB->where('equipos.id_equipo', $idEquipo);

        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            $datosEquipo = $query->result_array();

            //traer cliente
            $this->assetDB->select('admcustomers.cliId, admcustomers.cliRazonSocial');
            $this->assetDB->from('equipos');
            $this->assetDB->join('admcustomers', 'admcustomers.cliId=equipos.id_customer');
            $this->assetDB->where('equipos.id_empresa', $empId);
            $this->assetDB->where('equipos.id_equipo', $idEquipo);
            $query = $this->assetDB->get();
            if ($query->num_rows() != 0) {
                $datosEquipoCustomer = $query->result_array();
                $datosEquipo[0]["cliId"] = $datosEquipoCustomer[0]["cliId"];
                $datosEquipo[0]["cliRazonSocial"] = $datosEquipoCustomer[0]["cliRazonSocial"];
            } else {
                $datosEquipo[0]["cliId"] = null;
                $datosEquipo[0]["cliRazonSocial"] = null;
            }

            //traer grupo
            $this->assetDB->select('grupo.id_grupo, grupo.descripcion AS degr');
            $this->assetDB->from('equipos');
            $this->assetDB->join('grupo', 'grupo.id_grupo=equipos.id_grupo');
            $this->assetDB->where('equipos.id_empresa', $empId);
            $this->assetDB->where('equipos.id_equipo', $idEquipo);
            $query = $this->assetDB->get();
            if ($query->num_rows() != 0) {
                $datosEquipoCustomer = $query->result_array();
                $datosEquipo[0]["id_grupo"] = $datosEquipoCustomer[0]["id_grupo"];
                $datosEquipo[0]["degr"] = $datosEquipoCustomer[0]["degr"];
            } else {
                $datosEquipo[0]["id_grupo"] = null;
                $datosEquipo[0]["degr"] = null;
            }

            //traer adjunto
            $this->assetDB->select('equipos_archi_adjuntos.id_adjunto, equipos_archi_adjuntos.adjunto');
            $this->assetDB->from('equipos');
            $this->assetDB->join('equipos_archi_adjuntos', 'equipos.id_equipo = equipos_archi_adjuntos.id_equipo');
            $this->assetDB->where('equipos.id_equipo', $idEquipo);
            $this->assetDB->where('equipos_archi_adjuntos.eliminado !=', 1);
            $query = $this->assetDB->get();
            if ($query->num_rows() != 0) {
                $datosEquipoCustomer = $query->result_array();
                $arr_length = count($datosEquipoCustomer);
               
                for($i = 0; $i < $arr_length; $i++)
                {
                    $archivos[]=array(
                        'id_adjunto' => $datosEquipoCustomer[$i]["id_adjunto"], 
                        'adjunto' =>$datosEquipoCustomer[$i]["adjunto"]); 
                    $datosEquipo[0]["archivo"][$i] = $archivos[$i];
                }
            } else {
                $datosEquipo[0]["archivo"] = null;
            }

            return $datosEquipo;
        } else {
            return [];
        }
    }
    /**
	* Busca asiganciones de contratistas para un equipo determinado
	* @param integer $id_equipo ID del equipo
	* @return array lista de contratiastas coincidentes
	*/
    public function getContratistasEquipo($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getContratistasEquipo($idEquipo)");
        $this->assetDB->select('
            contratistaquipo.id_equipo, contratistaquipo.id_contratista,
            equipos.codigo,
            contratistas.nombre
            ');
        $this->assetDB->from('contratistaquipo');
        $this->assetDB->join('equipos', 'equipos.id_equipo=contratistaquipo.id_equipo');
        $this->assetDB->join('contratistas', 'contratistas.id_contratista = contratistaquipo.id_contratista');
        $this->assetDB->where('contratistaquipo.id_equipo', $idEquipo);
        $query = $this->assetDB->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    /**
	* Habilita el equipo seleccionado
	* @param integer $idequipo
	* @return integer id generado en insercion
	*/
    public function estado_alta($idEquipo){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | estado_alta(ID Equipo: $idEquipo)");
        $this->assetDB->where('id_equipo', $idEquipo);
        $this->assetDB->set('estado', 'AC');
        $res = $this->assetDB->update('equipos');

        if(!$res) return false;
        $ci =& get_instance();
        $userdata = $ci->session->userdata();

        $datos = array(
            'id_equipo' => $idEquipo,
            'lectura' => 0,
            'fecha' => (new DateTime())->format('Y-m-d H:i:s'),
            'usrId' => $userdata['id'],
            'observacion' => 'Lectura alta de equipo',
            'operario_nom' => '-',
            'turno' => 'Alta',
            'estado' => 'AC',
        );

        $this->assetDB->insert('historial_lecturas', $datos);
        return $this->assetDB->insert_id();
    }

    /**
     * Equipos:updateAdjuntoEquipo();
     *
     * @param  String   $adjunto    Nombre del archivo
     * @param  Int      $ultimoId   Id del equipo al que se le va a adjuntar archivo
     * @return String               Nombre del archivo adjuntado
     */
    public function updateAdjuntoEquipo($adjunto, $ultimoId)
    {
        $this->assetDB->where('id_equipo', $ultimoId);
        $query = $this->assetDB->update("equipos", $adjunto);
        return $adjunto;
    }

    public function updateAdjuntoEquipoV2($id_adjunto, $ultimoId)
    {
        $this->assetDB->where('id_equipo', $ultimoId);
        $query = $this->assetDB->update("equipos", $id_adjunto);
        return $id_adjunto;
    }

    /**
     * Equipos:guardaAdjuntoEquipo();
     *
     * @param  String   $data    id_equipo, adjunto
     * @return int       id_adjunto        
     */
    public function guardaAdjuntoEquipo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | guardaAdjuntoEquipo(" . json_encode($data) . ")");
        $this->assetDB->insert('equipos_archi_adjuntos', $data);
        $query = $this->assetDB->insert_id();
        return $query;
    }

    /**
     * Equipos:eliminarAdjunto
     * Elimina el Archivo Adjunto de un preventivo dado (no elimina el archivo).
     *
     * @param Int       $idPreventivo   Id de preventivo
     * @return Bool                     True o False
     */
    public function eliminarAdjunto($idEquipo)
    {
        $data = array('adjunto' => '');
        $this->assetDB->where('id_equipo', $idEquipo);
        $query = $this->assetDB->update("equipos", $data);
        return $query;
    }

    /**
     * Equipos:eliminaAdjunto
     * Elimina el Archivo Adjunto de un equipo(Marca como eliminado)
     *
     * @param Int       $idAdjunto  Id de archivo adjunto
     * @return Bool                     True o False
     */
    public function eliminaAdjunto($idAdjunto)
    {
        $data = array('eliminado' => '1');
        $this->assetDB->where('id_adjunto', $idAdjunto);
        $query = $this->assetDB->update("equipos_archi_adjuntos", $data);
        return $query;
    }


    /**
     * equipos_archi_adjuntos:updateAdjunto();
     *
     * @param  String   $data    idAdjunto, adjunto
     * @return int       id_adjunto        
     */
    public function updateAdjunto($archivo, $idAdjunto){
        $this->assetDB->where('id_adjunto', $idAdjunto);
        $query = $this->assetDB->update("equipos_archi_adjuntos", $archivo);
        return $archivo;
    }

    public function informe_equipos()
    {
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa'];

        $this->assetDB->select('count(*) as result');
        $this->assetDB->where('estado', 'AC');
        $this->assetDB->where('id_empresa', $empId);
        $data['eqActivos'] = $this->assetDB->get('equipos')->row()->result;

        $this->assetDB->select('count(*) as result');
        $this->assetDB->where('estado', 'RE');
        $this->assetDB->where('id_empresa', $empId);
        $data['eqReparacion'] = $this->assetDB->get('equipos')->row()->result;

        return $data;
    }
    /**
	* Obtiene la meta del equipo
	* @param integer $eq ID del equipo
	* @return array meta del equipo
	*/
    public function getMeta($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getMeta(" . json_encode($data) . ")");
        $this->assetDB->select('meta_disponibilidad');
        $this->assetDB->where('id_equipo', $data['eq']);
        $this->assetDB->from('equipos');
        $query = $this->assetDB->get();
        $meta = $query->result_array();
        return $meta[0]['meta_disponibilidad'];
    }
    /**
	* Asigna una meta al equipo
	* @param array $eq ID del equipo; $meta valor de la meta
	* @return array respuesta de operacion
	*/
    public function asignarMeta($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipos | getMeta(" . json_encode($data) . ")");
        $this->assetDB->set('meta_disponibilidad', $data['meta']);
        $this->assetDB->where('id_equipo', $data['eq']);
        return $this->assetDB->update('equipos');
    }
    /**
     * Busca los registros de equipos con los parametros indicados de paginado
	 * @param int inicio
	 * @param int tamaño
	 * @param string busqueda
	 * @return Array equipos
	 */
    public function equiposPaginados($start,$length,$search){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Equipo | equiposPaginados($start,$length,$search)");
        $empId = empresa();

        $srch="";
        if($search){
            $srch="and( equipos.codigo LIKE'%".$search."%' OR
                        equipos.descripcion LIKE'%".$search."%' OR
                        area.descripcion LIKE'%".$search."%' OR
                        proceso.descripcion LIKE'%".$search."%' OR
                        sector.descripcion LIKE'%".$search."%' OR
                        criticidad.descripcion LIKE'%".$search."%' OR
                        admcustomers.cliRazonSocial LIKE'%".$search."%' OR
                        equipos.estado LIKE'%".$search."%')";//creo q estado no hace falta
        }
        
        $qnr = "SELECT count(1) cant
                FROM equipos 
                INNER JOIN sector ON sector.id_sector=equipos.id_sector
                INNER JOIN empresas ON empresas.id_empresa = equipos.id_empresa
                INNER JOIN unidad_industrial ON unidad_industrial.id_unidad=equipos.id_unidad
                INNER JOIN criticidad ON criticidad.id_criti = equipos.id_criticidad
                INNER JOIN area ON area.id_area = equipos.id_area
                INNER JOIN proceso ON proceso.id_proceso = equipos.id_proceso
                INNER JOIN admcustomers ON admcustomers.cliId = equipos.id_customer
                WHERE equipos.estado != 'AN' AND equipos.id_empresa = $empId
                ".$srch;

        $qnr = $this->assetDB->query($qnr);
        $qnr = $qnr->row();
        $qnr = $qnr->cant;

        $q = "SELECT equipos.id_equipo,
                    equipos.codigo,
                    equipos.descripcion AS deeq,
                    equipos.estado AS estadoEquipo,
                    equipos.meta_disponibilidad AS meta_disp,
                    unidad_industrial.id_unidad,
                    unidad_industrial.descripcion AS deun,
                    area.id_area,
                    area.descripcion AS dear,
                    empresas.id_empresa,
                    empresas.descripcion AS deem,
                    sector.id_sector,
                    sector.descripcion AS desec,
                    criticidad.id_criti,
                    criticidad.descripcion AS decri,
                    grupo.form_id,
                    grupo.id_grupo,
                    admcustomers.cliId,
                    admcustomers.cliRazonSocial AS clie,
                    proceso.id_proceso,
                    proceso.descripcion AS depro
            FROM equipos 
            INNER JOIN sector ON sector.id_sector = equipos.id_sector
            INNER JOIN empresas ON empresas.id_empresa = equipos.id_empresa
            INNER JOIN unidad_industrial ON unidad_industrial.id_unidad = equipos.id_unidad
            INNER JOIN criticidad ON criticidad.id_criti = equipos.id_criticidad
            INNER JOIN area ON area.id_area = equipos.id_area
            INNER JOIN proceso ON proceso.id_proceso = equipos.id_proceso
            INNER JOIN grupo on grupo.id_grupo = equipos.id_grupo
            INNER JOIN admcustomers ON admcustomers.cliId = equipos.id_customer
            WHERE equipos.estado != 'AN' AND equipos.id_empresa = $empId
            ".$srch."
            ORDER BY equipos.id_equipo ASC
            LIMIT $start,$length";

        $r = $this->assetDB->query($q);

        $result = array (
            'numDataTotal' => $qnr,
            'datos' =>$r
        );

        return $result;
    }

    /**
     * equipos_archi_adjuntos:getAdjuntos();
     *
     * @param  String   $idEquipo   idEquipo 
     * @return int       array archivos adjuntos al equipo        
     */
    function getAdjuntos($idEquipo)
    {
        $this->assetDB->select('equipos_archi_adjuntos.id_adjunto, equipos_archi_adjuntos.adjunto');
        $this->assetDB->from('equipos');
        $this->assetDB->join('equipos_archi_adjuntos', 'equipos.id_equipo = equipos_archi_adjuntos.id_equipo');
        $this->assetDB->where('equipos_archi_adjuntos.eliminado !=', 1);
        $this->assetDB->where('equipos.id_equipo', $idEquipo);
        $query = $this->assetDB->get();
        if ($query->num_rows() != 0) {
            $datosEquipoCustomer = $query->result_array();
            $arr_length = count($datosEquipoCustomer);  
            for($i = 0; $i < $arr_length; $i++)
            {
                $archivos[]=array(
                    'id_adjunto' => $datosEquipoCustomer[$i]["id_adjunto"], 
                    'adjunto' =>$datosEquipoCustomer[$i]["adjunto"]); 
            }
        } else {
            $archivos[0] = null;
        }
        return $archivos;
    }


    /**
     * getFormxIdGrupo: obtiene el id del formulario asociado al grupo
     * @param int $id_grupo
     * @return bool true o false
     */

    function getFormxIdGrupo($id_grupo){
        $this->assetDB->select('form_id');
        $this->assetDB->from('grupo');
        $this->assetDB->where('grupo.id_grupo', $id_grupo); 
       // $sql="select form_id from grupo where id_grupo = $id_grupo";
        $query = $this->assetDB->get()->result();
        return $query;
    }

    /**
     * setInfoId: guarda en la tabla historial lecturas el info_id del formulario asociado
     * 
     * @return bool true o false
     */
    function guardaInfo_idLectura(){
    $info_id= $this->input->post('info_id');
    $equipo= $this->input->post('equipo');
        
    $id_lectura = $this->assetDB->select_max('id_lectura')->where('id_equipo', $equipo)->get('historial_lecturas')->row('id_lectura');
    
    $this->assetDB->set('info_id', $info_id);
    $this->assetDB->where('id_lectura', $id_lectura);
    return $this->assetDB->update('historial_lecturas');

   /*  $data = array(
        'info_id' => $info_id,
    );

    $this->assetDB->where('id_lectura', $id_lectura);
    $query = $this->assetDB->update("historial_lecturas", $data);
    return $query; */

    /* $this->assetDB->set('estado', $ultimoEstado);
        return $this->assetDB->update("equipos"); */
        
    } 

}
