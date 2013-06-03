<?php
class Shineisp_Controller_Admin extends Zend_Controller_Action {
	public function init() {
		/*
		 * Common for the whole admin controllers
		 */
		
		
		// Get authenticated user
		$auth = Zend_Auth::getInstance()->getIdentity();

		// Store logged ISP. I'm inside admin, se we use only the logged user
		if ( isset($auth['isp_id']) ) {
			$isp_id = intval($auth['isp_id']);
			
			$ISP = new ISP();
			Zend_Registry::set('ISP', $ISP->find($isp_id));
		}
		
		
		
		
		
		
		
		
		
		
		
		
		

    }	
}