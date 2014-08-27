<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Activos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'AVL_ACTIVO';
	protected $_primary = 'ID_ACTIVO';
	
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT
				E.ID_EQUIPO,
				A.NOMBRE AS MARCA,
				M.NOMBRE AS MODELO,
				E.DESCRIPCION,
				E.IMEI,
				E.IP
				FROM AVL_EQUIPOS E
				INNER JOIN AVL_MODELO_EQUIPOS M ON E.ID_MODELO = M.ID_MODELO
				INNER JOIN AVL_MARCA_EQUIPOS A ON M.ID_MARCA   = A.ID_MARCA
				ORDER BY E.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}

	public function getDataNoAssign(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_ACTIVO,
					M.DESCRIPCION AS MODELO,
					C.DESCRIPCION AS MARCA,
					A.DESCRIPCION,
					A.IDENTIFICADOR1 AS PLACAS,
					SERIE1  AS SERIE
					FROM AVL_ACTIVO A
					INNER JOIN AVL_MODELO_ACTIVO M ON A.ID_MODELO = M.ID_MODELO
					INNER JOIN AVL_MARCA_ACTIVO  C ON M.ID_MARCA  = C.ID_MARCA
					INNER JOIN AVL_TIPO_ACTIVO   T ON A.ID_TIPO   = T.ID_TIPO
					WHERE ID_ACTIVO NOT IN(
						SELECT ID_ACTIVO 
						FROM AVL_EQUIPO_ACTIVO	
					)
				ORDER BY A.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}
	
	public function getTableSearch($data){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$filter = '';
		if(isset($data['inputSearch']) && $data['inputSearch']!=""){
			$filter = "WHERE A.IDENTIFICADOR1  LIKE '%".$data['inputSearch']."%' 
						   OR M.DESCRIPCION    LIKE '%".$data['inputSearch']."%'
						   OR E.IMEI		   LIKE '%".$data['inputSearch']."%'
						   OR E.IP		       LIKE '%".$data['inputSearch']."%'";
		}else{
			$filter = "ORDER BY A.ID_ACTIVO DESC LIMIT 10";
		}
		
    	$sql ="SELECT  A.DESCRIPCION    AS CLIENTE, 
					   A.IDENTIFICADOR1 AS PLACAS,
					   M.DESCRIPCION    AS MODELO,
					   E.IMEI,
					   E.IP,
					   IF(P.FECHA_GPS IS NULL,'Sin Reporte',P.FECHA_GPS) AS U_REPORTE,
					   IF(P.UBICACION IS NULL,'Sin Reporte',P.FECHA_GPS) AS DIRECCION,
					   A.ID_ACTIVO   
				FROM AVL_ACTIVO A
				INNER JOIN AVL_MODELO_ACTIVO   M ON A.ID_MODELO  = M.ID_MODELO
				 LEFT JOIN AVL_ULTIMA_POSICION P ON A.ID_ACTIVO  = P.ID_ACTIVO
				INNER JOIN AVL_EQUIPO_ACTIVO   R ON A.ID_ACTIVO  = R.ID_ACTIVO
				INNER JOIN AVL_EQUIPOS         E ON R.ID_EQUIPO  = E.ID_EQUIPO ".$filter;    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getAllData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT  A.DESCRIPCION    AS CLIENTE, 
					   A.IDENTIFICADOR1 AS PLACAS,
					   M.DESCRIPCION    AS MODELO,
					   V.DESCRIPCION    AS MARCA,
					   E.IMEI,
					   E.IP,
					   A.ID_ACTIVO,
					   C.TELEFONO_FIJO,
					   C.TELEFONO_MOVIL,
					   CONCAT(D.CALLE,' #',D.NUMERO_EXT,', Col.',D.COLONIA,', ',D.MUNICIPIO,', ',D.ESTADO) AS DIRECCION,
					   L.DESCRIPCION AS COLOR,
					   A.SERIE1 AS SERIE,
					   R.ID_EQUIPO
				FROM AVL_ACTIVO A
				INNER JOIN AVL_MODELO_ACTIVO   M ON A.ID_MODELO  = M.ID_MODELO
				INNER JOIN AVL_MARCA_ACTIVO    V ON M.ID_MARCA   = V.ID_MARCA
				INNER JOIN AVL_COLORES         L ON A.ID_COLOR   = L.ID_COLOR
				INNER JOIN AVL_EQUIPO_ACTIVO   R ON A.ID_ACTIVO  = R.ID_ACTIVO
				INNER JOIN AVL_EQUIPOS         E ON R.ID_EQUIPO  = E.ID_EQUIPO 
				INNER JOIN AVL_CLIENTES_ACTIVO T ON A.ID_ACTIVO  = T.ID_ACTIVO
				INNER JOIN PROD_CLIENTES       C ON T.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_DOMICILIOS_CLIENTE D ON C.ID_CLIENTE = D.ID_CLIENTE			
				WHERE A.ID_ACTIVO = $idObject LIMIT 1";   	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	
	
	public function getLasPosition($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);
	    	$sql ="SELECT P.ID_ACTIVO,
							P.LATITUD,
							P.LONGITUD,
							P.ANGULO,
							P.VELOCIDAD,
							P.BATERIA,
							P.UBICACION,
							B.DESCRIPCION AS EVENTO,
							P.FECHA_GPS
							FROM AVL_ULTIMA_POSICION P
							INNER JOIN AVL_EVENTOS_SW B ON P.ID_EVENTO = B.ID_EVENTO
							WHERE P.ID_ACTIVO = $idObject LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	

	public function getHistoryByDay($idObject,$today=true){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$filter = ($today) ? 'AND CAST(FECHA_GPS AS DATE) = CURRENT_DATE': 'AND CAST(FECHA_GPS AS DATE) = DATE_SUB(CURDATE(),INTERVAL 1 DAY)';
    	$sql ="SELECT P.ID_ACTIVO,
						P.LATITUD,
						P.LONGITUD,
						P.ANGULO,
						P.VELOCIDAD,
						P.BATERIA,
						P.UBICACION,
						B.DESCRIPCION AS EVENTO,
						P.FECHA_GPS,
						P.ID_POSICION
						FROM AVL_HISTORICO P
						INNER JOIN AVL_EVENTOS_SW B ON P.ID_EVENTO = B.ID_EVENTO
						WHERE P.ID_ACTIVO = $idObject 
						$filter";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getActiveByPlaque($data){
		$result= Array();
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT  A.ID_ACTIVO,
					   E.ID_EQUIPO   
				FROM AVL_ACTIVO A
				INNER JOIN AVL_MODELO_ACTIVO   M ON A.ID_MODELO  = M.ID_MODELO
				INNER JOIN AVL_EQUIPO_ACTIVO   R ON A.ID_ACTIVO  = R.ID_ACTIVO
				INNER JOIN AVL_EQUIPOS         E ON R.ID_EQUIPO  = E.ID_EQUIPO 
				WHERE A.IDENTIFICADOR1 = '$data' LIMIT 1";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	
	public function getAdminTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT   A.ID_ACTIVO,
						M.DESCRIPCION AS MODELO,
						R.DESCRIPCION AS MARCA,
						C.DESCRIPCION AS COLOR,
						CONCAT (E.NOMBRE,' ',E.APELLIDOS) AS U_REGISTRO,
						CONCAT (I.NOMBRE,' ',I.APELLIDOS)  AS U_INSTALO,
						A.DESCRIPCION,
						A.IDENTIFICADOR1 AS PLACAS,
						A.SERIE1 AS MOTOR,
						R.ID_MARCA,
						M.ID_MODELO,
						A.ID_COLOR
				FROM AVL_ACTIVO A
						INNER JOIN AVL_MODELO_ACTIVO M ON A.ID_MODELO = M.ID_MODELO
						INNER JOIN AVL_MARCA_ACTIVO  R ON M.ID_MARCA  = R.ID_MARCA
						INNER JOIN AVL_COLORES       C ON A.ID_COLOR  = C.ID_COLOR
						INNER JOIN USUARIOS          E ON A.ID_USR_REGISTRO = E.ID_USUARIO
						 LEFT JOIN USUARIOS          I ON A.ID_USR_INSTALO  = I.ID_USUARIO 
						ORDER BY A.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getAdminData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT   A.ID_ACTIVO,
						M.DESCRIPCION AS MODELO,
						R.DESCRIPCION AS MARCA,
						C.DESCRIPCION AS COLOR,
						CONCAT (E.NOMBRE,' ',E.APELLIDOS) AS U_REGISTRO,
						CONCAT (I.NOMBRE,' ',I.APELLIDOS)  AS U_INSTALO,
						A.DESCRIPCION,
						A.IDENTIFICADOR1 AS PLACAS,
						A.SERIE1 AS MOTOR,
						R.ID_MARCA,
						M.ID_MODELO,
						A.ID_COLOR
				FROM AVL_ACTIVO A
						INNER JOIN AVL_MODELO_ACTIVO M ON A.ID_MODELO = M.ID_MODELO
						INNER JOIN AVL_MARCA_ACTIVO  R ON M.ID_MARCA  = R.ID_MARCA
						INNER JOIN AVL_COLORES       C ON A.ID_COLOR  = C.ID_COLOR
						INNER JOIN USUARIOS          E ON A.ID_USR_REGISTRO = E.ID_USUARIO
						LEFT  JOIN USUARIOS          I ON A.ID_USR_INSTALO  = I.ID_USUARIO 
				WHERE A.ID_ACTIVO = $idObject LIMIT 1";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	
    public function updateRow($data){
       $result     = Array();
        $result['status']  = false;
        $sql="UPDATE $this->_name
				SET ID_MODELO		= ".$data['inputModelo'].",
					ID_COLOR		= ".$data['inputColor'].",
					ID_TIPO			= 1,
					DESCRIPCION		= '".$data['inputDesc']."',
					IDENTIFICADOR1	= '".$data['inputPlacas']."',
					SERIE1			= '".$data['inputMotor']."',	
					CREADO        	= CURRENT_TIMESTAMP
				WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }  	
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT $this->_name
				SET ID_MODELO		= ".$data['inputModelo'].",
					ID_COLOR		= ".$data['inputColor'].",
					ID_TIPO			= 1,
					ID_USR_REGISTRO = ".$data['userRegister'].",
					DESCRIPCION		= '".$data['inputDesc']."',
					IDENTIFICADOR1	= '".$data['inputPlacas']."',
					SERIE1			= '".$data['inputMotor']."',	
					CREADO			=  CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }    
}