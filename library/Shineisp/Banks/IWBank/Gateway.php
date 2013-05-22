<?php

/*
 * Shineisp_Banks_IWBank_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_IWBank_Gateway
* Purpose:  Manage the communications with the IWBANK
* -------------------------------------------------------------
*/

class Shineisp_Banks_IWBank_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {
	
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
			$item_name = $translator->translate ( "Order no." ) . " " . $order['order_number'] . " - " . date ( 'Y' );
			
			$custom = self::getOrderID ();
			if (! self::isHidden ()) {
				$form .= "<div class=\"bank_" . $bank ['name'] . "\">" . $bank ['description'] . "</div>";
			}
			
			$form .= '<form id="iwsmile" method="POST" action="' . $url . '">';
			$form .= '<input type="hidden" name="cmd" value="cart" />';
			
			if (! self::isHidden ()) {
				$form .= '<input class="blue-button" type="submit" name="submit" value="' . $translator->translate ( 'Pay Now' ) . '">';
			}
			
			$form .= '<input type="hidden" name="PAYER_FIRSTNAME" value="' . $order ['Customers'] ['firstname'] . '">';
			$form .= '<input type="hidden" name="PAYER_LASTNAME" value="' . $order ['Customers'] ['lastname'] . '">';
			$form .= '<input type="hidden" name="PAYER_EMAIL" value="' . $order ['Customers'] ['email'] . '">';
			
			$form .= '<input type="hidden" name="ACCOUNT" value="' . $bank ['account'] . '">';
			$form .= '<input type="hidden" name="AMOUNT" value="' . number_format ( $order ['grandtotal'], 2, '.', '' ) . '">';
			$form .= '<input type="hidden" name="ITEM_NAME" value="' . $item_name . '">';
			$form .= '<input type="hidden" name="ITEM_NUMBER" value="1">';
			$form .= '<input type="hidden" name="CUSTOM" value="' . $custom . '">';
			$form .= '<input type="hidden" name="URL_CALLBACK" value="' . self::getUrlCallback () . '">';
			$form .= '<input type="hidden" name="QUANTITY" value="1">';
			$form .= '<input type="hidden" name="URL_OK" value="' . self::getUrlOk () . '">';
			$form .= '<input type="hidden" name="URL_BAD" value="' . self::getUrlKo () . '">';
			$form .= '<input type="hidden" name="FLAG_ONLY_IWS" value="0">';
			$form .= '</form>';
			
			if (self::doRedirect ()) {
				$form .= "<html><head></head><body>";
				$form .= $translator->translate ( 'You will be redirected to the bank secure website, please be patience.' );
				$form .= "<script type=\"text/javascript\">\n$('#iwsmile').submit();\n</script></body></html>";
			}
			
			return $form;
		}

        return "ERRORE";
	
	}
	
	/**
	 * Response
	 * Create the Order, Invoice and send an email to the customer
	 * @param $response from the Gateway Server
	 * @return order_id or false
	 */
	public function Response($response) {
		$bank = self::getModule ();
		$bankid = $bank ['bank_id'];
		
		if (! empty ( $response ['payment_status'] ) && $response ['payment_status'] == "Completed") {
			
			if (! empty ( $response ['item_number'] )) {
				
				// Get the indexes of the order 
				$orderid = trim ( $response ['custom'] );
				
				if (is_numeric ( $orderid ) && is_numeric ( $bankid )) {
					
					// Replacing the comma with the dot in the amount value. 
					$amount = str_replace ( ",", ".", $response ['amount'] );
					
					Shineisp_Commons_Utilities::logs ( "Adding the payment information: " . $response ['thx_id'], "iwbank.log" );
					$payment = Payments::addpayment($orderid, $response ['thx_id'], $bankid, 0, $amount);
					
					Shineisp_Commons_Utilities::logs ( "Set the order in the processing mode", "iwbank.log" );
					Orders::set_status ( $orderid, Statuses::id("paid", "orders") ); // Paid
					OrdersItems::set_status ( $orderid, Statuses::id("paid", "orders") ); // Paid
					
					return $orderid;
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
		Shineisp_Commons_Utilities::logs ( "Start callback", "iwbank.log" );
		
		// Get the orderid back from the bank post variables
		$orderid = trim ( $response ['custom'] );
		
		$ret = "";
		$payer_id = $response["payer_id"];
		$thx_id = $response["thx_id"];
		$verify_sign = $response["verify_sign"];
		$amount = $response["amount"];
		
		$code = '2E121E96A508BDBA39782E43D2ACC12274A991A7EDE25502F42D99542D26CF3D';
		
		//Inserire il merchant_key indicato all'interno del sito IWSMILE su POS VIRTUALE/Configurazione/Notifica Pagamento.
		$str = "thx_id=".$thx_id."&amount=".$amount."&verify_sign=".$verify_sign;
		$str .= "&payer_id=".$payer_id;
		$str .= "&merchant_key=".$code;
		
		Shineisp_Commons_Utilities::logs ( "Callback parameters: $str", "iwbank.log" );
		
		$url = "https://checkout.iwsmile.it/Pagamenti/trx.check";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		$content = curl_exec ($ch);
		$c_error = "NONE";
		
		$ret = 'NON DISPONIBILE';
		
		if (curl_errno($ch) != 0) {
			$c_error = curl_error($ch);
		}else{
			if(strstr($content,"OK")) {
				$ret = "VERIFICATO";
				
				Shineisp_Commons_Utilities::logs ( "Order Completed: $orderid", "iwbank.log" );
				
				// Complete the order
				Orders::Complete ( $orderid, true ); // Complete the order information and it executes all the tasks to do
				
				Shineisp_Commons_Utilities::logs ( "Confirm the payment for the order: $orderid", "iwbank.log" );
				Payments::confirm ( $orderid ); // Set the payment confirm 
				
			} elseif(strstr($content,"KO")) {
				// Order not verified
				$ret='NON VERIFICATO';
			} elseif(strstr($content,"IR")) {
				// Request is not valid 
				$ret='RICHIESTA NON VALIDA';
			} elseif(strstr($content,"EX")) {
				// Request expired
				$ret='RICHIESTA SCADUTA';
			}
		}
		curl_close ($ch);
		
		Shineisp_Commons_Utilities::logs ( "Callback Payment Result: $ret", "iwbank.log" );
		Shineisp_Commons_Utilities::logs ( "End callback", "iwbank.log" );
		return true;
	}
}