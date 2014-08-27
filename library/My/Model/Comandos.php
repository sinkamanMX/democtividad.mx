<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Comandos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'AVL_COMANDOS';
	protected $_primary = 'ID_COMANDO';
	
	public function getComandos($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_COMANDO,
				       A.DESCRIPCION,
				       A.PAQUETE
				FROM AVL_COMANDOS A
				  INNER JOIN AVL_COMANDOS_EQUIPOS B ON A.ID_COMANDO = B.ID_COMANDO
				WHERE B.ID_EQUIPO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	

	public function getComandoById($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM AVL_COMANDOS 
				WHERE ID_COMANDO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}		
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT AVL_COMANDOS_ENVIADOS
				SET	ID_EQUIPO	= ".$data['strInput'].",
	        		ID_USUARIO	= ".$data['userRegister'].",
	        		COMANDO		= '".$data['sComando']."',
	        		CREADO 		= CURRENT_TIMESTAMP";
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