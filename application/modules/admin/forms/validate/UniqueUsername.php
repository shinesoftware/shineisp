<?php
class Default_UniqueUsername extends Zend_Validate_Abstract {
	const EMAIL_EXISTS = "already exists within our databases.";
	protected $model;
	
	public function __construct(Users $model) {
		$this->model = $model;
	}
	
	public function isValid($value, $context = null) {
		$this->_setValue ( $value );
		$user = $this->model->getByUsername($value);
		
		if($user){
			if(count($user)>0){
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