<?php

class Setup_DatabaseController extends Zend_Controller_Action {
	
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
		
		if(empty($session->permissions) || $session->permissions == false){
			$this->_helper->redirector ( 'index', 'checker', 'setup');
		}
		
	}
	
	/**
	 * Step 1: Create the main form
	 */
	public function indexAction() {
		$session = new Zend_Session_Namespace ( 'setup' );
		if(empty($session->terms)){
			$this->_helper->redirector ( 'index', 'localization', 'setup', array ('error' => "You have to accept the license terms in order to install the software") );
		}
		$form = new Setup_Form_DatabaseForm( array ('action' => '/setup/database/save', 'method' => 'post') );
		
		$this->view->error = $this->getParam('error');
		$this->view->form = $form;
		return $this->_helper->viewRenderer ( 'form' );
	}

	/**
	 * Step 1.2: Check the db connection
	 */
	public function chkdbAction() {
		$request = $this->getRequest ();
		try{
			$hostname = $request->getParam('hostname');
			$username = $request->getParam('username');
			$password = $request->getParam('password');
			$database = $request->getParam('database');
			
			$conn = Shineisp_Commons_Utilities::chkdatabase($username, $password, $hostname, $database);
			if ($conn === TRUE) {
				echo 'Database Connection Success';
			}else{
				echo $conn;
			}
		}catch (Exception $e){
			echo $e->getMessage();
		}
		die();
	}
	
	/**
	 * Step 2: Save the data
	 */
	public function saveAction() {
		$session = new Zend_Session_Namespace ( 'setup' );
		$request = $this->getRequest ();
		$form = new Setup_Form_DatabaseForm( array ('action' => '/setup/database/save', 'method' => 'post' ) );
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index', 'index', 'setup' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the values posted
			$params = $form->getValues ();
			
			$conn = Shineisp_Commons_Utilities::chkdatabase($params['username'], $params['password'], $params['hostname'], $params['database']);
			if ($conn !== TRUE) {
				$this->_helper->redirector ( 'index', 'database', 'setup', array ('error' => $conn) );
			}else{
				$session->db->hostname = $params['hostname'];
				$session->db->database = $params['database'];
				$session->db->username = $params['username'];
				$session->db->password = $params['password'];
				$this->_helper->redirector ( 'index', 'preferences', 'setup' );
			}
			
		}
		$this->view->form = $form;
		return $this->_helper->viewRenderer ( 'form' );
	}
	
}