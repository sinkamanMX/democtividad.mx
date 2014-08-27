<?php

class main_PhonesController extends My_Controller_Action
{
	protected $_clase = 'mphones';
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
	    	$this->view->mOption = 'equipos';
			$classObject = new My_Model_Telefonos();
			$this->view->datatTable = $classObject->getDataTables($this->view->dataUser['ID_EMPRESA']);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
    
    public function getinfoAction(){
		$dataInfo = Array();
		$classObject 	= new My_Model_Telefonos();
		$functions 		= new My_Controller_Functions();
		$cMarcas 		= new My_Model_Marcastel();
		$cModelos 		= new My_Model_Modelostel();
		
		$sModelo		= '';
		$sMarca			= '';
		$sEstatus		= '';
		
		$aMarcas		= $cMarcas->getCbo();
        if($this->idToUpdate >-1){
			$dataInfo	= $classObject->getDataRow($this->idToUpdate);
			
			$sModelo    = $dataInfo['ID_MODELO'];
			$sMarca		= $dataInfo['ID_MARCA'];
			$sEstatus	= $dataInfo['ACTIVO'];
			$aModelos	= $cModelos->getCbo($sMarca);
			$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);
			
			$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);

			$this->view->eventos 	 = $functions->selectDb($aEventos);
			$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);
		}
	 	$this->dataIn['inputEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
	 	$this->dataIn['inputUser'] 	  = $this->view->dataUser['ID_USUARIO'];		
		
        if($this->operation=='update'){	  		
			if($this->idToUpdate>-1){
				
				 $validateIMEI = $classObject->validateData($this->dataIn['inputImei'],$this->idToUpdate,'imei');
				 if($validateIMEI){
					 $updated = $classObject->updateRow($this->dataIn);
					 if($updated['status']){
					 	if($this->dataIn['inputIdAssign']!=""){
						 	$insertRel = $classObject->setUser($this->idToUpdate,$this->dataIn['inputIdAssign']);
						 	if($insertRel){
						 		$dataInfo    = $classObject->getDataRow($this->idToUpdate);
								$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
					
								$this->view->eventos 	 = $functions->selectDb($aEventos);
								$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);						 		
						 		$this->resultop = 'okRegister';
						 	}
					 	}else{
									
						 	$dataInfo    = $classObject->getDataRow($this->idToUpdate);
							$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
				
							$this->view->eventos 	 = $functions->selectDb($aEventos);
							$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);						 	
						 	$this->resultop = 'okRegister';							 		
					 	}
					 }			 	
				 }else{
				 	$this->errors['eIMEI'] = '1';
				 }
			}else{
				$this->errors['status'] = 'no-info';
			}	
		}else if($this->operation=='new'){					
			$validateIMEI = $classObject->validateData($this->dataIn['inputImei'],-1,'imei');
			 if($validateIMEI){			 	
		 		$insert = $classObject->insertRow($this->dataIn);
		 		if($insert['status']){	
		 			$this->idToUpdate = $insert['id'];					 	
				 	if($this->dataIn['inputIdAssign']!=""){
					 	$insertRel = $classObject->setUser($insert['id'],$this->dataIn['inputIdAssign']);
					 	if($insertRel){
					 		$dataInfo    = $classObject->getDataRow($this->idToUpdate);
							$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
				
							$this->view->eventos 	 = $functions->selectDb($aEventos);
							$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);					 		
					 		$this->resultop = 'okRegister';
					 	}
				 	}else{
					 	$dataInfo    = $classObject->getDataRow($this->idToUpdate);
						$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
			
						$this->view->eventos 	 = $functions->selectDb($aEventos);
						$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);					 	
					 	$this->resultop = 'okRegister';							 		
				 	}
				}else{
					$this->errors['status'] = 'no-insert';
				}
			 }else{
			 	$this->errors['eIMEI'] = '1';
			 }			
		}else if($this->operation=='delete'){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
			    
			$this->dataIn['idEmpresa'] = 1; //Aqui va la variable que venga de la session
			$delete = $classObject->deleteRow($this->dataIn);
			if($delete['status']){
				$answer = Array('answer' => 'deleted'); 
			}	

	        echo Zend_Json::encode($answer);
	        die();   			
		}else if($this->operation=='deleteRel'){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
			    
			$this->dataIn['idEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
			
			$delete = $classObject->deleteRelAction($this->dataIn);
			if($delete['status']){
				$answer = Array('answer' => 'deleted'); 
			}	

	        echo Zend_Json::encode($answer);
	        die();   			
		}
		
		
    	if($this->operation=='addEvento'){
			if(isset($this->dataIn['inputEvento']) && $this->dataIn['inputEvento']){
				if($this->dataIn['inputEvento']== -99 || $this->dataIn['inputEvento']== "-99"){
					$insert = $classObject->setAllEventos($this->dataIn);
				}else{
					$insert = $classObject->setRelEventos($this->dataIn);
				}
					
				if($insert['status']){
					$dataInfo	= $classObject->getDataRow($this->idToUpdate);
					$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
					
					$this->view->eventos 	 = $functions->selectDb($aEventos);
					$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);
			 		$this->view->eventAction = true;
			 	}
			}
			
		}else if($this->operation=='deleteEvent'){
			if(isset($this->dataIn['idRelation']) && $this->dataIn['idRelation']){
				$delete = $classObject->deleteRelEvent($this->dataIn['idRelation']);
				if($delete['status']){
					$dataInfo	= $classObject->getDataRow($this->idToUpdate);
					$aEventos	= $classObject->getEventos($dataInfo['ID_TELEFONO']);
					
					$this->view->eventos 	 = $functions->selectDb($aEventos);
					$this->view->aRelEventos = $classObject->getRelEventos($dataInfo['ID_TELEFONO']);
			 		$this->view->eventAction = true;
				}										
			}
			
		}		
		$estatusIn	=	'';	
		
    	if(count($this->errors)>0 && $this->operation!=""){
			$dataInfo['DESCRIPCION'] 	= $this->dataIn['inputDesc'];
			$dataInfo['IMEI'] 			= $this->dataIn['inputImei'];
			$dataInfo['TELEFONO'] 		= $this->dataIn['inputTel'];
			$dataInfo['ID_MODELO'] 		= $this->dataIn['inputModelo'];
			$dataInfo['ID_MARCA'] 		= $this->dataIn['inputMarca'];						
			$estatusIn 					= $this->dataIn['inputEstatus'];

			$dataInfo['MARCA'] 			= $this->dataIn['inputMarca'];
			$dataInfo['MODELO'] 		= $this->dataIn['inputModelo'];
			
			$sModelo    = $dataInfo['ID_MODELO'];
			$sMarca		= $dataInfo['ID_MARCA'];
			
			$aModelos	= $cModelos->getCbo($sMarca);
			$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);			
		}		
		
		$this->view->status		= $functions->cboStatusString($sEstatus,$estatusIn);
		$this->view->marcas		= $functions->selectDb($aMarcas,$sMarca);			
		$this->view->data 		= $dataInfo; 
		$this->view->errors 	= $this->errors;	
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;	
		$this->view->mOption = 'mequipos';	
    }     
    
    public function searchactivosAction(){
    		try{
			$this->view->layout()->setLayout('layout_blank');

			$cClassObject = new My_Model_Telefonos();
			$aUsuarios    = $cClassObject->getDataNoAssign($this->view->dataUser['ID_EMPRESA']);
			$this->view->dataTable= $aUsuarios;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
}