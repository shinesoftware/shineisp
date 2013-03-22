<?php
/**
 *
 * @version 
 */
/**
 * Payments helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Payments extends Zend_View_Helper_Abstract {
	/**
	 * 
	 * Enter description here ...
	 */
	public function payments() {
		return $this;
	}
	
	/**
	 * Create the payment module for IWBank
	 * @param $id 
	 */
	public function getPaymentsForm($type, $id) {
		
		$payments = array ();
		$banks = Banks::findAllActive ( "classname", true );
		
		if (! empty ( $banks )) {
			foreach ( $banks as $bank ) {
				if (! empty ( $bank ['classname'] ) && class_exists ( $bank ['classname'] )) {
					if (class_exists ( $bank ['classname'] )) {
						
						$class = $bank ['classname'];
						$payment = new $class ( $id );
						$payment->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $bank ['classname'] ) );
						$payment->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $bank ['classname'] )  );
						$payment->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $bank ['classname'] )  );
						
						$payments [] = $payment->CreateForm ();
					
					}
				}
			}
		}
		
		$this->view->payments = $payments;
		
		// Path of the template
		return $this->view->render ( 'partials/payments.phtml' );
	}

}
