<?php
class Shineisp_Controller_Admin extends Shineisp_Controller_Common {
	/*
	 * Common for the whole admin controllers
	*/
	
	public function init() {
		// Get authenticated user
		$auth = Zend_Auth::getInstance()->getIdentity();

		// Store logged ISP. I'm inside admin, se we use only the logged user
		if ( isset($auth['isp_id']) ) {
			$isp_id = intval($auth['isp_id']);
			
			$ISP = new Isp();
			Shineisp_Registry::set('ISP', $ISP->find($isp_id));
		}
		
		// Load all the status in the registry
		$statusreg = Shineisp_Registry::get('Status');
		if(empty($statusreg)){
			$status = Statuses::getAll();
			Shineisp_Registry::set('Status', $status);
		}
		

		parent::init();
    }	
    
    public function postDispatch(){
    	$controller_name = $this->getRequest()->getControllerName();
    	$controller_action = $this->getRequest()->getActionName();
    	
    	$controller_name = ucwords($controller_name);
    	$controller_action = ucwords($controller_action);
    	
    	$controller_name = Shineisp_Registry::getInstance ()->Zend_Translate->translate($controller_name);
    	$controller_action = Shineisp_Registry::getInstance ()->Zend_Translate->translate($controller_action);
    	
		$this->view->headTitle()->append($controller_name);    	
		$this->view->headTitle()->append($controller_action);    	
    }
}