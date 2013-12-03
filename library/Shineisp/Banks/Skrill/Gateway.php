<?php

/*
 * Shineisp_Banks_Skrill_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_Skrill_Gateway
* Purpose:  Standard payment module
* -------------------------------------------------------------
*/

class Shineisp_Banks_Skrill_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {
	
	public function __construct($orderid) {
		parent::__construct ( $orderid );
		parent::setModule ( __CLASS__ );
	}
	
	/**
	 * CreateForm
	 * Create the skrill form
	 * @return string
	 */
	public function CreateForm() {
		$order = self::getOrder ();
		$bank = self::getModule ();
		$isp = Isp::getActiveISP();
		$translator = self::getTranslator ();
		
		// Check the skrill account field value
		if (empty ( $bank ['account'] )) {
			return null;
		}
		
		if ($order) {
			
			$form = "";
			$url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];

			if (! self::isHidden ()) {
				$form .= "<div class=\"skrill_".$bank ['name']."\">" . $bank ['description'] . "</div>";	
			}
			
			$form .= '<form id="skrill" method="POST" action="' . $url . '">';
			$form .= '<input type="hidden" name="recipient_description" value="' . $isp ['company'] . '"/>';
			$form .= '<input type="hidden" name="firstname" value="' . $order ['Customers'] ['firstname'] . '"/>';
			$form .= '<input type="hidden" name="lastname" value="' . $order ['Customers'] ['lastname'] . '"/>';
			$form .= '<input type="hidden" name="pay_from_email" value="' . $order ['Customers'] ['email'] . '"/>';
			$form .= '<input type="hidden" name="pay_to_email" value="' . $bank ['account'] . '"/>';
			$form .= '<input type="hidden" name="language" value="'.strtoupper($translator->getAdapter()->getLocale()).'"/>';
			$form .= '<input type="hidden" name="amount" value="' . number_format ( $order ['grandtotal'], 2, '.', '' ) . '"/>';
			$form .= '<input type="hidden" name="currency" value="EUR"/>';
			$form .= '<input type="hidden" name="transaction_id" value="' . self::getOrderID() .'"/>';
			$form .= '<input type="hidden" name="detail1_description" value="' . $translator->translate('Order No.') . '"/>';
			$form .= '<input type="hidden" name="detail1_text" value="' . self::getOrderID() . " - " . date ( 'Y' ) . '"/>';
			$form .= '<input type="hidden" name="return_url" value="' . self::getUrlOk() . '"/>';
			$form .= '<input type="hidden" name="cancel_url" value="' . self::getUrlKo() . '"/>';
			$form .= '<input type="hidden" name="status_url" value="' . self::getUrlCallback() . '"/>';
			
			if (!self::doRedirect ()) {
				$form .= '<input class="btn btn-success" type="submit" name="submit" value="' . $translator->translate ( 'Pay Now' ) . '">';
			}			
			$form .= '</form>';
			
			if (self::doRedirect ()) {
				$form .= $translator->translate ( 'You will be redirected to the secure bank website, please be patient.' );
				$form .= "<script type=\"text/javascript\">\nsetTimeout(function () {\n$('#skrill').submit();\n}, 3000);\n</script>";
			}
			
			return array('name' => $bank ['name'], 'description' => $bank ['description'], 'html' => $form);
		}
	
	}
	
	/**
	 * Response
	 * Create the Order, Invoice and send an email to the customer
	 * @param $response from the Gateway Server
	 * @return order_id or false
	 */
	public function Response($response) {
		
		if (! empty ( $response ['status'] ) && $response ['status'] == 2) {
			
			if (! empty ( $response ['item_number'] )) {
				
				$product = array ('id' => $response ['item_number'], 'name' => $response ['item_name'] );
				
				if(!empty($response ['transaction_id'])){

					$orderid = trim ( $response ['transaction_id'] );
					
					// Complete the order with the payment details
					if (Orders::Complete ( $orderid )) {
						return $orderid;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * CallBack
	 * This function is called by the skrill server in order to confirm the transaction previously executed
	 * @param $response from the Gateway Server
	 * @return boolean
	 */
	public function CallBack($response) {
		
		$concatFields = $response['merchant_id'].$response['transaction_id']
												.strtoupper(md5('Paste your secret word here'))
												.$response['mb_amount']
												.$response['mb_currency']
												.$response['status'];
		
		$MBEmail = 'merchant-email@example.com';
			
		// Ensure the signature is valid, the status code == 2,
		// and that the money is going to you
		if (strtoupper(md5($concatFields)) == $response['md5sig']
				&& $response['status'] == 2
				&& $response['pay_to_email'] == $MBEmail)
		{
			// Valid transaction.
				
			//TODO: generate the product keys and
			//      send them to your customer.
		}else{
			// Invalid transaction. Bail out
			exit;
		}
	}
}
