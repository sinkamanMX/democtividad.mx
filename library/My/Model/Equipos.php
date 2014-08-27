<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Equipos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'AVL_EQUIPOS';
	protected $_primary = 'ID_EQUIPO';
	
	
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

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT
				E.ID_EQUIPO,
				A.NOMBRE AS MARCA,
				M.NOMBRE AS MODELO,
				E.DESCRIPCION,
				E.IMEI,
				E.IP,
				E.ID_MODELO,
				A.ID_MARCA,
				E.ID_SERVIDOR,
				E.PUERTO,
				IF(R.ID_EQUIPO IS NOT NULL,CONCAT(C.DESCRIPCION,'-',C.IDENTIFICADOR1),'0') AS ASIGNADO,
				R.ID_ACTIVO
				FROM AVL_EQUIPOS E
				INNER JOIN AVL_MODELO_EQUIPOS M ON E.ID_MODELO = M.ID_MODELO
				INNER JOIN AVL_MARCA_EQUIPOS  A ON M.ID_MARCA   = A.ID_MARCA
				 LEFT JOIN AVL_EQUIPO_ACTIVO  R ON E.ID_EQUIPO  = R.ID_EQUIPO
				 LEFT JOIN AVL_ACTIVO         C ON R.ID_ACTIVO  = C.ID_ACTIVO
                WHERE E.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name			 
					SET ID_MODELO 	=  ".$data['inputModelo'].",
					ID_SERVIDOR		=  ".$data['inputServidor'].",
					DESCRIPCION		=  '".$data['inputDesc']."',
					IMEI			=  '".$data['inputImei']."',
					IP				=  '".$data['inputIp']."',
					PUERTO			=  '".$data['inputPuerto']."',
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
    
    public function updateRow($data){
       $result     = Array();
        $result['status']  = false;

        $sql="UPDATE $this->_name			 
					SET ID_MODELO 	=  ".$data['inputModelo'].",
					ID_SERVIDOR		=  ".$data['inputServidor'].",
					DESCRIPCION		=  '".$data['inputDesc']."',
					IMEI			=  '".$data['inputImei']."',
					IP				=  '".$data['inputIp']."',
					PUERTO			=  '".$data['inputPuerto']."'
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

    public function deleteRow($data){
        $result     = Array();
        $result['status']  = false;
        
		$sqlDel  	= "DELETE FROM AVL_EQUIPO_ACTIVO WHERE ID_EQUIPO = ".$data['catId'];
	    $queryDel   = $this->query($sqlDel,false);        

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
    
    public function deleteRelAction($data){
    	try{    	
       		$result     = Array();
        	$result['status']  = false;
        
			$sql  	= "DELETE FROM AVL_EQUIPO_ACTIVO WHERE ID_EQUIPO = ".$data['catId'];
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
    
    public function validateData($dataSearch,$idObject,$optionSearch){
		$result=true;		
		$this->query("SET NAMES utf8",false);
		$filter = ($optionSearch=='imei') ? ' IMEI = "'.$dataSearch.'"': ' IP = "'.$dataSearch.'"';
    	$sql ="SELECT $this->_primary
	    		FROM $this->_name
				WHERE ID_EQUIPO <> $idObject
                 AND  $filter";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = false;
		}
        
		return $result;		    	
    }
    
    public function setActivo($idObject,$idActivo){
        $result  = false;
        try{   
	        $sqlDel  = "DELETE FROM AVL_EQUIPO_ACTIVO WHERE ID_EQUIPO = $idObject";
	        $queryDel   = $this->query($sqlDel,false);
	        
	        $sql="INSERT INTO AVL_EQUIPO_ACTIVO		 
						SET ID_EQUIPO 	=  $idObject,
						ID_ACTIVO		=  $idActivo";
         
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
    
	public function getCbo($idObject,$idEmpresa){
		/*$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_UNIDAD AS ID, CONCAT(IDENTIFICADOR,'-',PLACAS) AS NAME
				FROM $this->_name
				WHERE ID_TRANSPORTISTA = $idObject
				  AND ID_EMPRESA  	   = $idEmpresa  
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	*/		
	}   

	public function getEventosHd($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_EVENTO_HW AS ID, DESCRIPCION AS NAME
					FROM AVL_EVENTOS_HW 
					WHERE ID_EVENTO_HW NOT IN 
					(
						SELECT E.ID_EVENTO_HW 
						FROM AVL_EVENTOS_EQUIPO E
						INNER JOIN AVL_EVENTOS_HW W ON E.ID_EVENTO_HW  = W.ID_EVENTO_HW
						WHERE W.ID_MODELO = $idObject
					)
					ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}
	
	public function getEventosSw($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_EVENTO AS ID, DESCRIPCION AS NAME
				FROM AVL_EVENTOS_SW 
				WHERE ID_EVENTO NOT IN 
				(
					SELECT E.ID_EVENTO 
					FROM AVL_EVENTOS_EQUIPO E
					INNER JOIN AVL_EVENTOS_SW W ON E.ID_EVENTO  = W.ID_EVENTO
					WHERE E.ID_EQUIPO = $idObject
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
    	$sql ="SELECT E.ID_EVENTO_HW, E.ID_EVENTO, W.DESCRIPCION AS EVENTO_W, S.DESCRIPCION AS EVENTO_S, E.ID_EVENTO_EQUIPO
				FROM AVL_EVENTOS_EQUIPO E
				INNER JOIN AVL_EVENTOS_HW W ON E.ID_EVENTO_HW = W.ID_EVENTO_HW
				INNER JOIN AVL_EVENTOS_SW S ON E.ID_EVENTO    = S.ID_EVENTO
				WHERE E.ID_EQUIPO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;					
	}
	
	public function setRelEventos($data){
        $result     = Array();
        $result['status']  = false;
        $sql="INSERT INTO AVL_EVENTOS_EQUIPO		 
					SET ID_EQUIPO 	=  ".$data['catId'].",
					ID_EVENTO		=  ".$data['inputEventSw'].",
					ID_EVENTO_HW	=  ".$data['inputEventHd'];
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
	
	public function deleteRelEvent($idRel){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  AVL_EVENTOS_EQUIPO
					 WHERE ID_EVENTO_EQUIPO = ".$idRel."  LIMIT 1";
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