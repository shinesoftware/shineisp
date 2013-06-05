<?php

/*
 * Shineisp_Banks_BankTransfer_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_IWBank_Gateway
* Purpose:  Manage the communications with the IWBANK
* -------------------------------------------------------------
*/

class Shineisp_Banks_BankTransfer_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {
	
	/**
	 * CreateForm
	 * @return string
	 */
	public function CreateForm() {
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$bank = Banks::findbyClassname ( __CLASS__ );
		
		try {
			$form = "<h2>" . $bank ['name'] . "</h2><p>" . $bank ['description'] . "</p>";	
			return $form;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	/**
	 * Response
	 * Create the Order, Invoice and send an email to the customer
	 * @param $response from the Gateway Server
	 * @return order_id or false
	 */
	public function Response($response) {
		
	}
	
	/**
	 * CallBack
	 * This function is called by the bank server in order to confirm the transaction previously executed
	 * @param $response from the Gateway Server
	 * @return boolean
	 */
	public function CallBack($response) {
	
	}
}
