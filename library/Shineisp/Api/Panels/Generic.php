<?php

/**
 * Shineisp_Api_Registrars_Generic
 * @author Shine Software
 *
 */

class Shineisp_Api_Panel_Generic extends Shineisp_Api_Panels_Base implements Shineisp_Api_Panels_Interface {

	/**
	 * Enumerate all the control panel actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the registrar's class 
	 * @access     public
	 */
	public Function getActions(){
		return $this->actions;
	}	

	
}
	