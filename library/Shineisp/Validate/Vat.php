<?php
class Shineisp_Validate_Vat extends Zend_Validate_Abstract {
	
	const INVALIDVAT = 'The VAT code is wrong.';
	protected $_messageTemplates = array (self::INVALIDVAT => "The VAT code is wrong." );
	
	public function isValid($value) {
		if (! empty ( $value )) {
			
			// Check the VAT using the EU service
			$endpoint = "http://isvat.appspot.com";
			
			// Get the VAT code for instance IT0000000000000
			$country = substr ( $value, 0, 2 );
			$number = substr ( $value, 2, strlen ( $value ) );
			
			// Create the call parameter
			$url = "$endpoint/$country/$number/?callback=";
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// Get the output
			$output = curl_exec ( $ch );
			curl_close ( $ch );
			
			// Check the returned string
			if($output == "(true)"){
				return true;
			}else{
				$this->_error ( self::INVALIDVAT );
				return false;
			}
		}
	}
}
