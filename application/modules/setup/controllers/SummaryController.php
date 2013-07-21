<?php

class Setup_SummaryController extends Zend_Controller_Action {
	
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
			$this->view->headScript ()->appendFile ($item['resource']);
		}
		
		foreach ( $css as $item ) {
			$this->view->headLink ()->appendStylesheet ( $item['resource'] );
		}
		
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		
		$session = new Zend_Session_Namespace ( 'setup' );
		Languages::setDefaultLanguage (PUBLIC_PATH, $session->locale );
		
		if(empty($session->db)){
			$this->_helper->redirector ( 'index', 'database', 'setup');
		}
		
		if(empty($session->permissions) || $session->permissions == false){
			$this->_helper->redirector ( 'index', 'checker', 'setup');
		}
	}
	
	/**
	 * Setup Summary
	 */
	public function indexAction() {
		$session = new Zend_Session_Namespace ( 'setup' );
		$this->view->summary = $session->preferences;
		return $this->_helper->viewRenderer ( 'form' );
	}
}