<?php

class CartItem {

	// Item attributes are all protected:
	protected $id;
	protected $qty;
	protected $sku;
	protected $term;
	protected $type;
	protected $name;
	protected $cost;
	protected $domain;
	protected $unitprice;
	protected $subtotal;
	protected $options;

	/**
	 * @return the $cost
	 */
	public function getCost() {
		return $this->cost;
	}

	/**
	 * @param field_type $cost
	 */
	public function setCost($cost) {
		$this->cost = $cost;
		return $this;
	}

	/**
	 * @return the $sku
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * @param field_type $sku
	 */
	public function setSku($sku) {
		$this->sku = $sku;
		return $this;
	}

	/**
	 * @return the $domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * @param field_type $domain
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/**
	 * @return the $qty
	 */
	public function getQty() {
		return $this->qty;
	}

	/**
	 * @param field_type $qty
	 */
	public function setQty($qty) {
		$this->qty = $qty;
		return $this;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $unitprice
	 */
	public function getUnitprice() {
		return $this->unitprice;
	}

	/**
	 * @return the $subtotal
	 */
	public function getSubtotal() {
		return $this->subtotal;
	}

	/**
	 * @param NULL $id
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param NULL $term
	 */
	public function setTerm($term) {
		$this->term = $term;
		return $this;
	}

	/**
	 * @param NULL $type
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @param field_type $unitprice
	 */
	public function setUnitprice($unitprice) {
		$this->unitprice = $unitprice;
		return $this;
	}

	/**
	 * @param field_type $subtotal
	 */
	public function setSubtotal($subtotal) {
		$this->subtotal = $subtotal;
		return $this;
	}

	/**
	 * @param field_type $options
	 */
	public function setOptions($options) {
		$this->options = $options;
		return $this;
	}

	// Method that returns the ID:
	public function getId()	{
		return $this->id;
	}

	// Method that returns the term:
	public function getTerm() {
		return $this->term;
	}

	// Method that returns the type:
	public function getType() {
		return $this->type;
	}

	// Method that returns the options:
	public function getOptions() {
		return $this->options;
	}
	
	// Constructor populates the attributes:
	public function __construct()	{
		$this->id = null;
		$this->term = null;
		$this->qty = 1;
		$this->name = null;
		$this->unitprice = null;
		$this->subtotal = null;
		$this->type = null;
		$this->options = null;
	}

} // End of Item class.