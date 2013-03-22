<?php

/*
 * Shineisp_Banks_Interface
* -------------------------------------------------------------
* Type:     Interface class
* Name:     Shineisp_Banks_Interface
* Purpose:  Banks Interface Class
* -------------------------------------------------------------
*/

interface Shineisp_Banks_Interface {
	
	/**
	 * CreateForm
	 * Create the bank form module 
	 */
	public function CreateForm();
	
	/**
	 * Response
	 * Handle the response from the bank
	 * @param array $response
	 */
	public function Response($response);
	
	/**
	 * CallBack
	 * This method can be called by the bank after the payment
	 * @param array $response
	 */
	public function CallBack($response);
    
}