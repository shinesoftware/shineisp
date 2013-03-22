<?php

/*
 * Shineisp_Api_Panels_Interface
* -------------------------------------------------------------
* Type:     Interface class
* Name:     Shineisp_Api_Panels_Interface
* Purpose:  Control Panel Interface Class
* -------------------------------------------------------------
*/

interface Shineisp_Api_Panels_Interface {
	
	/**
	 * Enumerate all the control panel actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the control panel class 
	 * @access     public
	 */
	public Function getActions();
		
}