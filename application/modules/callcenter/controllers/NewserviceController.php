<?php

class callcenter_NewserviceController extends My_Controller_Action
{	
	protected $_clase = 'mcallcenter';
	public $dataIn;	
	public $aService;
    public $aOptions = Array(		
		array("id"=>"2",'name'=>'Otro domicilio' )    
    );    	
    
    /*array("id"=>"1",'name'=> 'Centro de Instalaci&oacuten' ),*/

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
				
			$functions = new My_Controller_Functions();
			$estados   = new My_Model_Estados();
			$aEstados  = $estados->getCbo();
			$aMunicipios=new My_Model_Municipios();
			$aColonias = new My_Model_Colonias();
			$cCitas	   = new My_Model_Citas(); 			
			 
			$aNamespace = new Zend_Session_Namespace("sService");
			$this->view->estados= $functions->selectDb($aEstados);
			$this->view->genero = $functions->cboGenero();
			$tipoServicio		= $cCitas->getCboTipoServicio();
			$this->view->mismoDomicilio = $functions->cboOptions();
			$this->view->dirDomicilio   = $functions->cbo_from_array($this->aOptions,"1");
			$this->view->tipoCliente    = $functions->cboTipoCliente();
			$this->view->tipoService	= $functions->selectDb($tipoServicio);			

			
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']=="new"){
				if(isset($aNamespace->service)){
					unset($aNamespace->service);
				}
				
				$aNamespace->service = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/instalation');	
			}

			if(isset($aNamespace->service)){
				$this->view->data   = $aNamespace->service;
				$this->view->estados= $functions->selectDb($aEstados,$aNamespace->service['inputEstado']);
				$this->view->genero = $functions->cboGenero($aNamespace->service['inputGenero']);
				$this->view->mismoDomicilio = $functions->cboOptions($aNamespace->service['inputDom']);
				$this->view->tipoCliente    = $functions->cboTipoCliente($aNamespace->service['inputTipo']);
				$this->view->tipoService	= $functions->selectDb($tipoServicio,$aNamespace->service['inputTipService']);
				
				$dMunicipios = $aMunicipios->getCbo($aNamespace->service['inputEstado']);
				$this->view->municipios = $functions->selectDb($dMunicipios,$aNamespace->service['inputMunicipio']);
				$dColonia    = $aColonias->getCbo($aNamespace->service['inputMunicipio']);
				$this->view->colonias       = $functions->selectDb($dColonia,$aNamespace->service['inputcolonia']);
				$this->view->dirDomicilio   = $functions->cbo_from_array($this->aOptions,$aNamespace->service['inputDirDom']);
								
				$dMunicipios = $aMunicipios->getCbo($aNamespace->service['inputEstadoO']);
				$dColonia    = $aColonias->getCbo($aNamespace->service['inputMunicipioO']);
				$this->view->estadosO 		= $functions->selectDb($aEstados,$aNamespace->service['inputEstadoO']);
				$this->view->municipiosO 	= $functions->selectDb($dMunicipios,$aNamespace->service['inputMunicipioO']);
				$this->view->coloniasO      = $functions->selectDb($dColonia,$aNamespace->service['inputcoloniaO']);				
			}
						
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']=="searchCP"){
				if($this->dataIn['inputSearch']!=""){
					$cColonias = new My_Model_Colonias();
					
					$validateCp = $cColonias->validateCP($this->dataIn['inputSearch']);
					if(isset($validateCp['ID_COLONIA'])){						
						if($this->dataIn['typeSearch']=="0"){
							$this->view->estados 	= $functions->selectDb($aEstados,$validateCp['ID_ESTADO']);
							$dMunicipios = $aMunicipios->getCbo($validateCp['ID_ESTADO']);
							$this->view->municipios = $functions->selectDb($dMunicipios,$validateCp['ID_MUNICIPIO']);
													
							$dColonia    = $aColonias->getCbo($validateCp['ID_MUNICIPIO']);
							$this->view->colonias       = $functions->selectDb($dColonia,$validateCp['ID_COLONIA']);							
						}else{
							
							$this->view->estadosO 	= $functions->selectDb($aEstados,$validateCp['ID_ESTADO']);
							$dMunicipios = $aMunicipios->getCbo($validateCp['ID_ESTADO']);
							$this->view->municipiosO = $functions->selectDb($dMunicipios,$validateCp['ID_MUNICIPIO']);
													
							$dColonia    = $aColonias->getCbo($validateCp['ID_MUNICIPIO']);
							$this->view->coloniasO       = $functions->selectDb($dColonia,$validateCp['ID_COLONIA']);						
						}
					}
				}
				$dataRev = Array();

			  	$dataRev["inputRFC"]		= $this->dataIn['inputRFC'];
			  	$dataRev["inputRazon"] 		= $this->dataIn['inputRazon'];
			  	$dataRev["inputClave"]		= $this->dataIn['inputClave'];
			  	$dataRev["inputNombre"] 	= $this->dataIn['inputNombre'];
			  	$dataRev["inputApps"] 		= $this->dataIn['inputApps'];
			  	$dataRev["inputNac"] 		= $this->dataIn['inputNac'];
			  	$dataRev["inputGenero"] 	= $this->dataIn['inputGenero'];
			  	$dataRev["inputTel"] 		= $this->dataIn['inputTel'];
			  	$dataRev["inputCel"] 		= $this->dataIn['inputCel'];
			  	$dataRev["inputEmail"] 		= $this->dataIn['inputEmail'];
			  	$dataRev["inputEmailConf"] 	= $this->dataIn['inputEmailConf'];
			  	$dataRev["inputStreet"] 	= $this->dataIn['inputStreet'];
			  	$dataRev["inputNoExt"] 		= $this->dataIn['inputNoExt'];
			  	$dataRev["inputNoInt"] 		= $this->dataIn['inputNoInt'];
			  	$dataRev["inputCP"] 		= $this->dataIn['inputCP'];
			  	$dataRev["inputRefs"] 		= $this->dataIn['inputRefs'];			  
			  	$dataRev["inputStreetO"] 	= $this->dataIn['inputStreetO'];
			  	$dataRev["inputNoExtO"] 	= $this->dataIn['inputNoExtO'];
			  	$dataRev["inputNoIntO"] 	= $this->dataIn['inputNoIntO'];
			  	$dataRev["inputCPO"]   		= $this->dataIn['inputCPO'];
			  	$dataRev["inputRefsO"] 		= $this->dataIn['inputRefsO'];
			  	$dataRev["inputDom"] 		= $this->dataIn['inputDom'];
			  	$dataRev["inputDirDom"] 	= $this->dataIn['inputDirDom'];

				$this->view->data			= $dataRev;
				$this->view->genero 		= $functions->cboGenero($this->dataIn['inputGenero']);
				$this->view->mismoDomicilio = $functions->cboOptions($this->dataIn['inputDom']);
				$this->view->tipoCliente    = $functions->cboTipoCliente($this->dataIn['inputTipo']);
				$this->view->tipoService	= $functions->selectDb($tipoServicio,$this->dataIn['inputTipService']);				
				$this->view->dirDomicilio   = $functions->cbo_from_array($this->aOptions,$this->dataIn['inputDirDom']);
			}
			
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function instalationAction(){
		try{
		    $aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service)){
				$this->_redirect('/callcenter/newservice/index');				
			}
			
			$this->view->dataService = $aNamespace->service;
			$cEstados   = new My_Model_Estados();
			$cMunicipios=new My_Model_Municipios();
			$cColonias = new My_Model_Colonias();			

			$estado 	= $cEstados->getData($aNamespace->service['inputEstado']);
			$municipio 	= $cMunicipios->getData($aNamespace->service['inputMunicipio'],$aNamespace->service['inputEstado']);
			$colonia 	= $cColonias->getData($aNamespace->service['inputcolonia'],$aNamespace->service['inputMunicipio']);
			
			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->direction)){
					unset($aNamespace->direction);
				}
				$aNamespace->direction = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/datefinish');
			}

			if(isset($aNamespace->direction)){
				$this->view->data   = $aNamespace->direction;
			}
			
			if($aNamespace->service['inputDom'] == 1){
				$this->view->direccion = $aNamespace->service['inputStreet'].", ".$colonia['NOMBRE'].", ".$municipio['NOMBRE'].", ".
										 $estado['NOMBRE'].", "."Mexico";	
			}else{
				if($aNamespace->service['inputDirDom']=="2"){
					$estado 	= $cEstados->getData($aNamespace->service['inputEstadoO']);
					$municipio 	= $cMunicipios->getData($aNamespace->service['inputMunicipioO'],$aNamespace->service['inputEstadoO']);
					$colonia 	= $cColonias->getData($aNamespace->service['inputcoloniaO'],$aNamespace->service['inputMunicipioO']);					
					$this->view->direccion = "Mexico,".$estado['NOMBRE'].",".$municipio['NOMBRE'].",".$colonia['NOMBRE']." CP:,".$aNamespace->service['inputCPO'].",".$aNamespace->service['inputStreetO'];		
				}
			}
			
			$cinstalaciones = new My_Model_Cinstalaciones();
			$this->view->cInstalaciones = $cinstalaciones->getAll($this->view->dataUser['ID_EMPRESA']);	
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
    
    public function cardetailAction(){
    	/*
		try{
			$functions		= new My_Controller_Functions();
			$cMarcas 		= new My_Model_Activosmarcas();
			$cModelos 		= new My_Model_Activosmodelos();
			$cColores		= new My_Model_Colores();
			$sModelo		= '';
			$sMarca			= '';
			$sColor			= '';
			$aMarcas		= $cMarcas->getCbo();
			$aColores		= $cColores->getCbo();
				
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}				
			
			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->carDetail)){
					unset($aNamespace->carDetail);
				}
				$aNamespace->carDetail = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/datefinish');	
			}

			if(isset($aNamespace->carDetail)){
				$this->view->data   = $aNamespace->carDetail;
				$sMarca     = $this->view->data['inputMarca'];
				$sModelo	= $this->view->data['inputModelo'];
				$sColor		= $this->view->data['inputColor'];
				$aModelos	= $cModelos->getCbo($sMarca);
				$this->view->modelos    = $functions->selectDb($aModelos,$sModelo);
			}
			
			$this->view->marcas		= $functions->selectDb($aMarcas,$sMarca);			
			$this->view->colores	= $functions->selectDb($aColores,$sColor);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
        */    	
    }
    
    public function datefinishAction(){
    	try{
    		$errors 	= 0;
    		$iCliente 	= 0;
    		$iCita		= 0;
    		$iDomicilio = 0;
    		$sNameCliente='';
    		$iEmpresa   = $this->view->dataUser['ID_EMPRESA'];
    		$iUsuario   = $this->view->dataUser['ID_USUARIO'];
    		
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}	

			if(isset($this->dataIn['optReg'])){
				$aClienteData 	= $aNamespace->service;
				$aInstalacion 	= $aNamespace->direction;
				$aCarDetail	  	= $aNamespace->carDetail;
				
				$cEstados   	= new My_Model_Estados();
				$cMunicipios	= new My_Model_Municipios();
				$cColonias 		= new My_Model_Colonias();						
				$cClientes 		= new My_Model_Clientes();			
				$cCitas			= new My_Model_Citas();
				$cHorarios		= new My_Model_Horarios();
				$cInstalaciones = new My_Model_Cinstalaciones();				

				$estado 	= $cEstados->getData($aClienteData['inputEstado']);
				$municipio 	= $cMunicipios->getData($aClienteData['inputMunicipio'],$aClienteData['inputEstado']);
				$colonia 	= $cColonias->getData($aClienteData['inputcolonia'],$aClienteData['inputMunicipio']);					
				
				$aClienteData['sEstado'] 	= $estado['NOMBRE'];
				$aClienteData['sMunicipio'] = $municipio['NOMBRE'];
				$aClienteData['scolonia'] 	= $colonia['NOMBRE'];

				$aClienteData['sLatitud']	= 0.000000; 
				$aClienteData['sLongitud']	= 0.000000;	
								
				if($aClienteData['inputDom']==1 || $aClienteData['inputDirDom']==2){
					$aClienteData['sLatitud']	= $aInstalacion['inputLatitude'];
					$aClienteData['sLongitud']	= $aInstalacion['inputLongitude'];
				}
				/*
				 * 1.-Se inserta el cliente
				 */

				$insertCliente = $cClientes->insertRow($aClienteData);
				if(!$insertCliente['status']){
					Zend_Debug::dump("error al insertar el cliente");
					$errors++;
				}
				
				$iCliente 	  = $insertCliente['id'];
				$sNameCliente = $aClienteData['inputNombre']." ".$aClienteData['inputApps']; 	
				
				/*
				 * 2.-Se inserta el domicilio del Cliente
				 */
				if($errors==0){			
					$aClienteData['IdCLiente']  = $iCliente;
					
					$insertDireccion = $cClientes->insertDomCliente($aClienteData);
					if(!$insertDireccion['status']){
						Zend_Debug::dump("error al insertar el domicilio del cliente");
						$errors++;
					}	

					$iDomicilio = $insertDireccion['id'];
				}

				/*
				 * 3.-Se inserta la cita
				 */
				if($errors==0){
					$dataHorario			 	 = $cHorarios->getData($this->dataIn['inputhorario']); 					
					$this->dataIn['ID_EMPRESA']  = $iEmpresa;
					$this->dataIn['ID_USUARIO']  = $iUsuario;
					$this->dataIn['idDomicilio'] = $iDomicilio;
					$this->dataIn['ID_CLIENTE']  = $iCliente;
					$this->dataIn['inputHora']   = $dataHorario['HORA'];
					$this->dataIn['inputTipo']   = $aClienteData['inputTipService'];
					$insertCita = $cCitas->insertRow($this->dataIn);
					if(!$insertCita['status']){
						Zend_Debug::dump("error al insertar la cita");
						$errors++;
					}
					$iCita = $insertCita['id'];						
				}
				
				/*
				 * 3.1 Se inserta el horario asignado a la cita
				 */
				if($errors==0){
					$sucursales		= '';
					/*
					//Si la instalacion sera en el domicilio del cliente
					if($aClienteData['inputDom']==1){
						$dataSucursal   = $cInstalaciones->getCentroFromEdo($aClienteData['inputEstado']);
						if($dataSucursal['SUCURSALES']!="" && $dataSucursal['SUCURSALES']!="NULL"){
							$sucursales     = $dataSucursal['SUCURSALES'];	
						} 
					//Si la instalacion sera en otro domicilio dado por el cliente	
					}elseif($aClienteData['inputDirDom']== 2){					
						$dataSucursal   = $cInstalaciones->getCentroFromEdo($aClienteData['inputEstadoO']);					
						if($dataSucursal['SUCURSALES']!="" && $dataSucursal['SUCURSALES']!="NULL"){
							$sucursales     = $dataSucursal['SUCURSALES'];	
						}					
					//La instalacion sera en un centro de instalacion	
					}elseif($aClienteData['inputDirDom']== 1){
						$sucursales     = $aInstalacion['cboInstalacion'];
					}		*/			
					
					$sUassign 	= $cHorarios->getUserAssign($this->dataIn['inputDate'],$this->dataIn['inputhorario']);
					if($sUassign!="" && $sUassign>-1){
						$this->dataIn['uAssign'] = $sUassign;
						 
						$insertHorario = $cHorarios->insertRow($this->dataIn);
						if(!$insertHorario['status']){
							Zend_Debug::dump("error al insertar la cita");
							$errors++;
						}						
					}else{
						Zend_Debug::dump("error al insertar el horario");
						$errors++;
					}			
				}	

				/*
				 * 3.2 Se asigna el usuario a la cita
				 */
				if($errors==0){
					$this->dataIn['idCita']    = $iCita;					
					$insertRel	= $cCitas->assignUser($this->dataIn);		
					if(!$insertRel['status']){
						Zend_Debug::dump("error al insertar la cita");
						$errors++;
					}						
				}					

				/*
				 * 4.-Se inserta el domicilio de la cita
				 */	
				if($errors==0){
					$aClienteData['idCita']    = $iCita;
					$aClienteData['idCliente'] = $iCliente;	
									
					if($aClienteData['inputDom']==1){
						$insertaDomCita = $cCitas->insertDomCita($aClienteData);
						if(!$insertaDomCita['status']){
							Zend_Debug::dump("error al insertar el domicilio de la cita.");
							$errors++;
						}							
					}else{
						if($aClienteData['inputDirDom']==2){
							$estado 	= $cEstados->getData($aClienteData['inputEstadoO']);
							$municipio 	= $cMunicipios->getData($aClienteData['inputMunicipioO'],$aClienteData['inputEstadoO']);
							$colonia 	= $cColonias->getData($aClienteData['inputcoloniaO'],$aClienteData['inputMunicipioO']);					
							
							$aClienteData['sEstado'] 	= $estado['NOMBRE'];
							$aClienteData['sMunicipio'] = $municipio['NOMBRE'];
							$aClienteData['scolonia'] 	= $colonia['NOMBRE'];		
												
							$insertaDomCita = $cCitas->insertDomCitaOther($aClienteData);
							if(!$insertaDomCita['status']){
								Zend_Debug::dump("error al insertar el domicilio de la cita.");
								$errors++;
							}								
						}else if($aClienteData['inputDirDom']==1){
							/*Aqui se busca el centro de instalacion y se inserta*/
						}						 
						
					}
				}			

				/*
				 * 5.-Se inserta los valores extra de la cita
				 * 
				if($errors==0){
					$cModelosa	= new My_Model_Activosmodelos();
					$cMarcasa	= new My_Model_Activosmarcas();
					$cColores	= new My_Model_Colores();
					
					$dataModelo	= $cModelosa->getData($aCarDetail['inputModelo']);
					$dataMarca	= $cMarcasa->getData($aCarDetail['inputMarca']);
					$dataColor  = $cColores->getData($aCarDetail['inputColor']);
					
					$aCarDetail['idCita']	= $iCita;
					$aCarDetail['idCliente']= $iCliente;
					$aCarDetail['sMarca']  	= $dataMarca['DESCRIPCION'];
					$aCarDetail['sModelo'] 	= $dataModelo['DESCRIPCION'];
					$aCarDetail['nCliente'] = $sNameCliente;
					$aCarDetail['sColor']   = $dataColor['DESCRIPCION'];
					
					$insertExtra = $cCitas->insertExtraCitas($aCarDetail);
					if($insertExtra){
						$insertActPrev = $cCitas->insertActPrev($aCarDetail);
						if(!$insertActPrev){
							Zend_Debug::dump("error al insertar previo de la cita.");
							$errors++;
						}
					}else{
						Zend_Debug::dump("error al insertar extras de la cita.");
						$errors++;						
					}					
				}*/				

				/*
				 * 6.-Se inserta el formulario para la cita
				 */				
				if($errors==0){
					$aCarDetail['idCita'] = $iCita;
					$insertForm = $cCitas->insertaFormCita($aCarDetail);
					if(!$insertForm){
						Zend_Debug::dump("error al insertar el formulario.");
						$errors++;
					}						
				}	
								
				if($errors==0){
					$aNamespace = new Zend_Session_Namespace("sService");
					
		    		if(isset($aNamespace->service)){
						unset($aNamespace->service);
					}			
		    	    if(isset($aNamespace->direction)){
						unset($aNamespace->direction);
					}			
					if(isset($aNamespace->direction)){
						unset($aNamespace->direction);
					}
		    		if(isset($aNamespace->carDetail)){
						unset($aNamespace->carDetail);
					}					
	            	$this->_redirect('/callcenter/newservice/finish');	
	            		
				}else{
					$this->view->error = true;
				}
			}   	

    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	 
    }
    
    public function cancelAction(){
    	try{
			$aNamespace = new Zend_Session_Namespace("sService");

    		if(isset($aNamespace->service)){
				unset($aNamespace->service);
			}			
    	    if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}			
			if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}
    		if(isset($aNamespace->carDetail)){
				unset($aNamespace->carDetail);
			}			
			$this->_redirect('/callcenter/newservice/index');				
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	     	
    }
    
    public function gethorariosAction(){
    	try{
    		$result	= "";
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			if(isset($this->dataIn['dateID']) && $this->dataIn['dateID']!=""){
				
				$aNamespace = new Zend_Session_Namespace("sService");
				if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
					$this->_redirect('/callcenter/newservice/index');				
				}
	
				$aClienteData 	= $aNamespace->service;
				$aInstalacion 	= $aNamespace->direction;
				
				$cInstalaciones = new My_Model_Cinstalaciones();
				$cFunctions		= new My_Controller_Functions();
				$sucursales		= '';
				
				//Si la instalacion sera en el domicilio del cliente
				if($aClienteData['inputDom']==1){
					$dataSucursal   = $cInstalaciones->getCentroFromEdo($aClienteData['inputEstado']);
					if($dataSucursal['SUCURSALES']!="" && $dataSucursal['SUCURSALES']!="NULL"){
						$sucursales     = $dataSucursal['SUCURSALES'];	
					} 
				//Si la instalacion sera en otro domicilio dado por el cliente	
				}elseif($aClienteData['inputDirDom']== 2){					
					$dataSucursal   = $cInstalaciones->getCentroFromEdo($aClienteData['inputEstadoO']);					
					if($dataSucursal['SUCURSALES']!="" && $dataSucursal['SUCURSALES']!="NULL"){
						$sucursales     = $dataSucursal['SUCURSALES'];	
					}					
				//La instalacion sera en un centro de instalacion	
				}elseif($aClienteData['inputDirDom']== 1){
					$sucursales     = $aInstalacion['cboInstalacion'];
				}
				
				if($sucursales!=""){					
					$cHorarios 		= new My_Model_Horarios();
					$dataHorarios	= $cHorarios->getHorarios($sucursales,$this->dataIn['dateID']);
					if(count($dataHorarios)>0){
						$result = $cFunctions->cboHorarios($dataHorarios,$this->dataIn['dateID']);	
						if($result==""){
							$result = '<option value="">Sin Horarios Disponibles</option>';
						}
					}else{
						$result = '<option value="">Sin Horarios Disponibles</option>';	
					}
				}else{
					$result = '<option value="">Sin Horarios Disponibles</option>';
				}

			}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }       	
    }
    
    public function finishAction(){
    	
    }    
}