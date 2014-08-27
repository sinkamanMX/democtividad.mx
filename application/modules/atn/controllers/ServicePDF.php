<?php				
class atn_ServicesController extends My_Controller_Action
{	
	protected $_clase = 'mservices';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	
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
    		$this->view->dataUser['allwindow'] = true;   
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;		
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);

			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				$idSucursal	= $this->dataIn['cboInstalacion'];
				$idTecnico	= $this->dataIn['inputTecnicos'];
				$bShowUsers=true;
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
				$idSucursal	= $this->view->dataUser['ID_SUCURSAL'];
				$bShowUsers=true;
			}
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($idSucursal);
			$dataResume     = $cCitas->getResumeByDay($idSucursal,$dFechaIn,$dFechaFin,$idTecnico);
			$dataProcess	= $cFunciones->setResume($dataResume);
			
			$this->view->cInstalaciones 	= $cFunciones->selectDb($dataCenter,$idSucursal);
			$this->view->aTecnicos 			= $cFunciones->selectDb($aTecnicos,$idTecnico);	
			$this->view->data 				= $this->dataIn;
			$this->view->dataResume 	 	= $dataProcess;
			$this->view->dataResumeTotal 	= $dataProcess['TOTAL'];
			$this->view->showUsers			= $bShowUsers;
			$this->view->aResume 			= $dataResume;
			unset($this->view->dataResume['TOTAL']);	
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getlastpAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
				$cTecnicos  = new My_Model_Tecnicos();				
				$allInputs  = implode(',', $this->dataIn['strInput']);				
				$dataPos    = $cTecnicos->getLastPositions($allInputs);		
				foreach ($dataPos as $key => $items){
					if($items['ID']!="" && $items['ID']!="NULL")
					$result .= ($result!="") ? "!" : "";
					$result .=  $items['ID']."|".
								$items['FECHA_GPS']."|".
                                $items['EVENTO']."|".
                                $items['LATITUD']."|".
                                $items['LONGITUD']."|".
                                round($items['VELOCIDAD'],2)."|".
                                round($items['NIVEL_BATERIA'],2)."|".
                                $items['TIPO_GPS']."|".
                                $items['ANGULO']."|".
                                $items['UBICACION'];
				}
			}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }     	
    }
    
    public function getinformationAction(){
		$this->view->layout()->setLayout('blank');
		
		if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
			$cTecnicos		= new My_Model_Tecnicos();
			$cFunctions 	= new My_Controller_Functions();
			$cCitas			= new My_Model_Citas();
			$dToday			= Date("Y-m-d");
			$dToday			= '2014-08-20';
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($this->dataIn['strInput']);
			$this->view->aTecnicos = $cFunctions->selectDb($aTecnicos);
			
			$dataResume     = $cCitas->getResumeByDay($this->dataIn['strInput'],$dToday);
			$dataProcess	= $cFunctions->setResume($dataResume);
			$this->view->dataResume 	 = $dataProcess;
			$this->view->dataResumeTotal = $dataProcess['TOTAL'];
			unset($this->view->dataResume['TOTAL']);
		}
    	
    	$this->view->data = $this->dataIn;
    }
    

	public function exportsearchAction(){
		try{   						
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();
			
			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['strInput']);
				
				if(count($dataCita)>0){			
				/** PHPExcel */ 
				require_once 'PHPExcel.php';		
										
				if (!PHPExcel_Settings::setPdfRenderer(
						PHPExcel_Settings::PDF_RENDERER_DOMPDF,
						$this->realPath.'/PHPExcel/Classes/dompdf'
				)) {
					die(
						'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
						'<br />' .
						'at the top of this script as appropriate for your directory structure'
					);
				}
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
											 
					$styleHeader = new PHPExcel_Style();
					$styleAutor	 = new PHPExcel_Style();
					$styleTittle = new PHPExcel_Style();
					$styleHeadermin = new PHPExcel_Style();
		
					$styleHeader->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Calibri'
					    )
					));
					$styleHeadermin->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Calibri'
					    )
					));					
					$styleAutor->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '6E6E6E')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Calibri'
					    )
					));
					
					$styleTittle->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'BDBDBD')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Calibri'
					    )
					));					
				
					/**
					 * Header del Reporte
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'ORDEN DE INSTALACION');
					$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'B2:F2');
					$objPHPExcel->getActiveSheet()->getStyle('B2:F2')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAutor, 'B3:F3');
					$objPHPExcel->getActiveSheet()->getStyle('B3:F3')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Orden de servicio:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', $dataCita['ID']);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Fecha:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', $dataCita['FECHA_CITA']);					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Horario de la cita:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', $dataCita['HORA_CITA']);
					$objPHPExcel->getActiveSheet()->mergeCells('C7:F7');					
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', 'Registrada por:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', $dataCita['USR_REGISTRADO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('C8:F8');										

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Cliente:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', $dataCita['NOMBRE_CLIENTE']);
					$objPHPExcel->getActiveSheet()->mergeCells('C9:F9');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B10', 'Domicilio del cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C10', $dataCita['DIRECCION_CLIENTE1']);
					$objPHPExcel->getActiveSheet()->mergeCells('C10:F10');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C11', $dataCita['DIRECCION_CLIENTE2']);
					$objPHPExcel->getActiveSheet()->mergeCells('C11:F11');											
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B12', 'Domicilio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C12', $dataCita['DIRECCION_CITA1']);
					$objPHPExcel->getActiveSheet()->mergeCells('C12:F12');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C13', $dataCita['DIRECCION_CITA2']);
					$objPHPExcel->getActiveSheet()->mergeCells('C13:F13');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B14', utf8_encode('Personal asignado:'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C14', $dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('C14:F14');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B15', 'Inicio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C15', $dataCita['FECHA_INICIO']);
					$objPHPExcel->getActiveSheet()->mergeCells('C15:F15');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B16', 'Fin de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C16', $dataCita['FECHA_TERMINO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('C16:F16');	

					$rowControl		= 18;
					$zebraControl  	= 0;							
					
					$aForms = $cCitas->getFormsCita($this->dataIn['strInput']);					
					foreach($aForms as $key => $itemsForm){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $itemsForm['TITULO']);
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeadermin, 'B'.$rowControl.':F'.$rowControl);						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
																
						$rowControl++;	
						$aDataForms = $cCitas->getDataSendbyForms($this->dataIn['strInput'],$itemsForm['ID_FORMULARIO']);
						
						foreach($aDataForms as $items){							
							if($items['TIPO']=='ENCABEZADO'){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleTittle, 'B'.$rowControl.':F'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
												->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
								//Falta aplicar el estilo								
							
							}else{		
								/*------ La respuesta es una foto ------*/						
								if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){									
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
										->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);	
									$rowControl++;		

									if (file_exists($this->realPath.$items['CONTESTACION'])) {

										$objDrawing = new PHPExcel_Worksheet_Drawing();
										
										$objDrawing->setName('Picture1');
										$objDrawing->setDescription('Picture1');
										
										$objDrawing->setPath($this->realPath.$items['CONTESTACION']);
										$objDrawing->setHeight(120);
										$objDrawing->setWidth(120);
										$objDrawing->setOffsetX(120);
										$objDrawing->setOffsetY(50);
										$objDrawing->setResizeProportional(true);
										$objDrawing->setCoordinates('B'.$rowControl);
										
										$objPHPExcel->getActiveSheet()->getRowDimension('B'.$rowControl)->setRowHeight(150);
										$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
										$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
										
										if($items['T_ELEMENTO']=='10'){
											//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(60);	
										}else{
											//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(150);
										}
										
										$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(150);
										
										$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
									}else{
										$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), "Imagen no disponible.");								
										$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);										
									}
									
								/*------ La respuesta es texto    ------*/	
								}else{
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['CONTESTACION']);								
									$objPHPExcel->getActiveSheet()->mergeCells('D'.$rowControl.':F'.$rowControl);
																		
								}
								$rowControl++;
							}					
						}						
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);	

					$objPHPExcel->getActiveSheet()->setShowGridLines(true);
					$objPHPExcel->getActiveSheet()->setPrintGridLines(true);					
				
					$filename  = "Reporte_Cita_".date("YmdHi").".pdf";
	
					// Redirect output to a clientÕs web browser (PDF)
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');
				/*			
					$filename  = "Reporte_Cita_".date("YmdHi").".xlsx";
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');			
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
				*/				
				}
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }   
	}		   

}