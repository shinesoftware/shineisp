<?php

/**
 * Shineisp_Plugins_Registrars_Generic
 * @author Shine Software
 *
 */

class Shineisp_Plugins_Panel_Generic extends Shineisp_Plugins_Panels_Base implements Shineisp_Plugins_Panels_Interface {

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
	