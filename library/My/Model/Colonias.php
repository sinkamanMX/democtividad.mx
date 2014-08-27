<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Colonias extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'ZZ_SPM_COLONIAS';
	protected $_primary = 'ID_COLONIA';
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE AS NAME 
    			FROM $this->_name 
    			WHERE ID_MUNICIPIO = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCP($idObject,$idMun){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT CODIGO
    			FROM $this->_name 
    			WHERE ID_MUNICIPIO = $idMun 
    			  AND ID_COLONIA   = $idObject LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;				
	}
	
    public function getData($idObject,$idMunicipio){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject 
                AND   ID_MUNICIPIO    = $idMunicipio LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	
    
    public function validateCP($cpSearch){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
                FROM $this->_name
                WHERE CODIGO = '$cpSearch' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];
		}	
        
		return $result;	     	
    }
    
    public function getDataByCp($cpSearch){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE AS NAME 
    			FROM $this->_name 
    			WHERE CODIGO = '$cpSearch' ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;
    }    
}