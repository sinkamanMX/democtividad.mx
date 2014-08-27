<?php

class callcenter_MainController extends My_Controller_Action
{
	protected $_clase = 'callcenter';
	public    $dataIn = Array();
	protected $idEmpresa = -1;
	public    $aDbTables = Array (  'mun'        => Array('nameClass'=>'Municpios'),
									'colonia'    => Array('nameClass'=>'Colonias'),
									'horario'    => Array('nameClass'=>'Cinstalaciones'),
									'modeloe'    => Array('nameClass'=>'Modelos')
						);
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->idEmpresa		= $this->view->dataUser['ID_EMPRESA'];
		$this->view->moduleInfo = $perfiles->getDataMenu($this->_clase);
    }
    
    public function indexAction(){
    	
    }
}