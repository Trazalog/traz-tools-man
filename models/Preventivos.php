<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Preventivos extends CI_Model{
	function __construct(){
		parent::__construct();
        $this->assetDB = $this->load->database('asset_db', TRUE);
	}

    /**
	* Trae listado de Preventivos por empresa logueada
	* @param integer $empId
	* @return array listado de preventivos
	*/
	function preventivos_List(){	
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | preventivos_List()");
		$empId = empresa();

		$this->assetDB->select('preventivo.prevId, 
											preventivo.id_equipo, 
											tareas.descripcion AS deta, 
											equipos.descripcion AS des,                            
											grupo.descripcion AS des1,
											componentes.descripcion,
											periodo.descripcion AS periodoDesc,
											preventivo.cantidad,
											preventivo.ultimo,
											preventivo.horash,
											preventivo.prev_adjunto,
											preventivo.estadoprev AS estado');
		$this->assetDB->from('preventivo');
		$this->assetDB->join('equipos', 'equipos.id_equipo = preventivo.id_equipo');
		$this->assetDB->join('grupo', 'equipos.id_grupo=grupo.id_grupo');
		$this->assetDB->join('tareas', 'tareas.id_tarea = preventivo.id_tarea');
		$this->assetDB->join('componentes', 'componentes.id_componente = preventivo.id_componente');
		$this->assetDB->join('periodo', 'periodo.idperiodo = preventivo.perido');
		$this->assetDB->where('preventivo.estadoprev !=', 'AN');
		$this->assetDB->where('preventivo.id_empresa', $empId);	

		$query= $this->assetDB->get();

		if( $query->num_rows() > 0)
		{
			$data['data'] = $query->result_array();
			return  $data;
		}
	}
    /**
	* Trae equipos por empresa logueada
	* @param 
	* @return array lista de equipos
	*/
	function getequipo(){
        log_message('DEBUG',"#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getequipo()");
        $empId = empresa(); 

        $this->assetDB->select('equipos.*');
        $this->assetDB->from('equipos');
        $this->assetDB->join('grupo', 'grupo.id_grupo=equipos.id_grupo');
        $this->assetDB->join('sector', 'sector.id_sector=equipos.id_sector');
        $this->assetDB->join('empresas', 'empresas.id_empresa=equipos.id_empresa');
        $this->assetDB->join('unidad_industrial', 'unidad_industrial.id_unidad=equipos.id_unidad');
        $this->assetDB->join('criticidad', 'criticidad.id_criti=equipos.id_criticidad');
        $this->assetDB->join('area', 'area.id_area=equipos.id_area');
        $this->assetDB->join('proceso', 'proceso.id_proceso=equipos.id_proceso');
        $this->assetDB->join('admcustomers', 'admcustomers.cliId=equipos.id_customer');
        $this->assetDB->where('equipos.estado !=', 'AN');
        $this->assetDB->where('equipos.estado !=', 'IN');
        $this->assetDB->where('equipos.id_empresa', $empId);
        $this->assetDB->order_by('equipos.codigo', 'ASC');   
        
        $query= $this->assetDB->get();   

        if ($query->num_rows()!=0){
            return $query->result_array();
        }else{
            return false;
        }
	}
    /**
	* Trae unidades de tiempo
	* @param 
	* @return array lista de unidades de tiempo
	*/
    function getUnidTiempos(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getUnidTiempos()");
        $this->assetDB->select('unidad_tiempo.*');
        $this->assetDB->from('unidad_tiempo');       
        $query= $this->assetDB->get();

        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
    /**
	* Trae datos de equipo por ID para nuevo preventivo
	* @param integer $id_equipo
	* @return array lista de tareas
	*/
	function getEquipoNuevoPrevent($data = null){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getEquipoNuevoPrevent(".json_encode($data).")");
		$id_equipo = $data['id_equipo'];

		// $userdata = $this->session->userdata('user_data');
        // $empId = $userdata[0]['id_empresa'];
        $empId = 6;
        
    	$this->assetDB->select('equipos.*, marcasequipos.marcadescrip');
        $this->assetDB->from('equipos');    	
        $this->assetDB->join('marcasequipos','marca = marcaid', 'left');
    	$this->assetDB->where('equipos.id_equipo', $id_equipo);    	
//    	$this->assetDB->where('equipos.id_empresa', $empId); 	
    	$query= $this->assetDB->get();		

		if($query->num_rows()>0){
            return $query->result()[0];
        }
        else{
            return false;
        }
	}
    /**
	*  Trae tareas por empresa logueada
	* @param integer $empId
	* @return array lista de tareas
	*/
	function gettarea(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | gettarea()");
        $empId = empresa();

    	$this->assetDB->select('tareas.id_tarea AS value, tareas.descripcion AS label');
    	$this->assetDB->from('tareas');    	
    	$this->assetDB->where('tareas.id_empresa', $empId);
        $this->assetDB->where('estado', 'AC');
        $this->assetDB->order_by('label', 'ASC');
    	$query= $this->assetDB->get();

		if($query->num_rows()>0){
            return $query->result_array();
        }
        else{
            return false;
        }
	}	
    /**
	*  Trae componente segun id de equipo
	* @param integer $id id de equipo
	* @return array lista de componenetes
	*/
	function getcomponente($id){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getcomponente($id)");
        $this->assetDB->distinct();
	   	$this->assetDB->select('componentes.id_componente, componentes.descripcion');
    	$this->assetDB->from('componentes');
    	$this->assetDB->join('componenteequipo', 'componenteequipo.id_componente = componentes.id_componente');
        $this->assetDB->join('marcasequipos', 'componentes.marcaid = marcasequipos.marcaid');
    	$this->assetDB->where('componenteequipo.id_equipo', $id);
        $this->assetDB->where('componentes.estado', 'AC');
        $this->assetDB->where('marcasequipos.estado', 'AC');
        $this->assetDB->order_by('componentes.descripcion', 'ASC');
    	$query = $this->assetDB->get();
		if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
	}

	// Trae periodo de tiempo (dias)
	function getperiodo(){

		$query= $this->assetDB->get_where('periodo');
		if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
	}
	/**
	* Trae herramientas segun empresa logueada
	* @param 
	* @return array lista de herramientas
	*/
	public function getherramienta(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getherramienta()");
        $empId = empresa(); 

		$query= $this->assetDB->get_where('herramientas',array('id_empresa' => $empId));
		if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
	}
    /**
	* Trae herramientas por empresa logueada 
	* @param 
	* @return array lista de herramientas
	*/
	function getHerramientasB(){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getHerramientasB()");
        $empId = empresa();
		$this->assetDB->select('herramientas.herrId AS value, 
				herramientas.herrcodigo AS codigo,
				herramientas.herrmarca AS marca,
				herramientas.herrdescrip AS label');
		$this->assetDB->from('herramientas');      
		$this->assetDB->where('herramientas.id_empresa', $empId);
		//$this->assetDB->where('herramientas.estado !=', 'AN');
		$this->assetDB->order_by('label', 'ASC');
		$query = $this->assetDB->get();

		if($query->num_rows()>0){
            return $query->result_array();
		}else{
            return false;
		}
	}
    /**
	* Trae insumos (articles) por empresa logueada 
	* @param 
	* @return array lista de insumos
	*/
	function getinsumo(){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | getinsumo()");
        $empId = empresa();
        $this->assetDB->select('articles.artId AS value, 
                                            articles.artBarCode AS codigo,
                                            articles.artDescription AS label');
        $this->assetDB->from('articles');      
        $this->assetDB->where('articles.id_empresa', $empId);
        $this->assetDB->where('articles.artEstado !=', 'AN');
        $this->assetDB->order_by('label', 'ASC');
        $query = $this->assetDB->get();		
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return false;
		}
	}

	//Trae insumo por id
	function traerinsumo($data = null){
		$id = $data['id_insumo'];
		$userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 

        $this->assetDB->select('articles.*');
    	$this->assetDB->from('articles');    	
    	$this->assetDB->where('articles.artId', $id);    	
    	$this->assetDB->where('articles.id_empresa', $empId);

    	$query= $this->assetDB->get();
    	if($query->num_rows()>0){
                return $query->result();
       	}
        else{
                return false;
        }	
	}
    /**
	* Guarda Preventivo
	* @param array $data datos del preventivo
	* @return array respuesta de la consulta
	*/
	function insert_preventivo($data){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | insert_preventivo(" . json_encode($data). ")");
        $query = $this->assetDB->insert("preventivo",$data);
        $response['status'] = $query;

        if($response['status']){
            $response['id'] = $this->assetDB->insert_id();
            return $response;
        }else{
            return $response;
        }
    }
    /**
	* Guarda el batch de datos de herramientas de Preventivo
	* @param array $data2 datos de herramientas del preventivo
	* @return integer cantidad de inserciones de la consulta
	*/
	function insertPrevHerram($data2){
		log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | insertPrevHerram(" . json_encode($data2). ")");
        $query = $this->assetDB->insert_batch("tbl_preventivoherramientas",$data2);
        return $query;
    }
    /**
	* Guarda el batch de datos de insumos de Preventivo
	* @param array $data3 datos de insumos del preventivo
	* @return integer cantidad de inserciones de la consulta
	*/
    function insertPrevInsum($data3){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | insertPrevInsum(" . json_encode($data2). ")");
        $query = $this->assetDB->insert_batch("tbl_preventivoinsumos",$data3);
        return $query;
    }

    // Guarda el nombre de adjunto
    function updateAdjunto($adjunto,$ultimoId){
        $this->assetDB->where('prevId', $ultimoId);
        $query = $this->assetDB->update("preventivo",$adjunto);
        return $adjunto;
    }
    /**
    * Cambia el estado(eliminado logico) del preventivo por id
    * @param integer $idprev id de preventivo
    * @return bool true or false segun resultado de la operacion
    */
    public function update_preventivo($data, $idprev){
        log_message('DEBUG', "#TRAZA | TRAZ-TOOLS-MAN | Preventivos | update_preventivo()");
        $this->assetDB->where('prevId', $idprev);
        $query = $this->assetDB->update("preventivo",$data);
        return $query;
    }
    //////////////////////////edicion  datos    

    // Trae info de Preventivo a Editar - Listo
    function getInfoPreventivo($id){
        
        $this->assetDB->select('preventivo.prevId, 
                            preventivo.perido, 
                            preventivo.cantidad, 
                            preventivo.ultimo, 
                            preventivo.critico1, 
                            preventivo.estadoprev, 
                            preventivo.horash, 
                            preventivo.prev_duracion,
                            preventivo.prev_canth,
                            preventivo.prev_adjunto,
                            preventivo.id_unidad,
                            equipos.id_equipo, 
                            equipos.codigo, 
                            equipos.marca, 
                            equipos.fecha_ingreso, 
                            equipos.descripcion, 
                            equipos.ubicacion, 
                            componentes.id_componente, 
                            componentes.descripcion AS comp, 
                            tareas.id_tarea, 
                            tareas.descripcion AS descripta');
        $this->assetDB->from('preventivo');
        $this->assetDB->join('equipos', 'equipos.id_equipo=preventivo.id_equipo');
        $this->assetDB->join('tareas', 'tareas.id_tarea=preventivo.id_tarea');
        $this->assetDB->join('componentes', 'componentes.id_componente=preventivo.id_componente');       
        $this->assetDB->where('preventivo.prevId', $id);

        $query = $this->assetDB->get();

        if( $query->num_rows() > 0)
        {
          return $query->result_array();
        }
        else {
          return 0;
        }
    }

    // Trae herramientas ppor id de preventivo para Editar
    function getPreventivoHerramientas($id){
        
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 

        $this->assetDB->select('tbl_preventivoherramientas.cantidad,
                            herramientas.herrcodigo,
                            herramientas.herrmarca,
                            herramientas.herrdescrip,
                            herramientas.herrId');
        $this->assetDB->from('tbl_preventivoherramientas');
        $this->assetDB->join('herramientas', 'herramientas.herrId = tbl_preventivoherramientas.herrId');   
        $this->assetDB->where('tbl_preventivoherramientas.prevId', $id);        
        $this->assetDB->where('tbl_preventivoherramientas.id_empresa', $empId);
        $query= $this->assetDB->get();

        if( $query->num_rows() > 0)
        {
          return $query->result_array();
        }
        else {
          return 0;
        }
    }

    // Trae insumos por id de preventivo para Editar
    function getPreventivoInsumos($id){
        
        $userdata = $this->session->userdata('user_data');
        $empId = $userdata[0]['id_empresa']; 

        $this->assetDB->select('tbl_preventivoinsumos.id,
                            tbl_preventivoinsumos.cantidad,
                            articles.artBarCode,
                            articles.artId,
                            articles.artDescription,
                            articles.id_empresa');                            
        $this->assetDB->from('tbl_preventivoinsumos');
        $this->assetDB->join('articles', 'articles.artId = tbl_preventivoinsumos.artId');   
        $this->assetDB->where('tbl_preventivoinsumos.prevId', $id);        
        $this->assetDB->where('articles.id_empresa', $empId);
        $query= $this->assetDB->get(); 

        if( $query->num_rows() > 0)
        {
          return $query->result_array();
        }
        else {
          return 0;
        }
    }

    // Guarda edicion de Preventivo 
    function update_editar($data, $idp){
        // echo "id preventivo: ";
        // var_dump($idp);
     //echo "datosven modelo: ";
        //var_dump($data);
        $this->assetDB->where('prevId', $idp);
        $query = $this->assetDB->update("preventivo",$data);
        return $query;
    }

    // Update herramientas preventivo
    function deleteHerramPrev($id_preventivo){        
        $this->assetDB->where('prevId', $id_preventivo);
        $query = $this->assetDB->delete('tbl_preventivoherramientas');
        return $query;
    }

    function deleteInsumPrev($id_preventivo){
        $this->assetDB->where('prevId', $id_preventivo);
        $query = $this->assetDB->delete('tbl_preventivoinsumos');
        return $query;
    }

	










///////////////////
    function getProductos (){
  	 	$query = $this->assetDB->query("SELECT `herrId`,`herrcodigo`, `herrmarca`, `equip_est` FROM `herramientas`");
     	$i=0;
	    foreach ($query->result() as $row)
	    {
	        $productos[$i]['label'] = $row->herrcodigo;
	        $productos[$i]['value'] = $row->herrmarca;
	        $productos[$i]['id_herr'] = $row->herrId;
	        $i = $i++;
	    }
	    return $productos;
    }

    function getdatos($data = null){
		if($data == null)
		{
			return false;
		}
		else
		{

			$idh = $data['id_herramienta'];

			//Datos del usuario
			$query= $this->assetDB->get_where('herramientas',array('herrId'=>$idh));
			if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }

		}
	}

	function insumo($data = null){
		
		if($data == null)
		{
			return false;
		}
		else
		{

			$id_insumo = $data['artId'];

			//Datos del usuario
			$query= $this->assetDB->get_where('herramientas',array('artId'=>$id_insumo));
			if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }

		}
	}

	/*function traerinsumo($data = null){
		if($data == null)
		{
			return false;
		}
		else
		{
			$id = $data['id_insumo'];
			Datos del usuario
			$query= $this->assetDB->get_where('articles',array('artId'=>$id));
			if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
		}
	}*/



	public function agregar_componente($data){

        $query = $this->assetDB->insert("componentes",$data);
    	return $query;
    }

	public function insert_preventivoherramientas($data2)
    {
        $query = $this->assetDB->insert("tbl_preventivoherramientas",$data2);
        return $query;
    }

    public function insert_preventivoinsumos($data3)
    {
        $query = $this->assetDB->insert("tbl_preventivoinsumos",$data3);
        return $query;
    }

    public function agregar_insumo($data){

        $query = $this->assetDB->insert("articles",$data);
    	return $query;
    }

    public function insert_herramienta($data){
        $query = $this->assetDB->insert("herramientas",$data);
        return $query;
    }

    function get_pedido($id){

		$query= "SELECT *
				 FROM herramientas
				 WHERE id_herramienta=$id";

        $result = $this->assetDB->query($query);
		if($result->num_rows()>0){
            return $result->result_array();
        }
        else{
            return false;
        }

	}

    function geteditar($id){
	    $sql="SELECT preventivo.prevId, preventivo.perido, preventivo.cantidad, preventivo.ultimo, preventivo.critico1, preventivo.estadoprev, preventivo.horash, equipos.id_equipo, equipos.codigo, equipos.marca, equipos.fecha_ingreso, equipos.descripcion, equipos.ubicacion, componentes.id_componente, componentes.descripcion AS comp, tareas.id_tarea, tareas.descripcion AS descripta
	    	  FROM preventivo
	    	  JOIN equipos ON equipos.id_equipo=preventivo.id_equipo
	    	  JOIN tareas ON tareas.id_tarea=preventivo.id_tarea
	    	  JOIN componentes ON componentes.id_componente=preventivo.id_componente
	    	  WHERE prevId=$id
	    	  ";

	    $query= $this->assetDB->query($sql);

	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();
	    }
	    else {
	      return 0;
	    }
	}

	/*function getpreventivoherramientas($id){
	    $sql= "SELECT *
	    		FROM tbl_preventivoherramientas
    			JOIN herramientas ON herramientas.herrId = tbl_preventivoherramientas.herrId
				WHERE tbl_preventivoherramientas.prevId=$id
					";

	    $query= $this->assetDB->query($sql);

	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();
	    }
	    else {
	      return 0;
	    }
	}*/

	/*function getpreventivoinsumos($id){

	    $sql= "SELECT *
	    		from tbl_preventivoinsumos
    			JOIN articles ON articles.artId = tbl_preventivoinsumos.artId
				WHERE tbl_preventivoinsumos.prevId=$id
					";

	    $query= $this->assetDB->query($sql);

	    if( $query->num_rows() > 0)
	    {
	      return $query->result_array();
	    }
	    else {
	      return 0;
	    }
	}*/

	/*public function update_preventivo($data, $idprev){
        $this->assetDB->where('prevId', $idprev);
        $query = $this->assetDB->update("preventivo",$data);
        return $query;
    }

	public function update_editar($data, $idp){
	    $this->assetDB->where('prevId', $idp);
	    $query = $this->assetDB->update("preventivo",$data);
	    return $query;
	}

	public function editar_preventivoherramientas($data, $data4){
	    $this->assetDB->where('herrId', $data4);
	    $query = $this->assetDB->update("tbl_preventivoherramientas",$data);
	    return $query;
	}*/

	public function editar_preventivoinsumos($data, $data5){
	    $this->assetDB->where('artId', $data5);
	    $query = $this->assetDB->update("tbl_preventivoinsumos",$data);
	    return $query;
	}


    /**
     * Trae listado de equipos que tengan mantenimiento preventivo por horas
     *
     * @return  Array   Vuleca la variable o no devuelve nada
     */
    function getPreventivosPorHora()
    {
        $this->assetDB->select('equipos.codigo, equipos.descripcion, equipos.id_equipo, equipos.ultima_lectura, sector.descripcion as descripSector, preventivo.estadoprev, preventivo.prevId, preventivo.cantidad, preventivo.critico1');
        $this->assetDB->from('preventivo');
        $this->assetDB->join('equipos', 'equipos.id_equipo = preventivo.id_equipo', 'inner');
        $this->assetDB->join('sector', 'sector.id_sector = equipos.id_sector', 'inner');
        $this->assetDB->where('preventivo.perido', 'Horas');
        $this->assetDB->where('equipos.estado', 'AC');

        $query= $this->assetDB->get();

        if ($query->num_rows() > 0)
        {
            $preventivos  = $query->result_array();
            $data['data'] = $this->revisaEstadoPreventivosPorHoras( $preventivos );
            //$data['data'] = $query->result_array();

            return  $data;
        }
        else
        {
            return false;
        }
    }

    // bucle que recorra preventivos
    //      con id_equipo traigo historial_lecturas ->ultima lectura
    //      hago cuenta
    //      si es necesario llamo funcion que cambia estado de preventivo
    //      cambio $preventivos[estadoprev]
    // cierro bucle
    // devuelvo $preventivos
    function revisaEstadoPreventivosPorHoras( $preventivos )
    {
        $cantPreventivos = sizeof( $preventivos );
        for ($i=0; $i<$cantPreventivos; $i++)
        {
            $lecturaActual = $this->getLecturaActual( $preventivos[$i]['id_equipo'] );
            //dump( $lecturaActual, 'Lectura Actual' );
            //dump( $preventivos[$i]['ultima_lectura'], 'Ultima lectura' );
            //dump( $preventivos[$i]['cantidad'], 'cantidad' );
            //dump( $preventivos[$i]['critico1'], 'critico' );


            //1er caso: lecturaactual - ultimalectura >= cantidad  => estado vencido
            if (($lecturaActual - $preventivos[$i]['ultima_lectura']) >= $preventivos[$i]['cantidad'])
            {
                if ($preventivos[$i]['estadoprev'] != 'VE')
                {
                    $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'VE' );
                    $preventivos[$i]['estadoprev'] = 'VE';
                }
            }

            //2do caso: lecturaactual - ultimalectura < cantidad  => estado en curso
            if (($lecturaActual - $preventivos[$i]['ultima_lectura']) < $preventivos[$i]['cantidad'])
            {
                //3er caso: > cantidad => estado critico
                if (($lecturaActual - $preventivos[$i]['ultima_lectura']) > $preventivos[$i]['cantidad'])
                {
                    if ($preventivos[$i]['estadoprev'] != 'CR')
                    {
                        $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'CR' );
                        $preventivos[$i]['estadoprev'] = 'CR';
                    }
                }
                else // si no es critico => esta en curso
                {
                    if ($preventivos[$i]['estadoprev'] != 'C')
                    {
                        $this->cambiaEstadoPreventivo( $preventivos[$i]['prevId'], 'C' );
                        $preventivos[$i]['estadoprev'] = 'C';
                    }
                }
            }
        }
        return $preventivos;
    }

    /**
     * Devuelve la ultima lectura de un equipo determinado
     *
     * @param   String  $id_equipo  Equipo que se quiere saber la ultima lectura
     * @return  Int     Última lectura
     */
    function getLecturaActual( $id_equipo )
    {
        $this->assetDB->select('lectura');
        $this->assetDB->from('historial_lecturas');
        $this->assetDB->where('id_equipo', $id_equipo);
        $this->assetDB->order_by('id_lectura', 'desc');
        $this->assetDB->limit(1);

        $query= $this->assetDB->get();

        if ($query->num_rows() > 0)
        {
            $data  = $query->result_array();
            return  (int)$data[0]['lectura'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Cambia el campo Estado de la tabla preventivo
     *
     * @param   String  $idPreventivo   Id del preventivo a modificar
     * @param   String  $estado         Valor del nuevo estado
     * @return  bool                    Cambio correcto o incorrecto
     */
    function cambiaEstadoPreventivo( $idPreventivo, $estado )
    {
        $this->assetDB->trans_start();   // inicio transaccion

            $data = array(
                   'estadoprev' => $estado
                );
            $this->assetDB->where('prevId', $idPreventivo);
            $this->assetDB->update('preventivo', $data);

        $this->assetDB->trans_complete(); //fin transaccion

        if ($this->assetDB->trans_status() === FALSE)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function getpreventivos($idpe,$ideq){

		$sql="SELECT *
	    	  FROM preventivo
	    	  
	    	  WHERE prevId=$idpe AND id_equipo=$ideq AND estadoprev='C'
	    	  ";
	    
	    $query= $this->assetDB->query($sql);

		if($query->num_rows()>0){
		    return $query->result();
		}
		else{
		    return false;
		    }	

	}
	  	function insert_preventivoorden($data)
    {
        $query = $this->assetDB->insert("orden_trabajo",$data);
        return $query;
    }

    /**
     * Preventivos:eliminarAdjunto
     * Elimina el Archivo Adjunto de un preventivo dado (no elimina el archivo).
     *
     * @param Int       $idPreventivo   Id de preventivo
     * @return Bool                     True o False
     */
    public function eliminarAdjunto($idPreventivo)
    {
        $data  = array( 'prev_adjunto' => '' );
        $this->assetDB->where('prevId', $idPreventivo);
        $query = $this->assetDB->update("preventivo", $data);
        return $query;
    }
    
}
