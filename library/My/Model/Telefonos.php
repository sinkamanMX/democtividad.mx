<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Telefonos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_TELEFONOS';
	protected $_primary = 'ID_TELEFONO';
	
	public function getDataTables($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.ID_TELEFONO,
					A.DESCRIPCION AS MARCA,
					M.DESCRIPCION AS MODELO,
					E.DESCRIPCION,
					E.TELEFONO,
					E.IDENTIFICADOR
				FROM PROD_TELEFONOS E
				INNER JOIN PROD_MODELO_TELEFONO M ON E.ID_MODELO = M.ID_MODELO
				INNER JOIN PROD_MARCA_TELEFONO  A ON M.ID_MARCA   = A.ID_MARCA
				WHERE E.ID_EMPRESA = $idEmpresa
				ORDER BY E.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}  	
	
	public function getReporte($data){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT P.ID_TELEFONO, P.FECHA_TELEFONO, P.TIPO_GPS, P.LATITUD, P.LONGITUD,P.VELOCIDAD, P.NIVEL_BATERIA,P.UBICACION, E.DESCRIPCION_EVENTO AS EVENTO
				FROM PROD_HISTORICO_POSICION P
				INNER JOIN PROD_EVENTOS E ON P.ID_EVENTO = E.ID_EVENTO
				WHERE P.ID_TELEFONO = ".$data['strInput']."
				 AND  P.FECHA_TELEFONO BETWEEN '".$data['inputFechaIn']."'
				 						   AND '".$data['inputFechaFin']."'
				 ORDER BY P.FECHA_TELEFONO ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	

	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.DESCRIPCION, T.IDENTIFICADOR AS IMEI, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS ASIGNADO, M.DESCRIPCION AS MODELO, P.DESCRIPCION AS MARCA
			FROM PROD_TELEFONOS T
			INNER JOIN PROD_USR_TELEFONO R ON T.ID_TELEFONO = R.ID_TELEFONO
			INNER JOIN USUARIOS          U ON R.ID_USUARIO  = U.ID_USUARIO
			INNER JOIN PROD_MODELO_TELEFONO M ON T.ID_MODELO = M.ID_MODELO
			INNER JOIN PROD_MARCA_TELEFONO  P ON M.ID_MARCA  = P.ID_MARCA
			WHERE T.ID_TELEFONO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	
	
	public function getDataRow($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.ID_TELEFONO,
					   	T.ID_MODELO,
					   	T.DESCRIPCION,
					   	T.TELEFONO,
					   	T.IDENTIFICADOR,
					   	T.ACTIVO,
					   	IF(R.ID_TELEFONO IS NOT NULL,CONCAT(U.NOMBRE,' ',U.APELLIDOS),'0') AS ASIGNADO,
					   	T.ID_MODELO,
					   	M.ID_MARCA
				FROM PROD_TELEFONOS T
				INNER JOIN PROD_MODELO_TELEFONO M ON T.ID_MODELO = M.ID_MODELO
				INNER JOIN PROD_MARCA_TELEFONO  L ON M.ID_MARCA  = L.ID_MARCA
				LEFT JOIN PROD_USR_TELEFONO R ON T.ID_TELEFONO = R.ID_TELEFONO
				LEFT JOIN USUARIOS          U ON R.ID_USUARIO  = U.ID_USUARIO
				WHERE T.ID_TELEFONO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	

	
	public function getEventos($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_EVENTO AS ID, DESCRIPCION_EVENTO AS NAME
				FROM PROD_EVENTOS
				WHERE ID_EVENTO NOT IN
				(
				SELECT ID_EVENTO
				FROM PROD_EVENTO_TELEFONO
				WHERE ID_TELEFONO = $idObject
				)
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}
	
	public function getRelEventos($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.ID_EVENTO_TELEFONO AS ID, E.DESCRIPCION_EVENTO AS EVENTO
				FROM PROD_EVENTO_TELEFONO T
				INNER JOIN  PROD_EVENTOS E ON T.ID_EVENTO = E.ID_EVENTO
				WHERE T.ID_TELEFONO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}
	
	public function getDataNoAssign($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.USUARIO, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME, U.ID_USUARIO
					FROM USUARIOS U
					INNER JOIN USR_EMPRESA E ON U.ID_USUARIO  = E.ID_USUARIO
					WHERE U.FLAG_OPERACIONES = 1
					 AND U.ID_PERFIL         = 4
					 AND U.ID_USUARIO NOT IN
					 (
					 SELECT U.ID_USUARIO
					 FROM PROD_USR_TELEFONO T
					 INNER JOIN USUARIOS    U ON T.ID_USUARIO  = U.ID_USUARIO
					 INNER JOIN USR_EMPRESA E ON U.ID_USUARIO  = E.ID_USUARIO
					 INNER JOIN SUCURSALES  L ON E.ID_SUCURSAL = L.ID_SUCURSAL
					 INNER JOIN EMPRESAS    S ON L.ID_EMPRESA  = S.ID_EMPRESA
					WHERE S.ID_EMPRESA = $idEmpresa
					 )
					 ORDER BY NAME ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}

    public function validateData($dataSearch,$idObject,$optionSearch){
		$result=true;		
		$this->query("SET NAMES utf8",false);
		$filter = ($optionSearch=='imei') ? ' IDENTIFICADOR = "'.$dataSearch.'"': ' TELEFONO = "'.$dataSearch.'"';
    	$sql ="SELECT $this->_primary
	    		FROM $this->_name
				WHERE ID_TELEFONO <> $idObject
                 AND  $filter";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = false;
		}
        
		return $result;		    	
    }	
    
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET ID_EMPRESA		=  ".$data['inputEmpresa'].",
					ID_GRUPO		=  1,		
					ID_MODELO		=  ".$data['inputModelo'].",
					DESCRIPCION		=  '".$data['inputDesc']."',
					TELEFONO		=  '".$data['inputTel']."',	
					IDENTIFICADOR	=  '".$data['inputImei']."',
					ACTIVO			=  '".$data['inputEstatus']."',
					ID_USUARIO_ALTA =  ".$data['inputUser'].",
					FECHA_ALTA 		=  CURRENT_TIMESTAMP";
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
    
    public function updateRow($data){
       $result     = Array();
        $result['status']  = false;

        $sql="UPDATE $this->_name
				SET ID_MODELO		=   ".$data['inputModelo'].",
					DESCRIPCION		=  '".$data['inputDesc']."',
					TELEFONO		=  '".$data['inputTel']."',	
					IDENTIFICADOR	=  '".$data['inputImei']."',
					ACTIVO			=  '".$data['inputEstatus']."',
					ID_USUARIO_ACTUAL=  ".$data['inputUser']." 
			WHERE $this->_primary   = ".$data['catId']." LIMIT 1";
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

    public function deleteRow($data){
        $result     = Array();
        $result['status']  = false;  
        
		$sqlDel  = "DELETE FROM PROD_USR_TELEFONO WHERE ID_TELEFONO = ".$data['catId']."  LIMIT 1";
	    $queryDel   = $this->query($sqlDel,false);    

		$sqlDel2  = "DELETE FROM PROD_EVENTO_TELEFONO WHERE ID_TELEFONO = ".$data['catId']."  LIMIT 1";
	    $queryDel2   = $this->query($sqlDel2,false);   	    

        $sql="DELETE FROM  $this->_name
					 WHERE $this->_primary = ".$data['catId']."  LIMIT 1";
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

    public function setUser($idObject,$idUsuario){
        $result  = false;
        try{   
	        $sqlDel  = "DELETE FROM PROD_USR_TELEFONO WHERE ID_TELEFONO = $idObject";
	        $queryDel   = $this->query($sqlDel,false);
	        
	        $sql="INSERT INTO PROD_USR_TELEFONO		 
						SET ID_TELEFONO =  $idObject,
						ID_USUARIO		=  $idUsuario";
         
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	    	
    }  

    public function deleteRelAction($data){
    	try{    	
       		$result     = Array();
        	$result['status']  = false;
        
			$sql  	= "DELETE FROM PROD_USR_TELEFONO WHERE ID_TELEFONO = ".$data['catId'];
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

	public function setRelEventos($data){
        $result     = Array();
        $result['status']  = false;
        $sql="INSERT INTO PROD_EVENTO_TELEFONO		 
					SET ID_TELEFONO 	=  ".$data['catId'].",
						ID_EVENTO		=  ".$data['inputEvento'];
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
	
	public function setAllEventos($data){
        $result     = Array();
        $result['status']  = false;        
		$sql = "INSERT INTO PROD_EVENTO_TELEFONO (ID_EVENTO,ID_TELEFONO)
				(
					SELECT ID_EVENTO, ".$data['catId']." 
					FROM PROD_EVENTOS
					WHERE ID_EVENTO NOT IN 
					(
						SELECT ID_EVENTO
						FROM PROD_EVENTO_TELEFONO
						WHERE ID_TELEFONO = ".$data['catId']."
					)
				)";      
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
	
	public function deleteRelEvent($idRel){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  PROD_EVENTO_TELEFONO
					 WHERE ID_EVENTO_TELEFONO = ".$idRel."  LIMIT 1";
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
}