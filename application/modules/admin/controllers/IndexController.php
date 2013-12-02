<?php

/**
 * IndexController
 * 
 * @author 
 * @version 
 */

class Admin_IndexController extends Shineisp_Controller_Admin {
	
    
    public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( 'blank' );
    }	
    
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$auth = Zend_Auth::getInstance ();
		
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		
		if ($auth->hasIdentity ()) {
			$this->view->show_dashboard = true;
			$this->view->user = $auth->getIdentity();
			$this->getHelper ( 'layout' )->setLayout ( '1column' );
			
			$this->view->placeholder ( "admin_endbody" )->append ( $this->view->partial ( 'partials/graphs.phtml', array ('type' => 'year', 'div_element' => 'yeargraph', 'data' => Orders::MorrisGraphJs(date('Y')) ) ) );
			$this->view->placeholder ( "admin_endbody" )->append ( $this->view->partial ( 'partials/graphs.phtml', array ('type' => 'month', 'div_element' => 'monthgraph', 'data' => Orders::MorrisGraphJs(date('Y'), 'month') ) ) );
			$this->view->placeholder ( "admin_endbody" )->append ( $this->view->partial ( 'partials/graphs.phtml', array ('type' => 'quarter', 'div_element' => 'quartergraph', 'data' => Orders::MorrisGraphJs(date('Y'), 'quarter') ) ) );
			
		} else {
			$this->_helper->redirector ( 'index', 'login', 'admin' ); // back to login page
		}
	}
	
	
}
