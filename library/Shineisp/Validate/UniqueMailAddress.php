<?php
class Shineisp_Validate_UniqueMailAddress extends Zend_Validate_Abstract {
	const EXISTS = "already exists within our databases.";
	protected $model;
	
	public function __construct(Mails $model) {
		$this->model = $model;
	}
	
	public function isValid($value, $context = null) {
		$this->_setValue ( $value );
		$user = $this->model->getByUsername($value);
		
		if($user){
			if(count($user)>0){
				$this->_error ( self::EXISTS );
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