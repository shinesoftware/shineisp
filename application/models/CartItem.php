<?php

class CartItem {

	// Item attributes are all protected:
	protected $id;
	protected $term;
	protected $type;
	protected $options;

	// Constructor populates the attributes:
	public function __construct($id, $type, $term=NULL, $options=array())	{
		$this->id = $id;
		$this->term = $term;
		$this->type = $type;
		$this->options = $options;
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

} // End of Item class.