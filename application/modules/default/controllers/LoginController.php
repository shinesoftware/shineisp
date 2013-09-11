<?php

class LoginController extends Shineisp_Controller_Default {

	public function indexAction() {
		$redir = "";
		$params = $this->getRequest()->getParams();
	
		// If exist a redirect request the client will be redirect after the login action
		if(!empty($params['redir'])){
			$this->view->form = new Default_Form_LoginForm ( array ('action' => '/customer/signin/redir/' . $params['redir'], 'method' => 'post' ) );
		}else{
			$this->view->form = new Default_Form_LoginForm ( array ('action' => '/customer/signin/', 'method' => 'post' ) );
		}
		$this->_helper->viewRenderer ( 'login' );
	}
	
	public function noauthAction() {
		$this->_helper->viewRenderer ( 'noauth' );
	}
}