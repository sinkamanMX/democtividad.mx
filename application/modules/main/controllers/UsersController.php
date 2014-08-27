<?php

class main_UsersController extends My_Controller_Action
{
	protected $_clase = 'musers';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;	
		
    public function init()
    {
    	try{
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
		
		$this->dataIn = $this->_request->getParams();
		$this->validateNumbers = new Zend_Validate_Digits();
				
		if(isset($this->dataIn['optReg'])){
			$this->operation = $this->dataIn['optReg'];
			
			if($this->operation=='update'){
				$this->operation = $this->dataIn['optReg'];

				$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
			}	
		}
		
		if(isset($this->dataIn['catId']) && $this->validateNumbers->isValid($this->dataIn['catId'])){
			$this->idToUpdate 	   = $this->dataIn['catId'];	
		}else{
			$this->idToUpdate 	   = -1;
			$this->errors['status'] = 'no-info';
		}	

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function indexAction(){
    	try{
			$classObject = new My_Model_Usuarios();
			$this->view->datatTable = $classObject->getDataTables($this->view->dataUser);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  
    
    public function getinfoAction(){
    	try{
    		$dataInfo = Array();
    		$cFunctions	= new My_Controller_Functions();
    		$classObject= new My_Model_Usuarios();
    		$cPerfiles 	= new My_Model_Perfiles();
    		$cSucursales= new My_Model_Cinstalaciones();
    		$cHorarios 	= new My_Model_Horarios();
    		
    		$sPerfil	= '';
    		$sEstatus	= '';
    		$sOperaciones= '';
    		$sSucursales= '';  
    		$aHorarios  = Array();  		
    		
    		$aPerfiles	= $cPerfiles->getCbo();
    		$aSucursales= $cSucursales->getCbo($this->view->dataUser['ID_EMPRESA']);
    	    if($this->idToUpdate >-1){
    	    	$dataInfo	= $classObject->getData($this->idToUpdate);
    	    	$sPerfil	= $dataInfo['ID_PERFIL'];
    	    	$sEstatus	= $dataInfo['ACTIVO'];
				$sOperaciones= $dataInfo['FLAG_OPERACIONES'];
				$sSucursales=$dataInfo['ID_SUCURSAL'];
				
				$aHorarios  = $cHorarios->getAllDataByUser($dataInfo['ID_SUCURSAL'],$this->idToUpdate);
			}
			
			if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					 $validateUser = $classObject->validateData($this->dataIn['inputUsuario'],$this->idToUpdate,'user');
					 if($validateUser){
						$updated = $classObject->updateRow($this->dataIn);
						 if($updated['status']){	
					 		$dataInfo    	= $classObject->getData($this->idToUpdate);
			    	    	$sPerfil		= $dataInfo['ID_PERFIL'];
			    	    	$sEstatus		= $dataInfo['ACTIVO'];
							$sOperaciones	= $dataInfo['FLAG_OPERACIONES'];
							$sSucursales	= $dataInfo['ID_SUCURSAL'];	
							$aHorarios  	= $cHorarios->getAllDataByUser($dataInfo['ID_SUCURSAL'],$this->idToUpdate);				 		
					 		$this->resultop = 'okRegister';
						 }
					 }else{
					 	$this->errors['eUsuario'] = '1';
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}
			}else if($this->operation=='new'){
				$validateUser = $classObject->validateData($this->dataIn['inputUsuario'],-1,'user');
				 if($validateUser){
				 	$insert = $classObject->insertRow($this->dataIn);
			 		if($insert['status']){
			 			$this->idToUpdate = $insert['id'];
				 		$dataInfo    	= $classObject->getData($this->idToUpdate);
		    	    	$sPerfil		= $dataInfo['ID_PERFIL'];
		    	    	$sEstatus		= $dataInfo['ACTIVO'];
						$sOperaciones	= $dataInfo['FLAG_OPERACIONES'];
						$sSucursales	= $dataInfo['ID_SUCURSAL'];	
						$aHorarios  	= $cHorarios->getAllDataByUser($dataInfo['ID_SUCURSAL'],$this->idToUpdate);			 		
				 		$this->resultop = 'okRegister';
					}else{
						$this->errors['status'] = 'no-insert';
					}
				 }else{
				 	$this->errors['eUsuario'] = '1';
				 }			
			}else if($this->operation=='delete'){
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				$answer = Array('answer' => 'no-data');
				    
				$this->dataIn['idEmpresa'] = 1; //Aqui va la variable que venga de la session
				$delete = $classObject->deleteRow($this->dataIn);
				if($delete){
					$answer = Array('answer' => 'deleted'); 
				}	
	
		        echo Zend_Json::encode($answer);
		        die();   			
			}
			
    	    if($this->operation=='addEvento'){
    	    	$insert = $cHorarios->insertByUser($this->dataIn, $aHorarios);
    	    	if($insert['status']){
    	    		$aHorarios  	= $cHorarios->getAllDataByUser($dataInfo['ID_SUCURSAL'],$this->idToUpdate);
					$this->view->eventAction = true;
				}
			}			
			

			if(count($this->errors)>0 && $this->operation!=""){
				$dataInfo['ID_PERFIL'] 		= $this->dataIn['inputPerfil'];
				$dataInfo['ID_SUCURSAL'] 	= $this->dataIn['inputSucursal'];
				$dataInfo['USUARIO'] 		= $this->dataIn['inputUsuario'];
				$dataInfo['NOMBRE'] 		= $this->dataIn['inputNombre'];
				$dataInfo['APELLIDOS'] 		= $this->dataIn['inputApps'];
				$dataInfo['EMAIL'] 			= $this->dataIn['inputEmail'];
				$dataInfo['TEL_MOVIL'] 		= $this->dataIn['inputMovil'];
				$dataInfo['TEL_FIJO'] 		= $this->dataIn['inputTelFijo'];
				$dataInfo['ACTIVO'] 		= $this->dataIn['inputEstatus'];
				$dataInfo['FLAG_OPERACIONES']= $this->dataIn['inputOperaciones'];
				
    	    	$sPerfil	 = $dataInfo['ID_PERFIL'];
    	    	$sEstatus	 = $dataInfo['ACTIVO'];
				$sOperaciones= $dataInfo['FLAG_OPERACIONES'];
				$sSucursales =$dataInfo['ID_SUCURSAL'];	
			}		

			
    		$this->view->aHorarios	 = $aHorarios;
			$this->view->aPerfiles   = $cFunctions->selectDb($aPerfiles,$sPerfil);
			$this->view->aSucursales = $cFunctions->selectDb($aSucursales,$sSucursales);
			$this->view->aStatus  	 = $cFunctions->cboStatus($sEstatus);	
			$this->view->aOperaciones= $cFunctions->cboOptions($sOperaciones);		
				
			$this->view->data 		= $dataInfo; 
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;
				    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }     
}