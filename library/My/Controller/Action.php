<?php
/**
 * Archivo de definición de clase 
 * 
 * @package library.My.Controller
 * @author andres
 */

/**
 * Definición de clase de controlador genérico
 *
 * @package library.My.Controller
 * @author andres
 */
class My_Controller_Action extends Zend_Controller_Action
{
	/**
	 * Identidad
	 * 
	 * @var mixed
	 */
	protected $_identidad;
	
    /**
     * el contenido de este array son solo las rutas relativas, 
     * a estas se les concatenara el dominio y todo el tag de stylesheet
     * 
     * @var array 
     */
    protected $_css = array();

    protected $_js = array();
	
	/**
	 * Contextos, se pueden modificar en cada uno de los controladores hijos
	 *
	 * @var $contexts: arreglo de contextos
	 */
	public $contexts = array( );
	
   /**
    * Inicializa el contexto, el formato de respuesta a un action
    *
    * @return void
    */
    public function init() {		
    	if($this->_request->isXmlHttpRequest()) 
    		$this->_request->setParam("format","json");

        $contextSwitch = $this->_helper->contextSwitch();
        $contextSwitch->initContext();
    }
    

    /**
     * Método llamado previo a llamada de controlador
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->view->addScriptPath(APPLICATION_PATH.'/../library/');
    	// Proporcionar información de identidad a 
    	// variables en controlador y vista
    	$this->_identidad	   = $this->_getParam('identidad');
    	$this->view->nombreCompleto = $this->_identidad[0]["cNombre"]." ".$this->_identidad[0]["cApellido"];
    	$this->view->nameMenu       = $this->_identidad[0]["menu"];
        $mymodule = $this->_getParam('module', '');
        $mycontroller = $this->_getParam('controller', '');
        //Zend_Debug::dump($mymodule);die();
        $this->view->addScriptPath(APPLICATION_PATH.'/modules/'.$mymodule.'/views/scripts/'.$mycontroller."/");        
    }

    /**
     * Procedimiento posterior a llamado de controlador
     *
     * @return void
     */
    public function postDispatch(){
    	 //Agrega los stylesheet que se declaren en $_css
    	foreach ($this->_css as $item) $this->view->headLink()->appendStylesheet($item);
    	//Agrega los scripts que se declaren en $_js    	
    	foreach ($this->_js as $item) $this->view->headScript()->appendFile($item);
    	
    	//Agrega identidad al view solo si no trae el parametro "format"
    	if (isset($this->_identidad) && $this->_getParam("format","")=="") {
            $this->view->identidad = $this->_identidad;
            
	    	$this->view->menuTree = isset($this->menu)?$this->menu:array(
	    				"data"=>array(
	    					"label"=>'name', 
                        	 "identifier"=>'name',
                         	"items"=>array()
	    				)
	    		);
	        
        }
        
        // Parámetros de permisos ACL y Rol activo
        $this->view->acl  = $this->_getParam('acl', '');
    }
    
    /**
     * Transforma los resultados de un set de resultados JSON 
     * en el formato adecuado para ser procesados por un combo
     * 
     * @param string $data Datos JSON
     * @param label $label Etiqueta de combo <option>
     * @param label $identifier Valor asignado a etiqueta "value='...'"
     * @return string
     */
    public function jsonCombo($data, $label = 'label', $identifier = 'identifier')
    {
        
    }
}
