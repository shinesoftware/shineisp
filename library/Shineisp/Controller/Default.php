<?php
class Shineisp_Controller_Default extends Zend_Controller_Action {
	/*
	 * Common for the whole defaults controllers (frontend)
	 */
	
	public function init() {
		// Store logged ISP. I'm in the public area, se we use only the URL
		$ISP = Isp::findByUrl($_SERVER['HTTP_HOST']);
		Zend_Registry::set('ISP', $ISP);
    }	
}