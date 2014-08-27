<?php
/**
 * Archivo de definición de usuarios
 * 
 * @author EPENA
 * @package library.My.Models
 */

/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Tecnicos extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function getAll($idObject,$selectId=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sIdSearch = ($selectId==0) ? 'T.ID_TELEFONO': 'U.ID_USUARIO'; 		
    	$sql ="SELECT $sIdSearch AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME, E.ID_SUCURSAL
				FROM USR_EMPRESA E
				INNER JOIN USUARIOS   U ON E.ID_USUARIO  = U.ID_USUARIO AND U.ID_PERFIL = 4 AND U.FLAG_OPERACIONES = 1 
				INNER JOIN SUCURSALES S ON E.ID_SUCURSAL  = S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE S.ID_EMPRESA = $idObject
				ORDER BY E.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME
				FROM USR_EMPRESA E
				INNER JOIN USUARIOS U ON E.ID_USUARIO = U.ID_USUARIO 
										AND U.ID_PERFIL = 4 
										AND U.FLAG_OPERACIONES = 1 
				WHERE ID_SUCURSAL = $idObject
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function getLastPositions($values){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_TELEFONO AS ID,
				U.FECHA_TELEFONO AS FECHA_GPS,
				E.DESCRIPCION_EVENTO AS EVENTO,
				U.LATITUD,
				U.LONGITUD,
				U.TIPO_GPS,
				U.VELOCIDAD,
				U.NIVEL_BATERIA,
				U.ANGULO,
				U.UBICACION
				FROM PROD_ULTIMA_POSICION U
				INNER JOIN PROD_EVENTOS   E ON U.ID_EVENTO = E.ID_EVENTO
				WHERE U.ID_TELEFONO IN ($values)";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}
	
	public function getTecnicosBySucursal($values){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME, E.ID_SUCURSAL
				FROM USR_EMPRESA E
				INNER JOIN USUARIOS   U ON E.ID_USUARIO  = U.ID_USUARIO AND U.ID_PERFIL = 4 AND U.FLAG_OPERACIONES = 1 
				INNER JOIN SUCURSALES S ON E.ID_SUCURSAL  = S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE E.ID_SUCURSAL IN ($values)
				ORDER BY E.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;				
	}
}