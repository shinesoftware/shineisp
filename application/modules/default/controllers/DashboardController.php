<?php

class DashboardController extends Shineisp_Controller_Default {
    
    public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( '1column' );
    }
    
    public function indexAction() {
    	$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($NS->customer)) {
            $this->view->dashboard = true;
        } else {
            $this->view->dashboard = false;
        }
        $this->view->mex = $this->getRequest ()->getParam ( 'mex' );
        $this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->customer = $NS->customer;
		
		$this->view->headTitle()->prepend ( $NS->customer ['lastname'] . " " . $NS->customer ['firstname'] );
		
        // Clean up the tmp folder
        Shineisp_Commons_Utilities::cleantmp();
    }
}