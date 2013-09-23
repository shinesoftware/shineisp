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
		} else {
			$this->_helper->redirector ( 'index', 'login', 'admin' ); // back to login page
		}
	}
	
	
}
