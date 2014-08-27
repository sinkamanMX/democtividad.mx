<?php

class main_MapController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
    }
    
    public function indexAction(){
    	$result = 'no-info';
    	$this->dataIn = $this->_request->getParams();
    	
    	if(isset($this->dataIn['plaque']) && $this->dataIn['plaque']!=""){
    		$cActivos   =  new My_Model_Activos();
			$dataPlaque = $this->dataIn['plaque'];
			
			$dataActive = $cActivos->getActiveByPlaque($dataPlaque);
			if(@$dataActive['ID_ACTIVO'] !="" && isset($dataActive['ID_ACTIVO'])){
				$lastPosition 	  = $cActivos->getLasPosition($dataActive['ID_ACTIVO']); 
				if(isset($lastPosition['LATITUD']) && $lastPosition['LATITUD']!=""){
					$this->view->data = $lastPosition;
					$result = 'ok';		
				}else{
					$result = 'no-pos';	
				}
			}else{
				$result = 'no-info';	
			}
    	}
    	$this->view->dataOk = $result; 
    }
}