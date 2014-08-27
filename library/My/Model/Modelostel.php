<?php
/**
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Modelostel extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_MODELO_TELEFONO';
	protected $_primary = 'ID_MODELO';
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			WHERE ID_MARCA = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
}