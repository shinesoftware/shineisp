<?php
class Default_UniqueEmail extends Zend_Validate_Abstract {
	const EMAIL_EXISTS = "";
	protected $model;
	
	protected $_messageTemplates = array (self::EMAIL_EXISTS => 'Email "%value%" exists. Please login.' );
	
	public function __construct(Customers $model) {
		$this->model = $model;
	}
	
	public function isValid($value, $context = null) {
		$this->_setValue ( $value );
		$retval = $this->model->findbyemail($value);
		
		if($retval){
			$customer = $retval->toArray();
			if(count($customer)>0){
				$this->_error ( self::EMAIL_EXISTS );
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
		return false;
	}
}