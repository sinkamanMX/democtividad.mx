<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Clientes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CLIENTES';
	protected $_primary = 'ID_CLIENTE';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET  NOMBRE			= '".$data['inputNombre']."',
					APELLIDOS		= '".$data['inputApps']."',
					TELEFONO_FIJO	= '".$data['inputTel']."',
					TELEFONO_MOVIL	= '".$data['inputCel']."',
					EMAIL			= '".$data['inputEmail']."',
					RFC				= '".$data['inputRFC']."',
					RAZON_SOCIAL	= '".$data['inputRazon']."',
					COD_CLIENTE 	= '".$data['inputClave']."',
					TIPO_PERSONA	= '".$data['inputTipo']."',
					GENERO			= '".$data['inputGenero']."'";
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
	
	public function insertDomCliente($data){
        $result     = Array();
        $result['status']  = false;
        
        
        $sql="INSERT INTO PROD_DOMICILIOS_CLIENTE
				SET  ID_CLIENTE	= ".$data['IdCLiente'].",
					ESTADO		= '".$data['sEstado']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					COLONIA		= '".$data['scolonia']."',
					CALLE		= '".$data['inputStreet']."',
					CP			= '".$data['inputCP']."',
					NUMERO_EXT	= '".$data['inputNoExt']."',
					NUMERO_INT	= '".$data['inputNoInt']."',
					REFERENCIAS = '".$data['inputRefs']."', 
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
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