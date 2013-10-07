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
	
	public function payments() {
		return $this;
	}
	
	/**
	 * Create the payment module 
	 * @param $id 
	 */
	public function getPaymentsForm($orderid=NULL) {
		
		$payments = array ();
		$banks = Banks::findAllActive ( "classname", true );
		$translator = Shineisp_Registry::get ( 'Zend_Translate' );
		
		if (! empty ( $banks )) {
			foreach ( $banks as $bank ) {
				if (! empty ( $bank ['classname'] ) && class_exists ( $bank ['classname'] )) {
					
					if (class_exists ( $bank ['classname'] )) {
						$class = $bank ['classname'];
						$payment = new $class ( $orderid );
						$payments [] = $payment->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $bank ['classname'] ) )
												->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/" . md5 ( $bank ['classname'] ) )
												->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $bank ['classname'] )  )
												->setRedirect(false)
												->setFormHidden(false)
												->CreateForm ();;
					}
					
				}
			}
		}
		
		$this->view->payments = $payments;
		
		// Path of the template
		return $this->view->render ( 'partials/payments.phtml' );
	}

}
