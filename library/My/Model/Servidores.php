<?php
/**
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Servidores extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'AVL_SERVIDORES';
	protected $_primary = 'ID_SERVIDOR';
	
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
}