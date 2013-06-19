<?php
class Shineisp_Controller_Default extends Shineisp_Controller_Common {
	/*
	 * Common for the whole defaults controllers (frontend)
	 */
	
	public function init() {
		
		// Store logged ISP. I'm in the public area, se we use only the URL
		$ISP = Isp::findByUrl($_SERVER['HTTP_HOST']);

		Shineisp_Registry::set('ISP', $ISP);
		
		// Load all the status in the registry
		$statusreg = Shineisp_Registry::get('Status');
		if(empty($statusreg)){
			$status = Statuses::getAll();
			Shineisp_Registry::set('Status', $status);
		}
		
		parent::init();
    }	
}