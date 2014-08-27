<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Horarios extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_HORARIO';
	protected $_primary = 'ID_HORARIO';
	
	public function getHorarios($aSucursales,$fecha){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_HORARIO,CONCAT(HORA,'-',HORA_FIN ) AS HORARIOS, HORA, HORA_FIN
			 	FROM PROD_HORARIO 
			 	WHERE ID_SUCURSAL IN ($aSucursales)";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			foreach($query AS $key => $items){
				
				$assign 	= $this->getAsignados($items['ID_HORARIO'], $fecha);
				$disponibles= $this->getDisponibles($items['ID_HORARIO'], $fecha);
				
				$items['DISPONIBLES']	= (isset($disponibles['DISPONIBLES']) && $disponibles['DISPONIBLES']!="") ? $disponibles['DISPONIBLES'] : 0;
				$items['ASINGADOS']		= (isset($assign['ASIGNADOS'])   && $assign['ASIGNADOS']!="") ? $assign['ASIGNADOS'] : 0;
				$result[] = $items;		
			}			
		}	
		return $result;	
	}
	
	public function getDisponibles($idHorario,$fecha){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT COUNT(U.ID_USUARIO) AS DISPONIBLES
				FROM PROD_HORARIO_USUARIO U
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE U.ID_USUARIO NOT IN
				(
					SELECT ID_USUARIO
					FROM PROD_HORARIO_ASIGNADO
					WHERE ID_HORARIO = $idHorario 
					  AND DIA  		 = '$fecha'
				)
				AND U.ID_HORARIO = $idHorario  LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}
	
	public function getAsignados($idHorario,$fecha){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT COUNT(ID_ASIGNACION) AS ASIGNADOS
				FROM PROD_HORARIO_ASIGNADO
				WHERE ID_HORARIO = $idHorario 
				  AND DIA = '$fecha' LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}
	
    public function getUserAssign($fecha,$idHorario){
		$result= -1;
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT U.ID_USUARIO
				 FROM PROD_HORARIO_USUARIO U
				 INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				 WHERE U.ID_USUARIO NOT IN 
				 (
					 SELECT A.ID_USUARIO
					FROM PROD_HORARIO H
					INNER JOIN PROD_HORARIO_ASIGNADO A ON H.ID_HORARIO = A.ID_HORARIO
					WHERE A.DIA = '$fecha'
					  AND H.ID_HORARIO =  $idHorario
				 )
				 ORDER BY U.ID_USUARIO ASC LIMIT 1";	
    	Zend_Debug::dump($sql);
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['ID_USUARIO'];			
		}	
        
		return $result;	     	
    }	

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
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
        
        $sql=" INSERT INTO PROD_HORARIO_ASIGNADO
				 SET ID_USUARIO	= ".$data['uAssign'].",
				 ID_HORARIO		= ".$data['inputhorario'].",
				 DIA			= '".$data['inputDate']."'";
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
    
	public function getHorariosByUsers($idUser,$fecha){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT R.ID_HORARIO AS ID,CONCAT(HORA,'-',HORA_FIN ) AS NAME
				FROM PROD_HORARIO_USUARIO R
				INNER JOIN PROD_HORARIO H ON R.ID_HORARIO = H.ID_HORARIO
				WHERE R.ID_HORARIO NOT IN
				(
					SELECT ID_HORARIO
					FROM PROD_HORARIO_ASIGNADO
					WHERE ID_USUARIO = $idUser
					 AND DIA = '$fecha'
				)
				AND R.ID_USUARIO = $idUser
				GROUP BY R.ID_HORARIO ";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;		
		}	
		return $result;	
	} 

	public function getAllDataByUser($aSucursales,$idUser){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_HORARIO,CONCAT(HORA,'-',HORA_FIN ) AS HORARIOS, HORA, HORA_FIN
			 	FROM PROD_HORARIO 
			 	WHERE ID_SUCURSAL IN ($aSucursales)";
		$query   = $this->query($sql);
		if(count($query)>0){
			foreach($query AS $key => $items){
				$asssign    = $this->HorarioByUser($items['ID_HORARIO'], $idUser);				
				$items['ASSIGN'] = $asssign;
				$result[]	= $items;		
			}			
		}	
		return $result;	
	}	
	
	public function HorarioByUser($idHorario,$idObject){
		$result=false;
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  COUNT(ID_USUARIO) AS TOTAL
                FROM PROD_HORARIO_USUARIO
                WHERE ID_USUARIO = $idObject
                  AND ID_HORARIO = $idHorario LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			if($query[0]['TOTAL']==1){
				$result = true;	
			}
		}	
		return $result;	 		
	}
	
	
	public function insertByUser($data,$aHorarios){
        $result     = Array();
        $result['status']  = false;
        $countErrors= 0;
        try{
	        $delete = $this->deleteByUser($data);
	        if($delete){
	        	foreach ($aHorarios as $key => $items){
	        		if(isset($data['inputsCheck'.$items['ID_HORARIO']])  && $data['inputsCheck'.$items['ID_HORARIO']]=='on'){
	        			$dataInsert['catId'] 		= $data['catId'];
	        			$dataInsert['inputhorario'] = $items['ID_HORARIO'];
	        			$insert = $this->insertHorarioUser($dataInsert);
	        			
	        			if(!$insert){
	        				$countErrors++;
	        			}
	        		}
	        	}
	        	
	        	if($countErrors==0){
	        		$result['status']  = true;
	        	}
	        }
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}	
	
	
	public function insertHorarioUser($data){
        $result = false;
        $sql=" INSERT INTO PROD_HORARIO_USUARIO
				 SET ID_USUARIO	= ".$data['catId'].",
				 ID_HORARIO		= ".$data['inputhorario'];
        try{
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}
	
	public function deleteByUser($data){
    	try{    	
       		$result     = Array();
        	$result['status']  = false;
        
			$sql  	= "DELETE FROM PROD_HORARIO_USUARIO WHERE ID_USUARIO = ".$data['catId'];
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