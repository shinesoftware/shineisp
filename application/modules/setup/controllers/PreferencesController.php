<?php

class Setup_PreferencesController extends Zend_Controller_Action {
	
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
		
		if(empty($session->db)){
			$this->_helper->redirector ( 'index', 'database', 'setup');
		}
	}
	
	/**
	 * Step 1: Create the main form
	 */
	public function indexAction() {
		$form = new Setup_Form_PreferencesForm( array ('action' => '/setup/preferences/save', 'method' => 'post') );
		$this->view->error = $this->getParam('error');
		$this->view->form = $form;
		return $this->_helper->viewRenderer ( 'form' );
	}

	/**
	 * Step 2: Save the data
	 */
	public function saveAction() {
		$session = new Zend_Session_Namespace ( 'setup' );
		$request = $this->getRequest ();
		$form = new Setup_Form_PreferencesForm( array ('action' => '/setup/preferences/save', 'method' => 'post' ) );
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index', 'index', 'setup' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the values posted
			$params = $form->getValues ();
			
			$session->preferences = $params;
			
			// Create the main xml config file
			if(Settings::saveConfig($session->db)){
				if(Settings::createDb($params['sampledata'])){

					// Get the default ISP company
					$ispId = Isp::getActiveISPID();
					
					// Save the company information
					Isp::saveAll($params, $ispId);
					
					// Adding the user as administrator 
					AdminUser::addUser($params['firstname'], $params['lastname'], $params['email'], $params['password'], $ispId);
					
					// Redirect the user to the homepage
					$this->_helper->redirector ( 'index', 'summary', 'setup');
				}
			}
			
			// There was a problem the setup restarts
			$this->_helper->redirector ( 'index', 'index', 'setup');
			
		}
		$this->view->form = $form;
		return $this->_helper->viewRenderer ( 'form' );
	}
}