<?php
/**
 * Doctrine ACL Database Adapter
 * @author guest.it srl
 *
 */
class Shineisp_Auth_Adapter_Secretkey implements Zend_Auth_Adapter_Interface
{

	/**
	 * @var Doctrine_Table
	 */
	private $_table;

	/**
	 * The field name which will be the identifier
	 *
	 * @var string
	 */
	private $_fieldName;

	/**
	 * Actual identity value (my_all_known_sha1email)
	 *
	 * @var string
	 */
	private $_identity;

	/**
	 * Actual identity (isp, operator)
	 *
	 * @var string
	 */
	private $_type;

	/**
	 * Actual credential value (my_secret_password)
	 *
	 * @var string
	 */
	private $_credential;

	/**
	 * @return the $_type
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * @param string $_type
	 */
	public function setType($_type) {
		$this->_type = $_type;
	}

	public function  __construct(Doctrine_Table $table, $fieldName)
	{
		$this->_table = $table;
		$columnList = $this->_table->getColumnNames();
		
		//Check if the identity and credential are one of the column names...
		if (!in_array($fieldName, $columnList) ) {
			throw new Zend_Auth_Adapter_Exception("Invalid Column names are given as '{$fieldName}'");
		}
		
		$this->_fieldName = $fieldName;
	}

	/**
	 * @param string $i
	 */
	public function setIdentity($i)
	{
		$this->_identity = $i;
	}

	/**
	 * @param string $c
	 */
	public function setCredential($c)
	{
		$this->_credential = $c;
	}

	/**
	 * @param string $type [isp, operator] 
	 * @return Zend_Auth_Result
	 */
	public function authenticate($type="isp")
	{
		if($this->_type == "operator"){
			$result = AdminUser::checkOperatorCredencialsBySecretKey($this->_identity);
		}else{
			$result = AdminUser::checkIspCredencialsBySecretKey($this->_identity);
		}
		
		if(is_array($result)){
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $result);
		}elseif($result === false){
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
		}elseif(is_null($result)){
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null);
		}else{
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
		}
		
	}
}

