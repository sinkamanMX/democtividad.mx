<?php

class atn_RastreoController extends My_Controller_Action
{	
	protected $_clase = 'mrastreotels';
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
    
    public function indexAction(){
    	try{
			$this->view->dataUser['allwindow'] = true;   
			
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);
			$this->view->cInstalaciones = $cFunciones->selectDb($dataCenter);

			$this->view->aTecnicos = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA']);			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    		
    }  
    
    public function reporteAction(){
		$this->view->layout()->setLayout('layout_blank');    	
        try{
        	$dataRecorrido = Array();
        	if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
        		$cTelefonos = new My_Model_Telefonos();
				
        		if(!isset($this->dataIn['optReg'])){
        			$this->dataIn['inputFechaIn']  = Date("Y-m-d 00:00:00"); 
        			$this->dataIn['inputFechaFin'] = Date("Y-m-d 23:59:00");        				        		
        		}
        		$dataRecorrido =  $cTelefonos->getReporte($this->dataIn);
        	}
        	
			$this->view->aRecorrido = $dataRecorrido;
			$this->view->data		= $this->dataIn;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

	public function exportsearchAction(){
		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cTelefonos = new My_Model_Telefonos();
			
			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""     && 
			   isset($this->dataIn['inputFechaIn'])  && $this->dataIn['inputFechaIn']!="" && 
			   isset($this->dataIn['inputFechaFin']) && $this->dataIn['inputFechaFin']!=""){
			   	
			   	
				$dataInfo    = $cTelefonos->getData($this->dataIn['strInput']);	
				$nameClient = $this->view->dataUser['N_EMPRESA']." - ".$this->view->dataUser['N_SUCURSAL']; 
				$dateCreate = date("d-m-Y H:i");
				$createdBy	= $this->view->dataUser['USUARIO']; 			   	
			   	$dataRecorrido =  $cTelefonos->getReporte($this->dataIn);	
			   	
			   	
				/** PHPExcel */ 
				include 'PHPExcel.php';
				
				/** PHPExcel_Writer_Excel2007*/ 
				include 'PHPExcel/Writer/Excel2007.php';			
				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator("UDA")
										 ->setLastModifiedBy("UDA")
										 ->setTitle("Office 2007 XLSX")
										 ->setSubject("Office 2007 XLSX")
										 ->setDescription("Reporte del Viaje")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Reporte del Viaje");
				
										 
				$styleHeader = new PHPExcel_Style();
				$stylezebraTable = new PHPExcel_Style();  
	
				$styleHeader->applyFromArray(array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => '459ce6')
					)
				));
	
				$stylezebraTable->applyFromArray(array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
					)
				));	
	
				$zebraTable = array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
					)
				);

				
				/**
				 * Header del Reporte
				 **/
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $nameClient);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Historial');
				$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(20);
				$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A3', 'Reporte Creado '.$dateCreate.' por '.$createdBy);	
				$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
				$objPHPExcel->getActiveSheet()->getStyle('A1:H1')
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
				$objPHPExcel->getActiveSheet()->getStyle('A2:H2')
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
				$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');	
				$objPHPExcel->getActiveSheet()->getStyle('A3:H3')
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
												
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Telfono');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', $dataInfo['DESCRIPCION']);

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'IMEI');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', $dataInfo['IMEI']);	
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Marca-Modelo');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', $dataInfo['MARCA']."-".$dataInfo['MODELO']);	
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Asignado');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', $dataInfo['ASIGNADO']);			

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Fecha Inicio');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', $this->dataIn['inputFechaIn']);
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Fecha Fin');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', $this->dataIn['inputFechaFin']);	
			
				/**
				 * Detalle del Viaje
				 * */
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9', 'Fecha GPS');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Tipo');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Evento');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', 'Latitud');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', 'Longitud');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F9', 'Velocidad');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G9', 'Bateria');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H9', utf8_encode('Ubicacion'));
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A9:H9');
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A9:H9")->getFont()->setSize(12);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A9:H9")->getFont()->setBold(true);

				
				$rowControlHist=10;
				$zebraControl=0;				
				foreach($dataRecorrido as $reporte){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['FECHA_TELEFONO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['TIPO_GPS']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['EVENTO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), $reporte['LATITUD']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['LONGITUD']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), round($reporte['VELOCIDAD'],2)." kms/h");
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), round($reporte['NIVEL_BATERIA'],2)." %");
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControlHist), $reporte['UBICACION']);

					if($zebraControl++%2==1){
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':H'.$rowControlHist);			
					}				
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rowControlHist.':C'.$rowControlHist)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
					$objPHPExcel->getActiveSheet()->getStyle('H'.$rowControlHist)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
					$rowControlHist++;
				}					
				       
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);					
				
				$objPHPExcel->setActiveSheetIndex(0);
				$filename  = "RH_".$dataInfo['IMEI']."_".date("YmdHi").".xlsx";							
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');			
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
			}
			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
}