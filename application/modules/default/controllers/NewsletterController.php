<?php

class NewsletterController extends Shineisp_Controller_Default {
	
	public function preDispatch() {
	}
	
	public function optinAction() {
		$email = $this->getRequest ()->getParam('email');
		if(NewslettersSubscribers::optIn($email)){
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => "You have subscribed our newsletter correctly." ) );
		} else {
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => "There was a problem during the email registration, please check your email address." ) );
		}
	}
	
	public function optoutAction() {
		$email = $this->getRequest ()->getParam('id');
		if(NewslettersSubscribers::optOut($email)){
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => "You have unsubscribed our newsletter correctly." ) );
		} else {
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => "There was a problem during the email registration, please check your email address." ) );
		}
	}
}