<?php

/*
 * Shineisp_Banks_Blank_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_Blank_Gateway
* Purpose:  Standard payment module
* -------------------------------------------------------------
*/

class Shineisp_Banks_Blank_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {
	
	public function __construct($orderid) {
		parent::__construct ( $orderid );
		parent::setModule ( __CLASS__ );
	}
	
	/**
	 * CreateForm
	 * Create the bank form
	 * @return string
	 */
	public function CreateForm() {
		$order = self::getOrder ();
		$bank = self::getModule ();
		$translator = self::getTranslator ();

		// Check the bank account field value
		if (empty ( $bank ['account'] )) {
			return null;
		}
		
		if ($order) {
			$form = "";
			$url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];
			$item_name = $translator->translate ( "Order No." ) . " " . self::getOrderID() . " - " . date ( 'Y' );

			$custom = self::getOrderID() . "," . $bank ['bank_id'];
			if (! self::isHidden ()) {
				$form .= "<div class=\"bank_".$bank ['name']."\">" . $bank ['description'] . "</div>";	
			}
			
			$form .= '<form name="standard_payment" method="POST" action="' . $url . '">';
			$form .= '<input type="hidden" name="cmd" value="cart" />';
			
			if (! self::isHidden ()) {
				$form .= '<input type="image" src="/skins/default/base/images/banks/' . strtolower ( $bank ['name'] ) . '.gif" border="0" name="submit" title="' . $translator->translate ( 'Pay Now' ) . ": " . $bank ['name'] . '">';
			}
			
			$form .= '<input type="hidden" name="PAYER_FIRSTNAME" value="' . $order ['Customers'] ['firstname'] . '">';
			$form .= '<input type="hidden" name="PAYER_LASTNAME" value="' . $order ['Customers'] ['lastname'] . '">';
			$form .= '<input type="hidden" name="PAYER_EMAIL" value="' . $order ['Customers'] ['email'] . '">';
			
			$form .= '<input type="hidden" name="ACCOUNT" value="' . $bank ['account'] . '">';
			$form .= '<input type="hidden" name="AMOUNT" value="' . number_format ( $order ['grandtotal'], 2, '.', '' ) . '">';
			$form .= '<input type="hidden" name="ITEM_NAME" value="' . $item_name . '">';
			$form .= '<input type="hidden" name="ITEM_NUMBER" value="1">';
			$form .= '<input type="hidden" name="CUSTOM" value="' . $custom . '">';
			$form .= '<input type="hidden" name="URL_CALLBACK" value="' . self::getUrlCallback() . '">';
			$form .= '<input type="hidden" name="QUANTITY" value="1">';
			$form .= '<input type="hidden" name="URL_OK" value="' . self::getUrlOk() . '">';
			$form .= '<input type="hidden" name="URL_BAD" value="' . self::getUrlKo() . '">';
			$form .= '</form>';
			
			if (self::doRedirect()) {
				$form .= "<html><head></head><body>";
				$form .= $translator->translate ( 'You will be redirected to the secure bank website, please be patient.' );
				$form .= "<script type=\"text/javascript\">\ndocument.forms[0].submit();\n</script></body></html>";
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
		$bank = self::getModule ();
		
		if (! empty ( $response ['payment_status'] ) && $response ['payment_status'] == "Completed") {
			
			if (! empty ( $response ['item_number'] )) {
				$product = array ('id' => $response ['item_number'], 'name' => $response ['item_name'] );
				
				// Get the indexes of the order and bankid
				$indexes = trim ( $response ['custom'] );
				list ( $orderid, $bankid ) = explode ( ",", $indexes );
				
				if (is_numeric ( $orderid ) && is_numeric ( $bankid )) {
					$bank = Banks::find ( $bankid, null, true );
					
					// Replacing the comma with the dot in the amount value. 
					$amount = str_replace ( ",", ".", $response ['amount'] );
					$GatewayResponse ['id'] = $response ['thx_id'];
					$GatewayResponse ['item'] = $response ['item_name'];
					$GatewayResponse ['amount'] = $amount;
					$GatewayResponse ['bank_id'] = $bankid;
					$GatewayResponse ['status'] = $response ['payment_status'] == "Completed" ? 1 : 0;
				
				}
				
				// Complete the order with the payment details
				if (Orders::Complete ( $orderid )) {
					return $orderid;
				} else {
					return false;
				}
			}
		}
		return false;
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
