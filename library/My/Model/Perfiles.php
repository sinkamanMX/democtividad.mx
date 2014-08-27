<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Perfiles extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PERFILES';
	protected $_primary = 'ID_PERFIL';
	
	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
	
	public function getModuleDefault($idProfile){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				WHERE MP.ID_PERFIL = $idProfile
				 AND  MP.INICIO    = 1 AND M.ACTIVO = 1 LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	  		
	}   

	public function getModules($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT M.*, M.DESCRIPCION AS M_DESCRIPCION,N.DESCRIPCION AS N_DESCRIPCION,N.ID_MENU AS IDMENU, N.*, M.SCRIPT AS S_MODULE
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				INNER JOIN MENU    N ON M.ID_MENU    = N.ID_MENU 
				WHERE MP.ID_PERFIL = ".$idObject." AND M.ACTIVO = 1
				ORDER BY N.ID_MENU ASC, M.DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getDataModule($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MODULOS
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
	
	public function getDataMenu($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MENU
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}	
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT   ID_PERFIL,
						DESCRIPCION						
				FROM PERFILES
				ORDER BY DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	        
		return $result;			
	} 	
	

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
				FROM PERFILES
				WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	
    	
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;

        $agregar  = (isset($data['inputAgregar']) && $data['inputAgregar']=='on') ? "1": "0";
        $editar   = (isset($data['inputEditar'])  && $data['inputEditar']=='on')  ? "1": "0";
        $borrar	  = (isset($data['inputBorrar'])  && $data['inputBorrar']=='on')  ? "1": "0";
        $lectura  = (isset($data['inputLeer'])    && $data['inputLeer']=='on')    ? "1": "0";

        Zend_Debug::dump($data);
        $sql="INSERT INTO $this->_name	
        			SET DESCRIPCION	= '".$data['inputDescripcion']."',
						ACTIVO		= ".$data['inputEstatus'].",
						INSERTAR 	= ".$agregar.",
						EDITAR 		= ".$editar.",
						ELIMINAR 	= ".$borrar.",
						LECTURA  	= ".$lectura;
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

        $agregar  = (isset($data['inputAgregar']) && $data['inputAgregar']=='on') ? "1": "0";
        $editar   = (isset($data['inputEditar'])  && $data['inputEditar']=='on')  ? "1": "0";
        $borrar	  = (isset($data['inputBorrar'])  && $data['inputBorrar']=='on')  ? "1": "0";
        $lectura  = (isset($data['inputLeer'])    && $data['inputLeer']=='on')    ? "1": "0";
        
        $sql="UPDATE $this->_name	
        			SET DESCRIPCION	= '".$data['inputDescripcion']."',
						ACTIVO		= ".$data['inputEstatus'].",
						INSERTAR 	= ".$agregar.",
						EDITAR 		= ".$editar.",
						ELIMINAR 	= ".$borrar.",
						LECTURA  	= ".$lectura."
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
		$result = false;    	
    	try{    	
			$sql  	= "DELETE FROM $this->_name	 WHERE $this->_primary = ".$data['catId']." LIMIT 1";
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
}