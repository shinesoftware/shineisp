<?php
/**
 * Doctrine ACL Database Adapter
 * @author shinesoftware
 *
 */
class Shineisp_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
{

	/**
	 * @var Doctrine_Table
	 */
	private $_table;

	/**
	 * The field name which will be the identifier (email...)
	 *
	 * @var string
	 */
	private $_identityCol;

	/**
	 * The field name which will be used for credentials (password...)
	 *
	 * @var string
	 */
	private $_credentialCol;

	/**
	 * Actual identity value (my_all_known_email)
	 *
	 * @var string
	 */
	private $_identity;

	/**
	 * Actual credential value (my_secret_password)
	 *
	 * @var string
	 */
	private $_credential;

	public function  __construct(Doctrine_Table $table, $identityCol, $credentialCol)
	{
		$this->_table = $table;
		$columnList = $this->_table->getColumnNames();
		
		//Check if the identity and credential are one of the column names...
		if (!in_array($identityCol, $columnList) || !in_array($credentialCol, $columnList)) {
			throw new Zend_Auth_Adapter_Exception("Invalid Column names are given as '{$identityCol}' and '{$credentialCol}'");
		}
		
		$this->_credentialCol = $credentialCol; //Assign the column names...
		$this->_identityCol = $identityCol;
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
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		$result = AdminUser::checkCredencials($this->_identity, $this->_credential);
		
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

