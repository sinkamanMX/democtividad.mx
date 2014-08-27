<?php

class main_ActivosController extends My_Controller_Action
{
	protected $_clase = 'mactivos';
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
    		$classObject = new My_Model_Activos();
    		$this->view->datatTable = $classObject->getAdminTables();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  
    
    public function getinfoAction(){    	
		$dataInfo = Array();
		$classObject 	= new My_Model_Activos();
		$functions 		= new My_Controller_Functions();
		$cMarcas 		= new My_Model_Activosmarcas();
		$cModelos 		= new My_Model_Activosmodelos();
		$cColores		= new My_Model_Colores();

		$sModelo		= '';
		$sColor			= '';
		$sMarca			= '';
		
		$aColores		= $cColores->getCbo();		
		$aMarcas		= $cMarcas->getCbo();
		
        if($this->idToUpdate >-1){
			$dataInfo	= $classObject->getAdminData($this->idToUpdate);
			$sColor		= $dataInfo['ID_COLOR'];
			$sModelo	= $dataInfo['ID_MODELO'];
			$sMarca		= $dataInfo['ID_MARCA'];
			$aModelos	= $cModelos->getCbo($sMarca);
			$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);
		}

        if($this->operation=='update'){	  		
			if($this->idToUpdate>-1){
				$updated = $classObject->updateRow($this->dataIn);
				 if($updated['status']){			
					$dataInfo   = $classObject->getAdminData($this->idToUpdate);
					$sColor		= $dataInfo['ID_COLOR'];
					$sModelo	= $dataInfo['ID_MODELO'];
					$sMarca		= $dataInfo['ID_MARCA'];
					$aModelos	= $cModelos->getCbo($sMarca);
					$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);					
					$this->resultop = 'okRegister';
				 }
			}else{
				$this->errors['status'] = 'no-info';
			}	
		}else if($this->operation=='new'){
			$this->dataIn['userRegister']= $this->view->dataUser['ID_USUARIO'];
			$insert = $classObject->insertRow($this->dataIn);			
	 		if($insert['status']){	
	 			$this->idToUpdate = $insert['id'];	
		 		$dataInfo   = $classObject->getAdminData($this->idToUpdate);
				$sColor		= $dataInfo['ID_COLOR'];
				$sModelo	= $dataInfo['ID_MODELO'];
				$sMarca		= $dataInfo['ID_MARCA'];
				$aModelos	= $cModelos->getCbo($sMarca);
				$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);		 		
		 		$this->resultop = 'okRegister';
			}else{
				$this->errors['status'] = 'no-insert';
			}					
		}	
		
		

		if(count($this->errors)>0 && $this->operation!=""){
			$dataInfo['DESCRIPCION'] 	= $this->dataIn['inputDesc'];
			$dataInfo['ID_MODELO'] 		= $this->dataIn['inputModelo'];
			$dataInfo['ID_MARCA'] 		= $this->dataIn['inputMarca'];
			$dataInfo['ID_COLOR'] 		= $this->dataIn['inputColor'];
			$dataInfo['PLACAS'] 		= $this->dataIn['inputPlacas'];
			$dataInfo['MOTOR'] 			= $this->dataIn['inputMotor'];

			$sColor		= $dataInfo['ID_COLOR'];
			$sModelo	= $dataInfo['ID_MODELO'];
			$sMarca		= $dataInfo['ID_MARCA'];
			$aModelos	= $cModelos->getCbo($sMarca);
			$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);			
		}

		$this->view->marcas		= $functions->selectDb($aMarcas,$sMarca);		
		$this->view->aColores   = $functions->selectDb($aColores,$sColor);
		
		$this->view->data 		= $dataInfo; 
		$this->view->errors 	= $this->errors;	
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;	
		$this->view->mOption = 'mequipos';	
    }      
}