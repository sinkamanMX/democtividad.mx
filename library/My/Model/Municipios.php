<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Municipios extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'ZZ_SPM_MUNICIPIOS';
	protected $_primary = 'ID_MUNICIPIO';
		
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE AS NAME 
    			FROM $this->_name 
    			WHERE ID_ESTADO = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
    public function getData($idObject,$idEstado){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject 
                AND   ID_ESTADO = $idEstado LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }		
}