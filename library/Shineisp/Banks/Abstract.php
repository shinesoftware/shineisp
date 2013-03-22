<?php


/*
 * Shineisp_Banks_Abstract
* -------------------------------------------------------------
* Type:     Abstract class
* Name:     Shineisp_Banks_Abstract
* Purpose:  Banks Abstract Class
* -------------------------------------------------------------
*/

abstract class Shineisp_Banks_Abstract {
	private $orderid;
	private $order;
	private $module;
	private $translator;
	private $redirect;
	private $urlok;
	private $urlko;
	private $urlcallback;
	private $hiddenform = false;

	/**
	 * __construct
	 * @param integer $orderid
	 * @return array
	 */
	public function __construct($orderid){
		$order = Orders::getAllInfo($orderid, 'o.*, oi.*, c.*', true);
		$this->orderid = $orderid;
		if(!empty($order[0])){
			$this->order = $order[0];	
		}
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	/**
	 * setModule
	 * Set the module configuration
	 * @param string $classname
	 */
	public function setModule($classname){
		$this->module = Banks::findbyClassname ( $classname );
	}
	
	/**
	 * getOrderID
	 * get the order id set for the gateway payment module
	 * @return integer
	 */
	public function getOrderID(){
		return $this->orderid;
	}
	
	/**
	 * setFormHidden
	 * Hide all the images in order to create a trasparent form
	 * @param boolean $value
	 */
	public function setFormHidden($value){
		$this->hiddenform = $value;
	}
	
	/**
	 * setRedirect
	 * Redirect the user automatically
	 * @param boolean $value
	 */
	public function setRedirect($value){
		$this->redirect = $value;
	}
	
	/**
	 * setUrlOK
	 * Set the url for the success of the transaction
	 */
	public function setUrlOK($value){
		$this->urlok = $value;
	}
	
	/**
	 * setUrlKO
	 * Set the url for the UNsuccessful of the transaction
	 */
	public function setUrlKo($value){
		$this->urlko = $value;
	}
	
	/**
	 * setUrlOK
	 * Set the url for the success of the transaction
	 */
	public function setUrlCallback($value){
		$this->urlcallback = $value;
	}
	
	/**
	 * getTranslator
	 * Get the translator for the module
	 * @return Zend_Translate
	 */
	public function getTranslator(){
		return $this->translator;
	}
	
	/**
	 * getUrlOk
	 * Get the Url OK
	 * @return string
	 */
	public function getUrlOk(){
		return $this->urlok;
	}
	
	/**
	 * getUrlKo
	 * Get the Url Ko
	 * @return string
	 */
	public function getUrlKo(){
		return $this->urlko;
	}
	
	/**
	 * getUrlCallback
	 * Get the Url Callback
	 * @return string
	 */
	public function getUrlCallback(){
		return $this->urlcallback;
	}
	
	/**
	 * 
	 * Get the order set by the constructor
	 * @param array $order
	 */
	public function getOrder(){
		return $this->order;
	}
	
	/**
	 * Check if the form has been set as hidden
	 * @return boolean 
	 */
	public function isHidden(){
		return $this->hiddenform;
	}
	
	/**
	 * Check if the form must be start automatically
	 * @return boolean 
	 */
	public function doRedirect(){
		return $this->redirect;
	}
	
	/**
	 * 
	 * Get the configuration of the module
	 * @param array $order
	 */
	public function getModule(){
		return $this->module;
	}
	
}