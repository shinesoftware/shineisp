<?php

class Setup_CheckerController extends Zend_Controller_Action {
	
	/**
	 * Load all the resources
	 * @see Zend_Controller_Action::preDispatch()
	 */
	public function preDispatch() {
		$module = $this->getRequest ()->getModuleName ();
		$controller = $this->getRequest ()->getControllerName ();
		
		// Get all the resources set in the layout.xml file
		$css = Shineisp_Commons_Layout::getResources ( $module, $controller, "css", "base" );
		$js = Shineisp_Commons_Layout::getResources ( $module, $controller, "js", "base" );
		$template = Shineisp_Commons_Layout::getTemplate ( $module, $controller, "base" );
		
		$this->view->doctype ( 'XHTML1_TRANSITIONAL' );
		$this->view->addBasePath (  PUBLIC_PATH . "/skins/setup/base/"  );
		$this->view->headTitle ("ShineISP Setup");
		$this->view->headMeta ()->setName ( 'robots', "INDEX, FOLLOW");
		$this->view->headMeta ()->setName ( 'author', "Shine Software Company" );
		$this->view->headMeta ()->setName ( 'keywords', "shine software, isp software" );
		$this->view->headMeta ()->setName ( 'description', "This is the ShineISP setup configuration" );
		$this->view->headLink ()->headLink(array('rel' => 'icon', 'type' => 'image/x-icon', 'href' => "/skins/$module/base/images/favicon.ico"));
		$this->view->headTitle ()->setSeparator(' - ');
		
		foreach ($js as $item){
			$this->view->headScript ()->appendFile ($item);
		}
		
		foreach ( $css as $item ) {
			$this->view->headLink ()->appendStylesheet ( $item );
		}
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		
		$session = new Zend_Session_Namespace ( 'setup' );
		Languages::setDefaultLanguage (PUBLIC_PATH, $session->locale );
	}
	
	/**
	 * Load all the resources
	 * @see Zend_Controller_Action::preDispatch()
	 */
	public function indexAction() {
		$session = new Zend_Session_Namespace ( 'setup' );
		$session->permissions = false;
		
		$dirs = array();
		$errors = array();
		$applicationfolder = true;
		$publicfolder = true;
		
		$dirs[] = PUBLIC_PATH;
		$dirs[] = PUBLIC_PATH . "/media/";
		$dirs[] = PUBLIC_PATH . "/documents/";
		$dirs[] = PUBLIC_PATH . "/logs/";
		$dirs[] = PUBLIC_PATH . "/imports/";
		$dirs[] = PUBLIC_PATH . "/tmp/";
		$dirs[] = PUBLIC_PATH . "/cache/";
		$dirs[] = APPLICATION_PATH . "/configs/";
		$dirs[] = APPLICATION_PATH . "/configs/data/";
		$dirs[] = APPLICATION_PATH . "/configs/data/sql/";
		
		// create all the directories
		foreach ($dirs as $dir){
			
			if(!is_dir($dir)){
				if(!@mkdir($dir)){
					$errors[] = $dir;
				}
			}else{
				if(!Shineisp_Commons_Utilities::isWritable($dir)){
					$errors[] = $dir;
				}
			}
		}
		
		// check the public directory
		if(Shineisp_Commons_Utilities::isWritable(PUBLIC_PATH)){
			$this->view->public_folder = true;
		}else{
			$this->view->public_folder = false;
			$publicfolder = false;
		}
		
		// check the application config data directory
		if(Shineisp_Commons_Utilities::isWritable(APPLICATION_PATH . "/configs/data/")){
			$this->view->application_folder = true;
		}else{
			$this->view->application_folder = false;
			$applicationfolder = false;
		}
		
		$this->view->errors = $errors;
		
		if($publicfolder & $applicationfolder && count($errors) == 0){
			$session->permissions = true;
			$this->_helper->redirector ( 'index', 'localization', 'setup');
		}else{
			return $this->_helper->viewRenderer ( 'index' );
		}
	}
	
}