<?php

class atn_MainController extends My_Controller_Action
{	
	protected $_clase = 'matn';
	public $dataIn;	
	public $aService;
		
    public function init()
    {
    	try{	
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
			$this->dataIn 			= $this->_request->getParams();
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

    public function indexAction()
    {
		try{

        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getcitaspendientesAction(){
    	try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
			$cCitas = new My_Model_Citas();
			
			$dataCitas = $cCitas->getCitasPendientes();
			echo Zend_Json::encode($dataCitas);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
	public function searchcitasAction(){
		try{
			$this->view->layout()->setLayout('layout_blank'); 
			$formShow = 0;
			$cCitas   = new My_Model_Citas();
			$funtions = new My_Controller_Functions();
			$dataStatus = $cCitas->getCboStatus();
			
			if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=='search'){
				$fechaIn		= $this->dataIn['inputFechaIn'];
				$fechaFin		= $this->dataIn['inputFechaFin'];
				$status			= $this->dataIn['inputEstatus'];
				$stringSearch	= $this->dataIn['inputSearch'];
				
				$dataSearch     = $cCitas->getCitasSearch($fechaIn,$fechaFin,$stringSearch,$status);
				$this->view->dataSearch = $dataSearch;
				$formShow 		= 1;
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus);
			$this->view->showForm = $formShow;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	} 

	public function citadetalleAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			 			
			$cCitas     = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$cUsuarios  = new My_Model_Usuarios();
			
			$dataStatus = $cCitas->getCboStatus();
			$dataUsers  = $cUsuarios->getCbOperadores();
			$dataDate = Array();
			$dataStat = '';
			$opAsign  = '';
			$statusOpr = false;
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$iIdCita	= $this->dataIn['strInput'];
				$dataDate   = $cCitas->getCitasDet($iIdCita);
				$dataStat	= $dataDate['ID_ESTATUS'];
				$opAsign	= $dataDate['ID_OPERADOR'];					
				if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=="opSearch"){
					$inputPersonal = $this->dataIn['inputPersonal'];
					$this->dataIn['ID_USUARIO']  = $this->view->dataUser['ID_USUARIO'];					
					$dataToChange  = $cCitas->setRow($this->dataIn);
					if($dataToChange){
						if($inputPersonal != $opAsign && isset($this->dataIn['inputPersonal'])){
							$this->dataIn['ID_OPERADOR'] = $opAsign;
							$updateRowOp = $cCitas->changePersonal($this->dataIn);	
						}else{
							$statusOpr = true;	
						}
					}
				}				
				$dataDate   = $cCitas->getCitasDet($iIdCita);
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus,$dataStat);
			$this->view->personal = $funtions->selectDb($dataUsers,$opAsign);
			$this->view->data     = $dataDate;
			$this->view->statusOpr= $statusOpr;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	}
}
