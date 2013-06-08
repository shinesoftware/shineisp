<?php

/*
 * Shineisp_Plugins_Panels_Interface
* -------------------------------------------------------------
* Type:     Interface class
* Name:     Shineisp_Plugins_Panels_Interface
* Purpose:  Control Panel Interface Class
* -------------------------------------------------------------
*/

interface Shineisp_Plugins_Panels_Interface {
	
	/**
	 * Enumerate all the control panel actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the control panel class 
	 * @access     public
	 */
	public Function getActions();
	
	public Function create_client();
	public Function create_database();
	public Function create_ftp();
	public Function create_mail();
	public Function create_website();
	
		
}