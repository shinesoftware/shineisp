<?php
/**
 * Doctrine ACL Database Adapter
 * @author shinesoftware
 *
 */
class Shineisp_Auth_Adapter_Md5 implements Zend_Auth_Adapter_Interface
{

	/**
	 * @var Doctrine_Table
	 */
	private $_table;

	/**
	 * The field name which will be the identifier (md5email...)
	 *
	 * @var string
	 */
	private $_md5email;

	/**
	 * Actual identity value (my_all_known_md5email)
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

	public function  __construct(Doctrine_Table $table, $md5email)
	{
		$this->_table = $table;
		$columnList = $this->_table->getColumnNames();
		
		//Check if the identity and credential are one of the column names...
		if (!in_array($md5email, $columnList) ) {
			throw new Zend_Auth_Adapter_Exception("Invalid Column names are given as '{$md5email}'");
		}
		
		$this->_md5email = $md5email;
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
			$result = AdminUser::checkMD5OperatorCredencialsByPassCode($this->_identity);
		}else{
			$result = AdminUser::checkMD5IspCredencialsByPassCode($this->_identity);
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

