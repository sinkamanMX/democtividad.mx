<?php

class main_ProfilesController extends My_Controller_Action
{
	protected $_clase = 'mprofiles';
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
			$classObject = new My_Model_Perfiles();
			$this->view->datatTable = $classObject->getDataTables();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  
    
    
    public function getinfoAction(){
    	try{
    		$dataInfo = Array();
    		$cFunctions	= new My_Controller_Functions();
    		$classObject 	= new My_Model_Perfiles();
    		$sEstatus	= '';
    		
    	    if($this->idToUpdate >-1){
    	    	$dataInfo	= $classObject->getData($this->idToUpdate);
    	    	$sEstatus	= $dataInfo['ACTIVO'];
			}    		
			
			
    		if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
						$updated = $classObject->updateRow($this->dataIn);
						 if($updated['status']){	
					 		$dataInfo    = $classObject->getData($this->idToUpdate);
					 		$sEstatus	= $dataInfo['ACTIVO'];			 		
					 		$this->resultop = 'okRegister';
						 }else{
							$this->errors['status'] = 'no-info';
						}
				}else{
					$this->errors['status'] = 'no-info';
				}
			}else if($this->operation=='new'){
			 	$insert = $classObject->insertRow($this->dataIn);
		 		if($insert['status']){
		 			$this->idToUpdate = $insert['id'];
			 		$dataInfo    = $classObject->getData($this->idToUpdate);
		 			$sEstatus	 = $dataInfo['ACTIVO'];	
			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
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

			if(count($this->errors)>0 && $this->operation!=""){
				$dataInfo['DESCRIPCION'] 	= $this->dataIn['inputDescripcion'];
				$dataInfo['ACTIVO'] 		= $this->dataIn['inputEstatus'];
				$dataInfo['EDITAR'] 		= @$this->dataIn['inputEditar'];
				$dataInfo['LECTURA'] 		= @$this->dataIn['inputLeer'];
				$dataInfo['INSERTAR'] 		= @$this->dataIn['inputAgregar'];
				$dataInfo['ELIMINAR'] 		= @$this->dataIn['inputBorrar'];

    	    	$sEstatus	 = $dataInfo['ACTIVO'];	
			}				
			
			$this->view->aStatus  	 = $cFunctions->cboStatus($sEstatus);		
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