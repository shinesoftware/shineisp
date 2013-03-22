<?php
class Shineisp_Validate_Email extends Zend_Validate_Abstract {
	
	const ALREADYUSED = 'The email is already registered in our database.';
	protected $_messageTemplates = array (self::ALREADYUSED => "Email is already registered. Please login or if you have forgotten your password you can reset it." );
	
	public function isValid($value) {
		$customer = Customers::getCustomerbyEmail ( $value );
		if (isset ( $customer [0] )) {
			$this->_error ( self::ALREADYUSED );
			return false;
		}else{
			return true;
		}
	}
}
